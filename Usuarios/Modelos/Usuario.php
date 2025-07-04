<?php

require_once __DIR__ . '/../../conexion_db.php';

class Usuario
{
    public function registrarUsuario($nombres, $apellidos, $correo, $password, $rol)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO usuarios (nombres, apellidos, correo, password, id_rol) 
                      VALUES ('$nombres', '$apellidos', '$correo', '$password', '$rol')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        return $resultado;
    }

    public function obtenerTodosLosUsuarios()
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT u.id, u.nombres, u.apellidos, u.correo, r.nombre as rol FROM usuarios u JOIN roles r ON u.id_rol = r.id";
        $stmt = $conexion->query($sqlSelect);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuarios;
    }

    public function obtenerUsuarioPorId($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM usuarios WHERE id = '$id'";
        $stmt = $conexion->query($sqlSelect);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuario;
    }

    public function actualizarUsuario($id, $nombres, $apellidos, $correo, $rol)
    {
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

    public function eliminarUsuario($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlDelete = "DELETE FROM usuarios WHERE id = '$id'";
        $resultado = $conexion->exec($sqlDelete);
        $conn->desconectar();
        return $resultado;
    }

    public static function obtenerTodos()
    {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sql = "SELECT id_usuario, nombre, correo FROM usuarios ORDER BY nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarioConRol($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT u.id, u.nombres, u.apellidos, u.correo, u.id_rol, r.nombre as rol 
                      FROM usuarios u 
                      LEFT JOIN roles r ON u.id_rol = r.id 
                      WHERE u.id = '$id'";
        $usuario = $conexion->query($sqlSelect);
        $usuario = $usuario->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuario;
    }

    public function actualizarPerfil($id, $nombres, $apellidos, $correo, $password = null)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        if ($password) {
            $sqlUpdate = "UPDATE usuarios SET 
                                nombres = '$nombres', 
                                apellidos = '$apellidos', 
                                correo = '$correo',
                                password = '$password'
                          WHERE id = '$id'";
            $usuario = $conexion->exec($sqlUpdate);
        } else {
            $sqlUpdate = "UPDATE usuarios SET 
                                nombres = '$nombres', 
                                apellidos = '$apellidos', 
                                correo = '$correo'
                          WHERE id = '$id'";
            $usuario = $conexion->exec($sqlUpdate);
        }

        $resultado = $usuario;
        $conn->desconectar();
        return $resultado;
    }

    public function verificarCorreoExistente($correo, $idExcluir = null)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();

        if ($idExcluir) {
            $sqlSelect = "SELECT id FROM usuarios WHERE correo = '$correo' AND id != '$idExcluir'";
            $stmt = $conexion->query($sqlSelect);
        } else {
            $sqlSelect = "SELECT id FROM usuarios WHERE correo = '$correo'";
            $stmt = $conexion->query($sqlSelect);
        }

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $resultado !== false;
    }
}
