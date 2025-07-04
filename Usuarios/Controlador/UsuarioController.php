<?php

require_once __DIR__ . '/../Modelos/Usuario.php';

class UsuarioController
{
    public function registrarUsuario(array $datos)
    {
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

    public function actualizarUsuario(array $datos)
    {
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

    public function eliminarUsuario($id)
    {
        $usuario = new Usuario();
        $resultado = $usuario->eliminarUsuario($id);
        if ($resultado) {
            header("Location: ../../RolesPermisos/Vista/permisos/verPermisos.php");
            exit();
        } else {
            return "Error al eliminar el usuario.";
        }
    }

    public function actualizarPerfil(array $datos)
    {
        $usuario = new Usuario();

        if ($usuario->verificarCorreoExistente($datos['correo'], $datos['id'])) {
            return ["success" => false, "message" => "El correo electrónico ya está registrado por otro usuario."];
        }

        $password = null;
        if (!empty($datos['password'])) {
            $password = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        $resultado = $usuario->actualizarPerfil(
            $datos['id'],
            $datos['nombres'],
            $datos['apellidos'],
            $datos['correo'],
            $password
        );

        if ($resultado) {
            return ["success" => true, "message" => "Perfil actualizado exitosamente."];
        } else {
            return ["success" => false, "message" => "Error al actualizar el perfil."];
        }
    }

    public function obtenerPerfilUsuario($id)
    {
        $usuario = new Usuario();
        return $usuario->obtenerUsuarioConRol($id);
    }
}
