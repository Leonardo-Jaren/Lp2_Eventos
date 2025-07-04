<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Crear Nueva Reserva";
require_once '../../nav.php';

require_once '../Controlador/ReservaController.php';
require_once '../../Usuarios/Modelos/Usuario.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

$reservaController = new ReservaController();
$usuarioModel = new Usuario();

// Obtener usuarios para el select
$usuarios = $usuarioModel->obtenerTodosLosUsuarios();

$verificacion_resultado = '';
$verificacion_tipo = '';
$datos_formulario = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verificar_disponibilidad'])) {
        $datos_formulario = $_POST;
        
        if (!$_POST['fecha_evento'] || !$_POST['hora_inicio'] || !$_POST['hora_fin'] || !$_POST['id_usuario']) {
            $verificacion_resultado = 'Todos los campos son requeridos para verificar disponibilidad';
            $verificacion_tipo = 'warning';
        } elseif ($_POST['hora_fin'] <= $_POST['hora_inicio']) {
            $verificacion_resultado = 'La hora de fin debe ser posterior a la hora de inicio';
            $verificacion_tipo = 'warning';
        } else {
            try {
                $verificacion_resultado = $reservaController->verificarDisponibilidad($_POST);
                $verificacion_tipo = strpos($verificacion_resultado, 'disponible') !== false ? 'success' : 'error';
            } catch (Exception $e) {
                $verificacion_resultado = 'Error al verificar disponibilidad: ' . $e->getMessage();
                $verificacion_tipo = 'error';
            }
        }
    } elseif (isset($_POST['crear_reserva'])) {
        $datos_formulario = $_POST;
        
        // Validar campos requeridos
        if (!$_POST['titulo'] || !$_POST['fecha_evento'] || !$_POST['hora_inicio'] || !$_POST['hora_fin'] || !$_POST['id_usuario']) {
            $mensaje = 'Todos los campos marcados con * son requeridos';
            $tipo_mensaje = 'error';
        } elseif ($_POST['hora_fin'] <= $_POST['hora_inicio']) {
            $mensaje = 'La hora de fin debe ser posterior a la hora de inicio';
            $tipo_mensaje = 'error';
        } elseif (strtotime($_POST['fecha_evento']) < strtotime(date('Y-m-d'))) {
            $mensaje = 'No se puede crear un evento en una fecha pasada';
            $tipo_mensaje = 'error';
        } else {
            try {
                $resultado = $reservaController->guardar($_POST);
                if (strpos($resultado, 'correctamente') !== false) {
                    $_SESSION['mensaje'] = $resultado;
                    $_SESSION['tipo_mensaje'] = 'success';
                    header('Location: verReservas.php');
                    exit();
                } else {
                    $mensaje = $resultado;
                    $tipo_mensaje = 'error';
                }
            } catch (Exception $e) {
                $mensaje = 'Error al crear la reserva: ' . $e->getMessage();
                $tipo_mensaje = 'error';
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

            <form method="POST" action=""><?php // Procesar en la misma página ?>
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
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>" 
                                            <?php echo ($datos_formulario['id_usuario'] ?? '') == $usuario['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?>
                                    </option>
                                <?php endforeach; ?>
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
                        <button type="submit" name="crear_reserva" value="1" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const crearBtn = document.querySelector('button[name="crear_reserva"]');
    const verificarBtn = document.querySelector('button[name="verificar_disponibilidad"]');
    
    // Mejorar la experiencia del usuario
    if (crearBtn) {
        crearBtn.addEventListener('click', function(e) {
            // Confirmar antes de crear la reserva
            if (!confirm('¿Está seguro de que desea crear esta reserva?')) {
                e.preventDefault();
            }
        });
    }
    
    // Validar formulario antes de verificar disponibilidad
    if (verificarBtn) {
        verificarBtn.addEventListener('click', function(e) {
            const fecha = document.getElementById('fecha_evento').value;
            const horaInicio = document.getElementById('hora_inicio').value;
            const horaFin = document.getElementById('hora_fin').value;
            const usuario = document.getElementById('id_usuario').value;
            
            if (!fecha || !horaInicio || !horaFin || !usuario) {
                alert('Por favor, complete todos los campos requeridos antes de verificar disponibilidad.');
                e.preventDefault();
            } else if (horaFin <= horaInicio) {
                alert('La hora de fin debe ser posterior a la hora de inicio.');
                e.preventDefault();
            }
        });
    }
});
</script>

<?php include '../../layouts/footer.php'; ?>