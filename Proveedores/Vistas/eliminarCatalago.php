<?php

require_once '../Controlador/CatalogoServiciosController.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticacion/Vista/login.php");
    exit();
}

$id_servicio = $_GET['id'] ?? 0;
$id_proveedor = $_GET['id_proveedor'] ?? null;

$catalogoController = new CatalogoServiciosController();
$resultado = $catalogoController->eliminarServicio($id_servicio, $id_proveedor);

if ($resultado) {
    echo $resultado;
}