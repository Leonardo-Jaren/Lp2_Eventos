<?php

require_once '../Controlador/ProveedorController.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticacion/Vista/login.php");
    exit();
}
$id = $_GET["id"];
$proveedor_controller = new ProveedorController();
$proveedor_controller->eliminarProveedor($id);