<?php
require_once '../../conexion_db.php';
require_once '../../layouts/header.php';
require_once '../Modelos/Usuario.php';
require_once '../../nav.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$usuarioModel = new Usuario();
$usuarios = $usuarioModel->obtenerTodosLosUsuarios();
?>

<div class="container mx-auto mt-8 px-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-3xl font-bold text-white text-center">
            <i class="fas fa-users mr-3"></i>Gestión de Usuarios
        </h2>
        <p class="text-purple-100 text-center mt-2">Administra y visualiza todos los usuarios del sistema</p>
    </div>

    <div class="px-6 pt-6">
        <a href="/Lp2_Eventos/dashboard.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Atras
        </a>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Table Header Info -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Total de usuarios: <?php echo count($usuarios); ?></h3>
                <div class="flex space-x-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Activos</span>
                </div>
            </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-hashtag mr-2 text-gray-400"></i>ID
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-gray-400"></i>Nombres
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>Correo
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user-tag mr-2 text-gray-400"></i>Rol
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-cogs mr-2 text-gray-400"></i>Acciones
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($usuarios as $index => $usuario): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200 <?php echo $index % 2 == 0 ? 'bg-white' : 'bg-gray-25'; ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                                        <?php echo htmlspecialchars($usuario['id']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                <?php echo strtoupper(substr(htmlspecialchars($usuario['nombres']), 0, 2)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($usuario['nombres']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 flex items-center">
                                    <i class="fas fa-at text-gray-400 mr-2"></i>
                                    <?php echo htmlspecialchars($usuario['correo']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $rolClass = '';
                                $rolIcon = '';
                                switch(strtolower($usuario['rol'])) {
                                    case 'admin':
                                        $rolClass = 'bg-red-100 text-red-800';
                                        $rolIcon = 'fas fa-crown';
                                        break;
                                    case 'usuario':
                                        $rolClass = 'bg-green-100 text-green-800';
                                        $rolIcon = 'fas fa-user';
                                        break;
                                    case 'moderador':
                                        $rolClass = 'bg-yellow-100 text-yellow-800';
                                        $rolIcon = 'fas fa-user-shield';
                                        break;
                                    default:
                                        $rolClass = 'bg-gray-100 text-gray-800';
                                        $rolIcon = 'fas fa-user-circle';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $rolClass; ?>">
                                    <i class="<?php echo $rolIcon; ?> mr-1"></i>
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="actualizarusuario.php?id=<?php echo $usuario['id']; ?>" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar
                                    </a>
                                    <a href="eliminarUsuario.php?id=<?php echo $usuario['id']; ?>" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">
                                        <i class="fas fa-trash mr-1"></i>
                                        Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <?php if (empty($usuarios)): ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay usuarios registrados</h3>
                <p class="text-gray-500">Aún no se han registrado usuarios en el sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</div>