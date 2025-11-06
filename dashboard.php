<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: Autenticacion/Vista/login.php");
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
    header("Location: Autenticacion/Vista/login.php");
    exit();
}

$titulo_pagina = "Dashboard - Panel de Control";
require_once 'layouts/header.php';
require_once 'nav.php';
?>

<style>
    /* Estilos personalizados para el dashboard */
    .cards-container-two {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        max-width: 56rem; /* max-w-4xl */
        margin-left: auto;
        margin-right: auto;
    }
    
    .cards-container-normal {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    /* Para pantallas más pequeñas, asegurar que las tarjetas se apilen */
    @media (max-width: 640px) {
        .cards-container-two,
        .cards-container-normal {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<?php
?>

<div class="min-h-screen bg-gray-50">
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
        <?php
        // Contar tarjetas visibles
        $tarjetasVisibles = 2; // Eventos y Proveedores siempre visibles
        if ($rol === 'Administrador') $tarjetasVisibles++;
        if ($rol === 'Proveedor') $tarjetasVisibles++;
        
        // Determinar clases CSS según el número de tarjetas
        $containerClasses = ($tarjetasVisibles === 2) ? 'cards-container-two' : 'cards-container-normal';
        ?>
        <div class="<?php echo $containerClasses; ?>">
            <?php if ($rol === 'Administrador'): ?>
            <!-- Usuarios -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 w-full sm:w-80 lg:flex-1 lg:max-w-sm">
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
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 w-full sm:w-80 lg:flex-1 lg:max-w-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Eventos</h3>
                        <p class="text-gray-600">Gestionar eventos</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="Reserva/Vistas/verEventos.php" class="text-green-600 hover:text-green-800">Ver todos →</a>
                </div>
            </div>

            <!-- Proveedores -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 w-full sm:w-80 lg:flex-1 lg:max-w-sm">
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

            <!-- Perfil Proveedor -->
            <?php if ($rol === 'Proveedor'): ?>
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 w-full sm:w-80 lg:flex-1 lg:max-w-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Empresa</h3>
                        <p class="text-gray-600">Ver y editar tu perfil de empresa</p>
                    </div>
                </div>
                    <div class="mt-4">
                        <a href="Proveedores/Vistas/Perfil/verPerfilProveedor.php" class="text-yellow-600 hover:text-yellow-800">Ir al perfil →</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas del Sistema</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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

                <div class="flex flex-col items-center justify-center w-full">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $stats['usuarios']; ?></div>
                    <div class="text-sm text-gray-600">Usuarios</div>
                </div>
                
                <div class="flex flex-col items-center justify-center w-full">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $stats['proveedores']; ?></div>
                    <div class="text-sm text-gray-600">Proveedores</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'layouts/footer.php';
$conexion->desconectar(); 
?>