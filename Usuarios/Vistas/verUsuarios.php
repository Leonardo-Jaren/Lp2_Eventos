<?php
require_once '../../conexion_db.php';
require_once '../../layouts/header.php';
require_once '../Modelos/Usuario.php';

$usuarioModel = new Usuario();
$usuarios = $usuarioModel->obtenerTodosLosUsuarios();
?>

<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Lista de Usuarios</h2>
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Nombre</th>
                <th class="py-2 px-4 border-b">Correo</th>
                <th class="py-2 px-4 border-b">Rol</th>
                <th class="py-2 px-4 border-b">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($usuario['id']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($usuario['correo']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($usuario['rol']); ?></td>
                <td class="py-2 px-4 border-b">
                    <a href="actualizarusuario.php?id=<?php echo $usuario['id']; ?>" class="text-blue-600 hover:underline">Editar</a>
                    <a href="eliminarUsuario.php?id=<?php echo $usuario['id']; ?>" class="text-red-600 hover:underline ml-2">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>