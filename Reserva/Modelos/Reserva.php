<?php

require_once "../../conexion_db.php";

class Reserva{
    public function mostrar(){
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

    public function guardar($titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_organizador = null){
        // El cliente siempre es quien está logueado
        session_start();
        $id_cliente = $_SESSION['id'];
        
        // Verificar disponibilidad para el cliente
        if (!$this->verificarDisponibilidad($fecha_evento, $hora_inicio, $hora_fin, $id_cliente)) {
            return 0; // No disponible
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();

        $sql = "INSERT INTO eventos(titulo, descripcion, fecha_evento, hora_inicio, hora_fin, id_cliente, id_organizador, estado) 
                VALUES ('$titulo', '$descripcion', '$fecha_evento', '$hora_inicio', '$hora_fin', '$id_cliente', " . 
                ($id_organizador ? "'$id_organizador'" : "NULL") . ", 'confirmado')";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        
        return $resultado;
    }

    public function eliminar($id){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET estado = 'cancelado' WHERE id = $id";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function buscar($id){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT * FROM eventos WHERE id = $id";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerEventoPorId($id){
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
    
    public function actualizar($id, $titulo, $descripcion, $fecha_evento, $hora_inicio, $hora_fin, $id_usuario){
        // Verificar disponibilidad antes de actualizar (excluyendo el evento actual)
        if (!$this->verificarDisponibilidadExcluir($fecha_evento, $hora_inicio, $hora_fin, $id_usuario, $id)) {
            return 0; // No disponible
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

    public function verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, $id_usuario){
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

    public function verificarDisponibilidadExcluir($fecha, $hora_inicio, $hora_fin, $id_usuario, $id_excluir){
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

    public function obtenerPorUsuario($id_usuario){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT * FROM eventos WHERE id_cliente = '$id_usuario' AND estado != 'cancelado' 
                ORDER BY fecha_evento DESC, hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodasReservas(){
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

    public function obtenerHistorialReservas($id_usuario = null, $limite = 50){
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

    public function cambiarFechaReserva($id_evento, $nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $motivo = ''){
        // Obtener el evento actual
        $evento_actual = $this->buscar($id_evento);
        if (!$evento_actual) {
            return 0; // Evento no encontrado
        }
        
        $evento_data = $evento_actual->fetch();
        
        // Verificar disponibilidad antes de cambiar
        if (!$this->verificarDisponibilidadExcluir($nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $evento_data['id_cliente'], $id_evento)) {
            return 0; // No disponible
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE eventos SET fecha_evento = '$nueva_fecha', hora_inicio = '$nueva_hora_inicio', 
                hora_fin = '$nueva_hora_fin' WHERE id = $id_evento";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerReservasConFiltros($filtros = []){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT e.*, 
                       u_cliente.nombres as cliente_nombres, u_cliente.apellidos as cliente_apellidos,
                       u_organizador.nombres as organizador_nombres, u_organizador.apellidos as organizador_apellidos,
                       CONCAT(u_organizador.nombres, ' ', u_organizador.apellidos) as organizador
                FROM eventos e 
                LEFT JOIN usuarios u_cliente ON e.id_cliente = u_cliente.id 
                LEFT JOIN usuarios u_organizador ON e.id_organizador = u_organizador.id
                WHERE 1=1";
        
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
            $sql .= " AND e.id_cliente = '" . $filtros['id_usuario'] . "'";
        }
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (e.titulo LIKE '%" . $filtros['buscar'] . "%' OR e.descripcion LIKE '%" . $filtros['buscar'] . "%')";
        }
        
        $sql .= " ORDER BY e.fecha_evento DESC, e.hora_inicio DESC";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerCalendarioDisponibilidad($mes, $año, $id_usuario = null){
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
}
?>