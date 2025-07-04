<?php
require_once '../../conexion_db.php';
require_once '../../layouts/header.php';
require_once '../Controlador/UsuarioController.php';

$mensaje = '';
$tipoMensaje = '';

if (!empty($_POST)) {
    $usuarioController = new UsuarioController();
    
    require_once '../Modelos/Usuario.php';
    $usuarioModel = new Usuario();
    
    if ($usuarioModel->verificarCorreoExistente($_POST['correo'])) {
        $mensaje = "El correo electrónico ya está registrado. Por favor, use otro correo.";
        $tipoMensaje = 'error';
    } else {
        $resultado = $usuarioController->registrarUsuario($_POST);
        if ($resultado) {
            $mensaje = $resultado;
            $tipoMensaje = 'error';
        }
    }
}
?>
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Registrar Usuario</h2>

        <?php if ($mensaje): ?>
            <div class="mb-4 text-center text-sm text-red-600 font-semibold">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form action="registrarUsuario.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombres</label>
                <input type="text" name="nombres" placeholder="Nombres" required
                    value="<?php echo isset($_POST['nombres']) ? htmlspecialchars($_POST['nombres']) : ''; ?>"
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" placeholder="Apellidos" required
                    value="<?php echo isset($_POST['apellidos']) ? htmlspecialchars($_POST['apellidos']) : ''; ?>"
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Correo</label>
                <input type="email" name="correo" placeholder="Correo" required
                    value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" placeholder="Contraseña" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-xs text-gray-500">Mínimo 8 caracteres y al menos 1 número.</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="id_rol" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccione un rol</option>
                    <?php
                    try {
                        $conexion = new ConexionDB();
                        $conn = $conexion->conectar();
                        if ($conn) {
                            $sqlSelect = "SELECT id, nombre FROM roles";
                            $result = $conn->query($sqlSelect);
                            $selectedRol = isset($_POST['id_rol']) ? $_POST['id_rol'] : '';
                            foreach ($result as $row) {
                                $selected = ($selectedRol == $row['id']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>' . htmlspecialchars($row['nombre']) . '</option>';
                            }
                            $conexion->desconectar();
                        }
                    } catch (Exception $e) {
                        echo '<option value="" disabled>Error al cargar roles</option>';
                    }
                    ?>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition duration-200">
                Registrar
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <a href="../../Autenticación/Vista/login.php"
                class="text-blue-600 hover:text-blue-800 text-sm">
                ¿Ya tienes cuenta? Inicia sesión
            </a>
        </div>
    </div>
</div>