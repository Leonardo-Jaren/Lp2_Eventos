<?php

require_once '../../../conexion_db.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

if (!empty($_POST)) {
    require_once '../../Controlador/PermisosController.php';
    $permisosController = new PermisosController();
    $resultado = $permisosController->crearPermiso($_POST);
    
    if ($resultado) {
        echo '<div class="mt-4 text-center text-sm text-red-600 font-semibold">' .
             htmlspecialchars($mensaje) .
             '</div>';
    } else {
        header("Location: verPermisos.php");
        exit();
    }
}

require_once '../../../layouts/header.php';

?>

<div class="flex-1 bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="container mx-auto px-4 max-w-2xl">

        <!-- Header de la página -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-plus-circle text-blue-600 mr-3"></i>Crear Nuevo Permiso
            </h1>
            <p class="text-gray-600">Añade un nuevo permiso al sistema de gestión de eventos</p>
        </div>

        <!-- Formulario principal -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
            <form action="crearPermisos.php" method="POST" class="space-y-6">
                <input type="hidden" name="accion" value="crear">

                <!-- Campo Nombre del Permiso -->
                <div class="space-y-2">
                    <label for="nombre_permiso" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-key text-blue-500 mr-2"></i>Nombre del Permiso
                    </label>
                    <input
                        type="text"
                        id="nombre_permiso"
                        name="nombre_permiso"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 ease-in-out hover:border-gray-400"
                        placeholder="Ej: gestionar_eventos, ver_reportes, etc.">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Utiliza un nombre descriptivo y único para el permiso
                    </p>
                </div>

                <!-- Campo Descripción -->
                <div class="space-y-2">
                    <label for="descripcion" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-align-left text-blue-500 mr-2"></i>Descripción
                    </label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        required
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 ease-in-out hover:border-gray-400 resize-none"
                        placeholder="Describe las funcionalidades que incluye este permiso..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Proporciona una descripción clara de las acciones que permite este permiso
                    </p>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>Crear Permiso
                    </button>
                </div>
            </form>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-lightbulb text-blue-600 mt-0.5 mr-3"></i>
                <div>
                    <h3 class="text-sm font-semibold text-blue-800 mb-1">Consejos para crear permisos</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Usa nombres descriptivos y únicos para evitar confusiones</li>
                        <li>• Sigue una convención de nomenclatura consistente (ej: verbo_recurso)</li>
                        <li>• Proporciona descripciones claras para facilitar la gestión</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../../layouts/footer.php'; ?>