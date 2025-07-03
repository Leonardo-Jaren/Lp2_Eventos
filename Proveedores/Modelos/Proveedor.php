<?php
// models/Proveedor.php

// Esta ruta es correcta para tu estructura de carpetas. ¡Bien hecho!
require_once __DIR__ . '/../../conexion_db.php';

class Proveedor {

    // --- ATRIBUTOS ---
    private $id_proveedor;
    private $id_usuario;
    private $nombre_empresa;
    private $descripcion;
    private $direccion;

    /**
     * @var PDO Guarda la conexión para ser reutilizada por el objeto.
     */
    private $conexion;

    /**
     * El constructor se ejecuta al crear un nuevo objeto Proveedor.
     * Crea la conexión a la BD una sola vez por objeto.
     */
    public function __construct() {
        $db = new ConexionDB();
        $this->conexion = $db->conectar();
    }

    // --- GETTERS Y SETTERS ---
    // (Estos están perfectos, no necesitan cambios)
    public function getIdProveedor() { return $this->id_proveedor; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function getNombreEmpresa() { return $this->nombre_empresa; }
    public function getDescripcion() { return $this->descripcion; }
    public function getDireccion() { return $this->direccion; }
    public function setIdUsuario($id) { $this->id_usuario = $id; }
    public function setNombreEmpresa($nombre) { $this->nombre_empresa = $nombre; }
    public function setDescripcion($desc) { $this->descripcion = $desc; }
    public function setDireccion($dir) { $this->direccion = $dir; }

    // --- MÉTODOS DE BASE DE DATOS (CRUD) ---

    public static function mostrar() {
        $sql = "SELECT p.*, u.correo FROM proveedores p JOIN usuarios u ON p.id_usuario = u.id_usuario ORDER BY p.nombre_empresa";
        // CORRECCIÓN: Usar tu clase ConexionDB correctamente
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function encontrar($id) {
        $sql = "SELECT * FROM proveedores WHERE id_proveedor = :id";
        // CORRECCIÓN: Usar la conexión guardada en el objeto
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id_proveedor = $data['id_proveedor'];
            $this->id_usuario = $data['id_usuario'];
            $this->nombre_empresa = $data['nombre_empresa'];
            $this->descripcion = $data['descripcion'];
            $this->direccion = $data['direccion'];
            return true;
        }
        return false;
    }

    public function guardar() {
        if ($this->id_proveedor) {
            $sql = "UPDATE proveedores SET id_usuario = :id_usuario, nombre_empresa = :nombre_empresa, descripcion = :descripcion, direccion = :direccion WHERE id_proveedor = :id_proveedor";
        } else {
            $sql = "INSERT INTO proveedores(id_usuario, nombre_empresa, descripcion, direccion) VALUES(:id_usuario, :nombre_empresa, :descripcion, :direccion)";
        }
        
        // CORRECCIÓN: Usar la conexión guardada en el objeto
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':nombre_empresa', $this->nombre_empresa);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':direccion', $this->direccion);
        
        if ($this->id_proveedor) {
            $stmt->bindParam(':id_proveedor', $this->id_proveedor);
        }
        
        return $stmt->execute();
    }

    public static function eliminar($id) {
        $sql = "DELETE FROM proveedores WHERE id_proveedor = :id";
        // CORRECCIÓN: Usar tu clase ConexionDB correctamente
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtiene todas las reservas asociadas a un proveedor específico.
     *
     * @param int $id_proveedor El ID del proveedor.
     * @return array La lista de reservas.
     */
    public static function obtenerReservasPorProveedor($id_proveedor) {
        $sql = "SELECT 
                    r.nombre_evento,
                    r.fecha_evento,
                    r.lugar,
                    u.nombre AS nombre_cliente,
                    cs.nombre_servicio,
                    ep.estado_participacion
                FROM evento_proveedores ep
                JOIN catalogo_servicios cs ON ep.id_servicio = cs.id_servicio
                JOIN reservas r ON ep.id_reserva = r.id_reserva
                JOIN usuarios u ON r.id_cliente = u.id_usuario
                WHERE cs.id_proveedor = :id_proveedor
                ORDER BY r.fecha_evento DESC";

        $db = new ConexionDB();
        $conexion = $db->conectar();
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}