<?php

require_once '../../conexion_db.php';

class Sesion {
    public function verificarNombre($correo) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $resultado = $conexion->query($sqlSelect);
        $conn->desconectar();
        return $resultado;
    }

    public function cerrarSesion() {

    }
}
