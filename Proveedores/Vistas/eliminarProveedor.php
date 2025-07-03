<?php
session_start();
$titulo_pagina = "Confirmar Eliminación";
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';

// Obtener el ID del proveedor de la URL
$id = $_GET['id'] ?? null;
if (!$id) {
    // Si no hay ID, redirigir o mostrar un error
    header('Location: verProveedor.php');
    exit();
}

// Buscar los datos del proveedor para mostrarlos
$proveedor = new Proveedor();
$encontrado = $proveedor->encontrar($id);

if (!$encontrado) {
    // Si no se encuentra el proveedor, mostrar un mensaje
    echo "<div class='max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12'><p>Proveedor no encontrado.</p></div>";
    include '../../layouts/footer.php';
    exit();
}
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                Confirmar Eliminación de Proveedor
            </h1>
        </div>
        <div class="p-6">
            <p class="text-lg text-gray-800 mb-4">
                ¿Estás seguro de que deseas eliminar al proveedor <strong class="font-semibold"><?php echo htmlspecialchars($proveedor->getNombreEmpresa()); ?></strong>?
            </p>
            <p class="text-gray-600">
                Esta acción no se puede deshacer. Se eliminarán todos los datos asociados a este proveedor.
            </p>
            
            <form action="../Controlador/ProveedorController.php?action=eliminar" method="post" class="mt-6">
                <input type="hidden" name="id_proveedor" value="<?php echo $proveedor->getIdProveedor(); ?>">
                
                <div class="flex justify-end space-x-4">
                    <a href="verProveedor.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Sí, Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
