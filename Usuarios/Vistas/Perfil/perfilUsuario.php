<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: ../../../Autenticación/Vista/login.php");
    exit();
}

require_once __DIR__ . '/../../Controlador/UsuarioController.php';

$titulo_pagina = 'Mi Perfil';
$controlador = new UsuarioController();
$usuario = $controlador->obtenerPerfilUsuario($_SESSION['id']);

$mensaje = null;
$tipo_mensaje = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id' => $_SESSION['id'],
        'nombres' => trim($_POST['nombres']),
        'apellidos' => trim($_POST['apellidos']),
        'correo' => trim($_POST['correo']),
        'password' => !empty($_POST['password']) ? $_POST['password'] : null
    ];

    if (empty($datos['nombres']) || empty($datos['apellidos']) || empty($datos['correo'])) {
        $mensaje = "Todos los campos son obligatorios excepto la contraseña.";
        $tipo_mensaje = "error";
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El formato del correo electrónico no es válido.";
        $tipo_mensaje = "error";
    } elseif (!empty($datos['password']) && strlen($datos['password']) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = "error";
    } elseif (!empty($_POST['confirm_password']) && $_POST['password'] !== $_POST['confirm_password']) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipo_mensaje = "error";
    } else {
        $resultado = $controlador->actualizarPerfil($datos);
        if ($resultado['success']) {
            $mensaje = $resultado['message'];
            $tipo_mensaje = "success";
            // Recargar datos del usuario
            $usuario = $controlador->obtenerPerfilUsuario($_SESSION['id']);
        } else {
            $mensaje = $resultado['message'];
            $tipo_mensaje = "error";
        }
    }
}

require_once '../../../nav.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header de la página -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-user text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mi Perfil</h1>
                <p class="text-gray-600">Gestiona tu información personal y actualiza tus datos</p>
            </div>
        </div>
    </div>

    <!-- Mensajes de alerta -->
    <?php if ($mensaje): ?>
        <div class="mb-6">
            <?php if ($tipo_mensaje === 'success'): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php else: ?>
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información actual del usuario -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Información Actual
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-center mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold">
                            <?php echo strtoupper(substr($usuario['nombres'], 0, 1) . substr($usuario['apellidos'], 0, 1)); ?>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Correo Electrónico</label>
                        <p class="text-gray-900"><?php echo htmlspecialchars($usuario['correo']); ?></p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Rol del Sistema</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-user-tag mr-1"></i>
                            <?php echo htmlspecialchars($usuario['rol'] ?? 'Usuario'); ?>
                        </span>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">ID de Usuario</label>
                        <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($usuario['id']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de actualización -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                    Actualizar Información
                </h2>

                <form method="POST" class="space-y-6" x-data="{ showPassword: false, showConfirmPassword: false }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombres -->
                        <div>
                            <label for="nombres" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>
                                Nombres *
                            </label>
                            <input
                                type="text"
                                id="nombres"
                                name="nombres"
                                value="<?php echo htmlspecialchars($usuario['nombres']); ?>"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Ingresa tus nombres">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>
                                Apellidos *
                            </label>
                            <input
                                type="text"
                                id="apellidos"
                                name="apellidos"
                                value="<?php echo htmlspecialchars($usuario['apellidos']); ?>"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Ingresa tus apellidos">
                        </div>
                    </div>

                    <!-- Correo electrónico -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>
                            Correo Electrónico *
                        </label>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            value="<?php echo htmlspecialchars($usuario['correo']); ?>"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            placeholder="ejemplo@correo.com">
                    </div>

                    <!-- Separador -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-md font-medium text-gray-900 mb-4">
                            <i class="fas fa-lock mr-2 text-gray-600"></i>
                            Cambiar Contraseña (Opcional)
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Deja estos campos vacíos si no deseas cambiar tu contraseña actual.</p>
                    </div>

                    <!-- Nueva contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-1"></i>
                            Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                name="password"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Mínimo 6 caracteres">
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar contraseña -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-1"></i>
                            Confirmar Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                id="confirm_password"
                                name="confirm_password"
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Confirma tu nueva contraseña">
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Perfil
                        </button>

                        <a
                            href="../../../dashboard.php"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../../layouts/footer.php'; ?>