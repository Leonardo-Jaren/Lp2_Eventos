<?php
require_once __DIR__ . '/../../conexion_db.php';

class Reserva {
    private $conexion;

    public function __construct() {
        $conn = new ConexionDB();
        $this->conexion = $conn->conectar();
    }

    public function crearReserva($titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_usuario, $id_recurso = null) {
        if (!$this->verificarDisponibilidad($fecha_evento, $hora_inicio, $hora_fin, $id_usuario)) {
            throw new Exception("Ya existe un evento en esa fecha y hora");
        }
        $stmt = $this->conexion->prepare("INSERT INTO eventos (titulo, descripcion, fecha_evento, hora_inicio, hora_fin, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt->execute([$titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_usuario])) {
            throw new Exception("Error al crear el evento");
        }
        return $this->conexion->lastInsertId();
    }

    public function verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, $id_usuario, $id_evento_excluir = null) {
        $sql = "SELECT COUNT(*) as total FROM eventos WHERE id_usuario = ? AND fecha_evento = ? AND estado != 'cancelado'
                AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?) OR (hora_inicio >= ? AND hora_fin <= ?))";
        $params = [$id_usuario, $fecha, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin, $hora_inicio, $hora_fin];
        if ($id_evento_excluir) {
            $sql .= " AND id != ?";
            $params[] = $id_evento_excluir;
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] == 0;
    }

    public function editarReserva($id_evento, $titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_recurso = null) {
        $evento = $this->obtenerEventoPorId($id_evento);
        if (!$evento) throw new Exception("Evento no encontrado");
        if ($evento['fecha_evento'] != $fecha_evento || $evento['hora_inicio'] != $hora_inicio || $evento['hora_fin'] != $hora_fin) {
            if (!$this->verificarDisponibilidad($fecha_evento, $hora_inicio, $hora_fin, $evento['id_usuario'], $id_evento)) {
                throw new Exception("La nueva fecha y hora ya están ocupadas");
            }
        }
        $stmt = $this->conexion->prepare("UPDATE eventos SET titulo = ?, descripcion = ?, fecha_evento = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?");
        return $stmt->execute([$titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_evento]);
    }

    public function cancelarReserva($id_evento, $motivo = '') {
        $stmt = $this->conexion->prepare("UPDATE eventos SET estado = 'cancelado' WHERE id = ?");
        return $stmt->execute([$id_evento]);
    }

    public function cambiarFechaReserva($id_evento, $nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $motivo = '') {
        $evento = $this->obtenerEventoPorId($id_evento);
        if (!$evento) throw new Exception("Evento no encontrado");
        if (!$this->verificarDisponibilidad($nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $evento['id_usuario'], $id_evento)) {
            throw new Exception("La nueva fecha y hora ya están ocupadas");
        }
        $stmt = $this->conexion->prepare("UPDATE eventos SET fecha_evento = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?");
        return $stmt->execute([$nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $id_evento]);
    }

    public function obtenerReservas($filtros = []) {
        $sql = "SELECT e.*, u.nombres as organizador FROM eventos e LEFT JOIN usuarios u ON e.id_usuario = u.id WHERE 1=1";
        $params = [];
        foreach (['fecha_inicio' => 'e.fecha_evento >= ?', 'fecha_fin' => 'e.fecha_evento <= ?', 'estado' => 'e.estado = ?', 'id_usuario' => 'e.id_usuario = ?'] as $key => $condition) {
            if (!empty($filtros[$key])) {
                $sql .= " AND $condition";
                $params[] = $filtros[$key];
            }
        }
        $sql .= " ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodasReservas() {
        $stmt = $this->conexion->prepare("SELECT e.id, e.titulo, e.descripcion, e.fecha_evento, e.hora_inicio, e.hora_fin, e.estado, u.nombres as organizador FROM eventos e INNER JOIN usuarios u ON e.id_usuario = u.id ORDER BY e.fecha_evento DESC, e.hora_inicio DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialReservas($id_usuario = null, $limite = 50) {
        $sql = "SELECT e.*, u.nombres as organizador FROM eventos e LEFT JOIN usuarios u ON e.id_usuario = u.id WHERE e.fecha_evento < CURDATE()";
        $params = [];
        if ($id_usuario) {
            $sql .= " AND e.id_usuario = ?";
            $params[] = $id_usuario;
        }
        $sql .= " ORDER BY e.fecha_evento DESC LIMIT " . intval($limite);
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEventoPorId($id_evento) {
        $stmt = $this->conexion->prepare("SELECT e.*, u.nombres as organizador FROM eventos e LEFT JOIN usuarios u ON e.id_usuario = u.id WHERE e.id = ?");
        $stmt->execute([$id_evento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCalendarioDisponibilidad($mes, $año, $id_usuario = null) {
        $sql = "SELECT DATE(fecha_evento) as fecha, COUNT(*) as eventos FROM eventos WHERE MONTH(fecha_evento) = ? AND YEAR(fecha_evento) = ? AND estado != 'cancelado'";
        $params = [$mes, $año];
        if ($id_usuario) {
            $sql .= " AND id_usuario = ?";
            $params[] = $id_usuario;
        }
        $sql .= " GROUP BY DATE(fecha_evento)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRecursosDisponibles() {
        $stmt = $this->conexion->prepare("SELECT * FROM recursos WHERE disponible = 1 ORDER BY tipo, descripcion");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>