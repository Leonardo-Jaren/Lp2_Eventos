<?php

require_once '../../conexion_db.php';

class Proveedor {
    public static function obtenerTodosLosProveedores()
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT p.id, p.nombre_empresa, p.telefono, p.direccion, p.id_usuario,
                             CONCAT(u.nombres, ' ', u.apellidos) as nombre_usuario
                      FROM proveedores p
                      LEFT JOIN usuarios u ON p.id_usuario = u.id
                      ORDER BY p.nombre_empresa";
        $stmt = $conexion->query($sqlSelect);
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedores;
    }

    public function encontrarProveedor($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) as nombre_usuario
                      FROM proveedores p
                      LEFT JOIN usuarios u ON p.id_usuario = u.id
                      WHERE p.id = '$id'";
        $stmt = $conexion->query($sqlSelect);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedor;
    }

    public function validarNumeroTelefono($telefono)
    {
        if (preg_match('/^\d{9}$/', $telefono)) {
            return true;
        } else {
            return false;
        }
    }

    public function guardarProveedor($nombre_empresa, $telefono, $direccion, $id_usuario = null)
    {

        if (!$this->validarNumeroTelefono($telefono)) {
            return ['success' => false, 'message' => 'El número de teléfono debe tener 9 dígitos.'];
        }

        if ($id_usuario === '' || $id_usuario === '0') {
            $id_usuario = null;
        }

        if ($id_usuario === null) {
            session_start();
            $id_usuario = $_SESSION['id'] ?? null;
            if (!$id_usuario) {
                return ['success' => false, 'message' => 'Error: Usuario no autenticado.'];
            }
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlInsert = "INSERT INTO proveedores (nombre_empresa, telefono, direccion, id_usuario) 
                      VALUES ('$nombre_empresa', '$telefono', '$direccion', '$id_usuario')";
        $resultado = $conexion->exec($sqlInsert);
        $conn->desconectar();
        if ($resultado) {
            return ['success' => true, 'message' => 'Proveedor registrado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar el proveedor.'];
        }

    }

    public function actualizarProveedor($id, $nombre_empresa, $telefono, $direccion, $id_usuario = null)
    {
        if (!$this->validarNumeroTelefono($telefono)) {
            return ['success' => false, 'message' => 'El número de teléfono debe tener 9 dígitos.'];
        }

        if ($id_usuario === '' || $id_usuario === '0') {
            $id_usuario = null;
        }

        $conn = new ConexionDB();
        $conexion = $conn->conectar();

        if ($id_usuario !== null) {
            $sqlUpdate = "UPDATE proveedores SET 
                          nombre_empresa = '$nombre_empresa', 
                          telefono = '$telefono', 
                          direccion = '$direccion',
                          id_usuario = '$id_usuario'
                          WHERE id = '$id'";
        } else {
            $sqlUpdate = "UPDATE proveedores SET 
                          nombre_empresa = '$nombre_empresa', 
                          telefono = '$telefono', 
                          direccion = '$direccion' 
                          WHERE id = '$id'";
        }

        $resultado = $conexion->exec($sqlUpdate);
        $conn->desconectar();

        if ($resultado) {
            return ['success' => true, 'message' => 'Proveedor actualizado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el proveedor.'];
        }
    }

    public static function eliminarProveedor($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlDelete = "DELETE FROM proveedores WHERE id = '$id'";
        $resultado = $conexion->exec($sqlDelete);
        $conn->desconectar();
        return $resultado;
    }

    public static function obtenerUsuariosDisponibles()
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT u.id, CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo, u.correo, r.nombre as rol
                      FROM usuarios u
                      LEFT JOIN roles r ON u.id_rol = r.id
                      ORDER BY u.nombres, u.apellidos";
        $stmt = $conexion->query($sqlSelect);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuarios;
    }

    public function getNombreUsuario($id_usuario)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT CONCAT(nombres, ' ', apellidos) as nombre_completo FROM usuarios WHERE id = '$id_usuario'";
        $stmt = $conexion->query($sqlSelect);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $usuario['nombre_completo'] ?? '';
    }

    public function getNombreEmpresa($id)
    {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT nombre_empresa FROM proveedores WHERE id = '$id'";
        $stmt = $conexion->query($sqlSelect);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedor['nombre_empresa'] ?? '';
    }
}
