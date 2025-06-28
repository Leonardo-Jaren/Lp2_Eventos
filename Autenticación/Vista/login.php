<?php
require_once '../../layouts/header.php';
session_start();
if (isset($_SESSION['id'])) {
    header("Location: ../../dashboard.php");
    exit();
}
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Iniciar Sesión</h2>

        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input type="email" name="correo" placeholder="Correo electrónico" required
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

<?php
require_once '../Controlador/authController.php';
if (!empty($_POST)) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $errores = 0;
    if ($correo == "" || $password == "") {
        echo '<div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md text-center text-sm">Por favor, complete todos los campos.</div>';
        $errores++;
    }
    if ($errores == 0) {
        $authController = new AuthController();
        $resultado = $authController->iniciarSesion($correo, $password);
        if (strpos($resultado, 'Bienvenido') !== false) {
            echo '<div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md text-center text-sm">' . htmlspecialchars($resultado) . '</div>';
        } else {
            echo '<div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md text-center text-sm">' . htmlspecialchars($resultado) . '</div>';
        }
    }
}
