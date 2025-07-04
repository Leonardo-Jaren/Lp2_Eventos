<?php
// La sesión debe iniciarse al principio de todo.
session_start();

// 1. INCLUIR EL CONTROLADOR
// La vista ahora se comunicará con el controlador para obtener los datos.
require_once '../Controlador/ProveedorController.php';

// 2. VERIFICACIÓN DE SESIÓN
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

// 3. OBTENCIÓN DE DATOS A TRAVÉS DEL CONTROLADOR
$proveedorController = new ProveedorController();
$proveedores = $proveedorController->listar(); // El controlador se encarga de llamar al modelo.

// 4. INCLUIR ELEMENTOS DE LA PLANTILLA
$titulo_pagina = "Gestión de Proveedores";
include '../../layouts/header.php';
require_once '../../nav.php'; // Incluir la barra de navegación.
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-purple-700 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-truck-loading mr-3"></i>
                    Gestión de Proveedores
                </h1>
                <a href="crearProveedor.php" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition-transform transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Proveedor
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Estadísticas -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-4 text-white shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-users text-2xl mr-3"></i>
                        <div>
                            <p class="text-indigo-100 text-sm">Total Proveedores</p>
                            <p class="text-2xl font-bold"><?php echo count($proveedores); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-lg p-4 text-white shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <div>
                            <p class="text-teal-100 text-sm">Activos</p>
                            <p class="text-2xl font-bold"><?php echo count($proveedores); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg p-4 text-white shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-star text-2xl mr-3"></i>
                        <div>
                            <p class="text-rose-100 text-sm">Destacados</p>
                            <p class="text-2xl font-bold">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($proveedores)): ?>
                <!-- Estado vacío mejorado -->
                <div class="text-center py-16">
                    <div class="bg-gray-50 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-truck-loading text-6xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay proveedores registrados</h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">Comienza agregando tu primer proveedor para gestionar los servicios de eventos.</p>
                    <a href="crearProveedor.php" class="inline-flex items-center bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-plus mr-2"></i>Crear Primer Proveedor
                    </a>
                </div>
            <?php else: ?>
                <!-- Grid de Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($proveedores as $proveedor): ?>
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                            <!-- Header de la card -->
                            <div class="bg-gradient-to-r from-gray-700 to-gray-900 p-4">
                                <h3 class="text-white text-lg font-bold truncate"><?php echo htmlspecialchars($proveedor['empresa']); ?></h3>
                                <p class="text-gray-300 text-sm">Contacto: <?php echo htmlspecialchars($proveedor['nombres'] . ' ' . $proveedor['apellidos']); ?></p>
                            </div>

                            <!-- Contenido de la card -->
                            <div class="p-4 space-y-3">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-envelope text-gray-400 w-5 text-sm mr-3"></i>
                                    <span class="text-sm truncate"><?php echo htmlspecialchars($proveedor['correo']); ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                                        Activo
                                    </span>
                                    <span class="text-xs text-gray-500">ID: <?php echo $proveedor['id']; ?></span>
                                </div>
                            </div>

                            <!-- Footer con acciones -->
                            <div class="bg-gray-50 p-3 border-t border-gray-100">
                                <div class="flex justify-end items-center space-x-2">
                                    <a href="verCatalogo.php?id=<?php echo $proveedor['id']; ?>" class="bg-indigo-100 text-indigo-600 hover:bg-indigo-200 px-3 py-1 rounded-md text-sm font-medium transition duration-200" title="Ver Catálogo">
                                        <i class="fas fa-book-open mr-1"></i> Catálogo
                                    </a>
                                    <a href="editarProveedor.php?id=<?php echo $proveedor['id']; ?>" class="bg-yellow-100 text-yellow-600 hover:bg-yellow-200 px-3 py-1 rounded-md text-sm font-medium transition duration-200" title="Editar">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    <!-- El enlace de eliminar ahora pasa ambos IDs necesarios -->
                                    <a href="eliminarProveedor.php?id=<?php echo $proveedor['id']; ?>&id_usuario=<?php echo $proveedor['id_usuario']; ?>" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition duration-200" title="Eliminar">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>