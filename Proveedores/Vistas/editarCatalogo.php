<?php

session_start();
require_once '../../layouts/header.php';
require_once '../Modelos/CatalogoServicios.php';
require_once '../Modelos/Proveedor.php';

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$id_servicio = $_GET['id'] ?? null;
$id_proveedor_seleccionado = isset($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : null;

$servicio = null;
if ($id_servicio) {
    $servicioModel = new CatalogoServicios();
    $servicio = $servicioModel->obtenerServicio($id_servicio);
    if (!$id_proveedor_seleccionado && $servicio) {
        $id_proveedor_seleccionado = $servicio['id_proveedor'];
    }
}

$proveedores = Proveedor::obtenerTodosLosProveedores();

$proveedor_especifico = null;
if ($id_proveedor_seleccionado) {
    $proveedorModel = new Proveedor();
    $proveedor_especifico = $proveedorModel->encontrarProveedor($id_proveedor_seleccionado);
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id_proveedor_seleccionado) {
        $_POST['id_proveedor_redirect'] = $id_proveedor_seleccionado;
    }
    require_once '../Controlador/CatalogoServiciosController.php';
    $catalogoController = new CatalogoServiciosController();
    $mensaje = $catalogoController->editarServicio($_POST);
}
?>
<!-- Fondo con gradiente y decoración -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Tarjeta principal con diseño mejorado -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
            <!-- Header con diseño más elegante -->
            <div class="bg-gradient-to-r from-yellow-500 via-yellow-600 to-orange-600 text-white px-8 py-6 relative overflow-hidden">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-white opacity-5 rounded-full"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold flex items-center">
                        <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                            <i class="fas fa-edit text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">
                                <?php if ($proveedor_especifico): ?>
                                    Editar Servicio de <?php echo htmlspecialchars($proveedor_especifico['nombre_empresa']); ?>
                                <?php else: ?>
                                    Editar Servicio
                                <?php endif; ?>
                            </div>
                            <div class="text-yellow-100 text-sm font-normal mt-1">
                                <?php if ($servicio): ?>
                                    Modificando: <?php echo htmlspecialchars($servicio['nombre_servicio']); ?>
                                <?php else: ?>
                                    Edita la información del servicio
                                <?php endif; ?>
                            </div>
                        </div>
                    </h1>
                </div>
            </div>

            <div class="p-8">
                <!-- Mensaje de error mejorado -->
                <?php if ($mensaje): ?>
                    <div class="mb-8 p-5 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 shadow-lg">
                        <div class="flex items-center">
                            <div class="bg-red-100 p-2 rounded-full mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h3 class="text-red-800 font-semibold">Error al editar el servicio</h3>
                                <p class="text-red-700 mt-1"><?php echo htmlspecialchars($mensaje); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($servicio): ?>
                    <!-- Formulario con diseño mejorado -->
                    <form action="" method="POST" class="space-y-8">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($servicio['id']); ?>">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Columna izquierda -->
                            <div class="space-y-6">
                                <!-- Nombre del servicio -->
                                <div class="group">
                                    <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-tag text-blue-500 mr-2"></i>
                                        Nombre del Servicio
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="nombre_servicio" id="nombre" 
                                               value="<?php echo htmlspecialchars($servicio['nombre_servicio']); ?>"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-yellow-100 focus:border-yellow-500 transition-all duration-200 bg-gray-50 focus:bg-white" 
                                               placeholder="Ej: Catering premium, Decoración floral..." required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-edit text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="group">
                                    <label for="precio" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-dollar-sign text-green-500 mr-2"></i>
                                        Precio Base
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="precio" id="precio" step="0.01" min="0"
                                               value="<?php echo htmlspecialchars($servicio['precio']); ?>"
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-yellow-100 focus:border-yellow-500 transition-all duration-200 bg-gray-50 focus:bg-white pl-10" 
                                               placeholder="0.00" required>
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-semibold">$</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Ingresa el precio base del servicio</p>
                                </div>

                                <!-- Descripción -->
                                <div class="group">
                                    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-align-left text-blue-500 mr-2"></i>
                                        Descripción del Servicio
                                    </label>
                                    <div class="relative">
                                        <textarea name="descripcion" id="descripcion" rows="4"
                                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-yellow-100 focus:border-yellow-500 transition-all duration-200 bg-gray-50 focus:bg-white resize-none" 
                                                  placeholder="Describe detalladamente el servicio que ofreces..."><?php echo htmlspecialchars($servicio['descripcion'] ?? ''); ?></textarea>
                                        <div class="absolute top-3 right-3 pointer-events-none">
                                            <i class="fas fa-comment text-gray-400"></i>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Máximo 500 caracteres (opcional)</p>
                                </div>
                            </div>

                            <!-- Columna derecha -->
                            <div class="space-y-6">
                                <!-- Proveedor -->
                                <div class="group">
                                    <label for="proveedor_id" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-building text-purple-500 mr-2"></i>
                                        Proveedor
                                    </label>
                                    <div class="relative">
                                        <?php if ($id_proveedor_seleccionado && $proveedor_especifico): ?>
                                            <!-- Campo de solo lectura cuando viene de un proveedor específico -->
                                            <input type="hidden" name="id_proveedor" value="<?php echo $id_proveedor_seleccionado; ?>">
                                            <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm bg-yellow-50 border-yellow-200">
                                                <div class="flex items-center">
                                                    <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                                        <i class="fas fa-building text-yellow-600 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($proveedor_especifico['nombre_empresa']); ?></div>
                                                        <?php if (!empty($proveedor_especifico['empresa'])): ?>
                                                            <div class="text-sm text-gray-600"><?php echo htmlspecialchars($proveedor_especifico['empresa']); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="ml-auto">
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Seleccionado
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Select normal cuando no viene de un proveedor específico -->
                                            <select name="id_proveedor" id="proveedor_id" 
                                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-yellow-100 focus:border-yellow-500 transition-all duration-200 bg-gray-50 focus:bg-white appearance-none" required>
                                                <option value="">Seleccionar Proveedor...</option>
                                                <?php if (!empty($proveedores)): ?>
                                                    <?php foreach ($proveedores as $proveedor): ?>
                                                        <option value="<?php echo $proveedor['id']; ?>" 
                                                                <?php if ($servicio['id_proveedor'] == $proveedor['id']) echo 'selected'; ?>>
                                                            <?php echo htmlspecialchars($proveedor['nombre']); ?> 
                                                            <?php if (!empty($proveedor['empresa'])): ?>
                                                                - <?php echo htmlspecialchars($proveedor['empresa']); ?>
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="" disabled>No hay proveedores disponibles</option>
                                                <?php endif; ?>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                            <?php if (empty($proveedores)): ?>
                                                <p class="mt-2 text-xs text-amber-600 flex items-center">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    <a href="crearProveedor.php" class="underline hover:text-amber-800">Crear un proveedor primero</a>
                                                </p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de acción mejorados -->
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                            <a href="<?php echo $id_proveedor_seleccionado ? 'verCatalogo.php?id=' . $id_proveedor_seleccionado : 'verCatalogo.php'; ?>" 
                               class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-100 transition-all duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold rounded-xl hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-yellow-200 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Estado cuando no se encuentra el servicio -->
                    <div class="text-center py-16">
                        <div class="bg-gray-50 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-exclamation-triangle text-6xl text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Servicio no encontrado</h3>
                        <p class="text-gray-500 mb-6 max-w-sm mx-auto">No se pudo encontrar el servicio que intentas editar.</p>
                        <a href="<?php echo $id_proveedor_seleccionado ? 'verCatalogo.php?id=' . $id_proveedor_seleccionado : 'verCatalogo.php'; ?>" 
                           class="inline-flex items-center bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Catálogo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../../layouts/footer.php';