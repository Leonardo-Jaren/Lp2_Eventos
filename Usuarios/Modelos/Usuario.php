<?php

require_once '../../conexion_db.php';

class Usuario {
    public function registrarUsuario($nombre, $correo, $password, $rol) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO usuarios (nombre, correo, password, id_rol) 
                      VALUES ('$nombre', '$correo', '$password', '$rol')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodosLosUsuarios() {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT u.id, u.nombre, u.correo, r.nombre as rol FROM usuarios u JOIN roles r ON u.id_rol = r.id";
        $result = $conexion->query($sql);
        $usuarios = $result->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuarios;
    }

    public function obtenerUsuarioPorId($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuario;
    }

    public function actualizarUsuario($id, $nombre, $correo, $rol) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "UPDATE usuarios SET nombre = ?, correo = ?, id_rol = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([$nombre, $correo, $rol, $id]);
        $conn->desconectar();
        return $resultado;
    }

    public function eliminarUsuario($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $resultado = $stmt->execute([$id]);
        $conn->desconectar();
        return $resultado;
    }
}
?>