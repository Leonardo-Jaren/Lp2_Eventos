<?php
session_start();
$titulo_pagina = "Crear Nueva Reserva";
include '../../layouts/header.php';
?>

<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-calendar-plus mr-3"></i>
                Crear Nueva Reserva/Evento
            </h1>
        </div>
        
        <div class="p-6">
            <!-- Mensajes de alerta -->
            <div id="alertMessage" class="hidden mb-6 p-4 rounded-lg" role="alert"></div>

            <form id="formCrearReserva" method="POST" action="../Controlador/ReservaController.php?accion=crear">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Título del evento -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título del Evento *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="titulo" name="titulo" required>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <!-- Fecha y horarios -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento *</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="fecha_evento" name="fecha_evento" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="hora_inicio" name="hora_inicio" required>
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora de Fin *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="hora_fin" name="hora_fin" required>
                        </div>
                    </div>

                    <!-- Organizador y recurso -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Organizador *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="id_usuario" name="id_usuario" required>
                                <option value="">Seleccionar organizador...</option>
                                <!-- Aquí deberías cargar los usuarios desde la base de datos -->
                                <option value="1">Usuario Demo</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_recurso" class="block text-sm font-medium text-gray-700 mb-2">Recurso (Opcional)</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="id_recurso" name="id_recurso">
                                <option value="">Sin recurso específico</option>
                                <?php if (isset($recursos) && !empty($recursos)): ?>
                                    <?php foreach ($recursos as $recurso): ?>
                                        <option value="<?php echo $recurso['id']; ?>">
                                            <?php echo htmlspecialchars($recurso['tipo'] . ' - ' . $recurso['descripcion']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Panel de disponibilidad -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-clock text-blue-600 mr-2"></i>
                            Verificación de Disponibilidad
                        </h3>
                        <div id="disponibilidadStatus" class="text-gray-500">
                            Seleccione fecha, hora de inicio y fin para verificar disponibilidad
                        </div>
                        <div id="loadingDisponibilidad" class="hidden">
                            <div class="flex items-center text-blue-600">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Verificando disponibilidad...
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center" id="btnGuardar">
                            <i class="fas fa-save mr-2"></i>
                            Crear Reserva
                        </button>
                        <a href="verReservas.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 text-center flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Cancelar
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
    const form = document.getElementById('formCrearReserva');
    const fechaInput = document.getElementById('fecha_evento');
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');
    const organizadorInput = document.getElementById('id_usuario');
    const disponibilidadStatus = document.getElementById('disponibilidadStatus');
    const loadingDisponibilidad = document.getElementById('loadingDisponibilidad');
    const btnGuardar = document.getElementById('btnGuardar');

    // Verificar disponibilidad cuando cambien los campos relevantes
    function verificarDisponibilidad() {
        const fecha = fechaInput.value;
        const horaInicio = horaInicioInput.value;
        const horaFin = horaFinInput.value;
        const idUsuario = organizadorInput.value;

        if (!fecha || !horaInicio || !horaFin || !idUsuario) {
            disponibilidadStatus.innerHTML = 'Seleccione fecha, hora de inicio y fin para verificar disponibilidad';
            disponibilidadStatus.className = 'text-gray-500';
            return;
        }

        if (horaFin <= horaInicio) {
            disponibilidadStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>La hora de fin debe ser posterior a la hora de inicio';
            disponibilidadStatus.className = 'text-yellow-600';
            return;
        }

        // Mostrar loading
        loadingDisponibilidad.classList.remove('hidden');
        disponibilidadStatus.classList.add('hidden');

        // Hacer petición AJAX
        const params = new URLSearchParams({
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            id_usuario: idUsuario
        });

        fetch(`../Controlador/ReservaController.php?accion=verificar_disponibilidad&${params}`)
            .then(response => response.json())
            .then(data => {
                loadingDisponibilidad.classList.add('hidden');
                disponibilidadStatus.classList.remove('hidden');

                if (data.success) {
                    if (data.disponible) {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
                        disponibilidadStatus.className = 'text-green-600';
                        btnGuardar.disabled = false;
                        btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        disponibilidadStatus.innerHTML = '<i class="fas fa-times-circle mr-2"></i>' + data.message;
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

    // Event listeners para verificar disponibilidad
    fechaInput.addEventListener('change', verificarDisponibilidad);
    horaInicioInput.addEventListener('change', verificarDisponibilidad);
    horaFinInput.addEventListener('change', verificarDisponibilidad);
    organizadorInput.addEventListener('change', verificarDisponibilidad);

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const alertMessage = document.getElementById('alertMessage');
        const formData = new FormData(form);

        // Mostrar loading
        form.classList.add('opacity-60', 'pointer-events-none');
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creando...';

        fetch('../Controlador/ReservaController.php?accion=crear', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            form.classList.remove('opacity-60', 'pointer-events-none');
            btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Crear Reserva';

            if (data.success) {
                alertMessage.className = 'mb-6 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700';
                alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
                alertMessage.classList.remove('hidden');
                
                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'verReservas.php';
                }, 2000);
            } else {
                alertMessage.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700';
                alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i>' + data.message + '</div>';
                alertMessage.classList.remove('hidden');
            }

            // Scroll to top para mostrar el mensaje
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            form.classList.remove('opacity-60', 'pointer-events-none');
            btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Crear Reserva';
            
            alertMessage.className = 'mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700';
            alertMessage.innerHTML = '<div class="flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i>Error al procesar la solicitud</div>';
            alertMessage.classList.remove('hidden');
            
            console.error('Error:', error);
        });
    });
});
</script>

<?php include '../../layouts/footer.php'; ?>