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
require_once '../Modelos/Proveedor.php';
require_once '../../Proveedores/Controlador/ProveedorController.php';
require_once '../../Usuarios/Modelos/Usuario.php';

$usuarioModel = new Usuario();
$usuarioActual = $usuarioModel->obtenerUsuarioConRol($_SESSION['id']);
$esAdministrador = ($usuarioActual['rol'] === 'Administrador');

$id = $_GET['id'] ?? null;
$proveedor = null;
if ($id) {
    $proveedorModel = new Proveedor();
    $proveedor = $proveedorModel->encontrarProveedor($id);
}

$usuarios = [];
if ($esAdministrador) {
    $usuarios = Proveedor::obtenerUsuariosDisponibles();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proveedorController = new ProveedorController();
    $mensaje = $proveedorController->editarProveedor($_POST);
    if ($mensaje) {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . htmlspecialchars($mensaje);
        echo '</div>';
    }
}
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
            <?php if ($proveedor): ?>
                <p class="mt-2">Estás editando el proveedor: <strong><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></strong></p>
                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                    <div class="space-y-4">
                        <div>
                            <label for="nombre_empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                            <input type="text" name="nombre_empresa" id="nombre_empresa" value="<?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                        </div>
                        <div>
                            <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($proveedor['direccion']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                        </div>

                        <!-- Campo de Usuario -->
                        <div>
                            <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-user mr-2"></i>Usuario Asignado
                            </label>
                            <?php if ($esAdministrador): ?>
                                <!-- Select para administrador -->
                                <select name="id_usuario" id="id_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>"
                                            <?php echo ($proveedor['id_usuario'] == $usuario['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($usuario['nombre_completo'] . ' (' . $usuario['rol'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Como administrador, puedes reasignar este proveedor a otro usuario</p>
                            <?php else: ?>
                                <!-- Campo de solo lectura para usuarios normales -->
                                <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 flex items-center">
                                    <i class="fas fa-user-check text-yellow-500 mr-2"></i>
                                    <span class="text-gray-700">
                                        <?php echo htmlspecialchars($proveedor['nombre_usuario'] ?? 'Usuario no asignado'); ?>
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Solo los administradores pueden cambiar la asignación de usuario</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="verProveedor.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
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

<?php
require_once '../Controlador/ProveedorController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $controller = new ProveedorController();
    $mensaje = $controller->editarProveedor($_POST);

    if ($mensaje) {
        echo '<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . htmlspecialchars($mensaje);
        echo '</div>';
    }
}

require_once '../../layouts/footer.php';
