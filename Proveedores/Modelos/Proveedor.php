<?php

require_once 'conexion.php';

class Proveedor{

    private $nombre;
    private $correo;
    private $empresa;

    public function mostrar(){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT * FROM proveedores";
        $resultado = $conexion->query($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function guardar($nombre,$correo,$empresa){
        $ccnn = new ConexionDB();
        $conexion = $ccnn->conectar();
        $sql = "INSERT INTO proveedores(nombre,correo,empresa) 
        VALUES('$nombre','$correo','$empresa')";
        $resultado = $conexion->exec($sql);
        $ccnn->desconectar();
        return $resultado;
    }

    public function actualizar($id,$nombre,$correo,$empresa){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE proveedores SET nombre='$nombre', correo='$correo', empresa='$empresa' WHERE id='$id'";
        $resultado = $conexion->exec($sql);
        $conn->desconectar();
        return $resultado;
    }

    public function eliminar($id){
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "DELETE FROM proveedores WHERE id=$id";
        $resultado = $conexion->exec($sql);
        $conn->desconectar();
        return $resultado;
    }
}




