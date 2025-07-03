<?php
session_start();
$titulo_pagina = "Cancelar Reserva";
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

// Verificar si se pudo cargar el evento
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

// Verificar si el evento ya está cancelado
if ($evento['estado'] === 'cancelado') {
    echo '<div class="container mx-auto px-4 py-8">
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i> Este evento ya está cancelado.
                <a href="verReservas.php" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded text-sm transition duration-300">Volver a Reservas</a>
            </div>
          </div>';
    include '../../layouts/footer.php';
    exit;
}

// Calcular penalidad basada en días de anticipación
$fecha_evento = new DateTime($evento['fecha_evento']);
$hoy = new DateTime();
$dias_anticipacion = $hoy->diff($fecha_evento)->days;

$penalidad_sugerida = 0;
$mensaje_penalidad = '';

if ($dias_anticipacion < 1) {
    $penalidad_sugerida = 50;
    $mensaje_penalidad = 'Cancelación el mismo día del evento';
} elseif ($dias_anticipacion < 3) {
    $penalidad_sugerida = 35;
    $mensaje_penalidad = 'Cancelación con menos de 3 días de anticipación';
} elseif ($dias_anticipacion < 7) {
    $penalidad_sugerida = 25;
    $mensaje_penalidad = 'Cancelación con menos de 7 días de anticipación';
} elseif ($dias_anticipacion < 14) {
    $penalidad_sugerida = 15;
    $mensaje_penalidad = 'Cancelación con menos de 14 días de anticipación';
} elseif ($dias_anticipacion < 30) {
    $penalidad_sugerida = 10;
    $mensaje_penalidad = 'Cancelación con menos de 30 días de anticipación';
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="bg-red-500 text-white p-6 rounded-t-lg">
                <h1 class="text-2xl font-bold"><i class="fas fa-times-circle mr-2"></i> Cancelar Reserva</h1>
            </div>
            <div class="p-6">
                <!-- Mensajes de alerta -->
                <div id="alertMessage" class="hidden mb-6" role="alert"></div>

                <!-- Información del evento a cancelar -->
                <div class="bg-gray-50 rounded-lg border border-gray-200 mb-6">
                    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                        <h2 class="text-lg font-semibold text-gray-800"><i class="fas fa-info-circle mr-2"></i> Información del Evento</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <p class="text-gray-700"><span class="font-semibold">Título:</span> <?php echo htmlspecialchars($evento['titulo']); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Descripción:</span> <?php echo htmlspecialchars($evento['descripcion']); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Organizador:</span> <?php echo htmlspecialchars($evento['organizador']); ?></p>
                                <p class="text-gray-700">
                                    <span class="font-semibold">Estado:</span> 
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                        <?php echo $evento['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 
                                        ($evento['estado'] == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo ucfirst($evento['estado']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="space-y-3">
                                <p class="text-gray-700"><span class="font-semibold">Fecha:</span> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Hora:</span> <?php echo substr($evento['hora_inicio'], 0, 5) . ' - ' . substr($evento['hora_fin'], 0, 5); ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Recurso:</span> <?php echo $evento['tipo_recurso'] ? htmlspecialchars($evento['tipo_recurso']) : 'Sin recurso'; ?></p>
                                <p class="text-gray-700"><span class="font-semibold">Días restantes:</span> <?php echo $dias_anticipacion; ?> días</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertencia de cancelación -->
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Advertencia de Cancelación</h3>
                            <p class="text-sm text-red-700 mt-1">
                                ¿Está seguro de que desea cancelar esta reserva? Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información sobre penalidades -->
                <?php if ($penalidad_sugerida > 0): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Política de Penalidades</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    <?php echo $mensaje_penalidad; ?>. Se aplicará una penalidad del <span class="font-semibold"><?php echo $penalidad_sugerida; ?>%</span>.
                                </p>
                                <div class="text-xs text-yellow-600 mt-2">
                                    <p class="font-semibold">Escala de penalidades:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li>El mismo día: 50%</li>
                                        <li>Menos de 3 días: 35%</li>
                                        <li>Menos de 7 días: 25%</li>
                                        <li>Menos de 14 días: 15%</li>
                                        <li>Menos de 30 días: 10%</li>
                                        <li>Más de 30 días: Sin penalidad</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Sin Penalidad</h3>
                                <p class="text-sm text-green-700 mt-1">
                                    Como está cancelando con más de 30 días de anticipación, no se aplicará ninguna penalidad.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="formCancelarReserva" method="POST" action="../Controlador/ReservaController.php?accion=cancelar">
                    <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                    <input type="hidden" name="penalidad" value="<?php echo $penalidad_sugerida; ?>">
                    
                    <!-- Motivo de cancelación -->
                    <div class="mb-6">
                        <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Motivo de la Cancelación *
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" 
                                  id="motivo_cancelacion" 
                                  name="motivo_cancelacion" 
                                  rows="4" 
                                  required
                                  placeholder="Explique el motivo de la cancelación..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Este campo es obligatorio y será registrado en el historial.</p>
                    </div>

                    <!-- Confirmación -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="confirmar_cancelacion" 
                                   name="confirmar_cancelacion" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" 
                                   required>
                            <label for="confirmar_cancelacion" class="ml-2 block text-sm text-gray-700">
                                Confirmo que deseo cancelar esta reserva y acepto las penalidades aplicables.
                            </label>
                        </div>
                    </div>

                    <!-- Resumen de la cancelación -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">
                            <i class="fas fa-clipboard-list mr-2"></i> Resumen de la Cancelación
                        </h4>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between">
                                <span>Evento:</span>
                                <span class="font-medium"><?php echo htmlspecialchars($evento['titulo']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Fecha del evento:</span>
                                <span class="font-medium"><?php echo date('d/m/Y H:i', strtotime($evento['fecha_evento'] . ' ' . $evento['hora_inicio'])); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Días de anticipación:</span>
                                <span class="font-medium"><?php echo $dias_anticipacion; ?> días</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span>Penalidad aplicable:</span>
                                <span class="font-medium <?php echo $penalidad_sugerida > 0 ? 'text-red-600' : 'text-green-600'; ?>">
                                    <?php echo $penalidad_sugerida > 0 ? $penalidad_sugerida . '%' : 'Sin penalidad'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed" 
                                id="btnCancelar">
                            <i class="fas fa-times-circle mr-2"></i> Cancelar Reserva
                        </button>
                        <a href="verReservas.php" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                        <a href="editarReserva.php?id=<?php echo $evento['id']; ?>" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-edit mr-2"></i> Editar en su lugar
                        </a>
                    </div>

                    <input type="hidden" name="ajax" value="1">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="modalConfirmacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">¿Confirmar Cancelación?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Esta acción no se puede deshacer. El evento será cancelado permanentemente.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="btnConfirmarCancelacion" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 mb-2">
                    Sí, Cancelar Reserva
                </button>
                <button id="btnCerrarModal" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    No, Mantener Reserva
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCancelarReserva');
    const btnCancelar = document.getElementById('btnCancelar');
    const modalConfirmacion = document.getElementById('modalConfirmacion');
    const btnConfirmarCancelacion = document.getElementById('btnConfirmarCancelacion');
    const btnCerrarModal = document.getElementById('btnCerrarModal');
    const motivoTextarea = document.getElementById('motivo_cancelacion');
    const confirmarCheckbox = document.getElementById('confirmar_cancelacion');

    // Validar campos del formulario
    function validarFormulario() {
        const motivo = motivoTextarea.value.trim();
        const confirmado = confirmarCheckbox.checked;
        
        btnCancelar.disabled = !motivo || !confirmado;
    }

    // Event listeners para validación
    motivoTextarea.addEventListener('input', validarFormulario);
    confirmarCheckbox.addEventListener('change', validarFormulario);

    // Validación inicial
    validarFormulario();

    // Mostrar modal de confirmación
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        modalConfirmacion.classList.remove('hidden');
    });

    // Cerrar modal
    btnCerrarModal.addEventListener('click', function() {
        modalConfirmacion.classList.add('hidden');
    });

    // Confirmar cancelación
    btnConfirmarCancelacion.addEventListener('click', function() {
        modalConfirmacion.classList.add('hidden');
        procesarCancelacion();
    });

    function procesarCancelacion() {
        const alertMessage = document.getElementById('alertMessage');
        const formData = new FormData(form);

        // Mostrar loading
        form.style.opacity = '0.6';
        form.style.pointerEvents = 'none';
        btnCancelar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Cancelando...';

        fetch('../Controlador/ReservaController.php?accion=cancelar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            form.style.opacity = '1';
            form.style.pointerEvents = 'auto';
            btnCancelar.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Cancelar Reserva';

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
            btnCancelar.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Cancelar Reserva';
            
            alertMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6';
            alertMessage.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Error al procesar la cancelación';
            alertMessage.classList.remove('hidden');
            
            console.error('Error:', error);
        });
    }

    // Cerrar modal haciendo clic fuera de él
    modalConfirmacion.addEventListener('click', function(e) {
        if (e.target === modalConfirmacion) {
            modalConfirmacion.classList.add('hidden');
        }
    });
});
</script>

<?php include '../../layouts/footer.php'; ?>
