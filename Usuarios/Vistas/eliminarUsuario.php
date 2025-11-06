<?php
require_once '../Controlador/UsuarioController.php';

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticacion/Vista/login.php");
    exit();
}

$id = $_GET["id"];
$uc = new UsuarioController();
$uc->eliminarUsuario($id);
