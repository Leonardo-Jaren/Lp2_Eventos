<?php
// Controlador de Recursos - Estructura básica
// Este archivo será completado por otro desarrollador

class RecursoController {
    // Implementación pendiente
}

?>
                $this->editarRecurso();
                break;
            case 'eliminar':
                $this->eliminarRecurso();
                break;
            case 'cambiar_disponibilidad':
                $this->cambiarDisponibilidad();
                break;
            case 'verificar_disponibilidad':
                $this->verificarDisponibilidad();
                break;
            case 'obtener_recurso':
                $this->obtenerRecurso();
                break;
            case 'mas_utilizados':
                $this->recursosMasUtilizados();
                break;
            case 'listar':
            default:
                $this->listarRecursos();
        }
    }

    // Crear nuevo recurso
    public function crearRecurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $tipo = trim($_POST['tipo'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $disponible = isset($_POST['disponible']) ? 1 : 0;

                // Validaciones
                if (empty($tipo)) {
                    throw new Exception("El tipo de recurso es obligatorio");
                }

                if (strlen($tipo) > 50) {
                    throw new Exception("El tipo de recurso no puede exceder 50 caracteres");
                }

                $id_recurso = $this->recursoModel->crearRecurso($tipo, $descripcion, $disponible);

                if ($id_recurso) {
                    $response = [
                        'success' => true,
                        'message' => 'Recurso creado exitosamente',
                        'id' => $id_recurso
                    ];
                } else {
                    throw new Exception("Error al crear el recurso");
                }

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
                header('Location: ../Vistas/verRecursos.php');
                exit;
            }
        }

        // Cargar vista de crear recurso
        include '../Vistas/crearRecurso.php';
    }

    // Editar recurso existente
    public function editarRecurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? '';
                $tipo = trim($_POST['tipo'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $disponible = isset($_POST['disponible']) ? 1 : 0;

                // Validaciones
                if (empty($id) || empty($tipo)) {
                    throw new Exception("ID y tipo de recurso son obligatorios");
                }

                if (strlen($tipo) > 50) {
                    throw new Exception("El tipo de recurso no puede exceder 50 caracteres");
                }

                $resultado = $this->recursoModel->editarRecurso($id, $tipo, $descripcion, $disponible);

                if ($resultado) {
                    $response = [
                        'success' => true,
                        'message' => 'Recurso actualizado exitosamente'
                    ];
                } else {
                    throw new Exception("Error al actualizar el recurso");
                }

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
                header('Location: ../Vistas/verRecursos.php');
                exit;
            }
        }

        // Cargar vista de editar recurso
        $id = $_GET['id'] ?? '';
        if (!$id) {
            header('Location: ../Vistas/verRecursos.php');
            exit;
        }

        $recurso = $this->recursoModel->obtenerRecursoPorId($id);
        if (!$recurso) {
            $_SESSION['mensaje'] = 'Recurso no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: ../Vistas/verRecursos.php');
            exit;
        }

        include '../Vistas/editarRecurso.php';
    }

    // Eliminar recurso
    public function eliminarRecurso() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? '';

                if (empty($id)) {
                    throw new Exception("ID del recurso es obligatorio");
                }

                $resultado = $this->recursoModel->eliminarRecurso($id);

                if ($resultado) {
                    $response = [
                        'success' => true,
                        'message' => 'Recurso eliminado exitosamente'
                    ];
                } else {
                    throw new Exception("Error al eliminar el recurso");
                }

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
                header('Location: ../Vistas/verRecursos.php');
                exit;
            }
        }

        // Cargar vista de confirmar eliminación
        $id = $_GET['id'] ?? '';
        if (!$id) {
            header('Location: ../Vistas/verRecursos.php');
            exit;
        }

        $recurso = $this->recursoModel->obtenerRecursoPorId($id);
        if (!$recurso) {
            $_SESSION['mensaje'] = 'Recurso no encontrado';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: ../Vistas/verRecursos.php');
            exit;
        }

        include '../Vistas/eliminarRecurso.php';
    }

    // Listar recursos
    public function listarRecursos() {
        $filtros = [
            'tipo' => $_GET['tipo'] ?? '',
            'disponible' => $_GET['disponible'] ?? ''
        ];

        $recursos = $this->recursoModel->obtenerTodosRecursos($filtros);
        include '../Vistas/verRecursos.php';
    }

    // Cambiar disponibilidad
    public function cambiarDisponibilidad() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? '';
                $disponible = $_POST['disponible'] ?? '';

                if (empty($id) || $disponible === '') {
                    throw new Exception("Datos incompletos");
                }

                $resultado = $this->recursoModel->cambiarDisponibilidad($id, $disponible);

                $response = [
                    'success' => $resultado,
                    'message' => $resultado ? 'Disponibilidad actualizada' : 'Error al actualizar disponibilidad'
                ];

            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // Verificar disponibilidad de recurso
    public function verificarDisponibilidad() {
        try {
            $id_recurso = $_GET['id_recurso'] ?? '';
            $fecha = $_GET['fecha'] ?? '';
            $hora_inicio = $_GET['hora_inicio'] ?? '';
            $hora_fin = $_GET['hora_fin'] ?? '';
            $id_evento_excluir = $_GET['id_evento_excluir'] ?? null;

            if (empty($id_recurso) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {
                throw new Exception("Datos incompletos para verificar disponibilidad");
            }

            $disponible = $this->recursoModel->verificarDisponibilidadRecurso(
                $id_recurso, $fecha, $hora_inicio, $hora_fin, $id_evento_excluir
            );

            $response = [
                'success' => true,
                'disponible' => $disponible,
                'message' => $disponible ? 'Recurso disponible' : 'Recurso no disponible en esa fecha y hora'
            ];

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Obtener datos de un recurso específico
    public function obtenerRecurso() {
        try {
            $id = $_GET['id'] ?? '';

            if (empty($id)) {
                throw new Exception("ID del recurso es obligatorio");
            }

            $recurso = $this->recursoModel->obtenerRecursoPorId($id);

            if (!$recurso) {
                throw new Exception("Recurso no encontrado");
            }

            $response = [
                'success' => true,
                'recurso' => $recurso
            ];

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Obtener recursos más utilizados
    public function recursosMasUtilizados() {
        try {
            $limite = $_GET['limite'] ?? 10;
            $recursos = $this->recursoModel->obtenerRecursosMasUtilizados($limite);

            $response = [
                'success' => true,
                'recursos' => $recursos
            ];

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) == 'RecursoController.php') {
    session_start();
    $controller = new RecursoController();
    $controller->manejarAccion();
}

?>
