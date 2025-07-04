<?php
session_start();
include '../../layouts/header.php';

// 1. VERIFICACIÓN DE SESIÓN
// Esto es correcto, solo los usuarios logueados (ej. administradores) deberían poder crear proveedores.
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$mensaje = '';

// 2. PROCESAMIENTO DEL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Se incluye el controlador.
    require_once '../../Controlador/ProveedorController.php';
    
    // Se crea una instancia del controlador, como lo definimos en nuestro diseño.
    $proveedorController = new ProveedorController();
    
    // Se llama al método 'guardar' de la instancia del controlador.
    // El método se encargará de pasar los datos al modelo.
    // No es necesario modificar el array $_POST, el controlador y el modelo se encargarán.
    $proveedorController->guardar($_POST);
    
    // Nota: La redirección ahora se maneja dentro del método guardar() del controlador.
    // Este script terminará su ejecución allí si el guardado es exitoso.
    // Si hubiera un error, el controlador podría manejarlo o redirigir con un mensaje.
}
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
             <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-user-plus mr-3"></i>
                Registrar Nuevo Proveedor
            </h1>
        </div>
        <div class="p-6">
            <?php if (!empty($_GET['error'])): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- El formulario ahora apunta al controlador para seguir el patrón MVC. -->
            <form action="../../controllers/ProveedorController.php?action=guardar" method="post">
                <div class="space-y-6">
                    
                    <!-- SECCIÓN DE DATOS DE LA EMPRESA -->
                    <fieldset class="border-t border-gray-200 pt-4">
                        <legend class="text-lg font-semibold text-gray-900">Datos de la Empresa</legend>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                                <input type="text" name="empresa" id="empresa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label for="nombre_proveedor" class="block text-sm font-medium text-gray-700 mb-1">Nombre Público del Proveedor (Opcional)</label>
                                <input type="text" name="nombre_proveedor" id="nombre_proveedor" placeholder="Ej: DJ Tiesto, Catering Anita" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="correo_proveedor" class="block text-sm font-medium text-gray-700 mb-1">Correo de Contacto Público</label>
                                <input type="email" name="correo_proveedor" id="correo_proveedor" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECCIÓN DE DATOS DEL USUARIO (CONTACTO) -->
                    <fieldset class="border-t border-gray-200 pt-4">
                        <legend class="text-lg font-semibold text-gray-900">Cuenta de Usuario del Contacto</legend>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1">Nombres del Contacto</label>
                                <input type="text" name="nombres" id="nombres" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos del Contacto</label>
                                <input type="text" name="apellidos" id="apellidos" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label for="correo_usuario" class="block text-sm font-medium text-gray-700 mb-1">Correo para Iniciar Sesión</label>
                                <input type="email" name="correo_usuario" id="correo_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                             <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                                <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                    </fieldset>
                </div>
                
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="verProveedores.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>