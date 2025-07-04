<?php

require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';

class ProveedorController {
    public function registrarProveedor(array $datos) {
        $proveedor = new Proveedor();
        
        $id_usuario = null;
        if (isset($datos['id_usuario']) && !empty(trim($datos['id_usuario']))) {
            $id_usuario = (int)$datos['id_usuario'];
        }
        
        $resultado = $proveedor->guardarProveedor(
            $datos['nombre_empresa'],
            $datos['telefono'],
            $datos['direccion'],
            $id_usuario
        );
        
        if ($resultado['success']) {
            header('Location: verProveedor.php');
            exit();
        } else {
            return $resultado['message'];
        }
    }

    public function editarProveedor(array $datos) {
        $proveedor = new Proveedor();
        
        $id_usuario = null;
        if (isset($datos['id_usuario']) && !empty(trim($datos['id_usuario']))) {
            $id_usuario = (int)$datos['id_usuario'];
        }
        
        $resultado = $proveedor->actualizarProveedor(
            $datos['id'],
            $datos['nombre_empresa'],
            $datos['telefono'],
            $datos['direccion'],
            $id_usuario
        );
        
        if ($resultado['success']) {
            header('Location: verProveedor.php');
            exit();
        } else {
            return $resultado['message'];
        }
    }

    public function eliminarProveedor($id) {
        $proveedor = new Proveedor();
        $resultado = $proveedor->eliminarProveedor($id);
        $id = $_GET['id'] ?? null;
        if ($resultado) {
            header('Location: verProveedor.php');
            exit();
        } else {
            return "Error al eliminar el proveedor.";
        }
    }
}