<?php
session_start();
$titulo_pagina = "Editar Proveedor";
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';
require_once '../../Proveedores/Controlador/ProveedorController.php';

$id = $_GET['id'] ?? null;
$proveedor = null;
if ($id) {
    $proveedorModel = new Proveedor();
    $proveedor = $proveedorModel->encontrarProveedor($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proveedorController = new ProveedorController();
    $mensaje = $proveedorController->editarProveedor($_POST);
    if ($mensaje) {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . htmlspecialchars($mensaje);
        echo '</div>';
    }
}
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-yellow-500 text-white px-6 py-4">
             <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-edit mr-3"></i>
                Editar Proveedor
            </h1>
        </div>
        <div class="p-6">
            <?php if ($proveedor): ?>
                <p class="mt-2">Estás editando el proveedor: <strong><?php echo htmlspecialchars($proveedor['nombre']); ?></strong></p>
            <form action="" method="POST" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                <div class="space-y-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proveedor</label>
                        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                    </div>
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($proveedor['correo']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                    </div>
                    <div>
                        <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <input type="text" name="empresa" id="empresa" value="<?php echo htmlspecialchars($proveedor['empresa']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="verProveedor.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Actualizar Proveedor
                    </button>
                </div>
            </form>
            <?php else: ?>
                <div class="text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p>Error: Proveedor no encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Procesar el formulario cuando se envía
require_once '../Controlador/ProveedorController.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new ProveedorController();
    $mensaje = $controller->editarProveedor($_POST);
    if ($mensaje) {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . htmlspecialchars($mensaje);
        echo '</div>';
    }
}
?>

<?php 
require_once '../Controlador/ProveedorController.php';
if(!empty($_POST)) {
    $proveedorController = new ProveedorController();
    echo $proveedorController->editarProveedor($_POST);
}
include '../../layouts/footer.php'; 
?>