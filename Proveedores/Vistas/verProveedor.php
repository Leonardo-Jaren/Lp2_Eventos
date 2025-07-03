<?php
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';
require_once '../../nav.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}
$proveedorModel = new Proveedor();
$proveedores = $proveedorModel->obtenerTodosLosProveedores();

// Debug temporal - eliminar después
if (empty($proveedores)) {
    error_log("No se encontraron proveedores en la base de datos");
} else {
    error_log("Se encontraron " . count($proveedores) . " proveedores");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-purple-700 text-white px-6 py-4">
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
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <div>
                            <p class="text-green-100 text-sm">Activos</p>
                            <p class="text-2xl font-bold"><?php echo count($proveedores); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                    <div class="flex items-center">
                        <i class="fas fa-star text-2xl mr-3"></i>
                        <div>
                            <p class="text-purple-100 text-sm">Destacados</p>
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
                    <a href="crearProveedor.php" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-plus mr-2"></i>Crear Primer Proveedor
                    </a>
                </div>
            <?php else: ?>
                <!-- Grid de Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($proveedores as $proveedor): ?>
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                            <!-- Header de la card -->
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                                        <i class="fas fa-building text-white text-xl"></i>
                                    </div>
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                        <div class="w-2 h-2 bg-green-400 rounded-full opacity-60"></div>
                                        <div class="w-2 h-2 bg-green-400 rounded-full opacity-30"></div>
                                    </div>
                                </div>
                                <h3 class="text-white text-lg font-bold mt-3 truncate"><?php echo htmlspecialchars($proveedor['nombre']); ?></h3>
                                <p class="text-blue-100 text-sm"><?php echo htmlspecialchars($proveedor['empresa']); ?></p>
                            </div>

                            <!-- Contenido de la card -->
                            <div class="p-4 space-y-3">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-envelope text-blue-500 w-5 text-sm mr-3"></i>
                                    <span class="text-sm truncate"><?php echo htmlspecialchars($proveedor['correo']); ?></span>
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar-check text-green-500 w-5 text-sm mr-3"></i>
                                    <span class="text-sm">Servicios disponibles</span>
                                </div>

                                <!-- Badge de estado -->
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                                        Activo
                                    </span>
                                    <span class="text-xs text-gray-500">ID: <?php echo $proveedor['id']; ?></span>
                                </div>
                            </div>

                            <!-- Footer con acciones -->
                            <div class="bg-gray-50 p-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-2">
                                        <a href="verCatalogo.php?id=<?php echo $proveedor['id']; ?>" 
                                           class="bg-indigo-500 hover:bg-indigo-600 text-white p-2 rounded-lg transition duration-200 shadow-sm hover:shadow-md" 
                                           title="Ver Catálogo">
                                            <i class="fas fa-book-open text-xs"></i>
                                        </a>
                                        
                                        <a href="editarProveedor.php?id=<?php echo $proveedor['id']; ?>" 
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg transition duration-200 shadow-sm hover:shadow-md" 
                                           title="Editar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        
                                        <a href="eliminarProveedor.php?id=<?php echo $proveedor['id']; ?>" 
                                           class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition duration-200 shadow-sm hover:shadow-md" 
                                           title="Eliminar" 
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?')">
                                            <i class="fas fa-trash text-xs"></i>
                                        </a>
                                    </div>
                                    
                                    <button class="text-gray-400 hover:text-gray-600 transition duration-200" title="Más opciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación futura -->
                <div class="mt-8 flex justify-center">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span>Mostrando <?php echo count($proveedores); ?> de <?php echo count($proveedores); ?> proveedores</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>