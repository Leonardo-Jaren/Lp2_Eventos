<?php
require_once '../Controlador/UsuarioController.php';

$id = $_GET['id'] ?? null;

if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioController = new UsuarioController();
    $mensaje = $usuarioController->eliminarUsuario($id);
    if ($mensaje === true) {
        header("Location: verUsuarios.php");
        exit();
    } else {
        echo '<div class="modal-overlay">
                <div class="modal-box">
                    <h2 class="modal-title error">Error al eliminar</h2>
                    <p class="modal-text">'.htmlspecialchars($mensaje).'</p>
                    <a href="verUsuarios.php" class="modal-btn blue">Volver</a>
                </div>
              </div>
              <style>
                .modal-overlay {
                    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                    background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;
                }
                .modal-box {
                    background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); text-align: center; min-width: 320px;
                }
                .modal-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem; }
                .modal-title.error { color: #d32f2f; }
                .modal-text { margin-bottom: 1.5rem; color: #333; }
                .modal-btn {
                    display: inline-block; padding: 0.5rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold; border: none; cursor: pointer;
                }
                .modal-btn.blue { background: #1976d2; color: #fff; }
                .modal-btn.blue:hover { background: #115293; }
              </style>';
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Usuario</title>
    <style>
        body { background: #f3f3f3; margin: 0; }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;
        }
        .modal-box {
            background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); text-align: center; min-width: 320px;
        }
        .modal-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem; }
        .modal-title.danger { color: #d32f2f; }
        .modal-text { margin-bottom: 1.5rem; color: #333; }
        .modal-btn {
            display: inline-block; padding: 0.5rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; margin: 0 0.5rem;
        }
        .modal-btn.gray { background: #e0e0e0; color: #333; }
        .modal-btn.gray:hover { background: #bdbdbd; }
        .modal-btn.red { background: #d32f2f; color: #fff; }
        .modal-btn.red:hover { background: #b71c1c; }
        .modal-icon {
            width: 56px; height: 56px; margin-bottom: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="modal-overlay">
    <div class="modal-box">
        <svg class="modal-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="#d32f2f" stroke-width="2"/>
            <line x1="12" y1="8" x2="12" y2="12" stroke="#d32f2f" stroke-width="2" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1" fill="#d32f2f"/>
        </svg>
        <h2 class="modal-title danger">¿Eliminar Usuario?</h2>
        <p class="modal-text">Esta acción no se puede deshacer.<br>¿Estás seguro de que deseas eliminar este usuario?</p>
        <form method="POST" style="display: flex; justify-content: center;">
            <a href="verUsuarios.php" class="modal-btn gray">Cancelar</a>
            <button type="submit" class="modal-btn red">Eliminar</button>
        </form>
    </div>
</div>
</body>
</html>