<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Crear Nueva Reserva";
require_once '../../nav.php';

require_once '../Modelos/Reserva.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

$reservaModel = new Reserva();

$verificacion_resultado = '';
$verificacion_tipo = '';
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verificar_disponibilidad'])) {
        $fecha = $_POST['fecha_evento'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $id_usuario = $_POST['id_usuario'] ?? '';
        
        $datos_formulario = $_POST;
        
        if (!$fecha || !$hora_inicio || !$hora_fin || !$id_usuario) {
            $verificacion_resultado = 'Todos los campos son requeridos para verificar disponibilidad';
            $verificacion_tipo = 'warning';
        } elseif ($hora_fin <= $hora_inicio) {
            $verificacion_resultado = 'La hora de fin debe ser posterior a la hora de inicio';
            $verificacion_tipo = 'warning';
        } else {
            try {
                $disponible = $reservaModel->verificarDisponibilidad($fecha, $hora_inicio, $hora_fin, $id_usuario);
                if ($disponible) {
                    $verificacion_resultado = 'Horario disponible. Puede proceder a crear la reserva.';
                    $verificacion_tipo = 'success';
                } else {
                    $verificacion_resultado = 'Horario no disponible. Ya existe una reserva en este período.';
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
        <div class="bg-blue-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-calendar-plus mr-3"></i>
                Crear Nueva Reserva/Evento
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

            <form method="POST" action="../Controlador/ReservaController.php?accion=crear">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título del Evento *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="titulo" name="titulo" required value="<?php echo htmlspecialchars($datos_formulario['titulo'] ?? ''); ?>">
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($datos_formulario['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento *</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="fecha_evento" name="fecha_evento" required min="<?php echo date('Y-m-d'); ?>" 
                                   value="<?php echo htmlspecialchars($datos_formulario['fecha_evento'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="hora_inicio" name="hora_inicio" required value="<?php echo htmlspecialchars($datos_formulario['hora_inicio'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora de Fin *</label>
                            <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="hora_fin" name="hora_fin" required value="<?php echo htmlspecialchars($datos_formulario['hora_fin'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Organizador *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="id_usuario" name="id_usuario" required>
                                <option value="">Seleccionar organizador...</option>
                                <option value="1" <?php echo ($datos_formulario['id_usuario'] ?? '') == '1' ? 'selected' : ''; ?>>Usuario Demo</option>
                            </select>
                        </div>
                        <!-- <div>
                            <label for="id_recurso" class="block text-sm font-medium text-gray-700 mb-2">Recurso (Opcional)</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    id="id_recurso" name="id_recurso">
                                <option value="">Sin recurso específico</option>
                            </select>
                        </div> -->
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
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center">
                                <i class="fas fa-search mr-2"></i>
                                Verificar Disponibilidad
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center"
                                <?php echo ($verificacion_tipo !== 'success') ? 'disabled' : ''; ?>>
                            <i class="fas fa-save mr-2"></i>
                            Crear Reserva
                        </button>
                        <a href="verReservas.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 text-center flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>