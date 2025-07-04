<?php
session_start();
// 1. INCLUIR EL CONTROLADOR
// Se incluye el controlador que se encargará de toda la lógica.
require_once '../Controlador/ProveedorController.php';

// 2. VERIFICACIÓN DE SESIÓN
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

// 3. OBTENCIÓN DE DATOS
// Se crea una instancia del controlador.
$proveedorController = new ProveedorController();
$proveedor = null;

// Se obtiene el ID del proveedor desde la URL.
$id_proveedor = $_GET['id'] ?? 0;

if ($id_proveedor > 0) {
    // Se utiliza el controlador para buscar los datos del proveedor.
    // Este método nos devolverá un array con toda la info, incluyendo el id_usuario.
    $proveedor = $proveedorController->buscarPorId($id_proveedor);
}

// 4. INCLUIR EL HEADER
$titulo_pagina = "Confirmar Eliminación";
include '../../layouts/header.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                Confirmar Eliminación
            </h1>
        </div>
        <div class="p-6">
            <?php if ($proveedor): // El contenido solo se muestra si se encontró el proveedor ?>
                <p class="text-lg text-gray-800 mb-4">
                    ¿Estás seguro de que deseas eliminar al proveedor <strong class="font-semibold"><?php echo htmlspecialchars($proveedor['empresa']); ?></strong> (Contacto: <?php echo htmlspecialchars($proveedor['nombre']); ?>)?
                </p>
                <p class="text-gray-600">
                    Esta acción no se puede deshacer. Se eliminarán permanentemente el perfil del proveedor y su cuenta de usuario asociada.
                </p>
                
                <!-- 5. FORMULARIO CORREGIDO -->
                <!-- El formulario ahora apunta al controlador con la acción 'eliminar' -->
                <form action="../../controllers/ProveedorController.php?action=eliminar" method="post" class="mt-6">
                    
                    <!-- Se envían AMBOS IDs necesarios para una eliminación completa y limpia -->
                    <input type="hidden" name="id_proveedor" value="<?php echo htmlspecialchars($proveedor['id']); ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($proveedor['id_usuario']); ?>">
                    
                    <div class="flex justify-end space-x-4">
                        <a href="verProveedores.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Sí, Eliminar Permanentemente
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Mensaje de error si no se encontró el proveedor -->
                <div class="text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <p>Error: Proveedor no encontrado o ID no válido.</p>
                    <a href="verProveedores.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Volver a la lista</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>