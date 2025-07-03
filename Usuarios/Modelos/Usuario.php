<?php

require_once '../../conexion_db.php';

class Usuario {
    public function registrarUsuario($nombres, $apellidos, $correo, $password, $rol) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO usuarios (nombres, apellidos, correo, password, id_rol) 
                      VALUES ('$nombres', '$apellidos', '$correo', '$password', '$rol')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodosLosUsuarios() {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT u.id, u.nombres, u.apellidos, u.correo, r.nombre as rol FROM usuarios u JOIN roles r ON u.id_rol = r.id";
        $stmt = $conexion->query($sqlSelect);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuarios;
    }

    public function obtenerUsuarioPorId($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM usuarios WHERE id = '$id'";
        $stmt = $conexion->query($sqlSelect);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuario;
    }

    public function actualizarUsuario($id, $nombres, $apellidos, $correo, $rol) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlUpdate = "UPDATE usuarios SET 
                                nombres = '$nombres', 
                                apellidos = '$apellidos', 
                                correo = '$correo', 
                                id_rol = '$rol' WHERE id = '$id'";
        $resultado = $conexion->exec($sqlUpdate);
        $conn->desconectar();
        return $resultado;
    }

    public function eliminarUsuario($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlDelete = "DELETE FROM usuarios WHERE id = '$id'";
        $resultado = $conexion->exec($sqlDelete);
        $conn->desconectar();
        return $resultado;
    }

    public static function obtenerTodos() {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sql = "SELECT id_usuario, nombre, correo FROM usuarios ORDER BY nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>