<?php

require_once '../../Modelo/Permisos.php';

class PermisosController {
    public function crearPermiso(array $datos) {
        $permiso = new Permisos();
        $resultado = $permiso->crearPermiso(
            $datos['nombre_permiso'], 
            $datos['descripcion']);
        if ($resultado) {
            return null;
        } else {
            return "Error al crear el permiso.";
        }
    }

    public function actualizarPermiso(array $datos) {
        $permiso = new Permisos();
        $resultado = $permiso->actualizarPermiso(
            $datos['id'],
            $datos['nombre_permiso'],
            $datos['descripcion']
        );
        if ($resultado) {
            header('Location: verPermisos.php');
            exit();
        } else {
            return "Error al actualizar el permiso.";
        }
    }

    public function eliminarPermiso(int $id) {
        $permiso = new Permisos();
        $resultado = $permiso->eliminarPermiso($id);
        if ($resultado) {
            header('Location: verPermisos.php');
            exit();
        } else {
            return "Error al eliminar el permiso.";
        }
    }
}