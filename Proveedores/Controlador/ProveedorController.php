<?php
// Incluir los modelos necesarios una sola vez al principio.
require_once __DIR__ . '/../Modelos/Proveedor.php';
require_once __DIR__ . '/../Modelos/CatalogoServicios.php';

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
        $resultado = $proveedor->guardarProveedor(
            $datos['nombre'],
            $datos['correo'],
            $datos['empresa']
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
        $resultado = $proveedor->actualizarProveedor(
            $id_proveedor,
            $datos['nombre'],
            $datos['correo'],
            $datos['empresa']
        );
        
        if ($resultado['success']) {
            header('Location: ../views/proveedores/verProveedores.php?status=actualizado');
        } else {
            header('Location: ../views/proveedores/editarProveedor.php?id=' . $id_proveedor . '&error=' . urlencode($resultado['message']));
        }
        exit();
    }

    /**
     * Elimina un proveedor.
     * @param int $id_proveedor El ID del proveedor a eliminar.
     */
    public function eliminar(int $id_proveedor) {
        // ACTUALIZADO: Se llama al método estático eliminarProveedor y se quita el id_usuario.
        if ($id_proveedor > 0) {
            Proveedor::eliminarProveedor($id_proveedor);
        }
        
        // Redirige a la lista después de eliminar.
        header('Location: ../views/proveedores/verProveedores.php?status=eliminado');
        exit();
    }

    /**
     * Obtiene los servicios del catálogo de un proveedor específico.
     * @param int $id_proveedor El ID del proveedor.
     * @return array Un array con los servicios del proveedor.
     */
    public function verCatalogo(int $id_proveedor) {
        // Se mantiene la lógica, pero se podría ajustar al modelo de Catalogo si fuera necesario.
        return CatalogoServicios::obtenerPorProveedor($id_proveedor);
    }
}
?>