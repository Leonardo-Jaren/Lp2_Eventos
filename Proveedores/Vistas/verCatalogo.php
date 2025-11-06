<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticacion/Vista/login.php");
    exit();
}

require_once '../../nav.php';
$titulo_pagina = 'Catálogo de Servicios';
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';

$servicioModel = new CatalogoServicios();
$id_proveedor = $_GET['id_proveedor'] ?? 0;

$servicios = [];
$proveedorInfo = null;

if ($id_proveedor) {
    // Obtener servicios específicos del proveedor seleccionado
    $servicios = $servicioModel->obtenerServiciosPorProveedor($id_proveedor);
    
    // Obtener información del proveedor
    $proveedorModel = new Proveedor();
    $proveedorInfo = $proveedorModel->encontrarProveedor($id_proveedor);
} else {
    // Si no hay proveedor específico, mostrar todos los servicios
    $servicios = $servicioModel->obtenerTodosLosServicios();
}

$rol = $usuario['rol'] ?? 'Cliente';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-book-open mr-3"></i>
                    <?php if ($proveedorInfo): ?>
                        Catálogo de: <?php echo htmlspecialchars($proveedorInfo['nombre_empresa']); ?>
                    <?php else: ?>
                        Catálogo de Servicios
                    <?php endif; ?>
                </h1>
                <?php if ($proveedorInfo): ?>
                    <p class="text-indigo-200 text-sm mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Servicios ofrecidos por <?php echo htmlspecialchars($proveedorInfo['nombre_empresa']); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php if ($rol === 'Administrador' || ($rol === 'Proveedor' && $id_proveedor)): ?>
                <a href="crearCatalogo.php?id_proveedor=<?php echo urlencode($id_proveedor); ?>" 
                   class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold py-2 px-4 rounded shadow inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Agregar Servicio
                </a>
            <?php endif; ?>
        </div>
        <div class="p-6">
            <a href="verProveedor.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Volver a la lista de proveedores
            </a>
            
            <?php if ($proveedorInfo): ?>
                <!-- Información del proveedor -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-building text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900"><?php echo htmlspecialchars($proveedorInfo['nombre_empresa']); ?></h3>
                            <div class="text-sm text-blue-700 mt-1">
                                <span class="mr-4"><i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($proveedorInfo['telefono']); ?></span>
                                <span><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($proveedorInfo['direccion']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="overflow-x-auto">
                <table class="w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Nombre del servicio</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Descripción</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Precio Base</th>
                            <?php if ($rol === 'Administrador' || $rol === 'Proveedor'): ?>
                                <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Acciones</th>
                            <?php endif; ?>
                            <?php if ($rol === 'Cliente'): ?>
                                <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Solicitar</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($servicios)): ?>
                            <tr>
                                <td colspan="<?php echo ($rol === 'Cliente') ? '6' : '5'; ?>" class="px-4 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <?php if ($proveedorInfo): ?>
                                            <p class="text-lg font-medium">No hay servicios registrados</p>
                                            <p class="text-sm mt-1"><?php echo htmlspecialchars($proveedorInfo['nombre_empresa']); ?> aún no ha registrado servicios.</p>
                                        <?php else: ?>
                                            <p class="text-lg font-medium">No se encontraron servicios</p>
                                            <p class="text-sm mt-1">No hay servicios disponibles en este momento.</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 font-mono text-gray-900">#<?php echo htmlspecialchars($servicio['id']); ?></td>
                                    <td class="px-4 py-4 font-medium text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-cog text-indigo-500 mr-2"></i>
                                            <?php echo htmlspecialchars($servicio['nombre_servicio']); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-700">
                                        <?php 
                                        $descripcion = $servicio['descripcion'] ?? '';
                                        if ($descripcion) {
                                            $descripcionCorta = substr($descripcion, 0, 80);
                                            echo htmlspecialchars($descripcionCorta);
                                            if (strlen($descripcion) > 80) {
                                                echo '<span class="text-blue-500 cursor-pointer" title="' . htmlspecialchars($descripcion) . '">... ver más</span>';
                                            }
                                        } else {
                                            echo '<span class="text-gray-400 italic">Sin descripción</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-green-600 font-semibold">
                                            S/ <?php echo number_format($servicio['precio'], 2); ?>
                                        </span>
                                    </td>
                                    <?php if ($rol === 'Administrador' || $rol === 'Proveedor'): ?>
                                        <td class="px-4 py-4">
                                            <div class="flex space-x-2">
                                                <a href="editarCatalogo.php?id=<?php echo urlencode($servicio['id']); ?>&id_proveedor=<?php echo urlencode($id_proveedor); ?>" 
                                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                                   title="Editar servicio">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="eliminarCatalago.php?id=<?php echo urlencode($servicio['id']); ?>&id_proveedor=<?php echo urlencode($id_proveedor); ?>" 
                                                   class="text-red-600 hover:text-red-800 transition-colors duration-200" 
                                                   title="Eliminar servicio"
                                                   onclick="return confirm('¿Estás seguro de eliminar este servicio?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                    <?php if ($rol === 'Cliente'): ?>
                                        <td class="px-4 py-4">
                                            <a href="/Lp2_Eventos/Reserva/Vistas/crearEvento.php?id_proveedor=<?php echo urlencode($id_proveedor); ?>&id_servicio=<?php echo urlencode($servicio['id']); ?>" 
                                               class="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700 transition-colors duration-200">
                                                <i class="fas fa-calendar-plus mr-1"></i>Solicitar
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($servicios)): ?>
                <!-- Resumen de servicios -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-gray-600 mr-2"></i>
                            <span class="text-gray-700 font-medium">
                                Total de servicios: <?php echo count($servicios); ?>
                            </span>
                        </div>
                        <?php if ($proveedorInfo): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-1"></i>
                                Proveedor: <?php echo htmlspecialchars($proveedorInfo['nombre_empresa']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>