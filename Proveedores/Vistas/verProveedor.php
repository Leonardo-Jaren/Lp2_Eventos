<?php

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

require_once '../../nav.php';
$titulo_pagina = 'Gestión de Proveedores';
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';

$proveedorModel = new Proveedor();
$proveedores = $proveedorModel->obtenerTodosLosProveedores();
$rol = $usuario['rol'] ?? 'Cliente';

// Verificar si el usuario actual ya tiene un proveedor registrado
$usuarioTieneProveedor = $proveedorModel->existeProveedorParaUsuario($_SESSION['id']);

// Procesar mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $tipo_mensaje === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-purple-700 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-truck-loading mr-3"></i>
                    Gestión de Proveedores
                </h1>
                <?php if ($rol === 'Administrador' || ($rol === 'Proveedor' && !$usuarioTieneProveedor)): ?>
                    <a href="crearProveedor.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition-transform transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>
                        Nuevo proveedor
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="px-6 pt-6">
            <a href="/Lp2_Eventos/dashboard.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Atras
            </a>
        </div>

        <div class="p-6">
            <!-- Estadísticas -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-r from-orange-500 to-yellow-600 rounded-lg p-4 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-users text-2xl mr-3"></i>
                        <div>
                            <p class="text-blue-100 text-sm">Total Proveedores</p>
                            <p class="text-2xl font-bold"><?php echo count($proveedores); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-lg p-4 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-handshake text-2xl mr-3"></i>
                        <div>
                            <p class="text-blue-100 text-sm">Proveedores Activos</p>
                            <p class="text-2xl font-bold"><?php echo count($proveedores); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-4 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-star text-2xl mr-3"></i>
                        <div>
                            <p class="text-blue-100 text-sm">Mejor Valorados</p>
                            <p class="text-2xl font-bold"><?php echo min(count($proveedores), 5); ?></p>
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
                    <?php if ($rol === 'Administrador' || ($rol === 'Proveedor' && !$usuarioTieneProveedor)): ?>
                        <a href="crearProveedor.php" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-plus mr-2"></i>Crear Primer Proveedor
                        </a>
                    <?php elseif ($rol === 'Proveedor' && $usuarioTieneProveedor): ?>
                        <p class="text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-lg p-3 max-w-md mx-auto">
                            <i class="fas fa-info-circle mr-2"></i>
                            Ya tienes un proveedor registrado. Un usuario solo puede tener un proveedor asociado.
                        </p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Grid de Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($proveedores as $proveedor): ?>
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                            <!-- Header de la card -->
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-white truncate">
                                        <i class="fas fa-building mr-2"></i>
                                        <?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>
                                    </h3>
                                    <span class="bg-white bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                                        ID: <?php echo $proveedor['id']; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Contenido de la card -->
                            <div class="p-4">
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-phone text-blue-500 w-5"></i>
                                        <span class="ml-2 text-sm"><?php echo htmlspecialchars($proveedor['telefono']); ?></span>
                                    </div>
                                    <div class="flex items-start text-gray-600">
                                        <i class="fas fa-map-marker-alt text-red-500 w-5 mt-0.5"></i>
                                        <span class="ml-2 text-sm"><?php echo htmlspecialchars($proveedor['direccion']); ?></span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-user text-green-500 w-5"></i>
                                        <span class="ml-2 text-sm">
                                            <?php echo $proveedor['nombre_usuario'] ? htmlspecialchars($proveedor['nombre_usuario']) : 'Sin asignar'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer de la card -->
                            <div class="bg-gray-50 px-4 py-3 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <?php if ($rol === 'Administrador' || ($rol === 'Proveedor' && !$usuarioTieneProveedor)): ?>
                                    <div class="flex space-x-2">
                                        <a href="editarProveedor.php?id=<?php echo $proveedor['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                           title="Editar proveedor">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="eliminarProveedor.php?id=<?php echo $proveedor['id']; ?>" 
                                           class="text-red-600 hover:text-red-800 transition-colors duration-200" 
                                           title="Eliminar proveedor"
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este proveedor?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <a href="../Vistas/verCatalogo.php?id_proveedor=<?php echo $proveedor['id']; ?>" 
                                       class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-list mr-1"></i>Servicios
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Información adicional para usuarios con proveedor -->
                <?php if ($rol === 'Proveedor' && $usuarioTieneProveedor): ?>
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900">Información</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Ya tienes un proveedor registrado. Un usuario solo puede tener un proveedor asociado.
                                    Puedes editar la información de tu proveedor existente.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>