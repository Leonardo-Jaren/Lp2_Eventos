<?php
session_start();
$titulo_pagina = "Cambiar Fecha de Reserva";
include '../../layouts/header.php';

if (!isset($evento) || !$evento) {
    $id_evento = $_GET['id'] ?? '';
    if ($id_evento) {
        require_once '../Modelos/Reserva.php';
        
        try {
            $reservaModel = new Reserva();
            $evento = $reservaModel->obtenerEventoPorId($id_evento);
        } catch (Exception $e) {
            $evento = null;
        }
    }
}


if (!isset($evento) || !$evento) {
    echo '<div class="container mx-auto px-4 py-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i> Evento no encontrado.
                <a href="verReservas.php" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded text-sm transition duration-300">Volver a Reservas</a>
            </div>
          </div>';
    include '../../layouts/footer.php';
    exit;
}

$fecha_evento = new DateTime($evento['fecha_evento']);
$hoy = new DateTime();
$dias_anticipacion = $hoy->diff($fecha_evento)->days;

$penalidad_sugerida = 0;
if ($dias_anticipacion < 7) {
    $penalidad_sugerida = 25;
} elseif ($dias_anticipacion < 14) {
    $penalidad_sugerida = 15;
} elseif ($dias_anticipacion < 30) {
    $penalidad_sugerida = 10;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="bg-blue-500 text-white p-6 rounded-t-lg">
                <h1 class="text-2xl font-bold"><i class="fas fa-calendar-alt mr-2"></i> Cambiar Fecha de Reserva</h1>
            </div>
            <div class="p-6">
                <div id="alertMessage" class="hidden mb-6" role="alert"></div>

                <div class="bg-gray-50 rounded-lg border border-gray-200 mb-6">
                    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                        <h2 class="text-lg font-semibold text-gray-800"><i class="fas fa-info-circle mr-2"></i> Información Actual del Evento</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <p class="text-gray-700"><span class="font-semibold">Título:</span> <?php echo htmlspecialchars($evento['titulo']); ?></p>
                                <p class="text-gray-700">
                                    <span class="font-semibold">Estado:</span> 
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                        <?php echo $evento['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 
                                        ($evento['estado'] == 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                        <?php echo ucfirst($evento['estado']); ?>
                                    </span>
                                </p>
                                <p class="text-gray-700"><span class="font-semibold">Organizador:</span> <?php echo htmlspecialchars($evento['organizador']); ?></p>
                            </div>
                            <div class="space-y-3">
                                <p class="text-gray-700"><span class="font-semibold">Fecha Actual:</span> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Hora Actual:</span> <?php echo substr($evento['hora_inicio'], 0, 5) . ' - ' . substr($evento['hora_fin'], 0, 5); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Recurso:</span> <?php echo $evento['tipo_recurso'] ? htmlspecialchars($evento['tipo_recurso']) : 'Sin recurso'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($penalidad_sugerida > 0): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Política de Cambios</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Debido a que el evento está programado para dentro de <span class="font-semibold"><?php echo $dias_anticipacion; ?> días</span>, 
                                    se sugiere aplicar una penalidad del <span class="font-semibold"><?php echo $penalidad_sugerida; ?>%</span>.
                                </p>
                                <div class="text-xs text-yellow-600 mt-2">
                                    <p class="font-semibold">Política de penalidades:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li>Menos de 7 días: 25%</li>
                                        <li>7-14 días: 15%</li>
                                        <li>15-30 días: 10%</li>
                                        <li>Más de 30 días: Sin penalidad</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="formCambiarFecha" method="POST" action="../Controlador/ReservaController.php?accion=cambiar_fecha">
                    <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-calendar mr-2"></i> Nueva Fecha y Horario
                        </h3>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="nueva_fecha" class="block text-sm font-medium text-gray-700 mb-2">Nueva Fecha *</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   id="nueva_fecha" 
                                   name="nueva_fecha" 
                                   value="<?php echo $evento['fecha_evento']; ?>" 
                                   required
                                   min="<?php echo date('Y-m-d'); ?>">
                            <p class="text-xs text-gray-500 mt-1">
                                Fecha actual: <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?>
                            </p>
                        </div>
                        <div>
                            <label for="nueva_hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Nueva Hora de Inicio *</label>
                            <input type="time" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   id="nueva_hora_inicio" 
                                   name="nueva_hora_inicio" 
                                   value="<?php echo substr($evento['hora_inicio'], 0, 5); ?>" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                Hora actual: <?php echo substr($evento['hora_inicio'], 0, 5); ?>
                            </p>
                        </div>
                        <div>
                            <label for="nueva_hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Nueva Hora de Fin *</label>
                            <input type="time" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   id="nueva_hora_fin" 
                                   name="nueva_hora_fin" 
                                   value="<?php echo substr($evento['hora_fin'], 0, 5); ?>" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                Hora actual: <?php echo substr($evento['hora_fin'], 0, 5); ?>
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <h4 class="text-md font-medium text-gray-800 mb-3">
                                <i class="fas fa-clock mr-2"></i> Verificación de Disponibilidad
                            </h4>
                            <div id="disponibilidadStatus" class="text-gray-600">
                                Modifique la fecha o hora para verificar disponibilidad
                            </div>
                            <div id="loadingDisponibilidad" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Verificando disponibilidad...
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="bg-blue-100 px-4 py-3 border-b border-blue-200 rounded-t-lg">
                                <h4 class="text-md font-medium text-blue-800"><i class="fas fa-info-circle mr-2"></i> Detalles del Cambio</h4>
                            </div>
                            <div class="p-4">
                                <div id="resumenCambio" class="text-gray-600">
                                    Sin cambios por el momento
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="motivo_cambio" class="block text-sm font-medium text-gray-700 mb-2">Motivo del Cambio (Opcional)</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                  id="motivo_cambio" 
                                  name="motivo_cambio" 
                                  rows="3" 
                                  placeholder="Explique el motivo del cambio de fecha..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed" 
                                id="btnGuardar" 
                                disabled>
                            <i class="fas fa-calendar-check mr-2"></i> Cambiar Fecha
                        </button>
                        <a href="verReservas.php" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg text-center transition duration-300">
                            <i class="fas fa-arrow-left mr-2"></i> Cancelar
                        </a>
                        <a href="editarReserva.php?id=<?php echo $evento['id']; ?>" 
                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg text-center transition duration-300">
                            <i class="fas fa-edit mr-2"></i> Edición Completa
                        </a>
                    </div>

                    <input type="hidden" name="ajax" value="1">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCambiarFecha');
    const fechaInput = document.getElementById('nueva_fecha');
    const horaInicioInput = document.getElementById('nueva_hora_inicio');
    const horaFinInput = document.getElementById('nueva_hora_fin');
    const disponibilidadStatus = document.getElementById('disponibilidadStatus');
    const loadingDisponibilidad = document.getElementById('loadingDisponibilidad');
    const resumenCambio = document.getElementById('resumenCambio');
    const btnGuardar = document.getElementById('btnGuardar');

    const valoresOriginales = {
        fecha: '<?php echo $evento['fecha_evento']; ?>',
        hora_inicio: '<?php echo substr($evento['hora_inicio'], 0, 5); ?>',
        hora_fin: '<?php echo substr($evento['hora_fin'], 0, 5); ?>'
    };

    function actualizarResumenCambio() {
        const nuevaFecha = fechaInput.value;
        const nuevaHoraInicio = horaInicioInput.value;
        const nuevaHoraFin = horaFinInput.value;

        const hayCambios = nuevaFecha !== valoresOriginales.fecha || 
                          nuevaHoraInicio !== valoresOriginales.hora_inicio || 
                          nuevaHoraFin !== valoresOriginales.hora_fin;

        if (!hayCambios) {
            resumenCambio.innerHTML = 'Sin cambios detectados';
            resumenCambio.className = 'text-gray-600';
            return false;
        }

        let cambios = [];
        
        if (nuevaFecha !== valoresOriginales.fecha) {
            const fechaOriginal = new Date(valoresOriginales.fecha);
            const fechaNueva = new Date(nuevaFecha);
            cambios.push(`<span class="font-semibold">Fecha:</span> ${fechaOriginal.toLocaleDateString('es-ES')} → ${fechaNueva.toLocaleDateString('es-ES')}`);
        }

        if (nuevaHoraInicio !== valoresOriginales.hora_inicio) {
            cambios.push(`<span class="font-semibold">Hora de inicio:</span> ${valoresOriginales.hora_inicio} → ${nuevaHoraInicio}`);
        }

        if (nuevaHoraFin !== valoresOriginales.hora_fin) {
            cambios.push(`<span class="font-semibold">Hora de fin:</span> ${valoresOriginales.hora_fin} → ${nuevaHoraFin}`);
        }

        resumenCambio.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <h5 class="font-semibold text-yellow-800 mb-2"><i class="fas fa-exchange-alt mr-2"></i> Cambios Detectados:</h5>
                <ul class="list-disc list-inside space-y-1 text-yellow-700">
                    ${cambios.map(cambio => `<li>${cambio}</li>`).join('')}
                </ul>
            </div>
        `;

        return true;
    }

    function verificarDisponibilidad() {
        const hayCambios = actualizarResumenCambio();
        
        if (!hayCambios) {
            disponibilidadStatus.innerHTML = 'Modifique la fecha o hora para verificar disponibilidad';
            disponibilidadStatus.className = 'text-gray-600';
            btnGuardar.disabled = true;
            return;
        }

        const fecha = fechaInput.value;
        const horaInicio = horaInicioInput.value;
        const horaFin = horaFinInput.value;

        if (!fecha || !horaInicio || !horaFin) {
            disponibilidadStatus.innerHTML = 'Complete todos los campos de fecha y hora';
            disponibilidadStatus.className = 'text-gray-600';
            btnGuardar.disabled = true;
            return;
        }

        if (horaFin <= horaInicio) {
            disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> La hora de fin debe ser posterior a la hora de inicio';
            disponibilidadStatus.className = 'text-yellow-600';
            btnGuardar.disabled = true;
            return;
        }

        loadingDisponibilidad.classList.remove('hidden');
        disponibilidadStatus.classList.add('hidden');

        const params = new URLSearchParams({
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            id_usuario: <?php echo $evento['id_usuario']; ?>,
            id_evento_excluir: <?php echo $evento['id']; ?>
        });

        fetch(`../Controlador/ReservaController.php?accion=verificar_disponibilidad&${params}`)
            .then(response => response.json())
            .then(data => {
                loadingDisponibilidad.classList.add('hidden');
                disponibilidadStatus.classList.remove('hidden');

                if (data.success) {
                    if (data.disponible) {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Nueva fecha/hora disponible';
                        disponibilidadStatus.className = 'text-green-600';
                        btnGuardar.disabled = false;
                    } else {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Nueva fecha/hora no disponible';
                        disponibilidadStatus.className = 'text-red-600';
                        btnGuardar.disabled = true;
                    }
                } else {
                    disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Error: ' + data.message;
                    disponibilidadStatus.className = 'text-yellow-600';
                    btnGuardar.disabled = true;
                }
            })
            .catch(error => {
                loadingDisponibilidad.classList.add('hidden');
                disponibilidadStatus.classList.remove('hidden');
                disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Error al verificar disponibilidad';
                disponibilidadStatus.className = 'text-yellow-600';
                btnGuardar.disabled = true;
                console.error('Error:', error);
            });
    }

    fechaInput.addEventListener('change', verificarDisponibilidad);
    horaInicioInput.addEventListener('change', verificarDisponibilidad);
    horaFinInput.addEventListener('change', verificarDisponibilidad);

    // Verificación inicial
    actualizarResumenCambio();

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const alertMessage = document.getElementById('alertMessage');
        const formData = new FormData(form);

        // Mostrar loading
        form.style.opacity = '0.6';
        form.style.pointerEvents = 'none';
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Cambiando...';

        fetch('../Controlador/ReservaController.php?accion=cambiar_fecha', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            form.style.opacity = '1';
            form.style.pointerEvents = 'auto';
            btnGuardar.innerHTML = '<i class="fas fa-calendar-check mr-2"></i> Cambiar Fecha';

            if (data.success) {
                alertMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6';
                alertMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i> ' + data.message;
                alertMessage.classList.remove('hidden');
                
                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'verReservas.php';
                }, 2000);
            } else {
                alertMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6';
                alertMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> ' + data.message;
                alertMessage.classList.remove('hidden');
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            form.style.opacity = '1';
            form.style.pointerEvents = 'auto';
            btnGuardar.innerHTML = '<i class="fas fa-calendar-check mr-2"></i> Cambiar Fecha';
            
            alertMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6';
            alertMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Error al procesar la solicitud';
            alertMessage.classList.remove('hidden');
            
            console.error('Error:', error);
        });
    });
});
</script>

<?php include '../../layouts/footer.php'; ?>