<?php
session_start();
$titulo_pagina = "Historial de Reservas";
include '../../layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-700 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Historial de Reservas Pasadas
                    </h1>
                </div>
                <div>
                    <a href="verReservas.php" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Reservas Actuales
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Filtros específicos para historial -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Búsqueda en Historial
                    </h2>
                </div>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Usuario/Organizador</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="id_usuario" name="id_usuario">
                            <option value="">Todos los usuarios</option>
                            <!-- Aquí deberías cargar los usuarios desde la base de datos -->
                            <option value="1" <?php echo ($_GET['id_usuario'] ?? '') == '1' ? 'selected' : ''; ?>>Usuario Demo</option>
                        </select>
                    </div>
                    <div>
                        <label for="limite" class="block text-sm font-medium text-gray-700 mb-2">Mostrar</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="limite" name="limite">
                            <option value="50" <?php echo ($_GET['limite'] ?? '50') == '50' ? 'selected' : ''; ?>>50 registros</option>
                            <option value="100" <?php echo ($_GET['limite'] ?? '') == '100' ? 'selected' : ''; ?>>100 registros</option>
                            <option value="200" <?php echo ($_GET['limite'] ?? '') == '200' ? 'selected' : ''; ?>>200 registros</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="fecha_desde" name="fecha_desde" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>">
                    </div>
                    <div>
                        <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="fecha_hasta" name="fecha_hasta" value="<?php echo $_GET['fecha_hasta'] ?? date('Y-m-d'); ?>">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Buscar
                        </button>
                    </div>
                    <div class="md:col-span-5">
                        <div class="flex flex-wrap gap-2">
                            <a href="historialReservas.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Limpiar Filtros
                            </a>
                            <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200" onclick="exportarHistorial()">
                                <i class="fas fa-download mr-2"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Estadísticas rápidas -->
            <?php if (isset($historial) && !empty($historial)): ?>
                <?php
                $total_eventos = count($historial);
                $eventos_completados = 0;
                $eventos_cancelados = 0;
                $organizadores_unicos = [];

                foreach ($historial as $evento) {
                    if ($evento['estado'] == 'confirmado') $eventos_completados++;
                    if ($evento['estado'] == 'cancelado') $eventos_cancelados++;
                    $organizadores_unicos[$evento['organizador']] = true;
                }
                ?>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-blue-600 text-white rounded-lg p-6 text-center transform hover:scale-105 transition-transform duration-200">
                        <h3 class="text-3xl font-bold mb-2"><?php echo $total_eventos; ?></h3>
                        <p class="text-blue-100">Total de Eventos</p>
                    </div>
                    <div class="bg-green-600 text-white rounded-lg p-6 text-center transform hover:scale-105 transition-transform duration-200">
                        <h3 class="text-3xl font-bold mb-2"><?php echo $eventos_completados; ?></h3>
                        <p class="text-green-100">Completados</p>
                    </div>
                    <div class="bg-red-600 text-white rounded-lg p-6 text-center transform hover:scale-105 transition-transform duration-200">
                        <h3 class="text-3xl font-bold mb-2"><?php echo $eventos_cancelados; ?></h3>
                        <p class="text-red-100">Cancelados</p>
                    </div>
                    <div class="bg-cyan-600 text-white rounded-lg p-6 text-center transform hover:scale-105 transition-transform duration-200">
                        <h3 class="text-3xl font-bold mb-2"><?php echo count($organizadores_unicos); ?></h3>
                        <p class="text-cyan-100">Organizadores</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tabla de historial -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Título</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Fecha</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Duración</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Organizador</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Estado Final</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Recurso</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (isset($historial) && !empty($historial)): ?>
                            <?php foreach ($historial as $evento): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900"><?php echo $evento['id']; ?></td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($evento['titulo']); ?></div>
                                        <?php if (!empty($evento['descripcion'])): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($evento['descripcion'], 0, 40)) . (strlen($evento['descripcion']) > 40 ? '...' : ''); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <?php 
                                        $fecha_evento = new DateTime($evento['fecha_evento']);
                                        $hoy = new DateTime();
                                        $dias_pasados = $hoy->diff($fecha_evento)->days;
                                        ?>
                                        <div class="text-sm font-medium text-gray-900"><?php echo $fecha_evento->format('d/m/Y'); ?></div>
                                        <div class="text-sm text-gray-500">
                                            Hace <?php echo $dias_pasados; ?> día<?php echo $dias_pasados != 1 ? 's' : ''; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <?php 
                                        echo date('H:i', strtotime($evento['hora_inicio'])) . ' - ' . date('H:i', strtotime($evento['hora_fin']));
                                        
                                        // Calcular duración
                                        $inicio = new DateTime($evento['hora_inicio']);
                                        $fin = new DateTime($evento['hora_fin']);
                                        $duracion = $inicio->diff($fin);
                                        ?>
                                        <div class="text-sm text-gray-500">
                                            (<?php echo $duracion->h; ?>h <?php echo $duracion->i; ?>min)
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($evento['organizador']); ?></td>
                                    <td class="px-4 py-4">
                                        <?php
                                        $badgeClass = '';
                                        $icono = '';
                                        switch ($evento['estado']) {
                                            case 'confirmado':
                                                $badgeClass = 'bg-green-100 text-green-800';
                                                $icono = 'fas fa-check-circle';
                                                break;
                                            case 'cancelado':
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                $icono = 'fas fa-ban';
                                                break;
                                            default:
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                                $icono = 'fas fa-clock';
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                            <i class="<?php echo $icono; ?> mr-1"></i>
                                            <?php echo ucfirst($evento['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <?php if ($evento['tipo_recurso']): ?>
                                            <div class="flex items-center text-sm text-gray-900">
                                                <i class="fas fa-cube text-blue-600 mr-2"></i>
                                                <?php echo htmlspecialchars($evento['tipo_recurso']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500">Sin recurso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-2">
                                            <button type="button" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors duration-200" 
                                                    onclick="verDetalleHistorial(<?php echo $evento['id']; ?>)" title="Ver detalles">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            <?php if ($evento['estado'] == 'confirmado'): ?>
                                                <button type="button" class="bg-green-600 text-white p-2 rounded hover:bg-green-700 transition-colors duration-200" 
                                                        onclick="calificarEvento(<?php echo $evento['id']; ?>)" title="Calificar">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="bg-gray-600 text-white p-2 rounded hover:bg-gray-700 transition-colors duration-200" 
                                                    onclick="generarReporte(<?php echo $evento['id']; ?>)" title="Generar reporte">
                                                <i class="fas fa-file-pdf text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay historial disponible</h3>
                                        <p class="text-gray-500 mb-4">No se encontraron eventos pasados con los filtros aplicados</p>
                                        <a href="verReservas.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Ver Reservas Actuales
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación sencilla -->
            <?php if (isset($historial) && count($historial) >= ($_GET['limite'] ?? 50)): ?>
                <div class="flex justify-center mt-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Mostrando los últimos <?php echo count($historial); ?> eventos. 
                        <a href="?limite=<?php echo ($_GET['limite'] ?? 50) * 2; ?>" class="bg-blue-600 text-white px-4 py-2 rounded ml-3 hover:bg-blue-700 transition-colors duration-200">
                            Cargar más
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del historial -->
<div id="modalDetalleHistorial" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50" x-data="{ open: false }" x-show="open">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Detalles del Evento Pasado</h2>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detalleHistorialContent" class="p-6">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
                <p class="mt-2 text-gray-600">Cargando detalles...</p>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalleHistorial(idEvento) {
    const modal = document.getElementById('modalDetalleHistorial');
    const content = document.getElementById('detalleHistorialContent');
    
    content.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
            <p class="mt-2 text-gray-600">Cargando detalles del historial...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch(`../Controlador/ReservaController.php?accion=obtener_evento&id=${idEvento}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const evento = data.evento;
                const fechaEvento = new Date(evento.fecha_evento);
                const hoy = new Date();
                const diasPasados = Math.floor((hoy - fechaEvento) / (1000 * 60 * 60 * 24));
                
                content.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Información del Evento
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Título:</span>
                                    <span class="text-gray-900">${evento.titulo}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Descripción:</span>
                                    <span class="text-gray-900">${evento.descripcion || 'Sin descripción'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Estado Final:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getBadgeClass(evento.estado)}">
                                        ${evento.estado}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Organizador:</span>
                                    <span class="text-gray-900">${evento.organizador}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calendar text-blue-600 mr-2"></i>
                                Detalles Temporales
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Fecha:</span>
                                    <span class="text-gray-900">${formatDate(evento.fecha_evento)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Horario:</span>
                                    <span class="text-gray-900">${formatTime(evento.hora_inicio)} - ${formatTime(evento.hora_fin)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Hace:</span>
                                    <span class="text-gray-900">${diasPasados} días</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Recurso:</span>
                                    <span class="text-gray-900">${evento.tipo_recurso || 'Sin recurso'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            Resumen
                        </h3>
                        <div class="p-4 rounded-lg ${evento.estado === 'confirmado' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'}">
                            <div class="flex items-center">
                                <i class="fas fa-${evento.estado === 'confirmado' ? 'check-circle text-green-600' : 'exclamation-triangle text-yellow-600'} mr-2"></i>
                                <span class="text-gray-800">
                                    ${evento.estado === 'confirmado' 
                                        ? 'Este evento se completó exitosamente.' 
                                        : 'Este evento fue cancelado.'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="text-red-800">${data.message}</span>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800">Error al cargar los detalles</span>
                    </div>
                </div>
            `;
            console.error('Error:', error);
        });
}

function cerrarModal() {
    const modal = document.getElementById('modalDetalleHistorial');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function calificarEvento(idEvento) {
    window.location.href = `../../Calificaciones/Vista/calificarServicio.php?evento_id=${idEvento}`;
}

function generarReporte(idEvento) {
    window.location.href = `../../Reportes/Vista/generarReporte.php?evento_id=${idEvento}`;
}

function exportarHistorial() {
    const params = new URLSearchParams(window.location.search);
    params.set('exportar', '1');
    
    const link = document.createElement('a');
    link.href = `../Controlador/ReservaController.php?accion=exportar_historial&${params.toString()}`;
    link.download = `historial_reservas_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

function getBadgeClass(estado) {
    switch (estado) {
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

// Cerrar modal al hacer clic fuera
document.getElementById('modalDetalleHistorial').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>

<?php include '../../layouts/footer.php'; ?>