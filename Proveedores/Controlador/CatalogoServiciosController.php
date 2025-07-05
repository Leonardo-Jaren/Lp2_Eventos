<?php 

require_once '../Modelos/CatalogoServicios.php';

class CatalogoServiciosController {
    public function registrarProveedor(array $datos) {
        $catalogoServicios = new CatalogoServicios();
        $resultado = $catalogoServicios->guardarServicio(
            $datos['id_proveedor'],
            $datos['nombre_servicio'],
            $datos['descripcion'] ?? '',
            $datos['precio'],
            $datos['recursos'] ?? null
        );
        
        if ($resultado) {
            $id_proveedor = $datos['id_proveedor'];
            header("Location: verProveedor.php?id=" . $id_proveedor);
            exit();
        } else {
            return "Error al registrar el servicio.";
        }
    }

    public function editarServicio(array $datos) {
        $catalogoServicios = new CatalogoServicios();
        $resultado = $catalogoServicios->actualizarServicio(
            $datos['id'],
            $datos['id_proveedor'],
            $datos['nombre_servicio'],
            $datos['descripcion'] ?? '',
            $datos['precio'],
            $datos['recursos'] ?? null
        );
        
        if ($resultado) {
            $id_proveedor_redirect = $datos['id_proveedor_redirect'] ?? $datos['id_proveedor'] ?? null;
            if ($id_proveedor_redirect) {
                header("Location: verCatalogo.php?id=" . $id_proveedor_redirect);
            } else {
                header('Location: verCatalogo.php');
            }
            exit();
        } else {
            return "Error al actualizar el servicio.";
        }
    }

    public function eliminarServicio($id_servicio, $id_proveedor = null) {
        $catalogoServicios = new CatalogoServicios();
        $resultado = $catalogoServicios->eliminarServicio($id_servicio);
        
        if ($resultado) {
            if ($id_proveedor) {
                header("Location: verCatalogo.php?id=" . $id_proveedor);
            } else {
                header('Location: verCatalogo.php');
            }
            exit();
        } else {
            return "Error al eliminar el servicio.";
        }
    }
}