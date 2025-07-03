<?php
// Ubicación: Proveedores/Controlador/ProveedorController.php

// Iniciar la sesión para poder usar variables de sesión para los mensajes.
session_start();

// Incluir los modelos necesarios
require_once __DIR__ . '/../Modelos/Proveedor.php';
require_once __DIR__ . '/../Modelos/CatalogoServicios.php';

class ProveedorController {

    /**
     * Muestra la lista principal de todos los proveedores.
     * Acción: index
     */
    public function index() {
        $proveedores = Proveedor::mostrar(); // Llama al método estático del modelo
        require __DIR__ . '/../Vistas/verProveedores.php'; // Carga la vista y le pasa los datos
    }

    /**
     * Muestra el formulario para crear un nuevo proveedor.
     * Acción: crear
     */
    public function crear() {
        require __DIR__ . '/../Vistas/crearProveedor.php';
    }

    /**
     * Procesa los datos del formulario de creación y los guarda en la BD.
     * Acción: guardar
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación simple
            if (empty($_POST['nombre_empresa']) || empty($_POST['id_usuario'])) {
                $_SESSION['error_message'] = "El nombre de la empresa y el ID de usuario son obligatorios.";
                header('Location: index.php?module=Proveedores&action=crear');
                exit();
            }
            
            $proveedor = new Proveedor();
            
            // Usamos los métodos 'establecer' del modelo
            $proveedor->setIdUsuario($_POST['id_usuario']);
            $proveedor->setNombreEmpresa($_POST['nombre_empresa']);
            $proveedor->setDescripcion($_POST['descripcion']);
            $proveedor->setDireccion($_POST['direccion']);
            
            if ($proveedor->guardar()) {
                header('Location: index.php?module=Proveedores&action=index');
            } else {
                $_SESSION['error_message'] = "Error al guardar el proveedor en la base de datos.";
                header('Location: index.php?module=Proveedores&action=crear');
            }
            exit();
        }
    }

    /**
     * Muestra el formulario para editar un proveedor existente.
     * Acción: editar
     */
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) die('Error: ID no proporcionado.');
        
        $proveedor = new Proveedor();
        if ($proveedor->encontrar($id)) {
            // Si el proveedor se encuentra, carga la vista de edición con sus datos
            require __DIR__ . '/../Vistas/editarProveedor.php';
        } else {
            echo "Proveedor no encontrado.";
        }
    }

    /**
     * Procesa los datos del formulario de edición y actualiza la BD.
     * Acción: actualizar
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_proveedor'] ?? null;
            if (!$id) die('Error: ID no proporcionado.');
            
            $proveedor = new Proveedor();
            $proveedor->encontrar($id); // Carga el proveedor existente
            
            // Actualiza el objeto con los datos del formulario
            $proveedor->setIdUsuario($_POST['id_usuario']);
            $proveedor->setNombreEmpresa($_POST['nombre_empresa']);
            $proveedor->setDescripcion($_POST['descripcion']);
            $proveedor->setDireccion($_POST['direccion']);

            if ($proveedor->guardar()) {
                header('Location: index.php?module=Proveedores&action=index');
            } else {
                $_SESSION['error_message'] = "Error al actualizar el proveedor.";
                header('Location: index.php?module=Proveedores&action=editar&id=' . $id);
            }
            exit();
        }
    }

    /**
     * Elimina un proveedor de la base de datos.
     * Acción: eliminar
     */
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if (!$id) die('Error: ID no proporcionado.');

        if (Proveedor::eliminar($id)) {
            header('Location: index.php?module=Proveedores&action=index');
        } else {
            echo "Error al eliminar el proveedor.";
        }
        exit();
    }

    /**
     * Muestra el catálogo de servicios de un proveedor.
     * Acción: verCatalogo
     */
    public function verCatalogo() {
        $id = $_GET['id'] ?? null;
        if (!$id) die('Error: ID no proporcionado.');

        $proveedor = new Proveedor();
        $proveedor->encontrar($id);
        
        $servicios = CatalogoServicios::buscarPorProveedor($id);
        
        require __DIR__ . '/../Vistas/verCatalogo.php';
    }
    
    /**
     * Procesa la decisión de un proveedor de participar o no en un evento.
     * Acción: confirmarParticipacion
     */
    public function confirmarParticipacion() {
        // Incluir el nuevo modelo
        require_once __DIR__ . '/../Modelos/EventoProveedor.php';

        // Obtener los datos de la URL
        $id_reserva = $_GET['id_reserva'] ?? null;
        $id_servicio = $_GET['id_servicio'] ?? null;
        $decision = $_GET['decision'] ?? null;

        // Validar que los datos necesarios estén presentes
        if (!$id_reserva || !$id_servicio || !$decision) {
            $_SESSION['participacion_mensaje'] = 'Error: Faltan datos para procesar la solicitud.';
            $_SESSION['participacion_estado'] = 'error';
            header('Location: index.php?module=Proveedores&action=mostrarConfirmacion');
            exit();
        }

        // Llamar al modelo para actualizar la base de datos
        $exito = EventoProveedor::actualizarEstado($id_reserva, $id_servicio, $decision);

        if ($exito) {
            $_SESSION['participacion_mensaje'] = "¡Gracias! Tu decisión de '" . htmlspecialchars($decision) . "' ha sido registrada correctamente.";
            $_SESSION['participacion_estado'] = 'exito';
        } else {
            $_SESSION['participacion_mensaje'] = "Error: No se pudo registrar tu decisión. Es posible que el enlace no sea válido o ya haya sido utilizado.";
            $_SESSION['participacion_estado'] = 'error';
        }
        
        // Redirigir a una vista de confirmación genérica
        header('Location: index.php?module=Proveedores&action=mostrarConfirmacion');
        exit();
    }

    /**
     * Muestra la página de resultado después de que un proveedor confirma o rechaza.
     * Acción: mostrarConfirmacion
     */
    public function mostrarConfirmacion() {
        require __DIR__ . '/../Vistas/confirmarParticipacion.php';
    }

    /**
     * Prepara y fuerza la descarga de un PDF con la lista de reservas de un proveedor.
     * Acción: descargarPdf
     */
    public function descargarPdf() {
        $id_proveedor = $_GET['id'] ?? null;
        if (!$id_proveedor) {
            die('Error: ID de proveedor no proporcionado para el PDF.');
        }

        // Incluir la librería FPDF
        require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

        // Obtener los datos del proveedor para el título del PDF
        $proveedor = new Proveedor();
        if (!$proveedor->encontrar($id_proveedor)) {
            die('Error: Proveedor no encontrado.');
        }
        $nombre_empresa = $proveedor->getNombreEmpresa();

        // Obtener las reservas del proveedor
        $reservas = Proveedor::obtenerReservasPorProveedor($id_proveedor);

        // Crear una nueva instancia de FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Título del PDF
        $pdf->Cell(0, 10, utf8_decode('Reservas del Proveedor: ' . $nombre_empresa), 0, 1, 'C');
        $pdf->Ln(10);

        // Cabecera de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(50, 7, utf8_decode('Evento'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode('Fecha'), 1, 0, 'C', true);
        $pdf->Cell(40, 7, utf8_decode('Lugar'), 1, 0, 'C', true);
        $pdf->Cell(40, 7, utf8_decode('Servicio'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, utf8_decode('Estado'), 1, 1, 'C', true);

        // Datos de la tabla
        $pdf->SetFont('Arial', '', 8);
        foreach ($reservas as $reserva) {
            $pdf->Cell(50, 6, utf8_decode($reserva['nombre_evento']), 1);
            $pdf->Cell(30, 6, utf8_decode(date('d/m/Y', strtotime($reserva['fecha_evento']))), 1);
            $pdf->Cell(40, 6, utf8_decode($reserva['lugar']), 1);
            $pdf->Cell(40, 6, utf8_decode($reserva['nombre_servicio']), 1);
            $pdf->Cell(30, 6, utf8_decode($reserva['estado_participacion']), 1);
            $pdf->Ln();
        }

        // Salida del PDF (forzar descarga)
        $pdf->Output('D', utf8_decode('reservas_' . $nombre_empresa . '.pdf'));
        exit();
    }
}