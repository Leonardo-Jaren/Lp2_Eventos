<?php

require_once '../../conexion_db.php';

class Reserva {
    private $conexion;

    public function __construct() {
        $conn = new ConexionDB();
        $this->conexion = $conn->conectar();
    }

    public function crearReserva($titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_usuario, $id_recurso = null) {
        try {
            // Verificar disponibilidad básica
            if (!$this->verificarDisponibilidadSimple($fecha_evento, $hora_inicio, $hora_fin, $id_usuario)) {
                throw new Exception("Ya existe una reserva en esa fecha y hora para este organizador");
            }

            // Insertar en la tabla eventos
            $sql = "INSERT INTO eventos (titulo, descripcion, fecha_evento, hora_inicio, hora_fin, id_usuario, estado) 
                    VALUES (:titulo, :descripcion, :fecha_evento, :hora_inicio, :hora_fin, :id_usuario, 'pendiente')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fecha_evento', $fecha_evento);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':id_usuario', $id_usuario);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear la reserva");
            }

            $id_evento = $this->conexion->lastInsertId();

            // Si se especifica un recurso, crear una entrada en la tabla reservas
            if ($id_recurso) {
                $sql_reserva = "INSERT INTO reservas (id_evento, fecha, hora_inicio, hora_fin, id_recurso, estado) 
                               VALUES (:id_evento, :fecha, :hora_inicio, :hora_fin, :id_recurso, 'reservado')";
                
                $stmt_reserva = $this->conexion->prepare($sql_reserva);
                $stmt_reserva->bindParam(':id_evento', $id_evento);
                $stmt_reserva->bindParam(':fecha', $fecha_evento);
                $stmt_reserva->bindParam(':hora_inicio', $hora_inicio);
                $stmt_reserva->bindParam(':hora_fin', $hora_fin);
                $stmt_reserva->bindParam(':id_recurso', $id_recurso);
                $stmt_reserva->execute();
            }

            return $id_evento;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, $id_usuario, $id_evento_excluir = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM eventos e 
                WHERE e.id_usuario = :id_usuario 
                AND e.fecha_evento = :fecha 
                AND e.estado != 'cancelado'
                AND (
                    (e.hora_inicio <= :hora_inicio AND e.hora_fin > :hora_inicio) 
                    OR (e.hora_inicio < :hora_fin AND e.hora_fin >= :hora_fin)
                    OR (e.hora_inicio >= :hora_inicio AND e.hora_fin <= :hora_fin)
                )";
        
        if ($id_evento_excluir) {
            $sql .= " AND e.id != :id_evento_excluir";
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        
        if ($id_evento_excluir) {
            $stmt->bindParam(':id_evento_excluir', $id_evento_excluir);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] == 0;
    }

    public function verificarDisponibilidadEdicion($fecha, $hora_inicio, $hora_fin, $id_evento_excluir) {
        return $this->verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, null, $id_evento_excluir);
    }

    public function editarReserva($id_evento, $titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_recurso = null) {
        try {
            $this->conexion->beginTransaction();

            $evento = $this->obtenerEventoPorId($id_evento);
            if (!$evento) {
                throw new Exception("Evento no encontrado");
            }

            if ($evento['fecha_evento'] != $fecha_evento || 
                $evento['hora_inicio'] != $hora_inicio || 
                $evento['hora_fin'] != $hora_fin) {
                
                if (!$this->verificarDisponibilidad($fecha_evento, $hora_inicio, $hora_fin, $evento['id_usuario'], $id_evento)) {
                    throw new Exception("La nueva fecha y hora ya están ocupadas");
                }
            }

            $sqlEvento = "UPDATE eventos 
                         SET titulo = :titulo, descripcion = :descripcion, 
                             fecha_evento = :fecha_evento, hora_inicio = :hora_inicio, 
                             hora_fin = :hora_fin
                         WHERE id = :id_evento";
            
            $stmtEvento = $this->conexion->prepare($sqlEvento);
            $stmtEvento->bindParam(':titulo', $titulo);
            $stmtEvento->bindParam(':descripcion', $descripcion);
            $stmtEvento->bindParam(':fecha_evento', $fecha_evento);
            $stmtEvento->bindParam(':hora_inicio', $hora_inicio);
            $stmtEvento->bindParam(':hora_fin', $hora_fin);
            $stmtEvento->bindParam(':id_evento', $id_evento);
            
            if (!$stmtEvento->execute()) {
                throw new Exception("Error al actualizar el evento");
            }

            try {
                $sqlReserva = "UPDATE reservas 
                              SET fecha = :fecha, hora_inicio = :hora_inicio, 
                                  hora_fin = :hora_fin, id_recurso = :id_recurso 
                              WHERE id_evento = :id_evento";
                
                $stmtReserva = $this->conexion->prepare($sqlReserva);
                $stmtReserva->bindParam(':fecha', $fecha_evento);
                $stmtReserva->bindParam(':hora_inicio', $hora_inicio);
                $stmtReserva->bindParam(':hora_fin', $hora_fin);
                $stmtReserva->bindParam(':id_recurso', $id_recurso);
                $stmtReserva->bindParam(':id_evento', $id_evento);
                $stmtReserva->execute();
            } catch (Exception $e) {
            }

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            throw $e;
        }
    }

    public function cancelarReserva($id_evento, $motivo = '', $penalidad_porcentaje = 0) {
        try {
            $this->conexion->beginTransaction();

            $sqlEvento = "UPDATE eventos SET estado = 'cancelado' WHERE id = :id_evento";
            $stmtEvento = $this->conexion->prepare($sqlEvento);
            $stmtEvento->bindParam(':id_evento', $id_evento);
            
            if (!$stmtEvento->execute()) {
                throw new Exception("Error al cancelar el evento");
            }

            $sqlReserva = "UPDATE reservas SET estado = 'cancelado' WHERE id_evento = :id_evento";
            $stmtReserva = $this->conexion->prepare($sqlReserva);
            $stmtReserva->bindParam(':id_evento', $id_evento);
            
            if (!$stmtReserva->execute()) {
                throw new Exception("Error al cancelar la reserva");
            }

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            throw $e;
        }
    }

    public function cambiarFechaReserva($id_evento, $nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $motivo = '') {
        try {
            $evento = $this->obtenerEventoPorId($id_evento);
            if (!$evento) {
                throw new Exception("Evento no encontrado");
            }

            if (!$this->verificarDisponibilidad($nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $evento['id_usuario'], $id_evento)) {
                throw new Exception("La nueva fecha y hora ya están ocupadas");
            }

            $stmt = $this->conexion->prepare("
                UPDATE eventos 
                SET fecha_evento = ?, hora_inicio = ?, hora_fin = ?
                WHERE id = ?
            ");
            $stmt->execute([$nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $id_evento]);

            if (!empty($motivo)) {
                $stmt = $this->conexion->prepare("
                    INSERT INTO historial_eventos (id_evento, accion, observaciones, fecha_accion) 
                    VALUES (?, 'cambio_fecha', ?, NOW())
                ");
                $stmt->execute([$id_evento, 'Fecha cambiada: ' . $motivo]);
            }

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function obtenerReservas($filtros = []) {
        $sql = "SELECT e.*, r.id as reserva_id, r.estado as estado_reserva, 
                       u.nombres as organizador, rec.tipo as tipo_recurso
                FROM eventos e 
                LEFT JOIN reservas r ON e.id = r.id_evento 
                LEFT JOIN usuarios u ON e.id_usuario = u.id 
                LEFT JOIN recursos rec ON r.id_recurso = rec.id 
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND e.fecha_evento >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND e.fecha_evento <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['organizador'])) {
            $sql .= " AND u.nombres LIKE :organizador";
            $params[':organizador'] = '%' . $filtros['organizador'] . '%';
        }

        if (!empty($filtros['titulo'])) {
            $sql .= " AND e.titulo LIKE :titulo";
            $params[':titulo'] = '%' . $filtros['titulo'] . '%';
        }

        $sql .= " ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $stmt->bindValue($key, implode(',', $value));
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodasReservas() {
        try {
            $sql = "SELECT e.id, e.titulo, e.descripcion, e.fecha_evento, e.hora_inicio, e.hora_fin, 
                           e.estado, u.nombres as organizador, 
                           COALESCE(rec.tipo, 'Sin recurso') as tipo_recurso
                    FROM eventos e 
                    INNER JOIN usuarios u ON e.id_usuario = u.id 
                    LEFT JOIN reservas r ON e.id = r.id_evento 
                    LEFT JOIN recursos rec ON r.id_recurso = rec.id 
                    ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Si falla, intentar solo con eventos
            $sql = "SELECT e.id, e.titulo, e.descripcion, e.fecha_evento, e.hora_inicio, e.hora_fin, 
                           e.estado, 'Usuario' as organizador, 'Sin recurso' as tipo_recurso
                    FROM eventos e 
                    ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // Obtener historial de reservas
    public function obtenerHistorialReservas($filtros = []) {
        $sql = "SELECT e.*, r.id as reserva_id, r.estado as estado_reserva, 
                       u.nombres as organizador, rec.tipo as tipo_recurso
                FROM eventos e 
                LEFT JOIN reservas r ON e.id = r.id_evento 
                LEFT JOIN usuarios u ON e.id_usuario = u.id 
                LEFT JOIN recursos rec ON r.id_recurso = rec.id 
                WHERE e.fecha_evento < CURDATE()";

        $params = [];

        // Filtro por usuario
        if (!empty($filtros['id_usuario'])) {
            $sql .= " AND e.id_usuario = :id_usuario";
            $params[':id_usuario'] = $filtros['id_usuario'];
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        // Filtro por fecha desde
        if (!empty($filtros['desde'])) {
            $sql .= " AND e.fecha_evento >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['desde'];
        }

        // Filtro por fecha hasta
        if (!empty($filtros['hasta'])) {
            $sql .= " AND e.fecha_evento <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['hasta'];
        }

        $limite = isset($filtros['limite']) ? (int)$filtros['limite'] : 50;
        $sql .= " ORDER BY e.fecha_evento DESC LIMIT :limite";

        $stmt = $this->conexion->prepare($sql);
        
        // Bind de parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEventoPorId($id_evento) {
        try {
            $sql = "SELECT e.*, r.id as reserva_id, r.estado as estado_reserva, r.id_recurso,
                           u.nombres as organizador, rec.tipo as tipo_recurso
                    FROM eventos e 
                    LEFT JOIN reservas r ON e.id = r.id_evento 
                    LEFT JOIN usuarios u ON e.id_usuario = u.id 
                    LEFT JOIN recursos rec ON r.id_recurso = rec.id 
                    WHERE e.id = :id_evento";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_evento', $id_evento);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                return $resultado;
            }
        } catch (Exception $e) {
        }

        try {
            $sql = "SELECT e.*, u.nombres as organizador
                    FROM eventos e 
                    LEFT JOIN usuarios u ON e.id_usuario = u.id 
                    WHERE e.id = :id_evento";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_evento', $id_evento);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                // Agregar campos faltantes con valores por defecto
                $resultado['reserva_id'] = null;
                $resultado['estado_reserva'] = $resultado['estado'] ?? 'pendiente';
                $resultado['id_recurso'] = null;
                $resultado['tipo_recurso'] = 'Sin recurso';
                return $resultado;
            }
        } catch (Exception $e) {
            
        }

        
        $sql = "SELECT e.*, 'Usuario' as organizador
                FROM eventos e 
                WHERE e.id = :id_evento";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_evento', $id_evento);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $resultado['reserva_id'] = null;
            $resultado['estado_reserva'] = $resultado['estado'] ?? 'pendiente';
            $resultado['id_recurso'] = null;
            $resultado['tipo_recurso'] = 'Sin recurso';
        }
        
        return $resultado;
    }

    public function obtenerCalendarioDisponibilidad($mes, $año, $id_usuario = null) {
        $sql = "SELECT DATE(e.fecha_evento) as fecha, 
                       COUNT(*) as eventos_dia,
                       GROUP_CONCAT(CONCAT(e.hora_inicio, '-', e.hora_fin)) as horarios
                FROM eventos e 
                WHERE MONTH(e.fecha_evento) = :mes 
                AND YEAR(e.fecha_evento) = :año 
                AND e.estado != 'cancelado'";

        $params = [':mes' => $mes, ':año' => $año];

        if ($id_usuario) {
            $sql .= " AND e.id_usuario = :id_usuario";
            $params[':id_usuario'] = $id_usuario;
        }

        $sql .= " GROUP BY DATE(e.fecha_evento)";

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $stmt->bindValue($key, implode(',', $value));
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  
    public function obtenerRecursosDisponibles() {
        // Método temporalmente deshabilitado - será manejado por otro desarrollador
        return [];
        
        // Código original comentado:
        // $sql = "SELECT * FROM recursos WHERE disponible = 1 ORDER BY tipo";
        // $stmt = $this->conexion->prepare($sql);
        // $stmt->execute();
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar disponibilidad simple usando la tabla eventos
    public function verificarDisponibilidadSimple($fecha, $hora_inicio, $hora_fin, $id_usuario) {
        try {
            $sql = "SELECT COUNT(*) as conflictos 
                    FROM eventos 
                    WHERE id_usuario = :id_usuario 
                    AND fecha_evento = :fecha
                    AND estado != 'cancelado'
                    AND (
                        (hora_inicio BETWEEN :hora_inicio AND :hora_fin)
                        OR (hora_fin BETWEEN :hora_inicio AND :hora_fin)
                        OR (:hora_inicio BETWEEN hora_inicio AND hora_fin)
                        OR (:hora_fin BETWEEN hora_inicio AND hora_fin)
                    )";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['conflictos'] == 0;
        } catch (Exception $e) {
            // En caso de error, asumir que no está disponible por seguridad
            return false;
        }
    }
}

?>