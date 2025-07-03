<?php
session_start();
$titulo_pagina = "Editar Reserva";
include '../../layouts/header.php';

if (!isset($evento) || !$evento) {
    $id_evento = $_GET['id'] ?? '';
    if ($id_evento) {
        require_once '../Modelos/Reserva.php';
        require_once '../../Recursos/Modelos/Recurso.php';
        
        try {
            $reservaModel = new Reserva();
            $recursoModel = new Recurso();
            $evento = $reservaModel->obtenerEventoPorId($id_evento);
        } catch (Exception $e) {
            $evento = null;
        }
    }
}

if (!isset($evento) || !$evento) {
    echo '<div class="max-w-4xl mx-auto px-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Evento no encontrado.</span>
                    <a href="verReservas.php" class="ml-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-200">
                        Volver a Reservas
                    </a>
                </div>
            </div>
          </div>';
    include '../../layouts/footer.php';
    exit;
}

if (!isset($recursos)) {
    try {
        require_once '../../Recursos/Modelos/Recurso.php';
        $recursoModel = new Recurso();
    } catch (Exception $e) {
        $recursos = [];
    }
}

?>

<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-yellow-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-edit mr-3"></i>
                Editar Reserva/Evento
            </h1>
        </div>
        
        <div class="p-6">
            <!-- Mensajes de alerta -->
            <div id="alertMessage" class="hidden mb-6 p-4 rounded-lg" role="alert"></div>

            <!-- Información actual del evento -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información Actual
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-blue-800">Estado:</span>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $evento['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : ($evento['estado'] == 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                            <?php echo ucfirst($evento['estado']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Organizador:</span>
                        <span class="ml-2 text-blue-700"><?php echo htmlspecialchars($evento['organizador']); ?></span>
                    </div>
                </div>
            </div>

            <form id="formEditarReserva" method="POST" action="../Controlador/ReservaController.php?accion=editar">
                <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Título del evento -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título del Evento *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                               id="titulo" name="titulo" value="<?php echo htmlspecialchars($evento['titulo']); ?>" required>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                  id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($evento['descripcion']); ?></textarea>
                    </div>

                    <!-- Fecha y horarios -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento *</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="fecha_evento" name="fecha_evento" value="<?php echo $evento['fecha_evento']; ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Fecha original: <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                        </div>
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="hora_inicio" name="hora_inicio" value="<?php echo substr($evento['hora_inicio'], 0, 5); ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_inicio'], 0, 5); ?></p>
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora de Fin *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="hora_fin" name="hora_fin" value="<?php echo substr($evento['hora_fin'], 0, 5); ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_fin'], 0, 5); ?></p>
                        </div>
                    </div>

                    <!-- Recurso -->
                    <div>
                        <label for="id_recurso" class="block text-sm font-medium text-gray-700 mb-2">Recurso</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                id="id_recurso" name="id_recurso">
                            <option value="">Sin recurso específico</option>
                            <?php if (isset($recursos) && !empty($recursos)): ?>
                                <?php foreach ($recursos as $recurso): ?>
                                    <option value="<?php echo $recurso['id']; ?>" 
                                            <?php echo ($evento['id_recurso'] == $recurso['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($recurso['tipo'] . ' - ' . $recurso['descripcion']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Recurso actual: <?php echo $evento['tipo_recurso'] ? htmlspecialchars($evento['tipo_recurso']) : 'Sin recurso'; ?>
                        </p>
                    </div>

                    <!-- Panel de verificación de disponibilidad -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
                            Verificación de Disponibilidad
                        </h3>
                        <div id="disponibilidadStatus" class="text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>Horario actual disponible
                        </div>
                        <div id="loadingDisponibilidad" class="hidden">
                            <div class="flex items-center text-yellow-600">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Verificando disponibilidad...
                            </div>
                        </div>
                    </div>

                    <!-- Advertencia sobre cambios -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-yellow-900 mb-3 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Importante
                        </h3>
                        <ul class="text-sm text-yellow-800 space-y-1">
                            <li>• Si cambia la fecha o hora, se verificará automáticamente la disponibilidad</li>
                            <li>• Los cambios pueden afectar recursos asignados y notificaciones</li>
                            <li>• Este evento tiene estado: <strong><?php echo ucfirst($evento['estado']); ?></strong></li>
                        </ul>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center justify-center" id="btnGuardar">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Reserva
                        </button>
                        <a href="verReservas.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 text-center flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Cancelar
                        </a>
                        <a href="cambiarFechaReserva.php?id=<?php echo $evento['id']; ?>" class="bg-cyan-600 text-white px-6 py-2 rounded-lg hover:bg-cyan-700 transition-colors duration-200 text-center flex items-center justify-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Solo Cambiar Fecha
                        </a>
                    </div>
                </div>

                <input type="hidden" name="ajax" value="1">
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formEditarReserva');
    const fechaInput = document.getElementById('fecha_evento');
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');
    const disponibilidadStatus = document.getElementById('disponibilidadStatus');
    const loadingDisponibilidad = document.getElementById('loadingDisponibilidad');
    const btnGuardar = document.getElementById('btnGuardar');

    // Valores originales para comparar
    const valoresOriginales = {
        fecha: '<?php echo $evento['fecha_evento']; ?>',
        hora_inicio: '<?php echo substr($evento['hora_inicio'], 0, 5); ?>',
        hora_fin: '<?php echo substr($evento['hora_fin'], 0, 5); ?>'
    };

    function verificarDisponibilidad() {
        const fecha = fechaInput.value;
        const horaInicio = horaInicioInput.value;
        const horaFin = horaFinInput.value;
        const idUsuario = <?php echo $evento['id_usuario']; ?>;
        const idEvento = <?php echo $evento['id']; ?>;

        const hayCambios = fecha !== valoresOriginales.fecha || 
                          horaInicio !== valoresOriginales.hora_inicio || 
                          horaFin !== valoresOriginales.hora_fin;

        if (!hayCambios) {
            disponibilidadStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Sin cambios en fecha/hora';
            disponibilidadStatus.className = 'text-gray-500';
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
            return;
        }

        if (!fecha || !horaInicio || !horaFin) {
            disponibilidadStatus.innerHTML = 'Complete todos los campos de fecha y hora';
            disponibilidadStatus.className = 'text-gray-500';
            return;
        }

        if (horaFin <= horaInicio) {
            disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>La hora de fin debe ser posterior a la hora de inicio';
            disponibilidadStatus.className = 'text-yellow-600';
            return;
        }

        loadingDisponibilidad.classList.remove('hidden');
        disponibilidadStatus.classList.add('hidden');

        const params = new URLSearchParams({
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            id_usuario: idUsuario,
            id_evento_excluir: idEvento
        });

        fetch(`../Controlador/ReservaController.php?accion=verificar_disponibilidad&${params}`)
            .then(response => response.json())
            .then(data => {
                loadingDisponibilidad.classList.add('hidden');
                disponibilidadStatus.classList.remove('hidden');

                if (data.success) {
                    if (data.disponible) {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Nueva fecha/hora disponible';
                        disponibilidadStatus.className = 'text-green-600';
                        btnGuardar.disabled = false;
                        btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Nueva fecha/hora no disponible';
                        disponibilidadStatus.className = 'text-red-600';
                        btnGuardar.disabled = true;
                        btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error: ' + data.message;
                    disponibilidadStatus.className = 'text-yellow-600';
                }
            })
            .catch(error => {
                loadingDisponibilidad.classList.add('hidden');
                disponibilidadStatus.classList.remove('hidden');
                disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error al verificar disponibilidad';
                disponibilidadStatus.className = 'text-yellow-600';
                console.error('Error:', error);
            });
    }

    fechaInput.addEventListener('change', verificarDisponibilidad);
    horaInicioInput.addEventListener('change', verificarDisponibilidad);
    horaFinInput.addEventListener('change', verificarDisponibilidad);

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const alertMessage = document.getElementById('alertMessage');
        const formData = new FormData(form);

        // Mostrar loading
        form.classList.add('opacity-60', 'pointer-events-none');
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';

        fetch('../Controlador/ReservaController.php?accion=editar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            form.classList.remove('opacity-60', 'pointer-events-none');
            btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Actualizar Reserva';

            if (data.success) {
                alertMessage.className = 'mb-6 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700';
                alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
                alertMessage.classList.remove('hidden');
                
                setTimeout(() => {
                    window.location.href = 'verReservas.php';
                }, 2000);
            } else {
                alertMessage.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700';
                alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i>' + data.message + '</div>';
                alertMessage.classList.remove('hidden');
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            form.classList.remove('opacity-60', 'pointer-events-none');
            btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Actualizar Reserva';
            
            alertMessage.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700';
            alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i>Error al procesar la solicitud</div>';
            alertMessage.classList.remove('hidden');
            
            console.error('Error:', error);
        });
    });
});
</script>

<?php include '../../layouts/footer.php'; ?>