<?php
session_start();
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';

$id_proveedor = $_GET['id'] ?? 0;
$proveedor = new Proveedor();
$proveedor->encontrar($id_proveedor);

$servicios = CatalogoServicios::buscarPorProveedor($id_proveedor);
$titulo_pagina = "Catálogo de " . htmlspecialchars($proveedor->getNombreEmpresa());
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-book-open mr-3"></i>
                Catálogo de: <?php echo htmlspecialchars($proveedor->getNombreEmpresa()); ?>
            </h1>
        </div>
        <div class="p-6">
            <a href="verProveedores.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block"><i class="fas fa-arrow-left mr-2"></i>Volver a la lista</a>
            <div class="overflow-x-auto">
                <table class="w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Servicio</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Descripción</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Categoría</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Precio Base</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($servicios)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-500">
                                    <p>Este proveedor aún no ha registrado servicios.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></td>
                                    <td class="px-4 py-4 text-gray-700"><?php echo htmlspecialchars($servicio['descripcion_servicio']); ?></td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <?php echo htmlspecialchars($servicio['categoria']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-gray-900">S/ <?php echo number_format($servicio['precio_base'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>