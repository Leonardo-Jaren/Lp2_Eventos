<?php
require_once '../../layouts/header.php';
require_once '../Controlador/authController.php';

session_start();
if (isset($_SESSION['id'])) {
    header("Location: ../../dashboard.php");
    exit();
}

// Procesar el formulario si se envió
$mensaje = '';
$tipoMensaje = '';

if (!empty($_POST)) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $errores = 0;

    if ($correo == "" || $password == "") {
        $mensaje = '<i class="fas fa-exclamation-triangle mr-2"></i>Por favor, complete todos los campos obligatorios.';
        $tipoMensaje = 'error';
        $errores++;
    }

    if ($errores == 0) {
        $authController = new AuthController();
        $resultado = $authController->iniciarSesion($correo, $password);

        if (strpos($resultado, 'Bienvenido') !== false) {
            // Mensaje de éxito
            $mensaje = htmlspecialchars($resultado);
            $tipoMensaje = 'success';
        } elseif (strpos($resultado, 'Usuario no encontrado') !== false) {
            // Correo no existe
            $mensaje = '<i class="fas fa-exclamation-circle mr-2"></i>El correo electrónico ingresado no está registrado.';
            $tipoMensaje = 'error';
        } elseif (strpos($resultado, 'Contraseña incorrecta') !== false) {
            // Contraseña incorrecta
            $mensaje = '<i class="fas fa-lock mr-2"></i>La contraseña ingresada es incorrecta.';
            $tipoMensaje = 'error';
        } else {
            // Otros errores
            $mensaje = htmlspecialchars($resultado);
            $tipoMensaje = 'error';
        }
    }
}
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Iniciar Sesión</h2>

        <?php if ($mensaje != ''): ?>
            <div class="mb-4 p-3 rounded-md text-center text-sm <?php echo $tipoMensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="correo" placeholder="Correo electrónico" required
                    value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" placeholder="Contraseña" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition duration-200">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="../../Usuarios/Vistas/registrarUsuario.php"
                class="text-blue-600 hover:text-blue-800 text-sm">
                ¿No tienes cuenta? Regístrate
            </a>
        </div>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>