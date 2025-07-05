<?php
// nav.php - Navegación principal del sistema
$titulo_pagina = $titulo_pagina ?? 'Plannea - Sistema para Gestión de Eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Alpine.js para interactividad -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#6b7280',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        info: '#06b6d4'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Navigation -->
<nav class="bg-blue-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <i class="fas fa-calendar-alt text-2xl"></i>
                <div>
                    <h1 class="text-xl font-bold">Plannea</h1>
                    <?php if (isset($titulo_pagina) && $titulo_pagina !== 'Plannea - Sistema para Gestión de Eventos'): ?>
                        <p class="text-blue-200 text-sm"><?php echo htmlspecialchars($titulo_pagina); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-4">
                    <a href="/Lp2_Eventos/dashboard.php" class="hover:bg-blue-700 px-3 py-2 rounded transition duration-200">
                        <i class="fas fa-home mr-1"></i> Inicio
                    </a>
                    <a href="/Lp2_Eventos/Reserva/Vistas/verEventos.php" class="hover:bg-blue-700 px-3 py-2 rounded transition duration-200">
                        <i class="fas fa-calendar mr-1"></i> Eventos
                    </a>
                    <a href="/Lp2_Eventos/Reserva/Vistas/historialEventos.php" class="hover:bg-blue-700 px-3 py-2 rounded transition duration-200">
                        <i class="fas fa-history mr-1"></i> Historial
                    </a>
                </div>

                <?php if (isset($_SESSION['id'])): ?>
                    <?php
                    // Obtener información del usuario si no está ya disponible
                    if (!isset($usuario)) {
                        try {
                            require_once 'conexion_db.php';
                            $conexion = new ConexionDB();
                            $conn = $conexion->conectar();
                            $sqlUser = "SELECT u.nombres, u.correo, r.nombre as rol FROM usuarios u 
                                           LEFT JOIN roles r ON u.id_rol = r.id 
                                           WHERE u.id = ?";
                            $stmt = $conn->prepare($sqlUser);
                            $stmt->execute([$_SESSION['id']]);
                            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {
                            $usuario = ['nombres' => 'Usuario', 'rol' => 'Sistema'];
                        }
                    }
                    ?>
                    <div class="flex items-center space-x-4 border-l border-blue-500 pl-4">
                        <div class="text-right">
                            <p class="text-xs text-blue-200">Conectado como:</p>
                            <p class="font-medium text-white"><?php echo htmlspecialchars($usuario['nombres'] ?? 'Usuario'); ?></p>
                            <p class="text-xs text-blue-300"><?php echo htmlspecialchars($usuario['rol'] ?? 'Sistema'); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="/Lp2_Eventos/Usuarios/Vistas/Perfil/perfilUsuario.php"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md transition duration-200 flex items-center">
                                <i class="fas fa-user mr-1"></i> Mi Perfil
                            </a>
                            <a href="/Lp2_Eventos/Autenticación/Vista/logout.php"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md transition duration-200 flex items-center">
                                <i class="fas fa-sign-out-alt mr-1"></i> Salir
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="py-6">