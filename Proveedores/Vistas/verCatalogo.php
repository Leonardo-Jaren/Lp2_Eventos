<?php
session_start();
// 1. INCLUIR EL CONTROLADOR
// Se incluye el controlador que se encargará de obtener todos los datos.
require_once '../Controlador/ProveedorController.php';

// 2. OBTENCIÓN DE DATOS A TRAVÉS DEL CONTROLADOR
$proveedorController = new ProveedorController();
$proveedorData = null;
$servicios = [];

// Se obtiene el ID del proveedor desde la URL.
$id_proveedor = $_GET['id'] ?? 0;

if ($id_proveedor > 0) {
    // El controlador se encarga de buscar los datos del proveedor.
    $proveedorData = $proveedorController->buscarPorId($id_proveedor);
    // El controlador se encarga de buscar los servicios de ese proveedor.
    $servicios = $proveedorController->verCatalogo($id_proveedor);
}

// 3. INCLUIR EL HEADER
// Se define el título de la página dinámicamente.
$titulo_pagina = "Catálogo de " . htmlspecialchars($proveedorData['empresa'] ?? 'Proveedor Desconocido');
include '../../layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-book-open mr-3"></i>
                Catálogo de: <?php echo htmlspecialchars($proveedorData['empresa'] ?? 'Proveedor Desconocido'); ?>
            </h1>
        </div>
        <div class="p-6">
            <a href="verProveedor.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Volver a la lista de proveedores
            </a>
            
            <?php if ($proveedorData): // Se verifica si el proveedor fue encontrado ?>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Servicio</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Precio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($servicios)): ?>
                                <tr>
                                    <td colspan="2" class="px-4 py-12 text-center text-gray-500">
                                        <p>Este proveedor aún no ha registrado servicios en su catálogo.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($servicios as $servicio): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></td>
                                        <td class="px-4 py-4 font-mono text-gray-900">S/ <?php echo number_format($servicio['precio'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                 <!-- Mensaje de error si no se encontró el proveedor -->
                <div class="text-center text-red-500 py-10">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <p>Error: Proveedor no encontrado o ID no válido.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>