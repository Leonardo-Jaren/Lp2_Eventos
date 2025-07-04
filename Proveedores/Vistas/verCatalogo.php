<?php
include '../../layouts/header.php';
require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';
require_once '../../nav.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$servicioModel = new CatalogoServicios();
$id_proveedor = $_GET['id'] ?? 0;

$servicios = [];
if ($id_proveedor) {
    $servicios = $servicioModel->obtenerServiciosPorProveedor($id_proveedor);
} else {
    $servicios = $servicioModel->obtenerTodosLosServicios();
}

$proveedor = new Proveedor();
$proveedor->encontrarProveedor($id_proveedor);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-indigo-600 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl font-bold flex items-center">
            <i class="fas fa-book-open mr-3"></i>
            Catálogo de: <?php echo htmlspecialchars($proveedor->getNombreEmpresa($id_proveedor)); ?>
            </h1>
            <a href="crearCatalogo.php?id=<?php echo urlencode($id_proveedor); ?>" class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold py-2 px-4 rounded shadow inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Agregar Servicio
            </a>
        </div>
        <div class="p-6">
            <a href="verProveedor.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block"><i class="fas fa-arrow-left mr-2"></i>Volver a la lista</a>
            <div class="overflow-x-auto">
                <table class="w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Nombre del servicio</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Precio Base</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($servicios)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-500">
                                    <p>Este proveedor aún no ha registrado servicios.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 font-mono text-gray-900"><?php echo htmlspecialchars($servicio['id']); ?></td>
                                    <td class="px-4 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></td>
                                    <td class="px-4 py-4 font-mono text-gray-900">S/ <?php echo number_format($servicio['precio'], 2); ?></td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-2">
                                            <a href="editarCatalogo.php?id=<?php echo urlencode($servicio['id']); ?>&id_proveedor=<?php echo urlencode($id_proveedor); ?>" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="eliminarCatalago.php?id=<?php echo urlencode($servicio['id']); ?>&id_proveedor=<?php echo urlencode($id_proveedor); ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('¿Estás seguro de eliminar este servicio?');">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>