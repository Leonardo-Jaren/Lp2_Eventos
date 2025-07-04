<!-- <?php
session_start();
$titulo_pagina = "Confirmar Participación";
include '../../layouts/header.php';

// Obtener la decisión de la URL
$decision = $_GET['decision'] ?? 'indefinida';
$mensaje = "Tu decisión ha sido registrada como: " . htmlspecialchars($decision);

?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-green-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                Confirmación de Participación
            </h1>
        </div>
        <div class="p-6">
            <p class="text-lg text-gray-800 mb-4">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
            <div class="mt-6 flex justify-end">
                <a href="verProveedor.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Volver a la lista de proveedores
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
 -->

//en el caso que se use esta vista se necesita crear una nueva tabla 

CREATE TABLE evento_servicios (
  id_evento INT NOT NULL,
  id_servicio INT NOT NULL,
  estado ENUM('invitado', 'confirmado', 'rechazado') DEFAULT 'invitado',
  PRIMARY KEY (id_evento, id_servicio),
  FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE,
  FOREIGN KEY (id_servicio) REFERENCES servicios_proveedor(id) ON DELETE CASCADE
);

//agregar esta funcion en el modelo proveedor 
public static function actualizarEstadoParticipacion($id_evento, $id_servicio, $decision) {
    $db = new ConexionDB();
    $conn = $db->conectar();
    
    // Se escapa la decisión para seguridad básica.
    $estado_seguro = addslashes($decision);

    $sql = "UPDATE evento_servicios 
            SET estado = '$estado_seguro' 
            WHERE id_evento = '$id_evento' AND id_servicio = '$id_servicio'";
            
    $resultado = $conn->exec($sql);
    $db->desconectar();
    return $resultado;
}

