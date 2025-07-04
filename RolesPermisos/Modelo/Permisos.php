<?php

require_once '../../../conexion_db.php';

class Permisos {
    public function crearPermiso($nombre_permiso, $descripcion) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO permisos (nombre_permiso, descripcion) VALUES ('$nombre_permiso', '$descripcion')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        return $resultado;
    }

    public function actualizarPermiso($id, $nombre_permiso, $descripcion) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlUpdate = "UPDATE permisos 
                        SET nombre_permiso = '$nombre_permiso', descripcion = '$descripcion' 
                        WHERE id = '$id'";
        $resultado = $conexion->exec($sqlUpdate);
        $conn->desconectar();
        return $resultado;
    }
    
    public function eliminarPermiso($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlDelete = "DELETE FROM permisos WHERE id = '$id'";
        $resultado = $conexion->exec($sqlDelete);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodosLosPermisos() {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM permisos";
        $resultado = $conexion->query($sqlSelect);
        $conn->desconectar();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPermisoPorId($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM permisos WHERE id = '$id'";
        $resultado = $conexion->query($sqlSelect);
        $conn->desconectar();
        return $resultado->fetch(PDO::FETCH_ASSOC);
    }
}