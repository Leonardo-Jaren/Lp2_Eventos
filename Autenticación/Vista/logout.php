<?php
session_start();
session_unset();
$_SESSION = array();
session_destroy();
header("Location: ../../Autenticación/Vista/login.php");
exit();