<?php

require_once '../../../conexion_db.php';
require_once '../../Controlador/PermisosController.php';
require_once '../../Modelo/Permisos.php';
require_once '../../../layouts/header.php';
require_once '../../../nav.php';

$id = $_GET['id'] ?? null;
$permiso = null;

if ($id) {
    $permisoModel = new Permisos();
    $permiso = $permisoModel->obtenerPermisoPorId($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $permisoController = new PermisosController();
    $mensaje = $permisoController->actualizarPermiso($_POST);
    if ($mensaje) {
        echo '<div class="mt-4 text-center text-sm text-red-600 font-semibold">' .
            htmlspecialchars($mensaje) .
            '</div>';
    }
}
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Actualizar Permiso</h2>
        <?php if ($permiso): ?>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($permiso['id']); ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Permiso</label>
                    <input type="text" name="nombre_permiso" value="<?php echo htmlspecialchars($permiso['nombre_permiso']); ?>" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="descripcion" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($permiso['descripcion']); ?></textarea>
                </div>
                <div class="flex gap-4 mt-6">
                    <a href="/Lp2_Eventos/RolesPermisos/Vista/permisos/verPermisos.php"
                       class="flex-1 bg-red-500 text-white px-4 py-2 rounded-md text-center hover:bg-red-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Actualizar
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="text-red-600">Permiso no encontrado.</div>
        <?php endif; ?>
    </div>
</div>