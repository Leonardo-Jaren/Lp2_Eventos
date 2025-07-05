<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

// Procesamiento del formulario de cancelación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_evento'])) {
    require_once '../Controlador/EventoController.php';

    $eventoController = new EventoController();
    $id_evento = $_POST['id_evento'];
    $motivo = $_POST['motivo_cancelacion'] ?? '';
    
    if ($id_evento) {
        $resultado = $eventoController->eliminarEvento($id_evento);
        // El controlador ya maneja la redirección
    } else {
        $_SESSION['mensaje'] = 'ID de evento no válido';
        $_SESSION['tipo_mensaje'] = 'error';
    }
}

$titulo_pagina = "Cancelar Reserva";
require_once '../../nav.php';

require_once '../Modelos/Reserva.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Obtener evento
$id_evento = $_GET['id'] ?? '';
$evento = null;
if ($id_evento) {
    try {
        $eventoModel = new Evento();
        $evento = $eventoModel->obtenerEventoPorId($id_evento);
    } catch (Exception $e) {
        $evento = null;
    }
}

// Validaciones
if (!$evento) {
    echo '<div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i> Evento no encontrado.
                <a href="verReservas.php" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded text-sm">Volver a Reservas</a>
            </div>
          </div>';
    include '../../layouts/footer.php';
    exit;
}

if ($evento['estado'] === 'cancelado') {
    echo '<div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i> Este evento ya está cancelado.
                <a href="verReservas.php" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded text-sm">Volver a Reservas</a>
            </div>
          </div>';
    include '../../layouts/footer.php';
    exit;
}

$fecha_evento = new DateTime($evento['fecha_evento']);
$hoy = new DateTime();
$dias_anticipacion = $hoy->diff($fecha_evento)->days;
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $tipo_mensaje == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
            <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> mr-2"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-times-circle mr-3"></i>
                Cancelar Reserva
            </h1>
        </div>

        <div class="p-6">
            <!-- Información del evento -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Evento</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Título:</strong> <?php echo htmlspecialchars($evento['titulo']); ?></div>
                    <div><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></div>
                    <div><strong>Hora:</strong> <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento['hora_fin'])); ?></div>
                    <div><strong>Organizador:</strong> <?php echo htmlspecialchars($evento['organizador']); ?></div>
                </div>
                <?php if ($evento['descripcion']): ?>
                    <div class="mt-4"><strong>Descripción:</strong> <?php echo htmlspecialchars($evento['descripcion']); ?></div>
                <?php endif; ?>
            </div>

            <!-- Información de cancelación -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Información Importante
                </h3>
                <ul class="text-yellow-700 space-y-2">
                    <li>• La cancelación es irreversible</li>
                    <li>• Se notificará a todos los participantes</li>
                    <!-- <li>• Los recursos asignados quedarán disponibles nuevamente</li> -->
                    <?php if ($dias_anticipacion < 1): ?>
                        <li class="text-red-600 font-semibold">• <strong>Cancelación tardía:</strong> El evento es hoy o ya pasó</li>
                    <?php elseif ($dias_anticipacion < 7): ?>
                        <li class="text-orange-600">• <strong>Cancelación con poca anticipación:</strong> Menos de 7 días</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Formulario de cancelación -->
            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                
                <div>
                    <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo de la Cancelación *
                    </label>
                    <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Explique brevemente el motivo de la cancelación..."></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="confirmar_cancelacion" name="confirmar_cancelacion" required
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="confirmar_cancelacion" class="ml-2 text-sm text-gray-700">
                        Confirmo que deseo cancelar definitivamente esta reserva
                    </label>
                </div>

                <div class="flex justify-between pt-4">
                    <a href="verReservas.php" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-times-circle mr-2"></i>
                        Cancelar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
