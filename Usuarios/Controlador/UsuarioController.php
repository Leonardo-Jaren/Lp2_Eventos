<?php

require_once '../Modelos/Usuario.php';

class UsuarioController {
    public function registrarUsuario(array $datos) {
        $usuario = new Usuario();
        $resultado = $usuario->registrarUsuario(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['correo'],
            password_hash($datos['password'], PASSWORD_DEFAULT),
            $datos['id_rol']
        );
        if ($resultado) {
            // Redirigir al dashboard después del registro exitoso
            header("Location: ../../dashboard.php");
            exit();
        } else {
            return "Error al registrar el usuario.";
        }
    }

    public function actualizarUsuario(array $datos) {
        $usuario = new Usuario();
        $resultado = $usuario->actualizarUsuario(
            $datos['id'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['correo'],
            $datos['id_rol']
        );
        if ($resultado) {
            header("Location: ../Vistas/verUsuarios.php");
            exit();
        } else {
            return "Error al actualizar el usuario.";
        }
    }

    public function eliminarUsuario($id) {
        $usuario = new Usuario();
        $resultado = $usuario->eliminarUsuario($id);
        if ($resultado) {
            return true;
        } else {
            return "Error al eliminar el usuario.";
        }
    }
}
?>