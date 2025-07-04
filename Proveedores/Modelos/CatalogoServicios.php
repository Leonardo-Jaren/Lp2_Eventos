<?php
require_once '../../conexion_db.php';

class CatalogoServicios {
    public function obtenerTodosLosServicios() {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlSelect = "SELECT * FROM servicios_proveedor ORDER BY nombre_servicio";
        $stmt = $conexion->query($sqlSelect);
        $db->desconectar();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorProveedor($id_proveedor) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlSelect = "SELECT * FROM servicios_proveedor WHERE id_proveedor = '$id_proveedor' ORDER BY nombre_servicio";
        $stmt = $conexion->query($sqlSelect);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->desconectar();
        return $proveedor;
    }

    public function obtenerServicio($id) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlSelect = "SELECT * FROM servicios_proveedor WHERE id = '$id'";
        $stmt = $conexion->query($sqlSelect);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->desconectar();
        return $servicio;
    }

    public function guardarServicio($id_proveedor, $nombre_servicio, $precio_base) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlInsert = "INSERT INTO servicios_proveedor (id_proveedor, nombre_servicio, precio) 
                        VALUES ('$id_proveedor', '$nombre_servicio', '$precio_base')";
        $resultado = $conexion->exec($sqlInsert);
        $db->desconectar();
        return $resultado;
    }

    public function actualizarServicio($id_servicio, $id_proveedor, $nombre_servicio, $precio_base) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlUpdate = "UPDATE servicios_proveedor 
                      SET id_proveedor = '$id_proveedor', 
                          nombre_servicio = '$nombre_servicio', 
                          precio = '$precio_base' 
                      WHERE id = '$id_servicio'";
        $resultado = $conexion->exec($sqlUpdate);
        $db->desconectar();
        return $resultado;
    }

    public function eliminarServicio($id_servicio) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlDelete = "DELETE FROM servicios_proveedor WHERE id = '$id_servicio'";
        $resultado = $conexion->exec($sqlDelete);
        $db->desconectar();
        return $resultado;
    }

    public function obtenerServiciosPorProveedor($id_proveedor) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sqlSelect = "SELECT * FROM servicios_proveedor WHERE id_proveedor = '$id_proveedor' ORDER BY nombre_servicio";
        $stmt = $conexion->query($sqlSelect);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->desconectar();
        return $servicios;
    }
}