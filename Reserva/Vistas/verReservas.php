<?php
session_start();
$titulo_pagina = "Gestión de Reservas";
include '../../layouts/header.php';

// Incluir el modelo de reservas
require_once '../Modelos/Reserva.php';

// Procesar mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

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
    // Si no hay resultados con filtros, intentar obtener todas las reservas
    if (empty($reservas) && empty(array_filter($filtros))) {
        $reservas = $reservaModel->obtenerTodasReservas();
    }
    // Debug temporal - comentar en producción
    // echo "<!-- Debug: Se encontraron " . count($reservas) . " reservas -->";
} catch (Exception $e) {
    // Intentar método fallback
    try {
        $reservas = $reservaModel->obtenerTodasReservas();
    } catch (Exception $e2) {
        $reservas = [];
        $mensaje = "Error al cargar las reservas: " . $e->getMessage();
        $tipo_mensaje = 'error';
    }
    // Debug temporal - comentar en producción  
    // echo "<!-- Debug Error: " . $e->getMessage() . " -->";
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
                            <th class="px-4 py-3 text-left text-sm font-semibold">Recurso</th>
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
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <?php echo $reserva['tipo_recurso'] ? htmlspecialchars($reserva['tipo_recurso']) : 'Sin recurso'; ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-1">
                                            <button type="button" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors duration-200" 
                                                    onclick="verDetalle(<?php echo $reserva['id']; ?>)" title="Ver detalles">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            <?php if ($reserva['estado'] != 'cancelado'): ?>
                                                <a href="editarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-yellow-600 text-white p-2 rounded hover:bg-yellow-700 transition-colors duration-200" title="Editar">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                <a href="cambiarFechaReserva.php?id=<?php echo $reserva['id']; ?>" 
                                                   class="bg-gray-600 text-white p-2 rounded hover:bg-gray-700 transition-colors duration-200" title="Cambiar fecha">
                                                    <i class="fas fa-calendar-alt text-xs"></i>
                                                </a>
                                                <button type="button" class="bg-red-600 text-white p-2 rounded hover:bg-red-700 transition-colors duration-200" 
                                                        onclick="confirmarCancelacion(<?php echo $reserva['id']; ?>)" title="Cancelar">
                                                    <i class="fas fa-ban text-xs"></i>
                                                </button>
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

<!-- Modal para ver detalles -->
<div id="modalDetalle" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Detalles del Evento</h3>
            <button onclick="cerrarModalDetalle()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detalleContent" class="mb-4">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin fa-2x text-blue-500"></i>
                <p class="mt-2 text-gray-600">Cargando detalles...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar cancelación -->
<div id="modalCancelar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Confirmar Cancelación</h3>
            <button onclick="cerrarModalCancelar()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 mb-4">¿Está seguro que desea cancelar esta reserva?</p>
            <div class="mb-4">
                <label for="motivoCancelacion" class="block text-sm font-medium text-gray-700 mb-2">Motivo de cancelación (opcional)</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                          id="motivoCancelacion" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label for="penalidadPorcentaje" class="block text-sm font-medium text-gray-700 mb-2">Penalidad (%)</label>
                <input type="number" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       id="penalidadPorcentaje" min="0" max="100" value="0">
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="cerrarModalCancelar()" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Cancelar
            </button>
            <button id="btnConfirmarCancelacion" 
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-ban mr-2"></i> Cancelar Reserva
            </button>
        </div>
    </div>
</div>

<script>
let eventoIdACancelar = null;

// Funciones para manejar modales
function mostrarModalDetalle() {
    document.getElementById('modalDetalle').classList.remove('hidden');
}

function cerrarModalDetalle() {
    document.getElementById('modalDetalle').classList.add('hidden');
}

function mostrarModalCancelar() {
    document.getElementById('modalCancelar').classList.remove('hidden');
}

function cerrarModalCancelar() {
    document.getElementById('modalCancelar').classList.add('hidden');
}

function verDetalle(idEvento) {
    const content = document.getElementById('detalleContent');
    
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin fa-2x text-blue-500"></i>
            <p class="mt-2 text-gray-600">Cargando detalles...</p>
        </div>
    `;
    
    mostrarModalDetalle();

    fetch(`../Controlador/ReservaController.php?accion=obtener_evento&id=${idEvento}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const evento = data.evento;
                content.innerHTML = `
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-800">Información del Evento</h4>
                            <div class="space-y-2">
                                <p class="text-gray-700"><span class="font-medium">Título:</span> ${evento.titulo}</p>
                                <p class="text-gray-700"><span class="font-medium">Descripción:</span> ${evento.descripcion || 'Sin descripción'}</p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Estado:</span> 
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${getBadgeClass(evento.estado)}">${evento.estado}</span>
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-800">Detalles de la Reserva</h4>
                            <div class="space-y-2">
                                <p class="text-gray-700"><span class="font-medium">Fecha:</span> ${formatDate(evento.fecha_evento)}</p>
                                <p class="text-gray-700"><span class="font-medium">Hora:</span> ${formatTime(evento.hora_inicio)} - ${formatTime(evento.hora_fin)}</p>
                                <p class="text-gray-700"><span class="font-medium">Organizador:</span> ${evento.organizador}</p>
                                <p class="text-gray-700"><span class="font-medium">Recurso:</span> ${evento.tipo_recurso || 'Sin recurso asignado'}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i> ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Error al cargar los detalles
                </div>
            `;
            console.error('Error:', error);
        });
}

function confirmarCancelacion(idEvento) {
    eventoIdACancelar = idEvento;
    mostrarModalCancelar();
}

document.getElementById('btnConfirmarCancelacion').addEventListener('click', function() {
    if (!eventoIdACancelar) return;

    const motivo = document.getElementById('motivoCancelacion').value;
    const penalidad = document.getElementById('penalidadPorcentaje').value;

    const formData = new FormData();
    formData.append('id_evento', eventoIdACancelar);
    formData.append('motivo_cancelacion', motivo);
    formData.append('penalidad', penalidad);
    formData.append('ajax', '1');

    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Cancelando...';
    this.disabled = true;

    fetch('../Controlador/ReservaController.php?accion=cancelar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
            this.innerHTML = '<i class="fas fa-ban mr-2"></i> Cancelar Reserva';
            this.disabled = false;
        }
    })
    .catch(error => {
        alert('Error al procesar la cancelación');
        this.innerHTML = '<i class="fas fa-ban mr-2"></i> Cancelar Reserva';
        this.disabled = false;
        console.error('Error:', error);
    });
});

function getBadgeClass(estado) {
    switch (estado) {
        case 'pendiente': return 'bg-yellow-100 text-yellow-800';
        case 'confirmado': return 'bg-green-100 text-green-800';
        case 'cancelado': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

function formatTime(timeString) {
    return timeString.substring(0, 5);
}

// Cerrar modales al hacer clic fuera de ellos
document.getElementById('modalDetalle').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalDetalle();
    }
});

document.getElementById('modalCancelar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalCancelar();
    }
});
</script>

<?php include '../../layouts/footer.php'; ?>