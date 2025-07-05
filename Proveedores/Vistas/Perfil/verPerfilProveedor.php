<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Mi Perfil de Proveedor";
require_once '../../../nav.php';

require_once '../../Modelos/Proveedor.php';
require_once '../../../Usuarios/Modelos/Usuario.php';

// Verificar que el usuario tenga rol de Proveedor
$usuarioModel = new Usuario();
$usuarioActual = $usuarioModel->obtenerUsuarioConRol($_SESSION['id']);

if ($usuarioActual['rol'] !== 'Proveedor') {
    $_SESSION['mensaje'] = 'Acceso denegado. Solo los proveedores pueden acceder a esta página.';
    $_SESSION['tipo_mensaje'] = 'error';
    header("Location: /Lp2_Eventos/dashboard.php");
    exit();
}

// Obtener información del proveedor asociado al usuario
$proveedorModel = new Proveedor();
$proveedor = null;
$tieneProveedor = false;

// Buscar el proveedor del usuario actual
$todosProveedores = $proveedorModel->obtenerTodosLosProveedores();
foreach ($todosProveedores as $p) {
    if ($p['id_usuario'] == $_SESSION['id']) {
        $proveedor = $p;
        $tieneProveedor = true;
        break;
    }
}

// Procesar mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo $tipo_mensaje === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300'; ?>">
            <div class="flex items-center">
                <i class="fas fa-<?php echo $tipo_mensaje === 'error' ? 'exclamation-triangle' : 'check-circle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <i class="fas fa-building text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Mi Perfil de Proveedor</h1>
                        <p class="text-blue-100 mt-1">Información de tu empresa y servicios</p>
                    </div>
                </div>
                <?php if ($tieneProveedor): ?>
                    <a href="../editarProveedor.php?id=<?php echo $proveedor['id']; ?>"
                        class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition-transform transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Información
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navegación -->
        <div class="px-6 pt-6">
            <a href="/Lp2_Eventos/dashboard.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>

        <div class="p-6">
            <?php if (!$tieneProveedor): ?>
                <!-- Usuario sin proveedor registrado -->
                <div class="text-center py-16">
                    <div class="bg-gray-50 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-store-slash text-6xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes un proveedor registrado</h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">
                        Para acceder a todas las funcionalidades de proveedor, necesitas registrar tu empresa primero.
                    </p>
                    <a href="../crearProveedor.php"
                        class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-plus mr-2"></i>Registrar mi Empresa
                    </a>
                </div>
            <?php else: ?>
                <!-- Información del proveedor -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información Principal -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Información de la Empresa
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-building text-blue-500 mr-2"></i>
                                            <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-green-500 mr-2"></i>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($proveedor['telefono']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                        <div class="flex items-start">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-2 mt-1"></i>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($proveedor['direccion']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuario Asociado</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-purple-500 mr-2"></i>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($proveedor['nombre_usuario']); ?></span>
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Propietario</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Estado y Acciones -->
                    <div class="space-y-6">
                        <!-- Estado del Proveedor -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                                Estado
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Estado</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Activo
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">ID de Proveedor</span>
                                    <span class="text-sm font-medium text-gray-900">#<?php echo $proveedor['id']; ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Fecha de Registro</span>
                                    <span class="text-sm text-gray-900">Activo</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                                Acciones Rápidas
                            </h3>
                            <div class="space-y-3">
                                <a href="../verCatalogo.php?id_proveedor=<?php echo $proveedor['id']; ?>"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-list mr-2"></i>
                                    Ver mis Servicios
                                </a>
                                <a href="../crearCatalogo.php?id_proveedor=<?php echo $proveedor['id']; ?>"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Agregar Servicio
                                </a>
                                <a href="/Lp2_Eventos/Reserva/Vistas/verEventos.php"
                                    class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Ver mis Eventos
                                </a>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                                <i class="fas fa-address-card text-blue-600 mr-2"></i>
                                Información de Contacto
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-blue-500 w-5"></i>
                                    <span class="ml-2 text-sm text-blue-800"><?php echo htmlspecialchars($usuarioActual['correo']); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-blue-500 w-5"></i>
                                    <span class="ml-2 text-sm text-blue-800"><?php echo htmlspecialchars($proveedor['telefono']); ?></span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-blue-500 w-5 mt-0.5"></i>
                                    <span class="ml-2 text-sm text-blue-800"><?php echo htmlspecialchars($proveedor['direccion']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../../layouts/footer.php'; ?>