<?php
session_start();
$titulo_pagina = "Resultado de Confirmación";
include '../../layouts/header.php';

// Obtener el mensaje y el estado de la sesión
$mensaje = $_SESSION['participacion_mensaje'] ?? 'No hay ninguna acción que mostrar.';
$estado = $_SESSION['participacion_estado'] ?? 'info'; // 'exito', 'error', o 'info'

// Limpiar las variables de sesión para que no se muestren de nuevo
unset($_SESSION['participacion_mensaje'], $_SESSION['participacion_estado']);

// Determinar los colores y el ícono según el estado
$colores = [
    'exito' => 'bg-green-600',
    'error' => 'bg-red-600',
    'info' => 'bg-blue-600'
];

$iconos = [
    'exito' => 'fa-check-circle',
    'error' => 'fa-exclamation-triangle',
    'info' => 'fa-info-circle'
];

$color_clase = $colores[$estado] ?? 'bg-gray-600';
$icono_clase = $iconos[$estado] ?? 'fa-question-circle';

?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="<?php echo $color_clase; ?> text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas <?php echo $icono_clase; ?> mr-3"></i>
                Resultado de la Operación
            </h1>
        </div>
        <div class="p-6">
            <p class="text-lg text-gray-800 mb-4">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
            <div class="mt-6 flex justify-end">
                <a href="../../index.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
