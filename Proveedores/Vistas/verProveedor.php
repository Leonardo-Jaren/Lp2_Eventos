<?php
session_start();
$titulo_pagina = "Gestión de Proveedores";
// Incluir layout y modelo
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';

// Procesar mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Lógica para obtener los proveedores (puedes añadir filtros si lo deseas)
$proveedores = Proveedor::mostrar();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-truck-loading mr-3"></i>
                    Gestión de Proveedores
                </h1>
                <a href="crearProveedor.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition-transform transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Proveedor
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
                        <span><?php echo htmlspecialchars($mensaje); ?></span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-current hover:opacity-70"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Empresa</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Email Contacto</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($proveedores)): ?>
                            <tr>
                                <td colspan="3" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                                    <p>No se encontraron proveedores.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></td>
                                    <td class="px-4 py-4 text-gray-700"><?php echo htmlspecialchars($proveedor['correo']); ?></td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-2">
                                            <a href="verCatalogo.php?id=<?php echo $proveedor['id_proveedor']; ?>" class="bg-indigo-500 text-white p-2 rounded-lg hover:bg-indigo-600" title="Ver Catálogo"><i class="fas fa-book-open text-xs"></i></a>
                                            <a href="../Controlador/ProveedorController.php?action=descargarPdf&id=<?php echo $proveedor['id_proveedor']; ?>" class="bg-green-500 text-white p-2 rounded-lg hover:bg-green-600" title="Descargar Reservas"><i class="fas fa-file-pdf text-xs"></i></a>
                                            <a href="editarProveedor.php?id=<?php echo $proveedor['id_proveedor']; ?>" class="bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600" title="Editar"><i class="fas fa-edit text-xs"></i></a>
                                            <a href="../Controlador/ProveedorController.php?action=eliminar&id=<?php echo $proveedor['id_proveedor']; ?>" class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600" title="Eliminar" onclick="return confirm('¿Estás seguro?')"><i class="fas fa-trash text-xs"></i></a>
                                        </div>
                                    </td>
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