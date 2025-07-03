<?php
// Ubicación: Proveedores/Modelos/EventoProveedor.php
require_once __DIR__ . '/../../conexion_db.php';

class EventoProveedor {
    /**
     * Actualiza el estado de participación de un proveedor en un evento.
     *
     * @param int $id_reserva El ID de la reserva (evento).
     * @param int $id_servicio El ID del servicio ofrecido por el proveedor.
     * @param string $decision El nuevo estado ('confirmado' o 'rechazado').
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public static function actualizarEstado($id_reserva, $id_servicio, $decision) {
        // Validar que la decisión sea uno de los valores permitidos.
        $estados_permitidos = ['confirmado', 'rechazado'];
        if (!in_array($decision, $estados_permitidos)) {
            return false;
        }

        $sql = "UPDATE evento_proveedores 
                SET estado_participacion = :decision 
                WHERE id_reserva = :id_reserva AND id_servicio = :id_servicio";
        
        $db = new ConexionDB();
        $conexion = $db->conectar();
        
        $stmt = $conexion->prepare($sql);
        
        $stmt->bindParam(':decision', $decision);
        $stmt->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
        $stmt->bindParam(':id_servicio', $id_servicio, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>