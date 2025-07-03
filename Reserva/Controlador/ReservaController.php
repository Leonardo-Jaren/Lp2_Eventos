<?php

require_once '../Modelos/Reserva.php';
require_once '../Modelos/Calendario.php';

class ReservaController {
    private $reservaModel;

    public function __construct() {
        session_start(); // Asegurar que la sesión esté siempre iniciada
        $this->reservaModel = new Reserva();
    }

    public function manejarAccion() {
        $accion = $_GET['accion'] ?? 'listar';

        switch ($accion) {
            case 'crear':
                $this->crearReserva();
                break;
            case 'editar':
                $this->editarReserva();
                break;
            case 'cancelar':
                $this->cancelarReserva();
                break;
            case 'cambiar_fecha':
                $this->cambiarFechaReserva();
                break;
            case 'listar':
                $this->listarReservas();
                break;
            case 'historial':
                $this->mostrarHistorial();
                break;
            case 'calendario':
                $this->mostrarCalendario();
                break;
            case 'verificar_disponibilidad':
                $this->verificarDisponibilidad();
                break;
            case 'obtener_evento':
                $this->obtenerEvento();
                break;
            default:
                $this->listarReservas();
        }
    }

    public function crearReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Verificar que el usuario esté autenticado
                if (!isset($_SESSION['id'])) {
                    throw new Exception("Usuario no autenticado");
                }
                
                $titulo = $_POST['titulo'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $fecha_evento = $_POST['fecha_evento'] ?? '';
                $hora_inicio = $_POST['hora_inicio'] ?? '';
                $hora_fin = $_POST['hora_fin'] ?? '';
                $id_usuario = $_POST['id_usuario'] ?? $_SESSION['id'] ?? null; // Usar POST como primera opción
                $id_recurso = !empty($_POST['id_recurso']) ? $_POST['id_recurso'] : null;

                if (empty($titulo) || empty($fecha_evento) || empty($hora_inicio) || empty($hora_fin) || empty($id_usuario)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados. Usuario: " . ($id_usuario ?? 'no definido'));
                }

                if (strtotime($fecha_evento) < strtotime(date('Y-m-d'))) {
                    throw new Exception("No se puede crear un evento en una fecha pasada");
                }

                if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
                    throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
                }

                $id_evento = $this->reservaModel->crearReserva(
                    $titulo, $descripcion, $fecha_evento, 
                    $hora_inicio, $hora_fin, $id_usuario, $id_recurso
                );

                $response = [
                    'success' => true,
                    'message' => 'Reserva creada exitosamente',
                    'id_evento' => $id_evento
                ];

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['mensaje'] = $response['message'];
                $_SESSION['tipo_mensaje'] = $response['success'] ? 'success' : 'error';
                header('Location: ../Vistas/verReservas.php');
                exit;
            }
        }

        $recursos = [];
        include '../Vistas/crearReserva.php';
    }

    public function editarReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id_evento = $_POST['id_evento'] ?? '';
                $titulo = $_POST['titulo'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $fecha_evento = $_POST['fecha_evento'] ?? '';
                $hora_inicio = $_POST['hora_inicio'] ?? '';
                $hora_fin = $_POST['hora_fin'] ?? '';
                $id_recurso = !empty($_POST['id_recurso']) ? $_POST['id_recurso'] : null;

                if (empty($id_evento) || empty($titulo) || empty($fecha_evento) || empty($hora_inicio) || empty($hora_fin)) {
                    throw new Exception("Todos los campos obligatorios deben ser completados");
                }

                if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
                    throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
                }

                $this->reservaModel->editarReserva(
                    $id_evento, $titulo, $descripcion, 
                    $fecha_evento, $hora_inicio, $hora_fin, $id_recurso
                );

                $response = [
                    'success' => true,
                    'message' => 'Reserva actualizada exitosamente'
                ];

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['mensaje'] = $response['message'];
                $_SESSION['tipo_mensaje'] = $response['success'] ? 'success' : 'error';
                header('Location: ../Vistas/verReservas.php');
                exit;
            }
        }

        $id_evento = $_GET['id'] ?? '';
        if (!$id_evento) {
            header('Location: ../Vistas/verReservas.php');
            exit;
        }

        $evento = $this->reservaModel->obtenerEventoPorId($id_evento);
        $recursos = [];
        include '../Vistas/editarReserva.php';
    }

    public function cancelarReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id_evento = $_POST['id_evento'] ?? '';
                $motivo = $_POST['motivo_cancelacion'] ?? $_POST['motivo'] ?? '';
                $penalidad = $_POST['penalidad'] ?? 0;

                if (empty($id_evento)) {
                    throw new Exception("ID de evento requerido");
                }

                if (empty($motivo)) {
                    throw new Exception("El motivo de cancelación es requerido");
                }

                $this->reservaModel->cancelarReserva($id_evento, $motivo, $penalidad);

                $response = [
                    'success' => true,
                    'message' => 'Reserva cancelada exitosamente'
                ];

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['mensaje'] = $response['message'];
                $_SESSION['tipo_mensaje'] = $response['success'] ? 'success' : 'error';
                header('Location: ../Vistas/verReservas.php');
                exit;
            }
        }

        $id_evento = $_GET['id'] ?? '';
        if (!$id_evento) {
            header('Location: ../Vistas/verReservas.php');
            exit;
        }

        $evento = $this->reservaModel->obtenerEventoPorId($id_evento);
        include '../Vistas/cancelarReserva.php';
    }

    // Cambiar fecha de reserva
    public function cambiarFechaReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id_evento = $_POST['id_evento'] ?? '';
                $nueva_fecha = $_POST['nueva_fecha'] ?? '';
                $nueva_hora_inicio = $_POST['nueva_hora_inicio'] ?? '';
                $nueva_hora_fin = $_POST['nueva_hora_fin'] ?? '';
                $motivo_cambio = $_POST['motivo_cambio'] ?? '';

                if (empty($id_evento) || empty($nueva_fecha) || empty($nueva_hora_inicio) || empty($nueva_hora_fin)) {
                    throw new Exception("Todos los campos son requeridos");
                }

                if (strtotime($nueva_fecha) < strtotime(date('Y-m-d'))) {
                    throw new Exception("No se puede cambiar a una fecha pasada");
                }

                if (strtotime($nueva_hora_fin) <= strtotime($nueva_hora_inicio)) {
                    throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
                }

                $this->reservaModel->cambiarFechaReserva(
                    $id_evento, $nueva_fecha, $nueva_hora_inicio, $nueva_hora_fin, $motivo_cambio
                );

                $response = [
                    'success' => true,
                    'message' => 'Fecha de reserva cambiada exitosamente'
                ];

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $_SESSION['mensaje'] = $response['message'];
                $_SESSION['tipo_mensaje'] = $response['success'] ? 'success' : 'error';
                header('Location: ../Vistas/verReservas.php');
                exit;
            }
        }

        // Cargar vista de cambiar fecha
        $id_evento = $_GET['id'] ?? '';
        if (!$id_evento) {
            header('Location: ../Vistas/verReservas.php');
            exit;
        }

        $evento = $this->reservaModel->obtenerEventoPorId($id_evento);
        include '../Vistas/cambiarFechaReserva.php';
    }

    // Listar reservas con filtros
    public function listarReservas() {
        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'organizador' => $_GET['organizador'] ?? '',
            'titulo' => $_GET['titulo'] ?? ''
        ];

        $reservas = $this->reservaModel->obtenerReservas($filtros);
        include '../Vistas/verReservas.php';
    }

    // Mostrar historial de reservas
    public function mostrarHistorial() {
        // Si no se proporciona id_usuario, usar el de la sesión actual
        $id_usuario = $_GET['id_usuario'] ?? $_SESSION['id'] ?? null;
        $limite = $_GET['limite'] ?? 50;

        $historial = $this->reservaModel->obtenerHistorialReservas($id_usuario, $limite);
        include '../Vistas/historialReservas.php';
    }

    // Mostrar calendario
    public function mostrarCalendario() {
        $mes = $_GET['mes'] ?? date('n');
        $año = $_GET['año'] ?? date('Y');
        // Si no se proporciona id_usuario, usar el de la sesión actual
        $id_usuario = $_GET['id_usuario'] ?? $_SESSION['id'] ?? null;

        $eventos = $this->reservaModel->obtenerCalendarioDisponibilidad($mes, $año, $id_usuario);
        $calendario = Calendario::generarCalendarioMes($mes, $año, $eventos);
        
        include '../Vistas/calendario.php';
    }

    // Verificar disponibilidad (Ajax)
    public function verificarDisponibilidad() {
        header('Content-Type: application/json');
        
        try {
            $fecha = $_GET['fecha'] ?? '';
            $hora_inicio = $_GET['hora_inicio'] ?? '';
            $hora_fin = $_GET['hora_fin'] ?? '';
            $id_usuario = $_GET['id_usuario'] ?? '';
            $id_evento_excluir = $_GET['id_evento_excluir'] ?? null;

            if (empty($fecha) || empty($hora_inicio) || empty($hora_fin) || empty($id_usuario)) {
                throw new Exception("Parámetros faltantes");
            }

            $disponible = $this->reservaModel->verificarDisponibilidad(
                $fecha, $hora_inicio, $hora_fin, $id_usuario, $id_evento_excluir
            );

            echo json_encode([
                'success' => true,
                'disponible' => $disponible,
                'message' => $disponible ? 'Horario disponible' : 'Horario no disponible'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Obtener evento por ID (Ajax)
    public function obtenerEvento() {
        header('Content-Type: application/json');
        
        try {
            $id_evento = $_GET['id'] ?? '';
            
            if (empty($id_evento)) {
                throw new Exception("ID de evento requerido");
            }

            $evento = $this->reservaModel->obtenerEventoPorId($id_evento);
            
            if (!$evento) {
                throw new Exception("Evento no encontrado");
            }

            echo json_encode([
                'success' => true,
                'evento' => $evento
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}

// Inicializar controlador si es llamado directamente
if (basename($_SERVER['PHP_SELF']) == 'ReservaController.php') {
    session_start();
    $controller = new ReservaController();
    $controller->manejarAccion();
}

?>