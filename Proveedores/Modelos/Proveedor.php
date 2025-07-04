<?php

require_once '../../conexion_db.php';

class Proveedor {

    public static function obtenerTodosLosProveedores() {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT id, nombre, correo, empresa FROM proveedores ORDER BY nombre";
        $stmt = $conexion->query($sqlSelect);
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedores;
    }

    public function encontrarProveedor($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT * FROM proveedores WHERE id_proveedor = '$id'";
        // CORRECCIÓN: Usar la conexión guardada en el objeto
        $stmt = $conexion->query($sqlSelect);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedor;
    }

    public function verificarCorreoExistente($correo, $id_excluir = null) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        
        if ($id_excluir) {
            $sqlSelect = "SELECT COUNT(*) FROM proveedores WHERE correo = '$correo' AND id_proveedor != '$id_excluir'";
            $stmt = $conexion->query($sqlSelect);
        } else {
            $sqlSelect = "SELECT COUNT(*) FROM proveedores WHERE correo = '$correo'";
            $stmt = $conexion->query($sqlSelect);
        }
        
        $count = $stmt->fetchColumn();
        $conn->desconectar();
        return $count > 0;
    }

    public function guardarProveedor($nombre, $correo, $empresa) {
        // Verificar si el correo ya existe
        if ($this->verificarCorreoExistente($correo)) {
            return ['success' => false, 'message' => 'El correo electrónico ya está registrado para otro proveedor.'];
        }

        try {
            $conn = new ConexionDB();
            $conexion = $conn->conectar();
            $sqlInsert = "INSERT INTO proveedores (nombre, correo, empresa) 
                          VALUES ('$nombre', '$correo', '$empresa')";
            $resultado = $conexion->exec($sqlInsert);
            $conn->desconectar();
            
            if ($resultado) {
                return ['success' => true, 'message' => 'Proveedor registrado exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al registrar el proveedor.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    public function actualizarProveedor($id, $nombre, $correo, $empresa) {
        if ($this->verificarCorreoExistente($correo, $id)) {
            return ['success' => false, 'message' => 'El correo electrónico ya está registrado para otro proveedor.'];
        }

        try {
            $conn = new ConexionDB();
            $conexion = $conn->conectar();
            $sqlUpdate = "UPDATE proveedores SET 
                          nombre = '$nombre', 
                          correo = '$correo', 
                          empresa = '$empresa' 
                          WHERE id_proveedor = '$id'";
            $resultado = $conexion->exec($sqlUpdate);
            $conn->desconectar();
            
            if ($resultado) {
                return ['success' => true, 'message' => 'Proveedor actualizado exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar el proveedor.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    public static function eliminarProveedor($id) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlDelete = "DELETE FROM proveedores WHERE id_proveedor = '$id'";
        $resultado = $conexion->exec($sqlDelete);
        $conn->desconectar();
        return $resultado;
    }

    /**
     * Obtiene las reservas asociadas a un proveedor.
     * Nota: La relación entre servicios_proveedor y reservas/eventos no es directa en el esquema actual.
     * Esta consulta asume que se buscan eventos creados por el usuario asociado al proveedor,
     * y muestra los servicios que el proveedor ofrece.
     */
    public static function obtenerReservasPorProveedor($id_proveedor) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sql = "SELECT 
                    e.titulo AS nombre_evento,
                    e.fecha_evento,
                    u_creador.nombres AS nombre_cliente,
                    sp.nombre_servicio
                FROM proveedores p
                JOIN servicios_proveedor sp ON p.id = sp.id_proveedor
                JOIN usuarios u_proveedor ON p.id_usuario = u_proveedor.id
                JOIN eventos e ON u_proveedor.id = e.id_usuario
                JOIN reservas r ON e.id = r.id_evento
                JOIN usuarios u_creador ON e.id_usuario = u_creador.id
                WHERE p.id = :id_proveedor
                ORDER BY e.fecha_evento DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}