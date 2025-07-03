<?php
require_once '../../conexion_db.php';
require_once '../../layouts/header.php';
require_once '../Modelos/Usuario.php';
require_once '../Controlador/UsuarioController.php';

$id = $_GET['id'] ?? null;
$usuario = null;

if ($id) {
    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->obtenerUsuarioPorId($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioController = new UsuarioController();
    $mensaje = $usuarioController->actualizarUsuario($_POST);
    if ($mensaje) {
        echo '<div class="mt-4 text-center text-sm text-red-600 font-semibold">' .
             htmlspecialchars($mensaje) .
             '</div>';
    }
}
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Actualizar Usuario</h2>
        <?php if ($usuario): ?>
        <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombres</label>
                <input type="text" name="nombres" value="<?php echo htmlspecialchars($usuario['nombres']); ?>" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Correo</label>
                <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="id_rol" required class="w-full mt-1 p-2 border border-gray-300 rounded-md">
                    <?php
                    $conexion = new ConexionDB();
                    $conn = $conexion->conectar();
                    $sqlSelect = "SELECT id, nombre FROM roles";
                    $result = $conn->query($sqlSelect);
                    foreach ($result as $row) {
                        $selected = $usuario['id_rol'] == $row['id'] ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>' . htmlspecialchars($row['nombre']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Actualizar</button>
        </form>
        <?php else: ?>
            <div class="text-red-600">Usuario no encontrado.</div>
        <?php endif; ?>
    </div>
</div>
