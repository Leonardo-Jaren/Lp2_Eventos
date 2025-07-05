<?php

require_once '../Modelos/Proveedor.php';
require_once '../Modelos/CatalogoServicios.php';

/**
 * Controlador para gestionar todas las operaciones del Módulo de Proveedores.
 * Este controlador sigue un enfoque de Clase con métodos para cada acción.
 */
class ProveedorController {

    /**
     * Obtiene la lista de todos los proveedores.
     * @return array Un array con todos los proveedores.
     */
    public function listar() {
        // Llama al método estático del modelo para obtener los datos.
        // ACTUALIZADO: El nombre del método ahora es obtenerTodosLosProveedores.
        return Proveedor::obtenerTodosLosProveedores();
    }

    /**
     * Busca un proveedor específico por su ID.
     * @param int $id El ID del proveedor a buscar.
     * @return mixed Un array con los datos del proveedor o false si no se encuentra.
     */
    public function buscarPorId(int $id) {
        // ACTUALIZADO: El método no es estático, se crea una instancia.
        $proveedor = new Proveedor();
        return $proveedor->encontrarProveedor($id);
    }

    /**
     * Procesa los datos para guardar un nuevo proveedor.
     * @param array $datos Los datos del proveedor enviados desde un formulario.
     */
    public function guardar(array $datos) {
        // ACTUALIZADO: Se crea una instancia y se llama a guardarProveedor con parámetros individuales.
        $proveedor = new Proveedor();
        
        $id_usuario = null;
        if (isset($datos['id_usuario']) && !empty(trim($datos['id_usuario']))) {
            $id_usuario = (int)$datos['id_usuario'];
        }
        
        if ($id_usuario && $proveedor->existeProveedorParaUsuario($id_usuario)) {
            return 'Ya existe un proveedor registrado para este usuario. Un usuario solo puede tener un proveedor asociado.';
        }
        
        $resultado = $proveedor->guardarProveedor(
            $datos['nombre_empresa'],
            $datos['telefono'],
            $datos['direccion'],
            $id_usuario
        );
        
        // Se maneja la respuesta del modelo.
        if ($resultado['success']) {
            header('Location: ../views/proveedores/verProveedores.php?status=guardado');
        } else {
            // Se puede pasar el error por la URL para mostrarlo en la vista.
            header('Location: ../views/proveedores/crearProveedor.php?error=' . urlencode($resultado['message']));
        }
        exit();
    }

    /**
     * Procesa los datos para actualizar un proveedor existente.
     * @param int $id_proveedor El ID del proveedor a actualizar.
     * @param array $datos Los nuevos datos del proveedor.
     */
    public function actualizar(int $id_proveedor, array $datos) {
        // ACTUALIZADO: Se crea una instancia y se llama a actualizarProveedor.
        $proveedor = new Proveedor();
        
        $id_usuario = null;
        if (isset($datos['id_usuario']) && !empty(trim($datos['id_usuario']))) {
            $id_usuario = (int)$datos['id_usuario'];
        }
        
        $resultado = $proveedor->actualizarProveedor(
            $datos['id'],
            $datos['nombre_empresa'],
            $datos['telefono'],
            $datos['direccion'],
            $id_usuario
        );
        
        if ($resultado['success']) {
            header('Location: ../views/proveedores/verProveedores.php?status=actualizado');
        } else {
            header('Location: ../views/proveedores/editarProveedor.php?id=' . $id_proveedor . '&error=' . urlencode($resultado['message']));
        }
        exit();
    }

    public function eliminarProveedor($id) {
        $proveedor = new Proveedor();
        $resultado = $proveedor->eliminarProveedor($id);
        $id = $_GET['id'] ?? null;
        if ($resultado) {
            header('Location: verProveedor.php');
            exit();
        } else {
            return "Error al eliminar el proveedor.";
        }
    }
}
?>