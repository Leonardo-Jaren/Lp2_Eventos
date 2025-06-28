<?php

require_once '../Modelo/Sesion.php';

class AuthController {
    public function iniciarSesion($correo, $password) {
        $sesion = new Sesion();
        $resultado = $sesion->verificarNombre($correo);

        $correos = "";
        $id = "";
        $nombre = "";
        $passwordBD = "";
        $contador = 0;
        foreach ($resultado as $userLogin) {
            $correos = $userLogin['correo'];
            $id = $userLogin['id'];
            $nombre = $userLogin['nombre']; // Asegúrate de que 'nombre' esté en el resultado
            $passwordBD = $userLogin['password'];
            $contador++;
        }
        if ($contador != 0) {
            if (password_verify($password, $passwordBD)) {
                session_start();
                $_SESSION['id'] = $id;
                $_SESSION['correo'] = $correos;
                $_SESSION['nombre'] = $nombre;
                header("Location: ../../dashboard.php");
                return "Bienvenido, " . htmlspecialchars($nombre);
            } else {
                return "Contraseña incorrecta.";
            }
        } else {
            return "Usuario no encontrado.";
        }
    }
}