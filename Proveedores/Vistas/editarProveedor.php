<?php
session_start();
// Se incluye el controlador que se encargará de toda la lógica.
require_once '../Controlador/ProveedorController.php';

// 1. VERIFICACIÓN DE SESIÓN
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

// 2. OBTENCIÓN DE DATOS PARA EL FORMULARIO
// Se crea una instancia del controlador.
$proveedorController = new ProveedorController();
$proveedor = null;

// Se obtiene el ID del proveedor desde la URL.
$id_proveedor = $_GET['id'] ?? 0;

if ($id_proveedor > 0) {
    // Se utiliza el controlador para buscar los datos del proveedor.
    // El método buscarPorId debería devolver toda la información necesaria (de la tabla proveedores y usuarios).
    $proveedor = $proveedorController->buscarPorId($id_proveedor);
}

// Se incluye el header de la página.
$titulo_pagina = "Editar Proveedor";
include '../../layouts/header.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-yellow-500 text-white px-6 py-4">
             <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-edit mr-3"></i>
                Editar Proveedor
            </h1>
        </div>
        <div class="p-6">
            <?php if ($proveedor): // El formulario solo se muestra si se encontró el proveedor ?>
                
                <?php if (!empty($_GET['error'])): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- El formulario apunta directamente al controlador con la acción 'actualizar' -->
                <form action="../../controllers/ProveedorController.php?action=actualizar" method="post">
                    <!-- Campos ocultos para enviar los IDs necesarios -->
                    <input type="hidden" name="id_proveedor" value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($proveedor['id_usuario']); ?>">

                    <div class="space-y-6">
                        <!-- SECCIÓN DE DATOS DE LA EMPRESA -->
                        <fieldset class="border-t border-gray-200 pt-4">
                            <legend class="text-lg font-semibold text-gray-900">Datos de la Empresa</legend>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                                    <input type="text" name="empresa" id="empresa" value="<?php echo htmlspecialchars($proveedor['empresa']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                                <div>
                                    <label for="nombre_proveedor" class="block text-sm font-medium text-gray-700 mb-1">Nombre Público del Proveedor</label>
                                    <input type="text" name="nombre_proveedor" id="nombre_proveedor" value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                </div>
                                <div>
                                    <label for="correo_proveedor" class="block text-sm font-medium text-gray-700 mb-1">Correo de Contacto Público</label>
                                    <input type="email" name="correo_proveedor" id="correo_proveedor" value="<?php echo htmlspecialchars($proveedor['correo_proveedor']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                            </div>
                        </fieldset>

                        <!-- SECCIÓN DE DATOS DEL USUARIO (CONTACTO) -->
                        <fieldset class="border-t border-gray-200 pt-4">
                            <legend class="text-lg font-semibold text-gray-900">Datos del Usuario de Contacto</legend>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                                    <input type="text" name="nombres" id="nombres" value="<?php echo htmlspecialchars($proveedor['nombres']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                                <div>
                                    <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                                    <input type="text" name="apellidos" id="apellidos" value="<?php echo htmlspecialchars($proveedor['apellidos']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                                <div>
                                    <label for="correo_usuario" class="block text-sm font-medium text-gray-700 mb-1">Correo para Iniciar Sesión</label>
                                    <input type="email" name="correo_usuario" id="correo_usuario" value="<?php echo htmlspecialchars($proveedor['correo_usuario']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="verProveedores.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-200">
                            <i class="fas fa-save mr-2"></i>Actualizar Proveedor
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <div class="text-center text-red-500">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p>Error: Proveedor no encontrado o ID no válido.</p>
                    <a href="verProveedores.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Volver a la lista</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>