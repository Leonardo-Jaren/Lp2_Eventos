<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Editar Reserva";
require_once '../../nav.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

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

$verificacion_resultado = '';
$verificacion_tipo = '';
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verificar_disponibilidad'])) {
        $fecha = $_POST['fecha_evento'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $id_evento_actual = $_POST['id_evento'] ?? '';
        
        $datos_formulario = $_POST;
        
        if (!$fecha || !$hora_inicio || !$hora_fin) {
            $verificacion_resultado = 'Todos los campos son requeridos para verificar disponibilidad';
            $verificacion_tipo = 'warning';
        } elseif ($hora_fin <= $hora_inicio) {
            $verificacion_resultado = 'La hora de fin debe ser posterior a la hora de inicio';
            $verificacion_tipo = 'warning';
        } else {
            try {
                $reservaModel = new Reserva();
                $disponible = $reservaModel->verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, $evento['id_usuario'], $id_evento_actual);
                if ($disponible) {
                    $verificacion_resultado = 'Horario disponible. Puede proceder a actualizar la reserva.';
                    $verificacion_tipo = 'success';
                } else {
                    $verificacion_resultado = 'Horario no disponible. Ya existe otra reserva en este período.';
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

<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-yellow-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-edit mr-3"></i>
                Editar Reserva/Evento
            </h1>
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

            <form method="POST" action="../Controlador/ReservaController.php?accion=editar">
                <input type="hidden" name="id_evento" value="<?php echo $evento['id']; ?>">
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título del Evento *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                               id="titulo" name="titulo" value="<?php echo htmlspecialchars($datos_formulario['titulo'] ?? $evento['titulo']); ?>" required>
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                  id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($datos_formulario['descripcion'] ?? $evento['descripcion']); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento *</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="fecha_evento" name="fecha_evento" value="<?php echo htmlspecialchars($datos_formulario['fecha_evento'] ?? $evento['fecha_evento']); ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Fecha original: <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                        </div>
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="hora_inicio" name="hora_inicio" value="<?php echo htmlspecialchars($datos_formulario['hora_inicio'] ?? substr($evento['hora_inicio'], 0, 5)); ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_inicio'], 0, 5); ?></p>
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora de Fin *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                   id="hora_fin" name="hora_fin" value="<?php echo htmlspecialchars($datos_formulario['hora_fin'] ?? substr($evento['hora_fin'], 0, 5)); ?>" required>
                            <p class="text-xs text-gray-500 mt-1">Hora original: <?php echo substr($evento['hora_fin'], 0, 5); ?></p>
                        </div>
                    </div>

                    <!-- <div>
                        <label for="id_recurso" class="block text-sm font-medium text-gray-700 mb-2">Recurso</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                id="id_recurso" name="id_recurso">
                            <option value="">Sin recurso específico</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Recurso actual: <?php echo $evento['tipo_recurso'] ? htmlspecialchars($evento['tipo_recurso']) : 'Sin recurso'; ?>
                        </p>
                    </div> -->

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
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
                        <?php else: ?>
                            <div class="text-green-600 mb-3">
                                <i class="fas fa-check-circle mr-2"></i>Horario actual disponible
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex gap-2">
                            <button type="submit" name="verificar_disponibilidad" value="1" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center">
                                <i class="fas fa-search mr-2"></i>
                                Verificar Disponibilidad
                            </button>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-yellow-900 mb-3 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Importante
                        </h3>
                        <ul class="text-sm text-yellow-800 space-y-1">
                            <li>• Si cambia la fecha o hora, verifique la disponibilidad antes de guardar</li>
                            <!-- <li>• Los cambios pueden afectar recursos asignados y notificaciones</li> -->
                            <li>• Este evento tiene estado: <strong><?php echo ucfirst($evento['estado']); ?></strong></li>
                        </ul>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center justify-center"
                                <?php echo ($verificacion_resultado && $verificacion_tipo !== 'success') ? 'disabled' : ''; ?>>
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
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>