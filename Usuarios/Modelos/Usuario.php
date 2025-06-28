<?php

require_once '../../conexion_db.php';

class Usuario {
    public function registrarUsuario($nombre, $correo, $password, $rol) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO usuarios (nombre, correo, password, id_rol) 
                      VALUES ('$nombre', '$correo', '$password', '$rol')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        return $resultado;
    }
}