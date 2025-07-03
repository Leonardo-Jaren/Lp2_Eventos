<?php
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
                <?php echo $mensaje; ?>
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
