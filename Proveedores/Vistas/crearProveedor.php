<?php
session_start();
include '../../layouts/header.php';

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
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
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proveedor</label>
                        <input type="text" name="nombre" id="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <input type="text" name="empresa" id="empresa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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