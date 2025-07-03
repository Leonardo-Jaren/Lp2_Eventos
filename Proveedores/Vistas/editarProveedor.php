<?php
session_start();
$titulo_pagina = "Editar Proveedor";
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';

// Incluir el modelo de Usuario para obtener la lista de usuarios
require_once __DIR__ . '/../../Usuarios/Modelos/Usuario.php';
$usuarios = Usuario::obtenerTodos(); // Asume que tienes un método así

// Obtener ID y buscar el proveedor para pre-llenar el formulario
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Error: ID no proporcionado.");
}
$proveedor = new Proveedor();
$encontrado = $proveedor->encontrar($id);
if (!$encontrado) {
    die("Proveedor no encontrado.");
}
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-yellow-500 text-white px-6 py-4">
             <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-edit mr-3"></i>
                Editar Proveedor
            </h1>
        </div>
        <div class="p-6">
            <form action="../Controlador/ProveedorController.php?action=actualizar" method="post">
                <input type="hidden" name="id_proveedor" value="<?php echo $proveedor->getIdProveedor(); ?>">
                <div class="space-y-4">
                    <div>
                        <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-1">Usuario Asociado</label>
                        <select name="id_usuario" id="id_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">Seleccione un usuario</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id_usuario']; ?>" <?php echo ($proveedor->getIdUsuario() == $usuario['id_usuario']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($usuario['nombre']) . ' (' . htmlspecialchars($usuario['correo']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="nombre_empresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa" id="nombre_empresa" value="<?php echo htmlspecialchars($proveedor->getNombreEmpresa()); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($proveedor->getDescripcion()); ?></textarea>
                    </div>
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($proveedor->getDireccion()); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="verProveedores.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancelar</a>
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-save mr-2"></i>Actualizar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>