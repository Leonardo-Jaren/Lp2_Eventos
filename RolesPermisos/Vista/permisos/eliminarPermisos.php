<?php

require_once '../../Controlador/PermisosController.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}
$id = $_GET["id"];
$permisosController = new PermisosController();
$permisosController->eliminarPermiso($id);