<?php

require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';

class ProveedorController {
    public function registrarProveedor(array $datos) {
        $proveedor = new Proveedor();
        $resultado = $proveedor->guardarProveedor(
            $datos['nombre'],
            $datos['correo'],
            $datos['empresa']
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
        $resultado = $proveedor->actualizarProveedor(
            $datos['id'],
            $datos['nombre'],
            $datos['correo'],
            $datos['empresa']
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