<?php
require_once '../../conexion_db.php';

class CatalogoServicios {

    // --- ATRIBUTOS ---
    private $id_servicio;
    private $id_proveedor;
    private $nombre_servicio;
    private $descripcion_servicio;
    private $precio_base;
    private $categoria;
    private $conexion;

    /**
     * El constructor crea la conexión a la BD una sola vez por objeto.
     */
    public function __construct() {
        $db = new ConexionDB();
        $this->conexion = $db->conectar();
    }

    // --- GETTERS Y SETTERS ---
    public function getIdServicio() { return $this->id_servicio; }
    public function getIdProveedor() { return $this->id_proveedor; }
    public function getNombreServicio() { return $this->nombre_servicio; }
    public function getDescripcionServicio() { return $this->descripcion_servicio; }
    public function getPrecioBase() { return $this->precio_base; }
    public function getCategoria() { return $this->categoria; }

    public function setIdProveedor($id) { $this->id_proveedor = $id; }
    public function setNombreServicio($nombre) { $this->nombre_servicio = $nombre; }
    public function setDescripcionServicio($desc) { $this->descripcion_servicio = $desc; }
    public function setPrecioBase($precio) { $this->precio_base = $precio; }
    public function setCategoria($cat) { $this->categoria = $cat; }

    // --- MÉTODOS DE BASE DE DATOS (CRUD) ---

    /**
     * Obtiene todos los servicios de un proveedor específico.
     */
    public static function buscarPorProveedor($id_proveedor) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sql = "SELECT * FROM catalogo_servicios WHERE id_proveedor = :id_proveedor ORDER BY nombre_servicio";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $sql = "SELECT * FROM catalogo_servicios WHERE id_servicio = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id_servicio = $data['id_servicio'];
            $this->id_proveedor = $data['id_proveedor'];
            $this->nombre_servicio = $data['nombre_servicio'];
            $this->descripcion_servicio = $data['descripcion_servicio'];
            $this->precio_base = $data['precio_base'];
            $this->categoria = $data['categoria'];
            return true;
        }
        return false;
    }

    /**
     * Guarda (Inserta o Actualiza) un servicio usando los datos del objeto.
     */
    public function guardar() {
        if ($this->id_servicio) {
            $sql = "UPDATE catalogo_servicios 
                    SET id_proveedor = :id_proveedor, nombre_servicio = :nombre_servicio, descripcion_servicio = :descripcion_servicio, precio_base = :precio_base, categoria = :categoria 
                    WHERE id_servicio = :id_servicio";
        } else {
            $sql = "INSERT INTO catalogo_servicios(id_proveedor, nombre_servicio, descripcion_servicio, precio_base, categoria) 
                    VALUES(:id_proveedor, :nombre_servicio, :descripcion_servicio, :precio_base, :categoria)";
        }
        
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bindParam(':id_proveedor', $this->id_proveedor);
        $stmt->bindParam(':nombre_servicio', $this->nombre_servicio);
        $stmt->bindParam(':descripcion_servicio', $this->descripcion_servicio);
        $stmt->bindParam(':precio_base', $this->precio_base);
        $stmt->bindParam(':categoria', $this->categoria);
        
        if ($this->id_servicio) {
            $stmt->bindParam(':id_servicio', $this->id_servicio);
        }
        
        return $stmt->execute();
    }

    /**
     * Elimina un servicio de la base de datos por su ID.
     */
    public static function eliminar($id) {
        $db = new ConexionDB();
        $conexion = $db->conectar();
        $sql = "DELETE FROM catalogo_servicios WHERE id_servicio = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}