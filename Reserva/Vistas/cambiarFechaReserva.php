<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}
require_once '../../nav.php';

$titulo_pagina = "Cambiar Fecha de Reserva";
include '../../layouts/header.php';

require_once '../Modelos/Reserva.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

if (!isset($evento) || !$evento) {
    $id_evento = $_GET['id'] ?? '';
    if ($id_evento) {
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

$verificacion_resultado = '';
$verificacion_tipo = '';
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verificar_disponibilidad'])) {
        $nueva_fecha = $_POST['nueva_fecha'] ?? '';
        $nueva_hora_inicio = $_POST['nueva_hora_inicio'] ?? '';
        $nueva_hora_fin = $_POST['nueva_hora_fin'] ?? '';
        
        $datos_formulario = $_POST;
        
        if (!$nueva_fecha || !$nueva_hora_inicio || !$nueva_hora_fin) {
            $verificacion_resultado = 'Todos los campos son requeridos';
            $verificacion_tipo = 'warning';
        } elseif ($nueva_hora_fin <= $nueva_hora_inicio) {
            $verificacion_resultado = 'La hora de fin debe ser posterior a la hora de inicio';
            $verificacion_tipo = 'warning';
        } else {
            try {
                $reservaModel = new Reserva();
                $disponible = $reservaModel->verificarDisponibilidadEdicion($nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $evento['id']);
                if ($disponible) {
                    $verificacion_resultado = 'Nueva fecha/hora disponible. Puede proceder con el cambio.';
                    $verificacion_tipo = 'success';
                } else {
                    $verificacion_resultado = 'Nueva fecha/hora no disponible. Ya existe otra reserva en este período.';
                    $verificacion_tipo = 'error';
                }
            } catch (Exception $e) {
                $verificacion_resultado = 'Error al verificar disponibilidad: ' . $e->getMessage();
                $verificacion_tipo = 'error';
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="bg-blue-500 text-white p-6 rounded-t-lg">
                <h1 class="text-2xl font-bold"><i class="fas fa-calendar-alt mr-2"></i> Cambiar Fecha de Reserva</h1>
            </div>
            
            <div class="p-6">
                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
                        <div class="flex items-center">
                            <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
                            <span><?php echo htmlspecialchars($mensaje); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h2 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información Actual de la Reserva
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-800">Título:</span>
                            <span class="ml-2 text-blue-700"><?php echo htmlspecialchars($evento['titulo']); ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Estado:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $evento['estado'] == 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo ucfirst($evento['estado']); ?>
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Fecha Actual:</span>
                            <span class="ml-2 text-blue-700"><?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Horario Actual:</span>
                            <span class="ml-2 text-blue-700"><?php echo substr($evento['hora_inicio'], 0, 5); ?> - <?php echo substr($evento['hora_fin'], 0, 5); ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Organizador:</span>
                            <span class="ml-2 text-blue-700"><?php echo htmlspecialchars($evento['organizador']); ?></span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-800">Días de Anticipación:</span>
                            <span class="ml-2 text-blue-700"><?php echo $dias_anticipacion; ?> días</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="../Controlador/ReservaController.php?accion=cambiar_fecha">
                    <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="nueva_fecha" class="block text-sm font-medium text-gray-700 mb-2">Nueva Fecha *</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       id="nueva_fecha" name="nueva_fecha" required min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($datos_formulario['nueva_fecha'] ?? $evento['fecha_evento']); ?>">
                                <p class="text-xs text-gray-500 mt-1">Fecha original: <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                            </div>
                            <div>
                                <label for="nueva_hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Nueva Hora de Inicio *</label>
                                <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       id="nueva_hora_inicio" name="nueva_hora_inicio" required
                                       value="<?php echo htmlspecialchars($datos_formulario['nueva_hora_inicio'] ?? substr($evento['hora_inicio'], 0, 5)); ?>">
                                <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_inicio'], 0, 5); ?></p>
                            </div>
                            <div>
                                <label for="nueva_hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Nueva Hora de Fin *</label>
                                <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       id="nueva_hora_fin" name="nueva_hora_fin" required
                                       value="<?php echo htmlspecialchars($datos_formulario['nueva_hora_fin'] ?? substr($evento['hora_fin'], 0, 5)); ?>">
                                <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_fin'], 0, 5); ?></p>
                            </div>
                        </div>

                        <div>
                            <label for="motivo_cambio" class="block text-sm font-medium text-gray-700 mb-2">Motivo del Cambio (Opcional)</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                      id="motivo_cambio" name="motivo_cambio" rows="3" 
                                      placeholder="Describa el motivo del cambio de fecha/hora..."><?php echo htmlspecialchars($datos_formulario['motivo_cambio'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label for="penalidad" class="block text-sm font-medium text-gray-700 mb-2">Penalidad por Cambio (%)</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="penalidad" name="penalidad" min="0" max="100" 
                                   value="<?php echo htmlspecialchars($datos_formulario['penalidad'] ?? $penalidad_sugerida); ?>">
                            <p class="text-xs text-gray-500 mt-1">
                                Penalidad sugerida: <?php echo $penalidad_sugerida; ?>% 
                                (basada en <?php echo $dias_anticipacion; ?> días de anticipación)
                            </p>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                Verificación de Disponibilidad
                            </h3>
                            
                            <?php if ($verificacion_resultado): ?>
                                <div class="mb-3 p-3 rounded-lg <?php 
                                    echo $verificacion_tipo == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 
                                        ($verificacion_tipo == 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' : 'bg-red-100 border border-red-400 text-red-700'); 
                                ?>">
                                    <div class="flex items-center">
                                        <i class="fas fa-<?php echo $verificacion_tipo == 'success' ? 'check-circle' : ($verificacion_tipo == 'warning' ? 'exclamation-triangle' : 'times-circle'); ?> mr-2"></i>
                                        <span><?php echo htmlspecialchars($verificacion_resultado); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex gap-2">
                                <button type="submit" name="verificar_disponibilidad" value="1" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                                    <i class="fas fa-search mr-2"></i>
                                    Verificar Disponibilidad
                                </button>
                            </div>
                        </div>

                        <?php 
                        $hay_cambios = false;
                        if (!empty($datos_formulario)) {
                            $hay_cambios = $datos_formulario['nueva_fecha'] != $evento['fecha_evento'] ||
                                          $datos_formulario['nueva_hora_inicio'] != substr($evento['hora_inicio'], 0, 5) ||
                                          $datos_formulario['nueva_hora_fin'] != substr($evento['hora_fin'], 0, 5);
                        }
                        ?>

                        <?php if ($hay_cambios): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h5 class="font-semibold text-yellow-800 mb-2"><i class="fas fa-exchange-alt mr-2"></i> Cambios Detectados:</h5>
                                <ul class="list-disc list-inside space-y-1 text-yellow-700">
                                    <?php if ($datos_formulario['nueva_fecha'] != $evento['fecha_evento']): ?>
                                        <li><span class="font-semibold">Fecha:</span> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?> → <?php echo date('d/m/Y', strtotime($datos_formulario['nueva_fecha'])); ?></li>
                                    <?php endif; ?>
                                    <?php if ($datos_formulario['nueva_hora_inicio'] != substr($evento['hora_inicio'], 0, 5)): ?>
                                        <li><span class="font-semibold">Hora de inicio:</span> <?php echo substr($evento['hora_inicio'], 0, 5); ?> → <?php echo $datos_formulario['nueva_hora_inicio']; ?></li>
                                    <?php endif; ?>
                                    <?php if ($datos_formulario['nueva_hora_fin'] != substr($evento['hora_fin'], 0, 5)): ?>
                                        <li><span class="font-semibold">Hora de fin:</span> <?php echo substr($evento['hora_fin'], 0, 5); ?> → <?php echo $datos_formulario['nueva_hora_fin']; ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-yellow-900 mb-3 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Información Importante
                            </h3>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• El cambio de fecha puede generar una penalidad según la anticipación</li>
                                <li>• Se verificará automáticamente la disponibilidad del nuevo horario</li>
                                <li>• Los participantes serán notificados del cambio automáticamente</li>
                                <li>• Este cambio quedará registrado en el historial del evento</li>
                            </ul>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center"
                                    <?php echo ($verificacion_resultado && $verificacion_tipo !== 'success') ? 'disabled' : ''; ?>>
                                <i class="fas fa-calendar-check mr-2"></i>
                                Cambiar Fecha
                            </button>
                            <a href="verReservas.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 text-center flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Cancelar
                            </a>
                            <a href="editarReserva.php?id=<?php echo $evento['id']; ?>" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200 text-center flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edición Completa
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
