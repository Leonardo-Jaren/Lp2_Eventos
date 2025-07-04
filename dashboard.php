<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: Autenticación/Vista/login.php");
    exit();
}

require_once 'conexion_db.php';

$conexion = new ConexionDB();
$conn = $conexion->conectar();

$sqlUser = "SELECT u.nombres, u.correo, r.nombre as rol FROM usuarios u 
            LEFT JOIN roles r ON u.id_rol = r.id 
            WHERE u.id = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->execute([$_SESSION['id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$rol = $usuario['rol'] ?? 'Cliente';

if (!$usuario) {
    session_destroy();
    header("Location: Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Dashboard - Panel de Control";
require_once 'layouts/header.php';
require_once 'nav.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Banner de Dashboard -->
    <!-- <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Sistema para Gestión de Eventos</h1>
                    <p class="text-gray-600">Panel de control del sistema de eventos</p>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="bg-blue-50 rounded-lg px-4 py-2">
                        <p class="text-sm text-blue-600 font-medium">
                            <i class="fas fa-calendar-check mr-1"></i>
                            Sistema activo
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Fecha actual:</p>
                        <p class="font-medium text-gray-900"><?php echo date('d/m/Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de Bienvenida -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-8 mb-8">
            <div class="text-white">
                <h2 class="text-4xl font-bold mb-2">
                    ¡Bienvenido, <?php echo htmlspecialchars($usuario['nombres']); ?>! 👋
                </h2>
                <p class="text-blue-100 text-lg">
                    Nos alegra verte de nuevo. Aquí tienes acceso a todas las funcionalidades del sistema.
                </p>
                <div class="mt-4 flex items-center space-x-4 text-blue-100">
                    <span class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <?php echo htmlspecialchars($usuario['correo'] ?? 'No disponible'); ?>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($usuario['rol'] ?? 'Sin rol'); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Navegación -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if ($rol === 'Administrador'): ?>
            <!-- Usuarios -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a2.25 2.25 0 01-2.25 2.25H8.25A2.25 2.25 0 016 18V6a2.25 2.25 0 012.25-2.25h6.792c.103 0 .204.02.296.059l3.068 1.534A2.25 2.25 0 0120.25 7.5V18z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Usuarios</h3>
                        <p class="text-gray-600">Gestionar usuarios del sistema</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="Usuarios/Vistas/verUsuarios.php" class="text-blue-600 hover:text-blue-800">Ver todos →</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reservas -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Reservas</h3>
                        <p class="text-gray-600">Gestionar reservas de eventos</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="Reserva/Vistas/verReservas.php" class="text-green-600 hover:text-green-800">Ver todas →</a>
                </div>
            </div>

            <!-- Proveedores -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Proveedores</h3>
                        <p class="text-gray-600">Gestionar proveedores de servicios</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="Proveedores/Vistas/verProveedor.php" class="text-purple-600 hover:text-purple-800">Ver todos →</a>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas del Sistema</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <?php
                // Obtener estadísticas
                $stats = [];

                // Total usuarios
                $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
                $stats['usuarios'] = $result->fetch()['total'];

                // Total proveedores
                $result = $conn->query("SELECT COUNT(*) as total FROM proveedores");
                $stats['proveedores'] = $result->fetch()['total'] ?? 0;
                ?>

                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $stats['usuarios']; ?></div>
                    <div class="text-sm text-gray-600">Usuarios</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $stats['proveedores']; ?></div>
                    <div class="text-sm text-gray-600">Proveedores</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $conexion->desconectar(); ?>