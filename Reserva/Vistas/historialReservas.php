<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}
require_once '../../nav.php';

$titulo_pagina = "Historial de Reservas";
include '../../layouts/header.php';

require_once '../Modelos/Reserva.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Manejar ver detalle
$evento_detalle = null;
if (isset($_GET['ver_detalle'])) {
    $reservaModel = new Reserva();
    try {
        $evento_detalle = $reservaModel->obtenerEventoPorId($_GET['ver_detalle']);
        if (!$evento_detalle) {
            $mensaje = "Evento no encontrado";
            $tipo_mensaje = 'error';
        }
    } catch (Exception $e) {
        $mensaje = "Error al cargar el evento: " . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Obtener historial con filtros
$reservaModel = new Reserva();
$filtros = [
    'id_usuario' => $_GET['id_usuario'] ?? '',
    'limite' => $_GET['limite'] ?? '50',
    'desde' => $_GET['desde'] ?? '',
    'hasta' => $_GET['hasta'] ?? '',
    'estado' => $_GET['estado'] ?? ''
];

try {
    $reservasPasadas = $reservaModel->obtenerHistorialReservas($filtros);
} catch (Exception $e) {
    $reservasPasadas = [];
    $mensaje = "Error al cargar el historial: " . $e->getMessage();
    $tipo_mensaje = 'error';
}
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-700 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-history mr-3"></i>
                    Historial de Reservas Pasadas
                </h1>
                <a href="verReservas.php" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i>Reservas Actuales
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-search mr-2"></i>Búsqueda en Historial
                </h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <select name="id_usuario" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos los usuarios</option>
                            <option value="1" <?php echo ($_GET['id_usuario'] ?? '') == '1' ? 'selected' : ''; ?>>Usuario Demo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mostrar</label>
                        <select name="limite" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="50" <?php echo ($_GET['limite'] ?? '50') == '50' ? 'selected' : ''; ?>>50 registros</option>
                            <option value="100" <?php echo ($_GET['limite'] ?? '') == '100' ? 'selected' : ''; ?>>100 registros</option>
                            <option value="200" <?php echo ($_GET['limite'] ?? '') == '200' ? 'selected' : ''; ?>>200 registros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input type="date" name="desde" value="<?php echo $_GET['desde'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                        <input type="date" name="hasta" value="<?php echo $_GET['hasta'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="confirmado" <?php echo ($_GET['estado'] ?? '') == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="cancelado" <?php echo ($_GET['estado'] ?? '') == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="md:col-span-5 flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <a href="historialReservas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabla de historial -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Título</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Fecha</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Hora</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Organizador</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Estado Final</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Hace</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($reservasPasadas)): ?>
                            <?php foreach ($reservasPasadas as $reserva): ?>
                                <?php 
                                $fechaEvento = new DateTime($reserva['fecha_evento']);
                                $hoy = new DateTime();
                                $diasPasados = $hoy->diff($fechaEvento)->days;
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900"><?php echo $reserva['id']; ?></td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reserva['titulo']); ?></div>
                                        <?php if (!empty($reserva['descripcion'])): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($reserva['descripcion'], 0, 50)) . (strlen($reserva['descripcion']) > 50 ? '...' : ''); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></td>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?> - 
                                        <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($reserva['organizador'] ?? 'Sin organizador'); ?></td>
                                    <td class="px-4 py-4">
                                        <?php
                                        $badgeClass = $reserva['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500"><?php echo $diasPasados; ?> días</td>
                                    <td class="px-4 py-4">
                                        <a href="?ver_detalle=<?php echo $reserva['id']; ?>" 
                                           class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors duration-200" title="Ver detalles">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay registros en el historial</h3>
                                        <p class="text-gray-500">No se encontraron reservas pasadas con los filtros aplicados</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($evento_detalle): ?>
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Detalles del Historial</h3>
            <a href="?" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Evento</h3>
                <div class="space-y-3">
                    <div><strong>Título:</strong> <?php echo htmlspecialchars($evento_detalle['titulo']); ?></div>
                    <div><strong>Descripción:</strong> <?php echo htmlspecialchars($evento_detalle['descripcion']) ?: 'Sin descripción'; ?></div>
                    <div><strong>Estado Final:</strong> 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $evento_detalle['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo ucfirst($evento_detalle['estado']); ?>
                        </span>
                    </div>
                    <div><strong>Organizador:</strong> <?php echo htmlspecialchars($evento_detalle['organizador']); ?></div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalles Temporales</h3>
                <div class="space-y-3">
                    <div><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evento_detalle['fecha_evento'])); ?></div>
                    <div><strong>Horario:</strong> <?php echo date('H:i', strtotime($evento_detalle['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento_detalle['hora_fin'])); ?></div>
                    <div><strong>Hace:</strong> 
                        <?php 
                        $fechaEvento = new DateTime($evento_detalle['fecha_evento']);
                        $hoy = new DateTime();
                        $diasPasados = $hoy->diff($fechaEvento)->days;
                        echo $diasPasados; 
                        ?> días
                    </div>
                    <div><strong>Recurso:</strong> <?php echo htmlspecialchars($evento_detalle['tipo_recurso']) ?: 'Sin recurso'; ?></div>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <div class="p-4 rounded-lg <?php echo $evento_detalle['estado'] === 'confirmado' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'; ?>">
                <div class="flex items-center">
                    <i class="fas fa-<?php echo $evento_detalle['estado'] === 'confirmado' ? 'check-circle text-green-600' : 'exclamation-triangle text-yellow-600'; ?> mr-2"></i>
                    <span class="text-gray-800">
                        <?php echo $evento_detalle['estado'] === 'confirmado' ? 'Este evento se completó exitosamente.' : 'Este evento fue cancelado.'; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <a href="?" class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Cerrar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../../layouts/footer.php'; ?>
