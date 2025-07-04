<?php
session_start();
require_once __DIR__ . '/../Modelos/Reserva.php';
require_once __DIR__ . '/../Modelos/Calendario.php';

class ReservaController {
    private $reservaModel;

    public function __construct() {
        $this->reservaModel = new Reserva();
    }

    public function manejarAccion() {
        $accion = $_GET['accion'] ?? 'listar';
        switch ($accion) {
            case 'crear': $this->crearReserva(); break;
            case 'editar': $this->editarReserva(); break;
            case 'cancelar': $this->cancelarReserva(); break;
            case 'cambiar_fecha': $this->cambiarFechaReserva(); break;
            case 'historial': $this->mostrarHistorial(); break;
            case 'calendario': $this->mostrarCalendario(); break;
            case 'verificar_disponibilidad': $this->verificarDisponibilidad(); break;
            case 'obtener_evento': $this->obtenerEvento(); break;
            default: $this->listarReservas();
        }
    }

    public function crearReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $datos = $this->validarDatosReserva($_POST);
                $id_evento = $this->reservaModel->crearReserva($datos['titulo'], $datos['descripcion'], 
                    $datos['fecha_evento'], $datos['hora_inicio'], $datos['hora_fin'], 
                    $datos['id_usuario'], $datos['id_recurso']);
                $this->responder(true, 'Reserva creada exitosamente', $id_evento);
            } catch (Exception $e) {
                $this->responder(false, $e->getMessage());
            }
        } else {
            $recursos = [];
            include '../Vistas/crearReserva.php';
        }
    }

    public function editarReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $datos = $this->validarDatosReserva($_POST);
                $id_evento = $_POST['id_evento'] ?? '';
                if (empty($id_evento)) throw new Exception("ID de evento requerido");
                $this->reservaModel->editarReserva($id_evento, $datos['titulo'], $datos['descripcion'], 
                    $datos['fecha_evento'], $datos['hora_inicio'], $datos['hora_fin'], $datos['id_recurso']);
                $this->responder(true, 'Reserva actualizada exitosamente');
            } catch (Exception $e) {
                $this->responder(false, $e->getMessage());
            }
        } else {
            $evento = $this->obtenerEventoORedireccionar();
            $recursos = [];
            include '../Vistas/editarReserva.php';
        }
    }

    public function cancelarReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id_evento = $_POST['id_evento'] ?? '';
                if (empty($id_evento)) throw new Exception("ID de evento requerido");
                $this->reservaModel->cancelarReserva($id_evento, $_POST['motivo'] ?? '');
                $this->responder(true, 'Reserva cancelada exitosamente');
            } catch (Exception $e) {
                $this->responder(false, $e->getMessage());
            }
        } else {
            $evento = $this->obtenerEventoORedireccionar();
            include '../Vistas/cancelarReserva.php';
        }
    }

    public function cambiarFechaReserva() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->validarCamposRequeridos($_POST, ['id_evento', 'nueva_fecha', 'nueva_hora_inicio', 'nueva_hora_fin']);
                $this->reservaModel->cambiarFechaReserva($_POST['id_evento'], $_POST['nueva_fecha'], 
                    $_POST['nueva_hora_inicio'], $_POST['nueva_hora_fin'], $_POST['motivo'] ?? '');
                $this->responder(true, 'Fecha cambiada exitosamente');
            } catch (Exception $e) {
                $this->responder(false, $e->getMessage());
            }
        } else {
            $evento = $this->obtenerEventoORedireccionar();
            include '../Vistas/cambiarFechaReserva.php';
        }
    }

    public function listarReservas() {
        $filtros = $_GET;
        $reservas = $this->reservaModel->obtenerReservas($filtros);
        include '../Vistas/verReservas.php';
    }

    public function mostrarHistorial() {
        $id_usuario = $_GET['id_usuario'] ?? $_SESSION['id'] ?? null;
        $limite = $_GET['limite'] ?? 50;
        $historial = $this->reservaModel->obtenerHistorialReservas($id_usuario, $limite);
        include '../Vistas/historialReservas.php';
    }

    public function mostrarCalendario() {
        $mes = $_GET['mes'] ?? date('n');
        $año = $_GET['año'] ?? date('Y');
        $id_usuario = $_GET['id_usuario'] ?? $_SESSION['id'] ?? null;
        $eventos = $this->reservaModel->obtenerCalendarioDisponibilidad($mes, $año, $id_usuario);
        $calendario = Calendario::generarCalendarioMes($mes, $año, $eventos);
        include '../Vistas/calendario.php';
    }

    public function verificarDisponibilidad() {
        header('Content-Type: application/json');
        try {
            $this->validarCamposRequeridos($_GET, ['fecha', 'hora_inicio', 'hora_fin']);
            $id_usuario = $_GET['id_usuario'] ?? $_SESSION['id'] ?? '';
            if (empty($id_usuario)) throw new Exception("Usuario requerido");
            $disponible = $this->reservaModel->verificarDisponibilidad($_GET['fecha'], $_GET['hora_inicio'], 
                $_GET['hora_fin'], $id_usuario, $_GET['id_evento_excluir'] ?? null);
            echo json_encode(['success' => true, 'disponible' => $disponible]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function obtenerEvento() {
        header('Content-Type: application/json');
        try {
            $id_evento = $_GET['id_evento'] ?? '';
            if (empty($id_evento)) throw new Exception("ID de evento requerido");
            $evento = $this->reservaModel->obtenerEventoPorId($id_evento);
            echo json_encode(['success' => true, 'evento' => $evento]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function obtenerEventoORedireccionar() {
        $id_evento = $_GET['id'] ?? '';
        if (!$id_evento) {
            header('Location: ../Vistas/verReservas.php');
            exit;
        }
        return $this->reservaModel->obtenerEventoPorId($id_evento);
    }

    private function validarCamposRequeridos($datos, $campos) {
        foreach ($campos as $campo) {
            if (empty($datos[$campo])) throw new Exception("Todos los campos son requeridos");
        }
    }

    private function validarDatosReserva($datos) {
        $this->validarCamposRequeridos($datos, ['titulo', 'fecha_evento', 'hora_inicio', 'hora_fin']);
        if (strtotime($datos['fecha_evento']) < strtotime(date('Y-m-d'))) {
            throw new Exception("No se puede crear un evento en una fecha pasada");
        }
        if (strtotime($datos['hora_fin']) <= strtotime($datos['hora_inicio'])) {
            throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
        }
        return [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'] ?? '',
            'fecha_evento' => $datos['fecha_evento'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_fin' => $datos['hora_fin'],
            'id_usuario' => $datos['id_usuario'] ?? $_SESSION['id'] ?? '',
            'id_recurso' => !empty($datos['id_recurso']) ? $datos['id_recurso'] : null
        ];
    }

    private function responder($success, $mensaje, $id_evento = null) {
        $response = ['success' => $success, 'message' => $mensaje];
        if ($id_evento) $response['id_evento'] = $id_evento;
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $_SESSION['mensaje'] = $mensaje;
            $_SESSION['tipo_mensaje'] = $success ? 'success' : 'error';
            header('Location: ../Vistas/verReservas.php');
        }
        exit;
    }
}

$controller = new ReservaController();
$controller->manejarAccion();
?>