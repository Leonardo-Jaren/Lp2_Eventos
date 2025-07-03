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
     * Muestra una página de confirmación de participación.
     * Acción: confirmarParticipacion
     */
    public function confirmarParticipacion() {
        // En una aplicación real, aquí obtendrías los datos de la URL o un formulario
        $decision = $_GET['decision'] ?? 'indefinida';
        $mensaje = "Tu decisión ha sido registrada como: " . htmlspecialchars($decision);
        
        // Aquí iría la lógica para llamar a un modelo y actualizar la tabla 'evento_proveedores'
        // Ejemplo: EventoProveedor::actualizarEstado($id_reserva, $id_servicio, $decision);
        
        require __DIR__ . '/../Vistas/confirmarParticipacion.php';
    }

    /**
     * Prepara y fuerza la descarga de un PDF con la lista de reservas de un proveedor.
     * Acción: descargarPdf
     */
    public function descargarPdf() {
        $id_proveedor = $_GET['id'] ?? null;
        if (!$id_proveedor) die('Error: ID no proporcionado.');
        
        echo "Lógica para generar el PDF de reservas del proveedor con ID: $id_proveedor. Necesitarás una librería como FPDF o TCPDF.";
        
        // --- CÓDIGO DE EJEMPLO PARA FPDF ---
        // 1. Incluir la librería: require_once __DIR__ . '/../../lib/fpdf/fpdf.php';
        // 2. Obtener datos de la BD: $reservas = ...;
        // 3. Crear el objeto PDF: $pdf = new FPDF();
        // 4. Añadir página, fuentes y celdas con los datos: $pdf->AddPage(); ...
        // 5. Forzar la descarga: $pdf->Output('D', 'reservas.pdf');
    }
}