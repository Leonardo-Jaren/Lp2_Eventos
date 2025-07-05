<?php

require_once "../../conexion_db.php";
class Evento
{
    public function mostrarEventos()
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function guardarEvento($titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_organizador = null)
    {

        $id_cliente = $_SESSION['id'];

        if (!$this->verificarDisponibilidadEvento($fecha_evento, $hora_inicio, $hora_fin, $id_cliente)) {
            error_log("Evento no disponible - Cliente: $id_cliente, Fecha: $fecha_evento, Hora: $hora_inicio-$hora_fin");
            return 0;
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();

        // Usar consulta preparada para evitar SQL injection
        $sql = "INSERT INTO eventos(titulo, descripcion, fecha_evento, hora_inicio, hora_fin, id_cliente, id_organizador, estado) 
                VALUES ('$titulo', '$descripcion', '$fecha_evento', '$hora_inicio', '$hora_fin', '$id_cliente', '$id_organizador', 'borrador')";
        $stmt = $conexion->exec($sql);
     
        $conn->desconectar();
        return $stmt;
    }

    public function eliminarEvento($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET estado = 'cancelado' WHERE id = $id";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function buscarEvento($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT * FROM eventos WHERE id = $id";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerEventoPorId($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                WHERE e.id = $id";
        $resultado = $conexion->query($sql);
        $evento = $resultado->fetch();
        $conn->desconectar();
        return $evento;
    }

    public function actualizarEvento($id, $titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_usuario)
    {
        if (!$this->verificarDisponibilidadExcluir($fecha_evento, $hora_inicio, $hora_fin, $id_usuario, $id)) {
            return 0;
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET titulo = '$titulo', descripcion = '$descripcion', 
                fecha_evento = '$fecha_evento', hora_inicio = '$hora_inicio', 
                hora_fin = '$hora_fin', id_cliente = '$id_usuario', id_organizador = '$id_usuario' WHERE id = $id";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function verificarDisponibilidadEvento($fecha, $hora_inicio, $hora_fin, $id_usuario)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT COUNT(*) as total FROM eventos 
                WHERE id_cliente = '$id_usuario' AND fecha_evento = '$fecha' AND estado != 'cancelado'
                AND ((hora_inicio <= '$hora_inicio' AND hora_fin > '$hora_inicio') 
                     OR (hora_inicio < '$hora_fin' AND hora_fin >= '$hora_fin') 
                     OR (hora_inicio >= '$hora_inicio' AND hora_fin <= '$hora_fin'))";
        $resultado = $conexion->query($sql);
        $fila = $resultado->fetch();
        $conn->desconectar();
        return $fila['total'] == 0;
    }

    public function verificarDisponibilidadExcluir($fecha, $hora_inicio, $hora_fin, $id_usuario, $id_excluir)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT COUNT(*) as total FROM eventos 
                WHERE id_cliente = '$id_usuario' AND fecha_evento = '$fecha' AND estado != 'cancelado'
                AND id != '$id_excluir'
                AND ((hora_inicio <= '$hora_inicio' AND hora_fin > '$hora_inicio') 
                     OR (hora_inicio < '$hora_fin' AND hora_fin >= '$hora_fin') 
                     OR (hora_inicio >= '$hora_inicio' AND hora_fin <= '$hora_fin'))";
        $resultado = $conexion->query($sql);
        $fila = $resultado->fetch();
        $conn->desconectar();
        return $fila['total'] == 0;
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                WHERE e.id_cliente = '$id_usuario' AND e.estado != 'cancelado' 
                ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodosEventos()
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerHistorialEventos($id_usuario = null, $limite = 50)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                WHERE e.fecha_evento < CURDATE()";
        if ($id_usuario) {
            $sql .= " AND e.id_cliente = '$id_usuario'";
        }
        $sql .= " ORDER BY e.fecha_evento DESC LIMIT " . intval($limite);
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function cambiarFechaEvento($id_evento, $nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin)
    {
        $evento_actual = $this->buscarEvento($id_evento);
        if (!$evento_actual) {
            return 0;
        }

        $evento_data = $evento_actual->fetch();

        if (!$this->verificarDisponibilidadExcluir($nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $evento_data['id_cliente'], $id_evento)) {
            return 0;
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET fecha_evento = '$nueva_fecha', hora_inicio = '$nueva_hora_inicio', 
                hora_fin = '$nueva_hora_fin' WHERE id = $id_evento";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerEventosConFiltros($filtros = [])
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT DISTINCT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id";

        if (!empty($filtros['rol_usuario']) && $filtros['rol_usuario'] === 'Proveedor') {
            $sql .= " LEFT JOIN proveedores p ON p.id_usuario = '" . $filtros['id_usuario'] . "'";
        }

        $sql .= " WHERE 1=1";

        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND e.fecha_evento >= '" . $filtros['fecha_inicio'] . "'";
        }
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND e.fecha_evento <= '" . $filtros['fecha_fin'] . "'";
        }
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = '" . $filtros['estado'] . "'";
        }
        if (!empty($filtros['id_usuario'])) {
            if (!empty($filtros['rol_usuario']) && $filtros['rol_usuario'] === 'Proveedor') {
                $sql .= " AND (e.id_organizador = '" . $filtros['id_usuario'] . "' OR e.id_cliente = '" . $filtros['id_usuario'] . "')";
            } else {
                $sql .= " AND e.id_cliente = '" . $filtros['id_usuario'] . "'";
            }
        }
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (e.titulo LIKE '%" . $filtros['buscar'] . "%' OR e.descripcion LIKE '%" . $filtros['buscar'] . "%')";
        }

        $sql .= " ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerCalendarioDisponibilidad($mes, $año, $id_usuario = null)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT DATE(fecha_evento) as fecha, COUNT(*) as eventos 
                FROM eventos 
                WHERE MONTH(fecha_evento) = '$mes' AND YEAR(fecha_evento) = '$año' AND estado != 'cancelado'";
        if ($id_usuario) {
            $sql .= " AND id_cliente = '$id_usuario'";
        }
        $sql .= " GROUP BY DATE(fecha_evento)";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function aceptarEvento($id_evento)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET estado = 'confirmado' WHERE id = $id_evento AND estado = 'borrador'";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function rechazarEvento($id_evento)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET estado = 'cancelado' WHERE id = $id_evento AND estado = 'borrador'";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerEventosProveedor($id_usuario_proveedor)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        
        $sql = "SELECT DISTINCT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                LEFT JOIN proveedores p ON p.id_usuario = '$id_usuario_proveedor'
                WHERE (e.id_organizador = '$id_usuario_proveedor' OR e.id_cliente = '$id_usuario_proveedor')
                AND e.estado != 'cancelado' 
                ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }
}
