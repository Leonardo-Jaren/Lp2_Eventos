<?php
require_once '../../conexion_db.php'; // Asegúrate de que la ruta sea correcta.

/**
 * ADVERTENCIA: Este código utiliza un estilo simplificado a petición del usuario.
 * Para mayor seguridad en un entorno real, se deben usar consultas preparadas.
 */
class CatalogoServicios {

    /**
     * Obtiene todos los servicios de un proveedor específico.
     */
    public static function obtenerPorProveedor($id_proveedor) {
        $db = new ConexionDB();
        $conn = $db->conectar();
        
        $sql = "SELECT * FROM servicios_proveedor WHERE id_proveedor = '$id_proveedor' ORDER BY nombre_servicio";
        
        $stmt = $conn->query($sql);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $db->desconectar();
        return $servicios;
    }
    
    /**
     * Guarda un nuevo servicio en el catálogo de un proveedor.
     */
    public static function guardar($datos) {
        $db = new ConexionDB();
        $conn = $db->conectar();

        $id_proveedor = $datos['id_proveedor'];
        $nombre_servicio = addslashes($datos['nombre_servicio']);
        $precio = $datos['precio'];

        $sql = "INSERT INTO servicios_proveedor (id_proveedor, nombre_servicio, precio) 
                VALUES ('$id_proveedor', '$nombre_servicio', '$precio')";

        $resultado = $conn->exec($sql);
        
        $db->desconectar();
        return $resultado;
    }

    /**
     * Actualiza un servicio existente.
     */
    public static function actualizar($id_servicio, $datos) {
        $db = new ConexionDB();
        $conn = $db->conectar();

        $nombre_servicio = addslashes($datos['nombre_servicio']);
        $precio = $datos['precio'];

        $sql = "UPDATE servicios_proveedor SET 
                    nombre_servicio = '$nombre_servicio', 
                    precio = '$precio' 
                WHERE id = '$id_servicio'";

        $resultado = $conn->exec($sql);
        
        $db->desconectar();
        return $resultado;
    }

    /**
     * Elimina un servicio del catálogo.
     */
    public static function eliminar($id_servicio) {
        $db = new ConexionDB();
        $conn = $db->conectar();
        
        $sql = "DELETE FROM servicios_proveedor WHERE id = '$id_servicio'";
        
        $resultado = $conn->exec($sql);
        
        $db->desconectar();
        return $resultado;
    }
}
?>