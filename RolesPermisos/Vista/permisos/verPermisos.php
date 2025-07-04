<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

require_once '../../../nav.php';
$titulo_pagina = 'Gestión de Permisos';
require_once '../../Modelo/Permisos.php';
require_once '../../../layouts/header.php';
require_once '../../../conexion_db.php';

$permisoModel = new Permisos();
$permisos = $permisoModel->obtenerTodosLosPermisos();

?>

<div class="container mx-auto mt-8 px-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-3xl font-bold text-white text-center">
            <i class="fas fa-key mr-3"></i>Gestión de Permisos
        </h2>
        <p class="text-purple-100 text-center mt-2">Administra y visualiza todos los permisos del sistema</p>
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
                <h3 class="text-lg font-semibold text-gray-800">Total de permisos: <?php echo count($permisos); ?></h3>
                <div class="flex space-x-2">
                    <a href="crearPermisos.php" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Nuevo Permiso
                    </a>
                </div>
            </div>
        </div>
        <!-- Responsive Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nombre del Permiso
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($permisos as $permiso): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($permiso['id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($permiso['nombre_permiso']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($permiso['descripcion']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="../permisos/actulizarPermisos.php?id=<?php echo $permiso['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="eliminarPermisos.php?id=<?php echo $permiso['id']; ?>"
                                    onclick="return confirm('¿Estás seguro de eliminar este permiso?');"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- No Permisos Message -->
        <?php if (empty($permisos)): ?>
            <div class="px-6 py-4 text-center">
                <p class="text-gray-500">No hay permisos disponibles.</p>
            </div>
        <?php endif; ?>
    </div>
</div>