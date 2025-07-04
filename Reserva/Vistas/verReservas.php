<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

require_once '../../nav.php';
$titulo_pagina = "Gestión de Reservas";
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

// Obtener reservas con filtros
$reservaModel = new Reserva();
$filtros = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'titulo' => $_GET['titulo'] ?? ''
];

try {
    $reservas = $reservaModel->obtenerReservas($filtros);
    if (empty($reservas) && empty(array_filter($filtros))) {
        $reservas = $reservaModel->obtenerTodasReservas();
    }
} catch (Exception $e) {
    $reservas = [];
    $mensaje = "Error al cargar las reservas: " . $e->getMessage();
    $tipo_mensaje = 'error';
}
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Gestión de Reservas
                </h1>
                <div class="space-x-2">
                    <a href="crearReserva.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Nueva Reserva
                    </a>
                    <a href="historialReservas.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-history mr-2"></i>Historial
                    </a>
                </div>
            </div>
        </div>
        <div class="px-6 pt-6">
            <a href="/Lp2_Eventos/dashboard.php" class="text-blue-600 hover:text-blue-800 mb-6 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Atras
            </a>
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
                    <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                </h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                        <input type="date" name="fecha_fin" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="pendiente" <?php echo ($_GET['estado'] ?? '') == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="confirmado" <?php echo ($_GET['estado'] ?? '') == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="cancelado" <?php echo ($_GET['estado'] ?? '') == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" name="titulo" value="<?php echo $_GET['titulo'] ?? ''; ?>" placeholder="Buscar por título..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <a href="verReservas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabla de reservas -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Título</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Fecha</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Hora</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Organizador</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($reservas)): ?>
                            <?php foreach ($reservas as $reserva): ?>
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
                                        $badgeClass = match($reserva['estado']) {
                                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'confirmado' => 'bg-green-100 text-green-800',
                                            'cancelado' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-1">
                                            <a href="?ver_detalle=<?php echo $reserva['id']; ?>" 
                                               class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors duration-200" title="Ver detalles">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <?php if ($reserva['estado'] != 'cancelado'): ?>
                                                <a href="editarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-yellow-600 text-white p-2 rounded hover:bg-yellow-700 transition-colors duration-200" title="Editar">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                <a href="cancelarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-red-600 text-white p-2 rounded hover:bg-red-700 transition-colors duration-200" title="Cancelar">
                                                    <i class="fas fa-times text-xs"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay reservas</h3>
                                        <p class="text-gray-500 mb-4">No se encontraron reservas con los filtros aplicados</p>
                                        <a href="crearReserva.php" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>Crear Primera Reserva
                                        </a>
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
            <h3 class="text-lg font-bold text-gray-900">Detalles del Evento</h3>
            <a href="?" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Información General</h4>
                <div class="space-y-2">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($evento_detalle['titulo']); ?></p>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($evento_detalle['descripcion']) ?: 'Sin descripción'; ?></p>
                    <p><strong>Estado:</strong> <span class="px-2 py-1 rounded text-xs <?php echo match($evento_detalle['estado']) {
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'confirmado' => 'bg-green-100 text-green-800',
                        'cancelado' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    }; ?>"><?php echo ucfirst($evento_detalle['estado']); ?></span></p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Detalles Temporales</h4>
                <div class="space-y-2">
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evento_detalle['fecha_evento'])); ?></p>
                    <p><strong>Hora Inicio:</strong> <?php echo date('H:i', strtotime($evento_detalle['hora_inicio'])); ?></p>
                    <p><strong>Hora Fin:</strong> <?php echo date('H:i', strtotime($evento_detalle['hora_fin'])); ?></p>
                    <p><strong>Organizador:</strong> <?php echo htmlspecialchars($evento_detalle['organizador']); ?></p>
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
