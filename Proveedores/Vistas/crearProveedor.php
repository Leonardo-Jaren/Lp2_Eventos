<?php
session_start();
include '../../layouts/header.php';

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

// Obtener información del usuario actual y su rol
require_once '../../Usuarios/Modelos/Usuario.php';
require_once '../Modelos/Proveedor.php';

$usuarioModel = new Usuario();
$usuarioActual = $usuarioModel->obtenerUsuarioConRol($_SESSION['id']);
$esAdministrador = ($usuarioActual['rol'] === 'Administrador');

$proveedor = new Proveedor();
if (!$esAdministrador && $proveedor->existeProveedorParaUsuario($_SESSION['id'])) {
    $_SESSION['mensaje'] = 'Ya tienes un proveedor registrado. No puedes crear más de uno.';
    $_SESSION['tipo_mensaje'] = 'error';
    header("Location: verProveedor.php");
    exit();
}

// Si es administrador, obtener lista de usuarios
$usuarios = [];
if ($esAdministrador) {
    $usuarios = Proveedor::obtenerUsuariosDisponibles();
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../Controlador/ProveedorController.php';
    $proveedorController = new ProveedorController();
    $mensaje = $proveedorController->registrarProveedor($_POST);
}

?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
             <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-plus-circle mr-3"></i>
                Crear Nuevo Proveedor
            </h1>
        </div>
        <div class="p-6">
            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span><?php echo htmlspecialchars($mensaje); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="crearProveedor.php" method="post">
                <div class="space-y-4">
                    <div>
                        <label for="nombre_empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa" id="nombre_empresa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <!-- Campo de Usuario -->
                    <div>
                        <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Usuario Asignado
                        </label>
                        <?php if ($esAdministrador): ?>
                            <!-- Select para administrador -->
                            <select name="id_usuario" id="id_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="<?php echo $usuarioActual['id']; ?>" selected>
                                    <?php echo htmlspecialchars($usuarioActual['nombres'] . ' ' . $usuarioActual['apellidos'] . ' (Usar mi usuario)'); ?>
                                </option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php if ($usuario['id'] != $usuarioActual['id']): ?>
                                        <option value="<?php echo $usuario['id']; ?>">
                                            <?php echo htmlspecialchars($usuario['nombre_completo'] . ' (' . $usuario['rol'] . ')'); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Como administrador, puedes asignar este proveedor a cualquier usuario</p>
                        <?php else: ?>
                            <!-- Campo de solo lectura para usuarios normales -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 flex items-center">
                                <i class="fas fa-user-check text-blue-500 mr-2"></i>
                                <span class="text-gray-700"><?php echo htmlspecialchars($usuarioActual['nombres'] . ' ' . $usuarioActual['apellidos'] . ' (' . $usuarioActual['rol'] . ')'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="verProveedor.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
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