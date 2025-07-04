<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Gestión de Reservas";
require_once '../../nav.php';

// Incluir el modelo de reservas
require_once '../Modelos/Reserva.php';

// Procesar mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Manejar ver detalle de evento
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
    'organizador' => $_GET['organizador'] ?? '',
    'titulo' => $_GET['titulo'] ?? ''
];

try {
    $reservas = $reservaModel->obtenerReservas($filtros);
    if (empty($reservas) && empty(array_filter($filtros))) {
        $reservas = $reservaModel->obtenerTodasReservas();
    }
} catch (Exception $e) {
    try {
        $reservas = $reservaModel->obtenerTodasReservas();
    } catch (Exception $e2) {
        $reservas = [];
        $mensaje = "Error al cargar las reservas: " . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        Gestión de Reservas y Eventos
                    </h1>
                </div>
                <div>
                    <a href="crearReserva.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Reserva
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Mensajes de alerta -->
            <?php if ($mensaje): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
                        <span><?php echo htmlspecialchars($mensaje); ?></span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-current hover:opacity-70">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filtros de búsqueda -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-filter mr-2"></i>
                        Filtros de Búsqueda
                    </h2>
                </div>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="fecha_inicio" name="fecha_inicio" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">
                    </div>
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="fecha_fin" name="fecha_fin" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="pendiente" <?php echo ($_GET['estado'] ?? '') == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="confirmado" <?php echo ($_GET['estado'] ?? '') == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="cancelado" <?php echo ($_GET['estado'] ?? '') == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <label for="organizador" class="block text-sm font-medium text-gray-700 mb-2">Organizador</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="organizador" name="organizador" placeholder="Nombre organizador" value="<?php echo $_GET['organizador'] ?? ''; ?>">
                    </div>
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="titulo" name="titulo" placeholder="Título evento" value="<?php echo $_GET['titulo'] ?? ''; ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Buscar
                        </button>
                    </div>
                    <div class="md:col-span-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="verReservas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Limpiar
                            </a>
                            <a href="historialReservas.php" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 transition-colors duration-200">
                                <i class="fas fa-history mr-2"></i>
                                Ver Historial
                            </a>
                            <a href="../Controlador/ReservaController.php?accion=calendario" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <i class="fas fa-calendar mr-2"></i>
                                Vista Calendario
                            </a>
                        </div>
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
                        <?php if (isset($reservas) && !empty($reservas)): ?>
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
                                    <td class="px-4 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($reserva['organizador']); ?></td>
                                    <td class="px-4 py-4">
                                        <?php
                                        $badgeClass = '';
                                        switch ($reserva['estado']) {
                                            case 'pendiente':
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'confirmado':
                                                $badgeClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelado':
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <!-- <td class="px-4 py-4 text-sm text-gray-900">
                                        <?php echo $reserva['tipo_recurso'] ? htmlspecialchars($reserva['tipo_recurso']) : 'Sin recurso'; ?>
                                    </td> -->
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
                                                <a href="cambiarFechaReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-gray-600 text-white p-2 rounded hover:bg-gray-700 transition-colors duration-200" title="Cambiar fecha">
                                                    <i class="fas fa-calendar-alt text-xs"></i>
                                                </a>
                                                <a href="cancelarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-red-600 text-white p-2 rounded hover:bg-red-700 transition-colors duration-200" title="Cancelar"
                                                   onclick="return confirm('¿Está seguro que desea cancelar esta reserva?')">
                                                    <i class="fas fa-ban text-xs"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron reservas</h3>
                                        <p class="text-gray-500 mb-4">No hay reservas con los filtros aplicados</p>
                                        <a href="crearReserva.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Crear Primera Reserva
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

<!-- Modal para ver detalles (PHP) -->
<?php if ($evento_detalle): ?>
<div id="modalDetalle" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Detalles del Evento</h3>
            <a href="?" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        <div class="mb-4">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800">Información del Evento</h4>
                    <div class="space-y-2">
                        <p class="text-gray-700"><span class="font-medium">Título:</span> <?php echo htmlspecialchars($evento_detalle['titulo']); ?></p>
                        <p class="text-gray-700"><span class="font-medium">Descripción:</span> <?php echo htmlspecialchars($evento_detalle['descripcion'] ?? '') ?: 'Sin descripción'; ?></p>
                        <p class="text-gray-700">
                            <span class="font-medium">Estado:</span> 
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?php 
                                echo $evento_detalle['estado'] == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($evento_detalle['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); 
                            ?>"><?php echo ucfirst($evento_detalle['estado']); ?></span>
                        </p>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800">Detalles de la Reserva</h4>
                    <div class="space-y-2">
                        <p class="text-gray-700"><span class="font-medium">Fecha:</span> <?php echo date('d/m/Y', strtotime($evento_detalle['fecha_evento'])); ?></p>
                        <p class="text-gray-700"><span class="font-medium">Hora:</span> <?php echo date('H:i', strtotime($evento_detalle['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento_detalle['hora_fin'])); ?></p>
                        <p class="text-gray-700"><span class="font-medium">Organizador:</span> <?php echo htmlspecialchars($evento_detalle['organizador']); ?></p>
                        <!-- <p class="text-gray-700"><span class="font-medium">Recurso:</span> <?php echo htmlspecialchars($evento_detalle['tipo_recurso'] ?? '') ?: 'Sin recurso asignado'; ?></p> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <a href="?" class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Cerrar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../../layouts/footer.php'; ?>