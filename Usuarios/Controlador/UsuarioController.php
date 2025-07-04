<?php

require_once '../Modelos/Usuario.php';

class UsuarioController {
    public function registrarUsuario(array $datos) {
        // Validar contraseña: mínimo 8 caracteres y al menos 1 número
        if (
            strlen($datos['password']) < 8 ||
            !preg_match('/\d/', $datos['password'])
        ) {
            return "La contraseña debe tener al menos 8 caracteres y contener al menos un número.";
        }

        $usuario = new Usuario();
        $resultado = $usuario->registrarUsuario(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['correo'],
            password_hash($datos['password'], PASSWORD_DEFAULT),
            $datos['id_rol']
        );  
        if ($resultado) {
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