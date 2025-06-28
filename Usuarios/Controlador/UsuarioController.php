<?php

require_once '../Modelos/Usuario.php';

class UsuarioController {
    public function registrarUsuario(array $datos) {
        $usuario = new Usuario();
        $resultado = $usuario->registrarUsuario(
            $datos['nombre'],
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
}