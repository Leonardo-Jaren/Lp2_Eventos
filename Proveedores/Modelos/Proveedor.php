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

    public static function obtenerReservasPorProveedor($id_proveedor) {
        $conn = new ConexionDB();
        $conexion = $conn->conectar();
        $sqlSelect = "SELECT 
                    r.nombre_evento,
                    r.fecha_evento,
                    r.lugar,
                    u.nombres AS nombre_cliente,
                    cs.nombre_servicio,
                    ep.estado_participacion
                FROM evento_proveedores ep
                JOIN catalogo_servicios cs ON ep.id_servicio = cs.id_servicio
                JOIN reservas r ON ep.id_reserva = r.id_reserva
                JOIN usuarios u ON r.id_cliente = u.id_usuario
                WHERE cs.id_proveedor = '$id_proveedor'
                ORDER BY r.fecha_evento DESC";
        $stmt = $conexion->query($sqlSelect);
        $proveedor_reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn->desconectar();
        return $proveedor_reservas;
    }
}