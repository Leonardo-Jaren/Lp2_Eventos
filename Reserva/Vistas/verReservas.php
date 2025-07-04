<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: /Lp2_Eventos/Autenticación/Vista/login.php");
    exit();
}

$titulo_pagina = "Ver Reservas";
require_once '../../nav.php';

require_once '../Controlador/ReservaController.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Procesar búsqueda
$buscar = $_GET['buscar'] ?? '';

$reservaController = new ReservaController();

// Aplicar búsqueda si existe
if (!empty($buscar)) {
    $filtros = ['buscar' => $buscar];
    $reservas = $reservaController->obtenerConFiltros($filtros);
} else {
    $reservas = $reservaController->mostrar();
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Reservas</h1>
                <a href="crearReserva.php" 
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Reserva
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($mensaje)): ?>
            <div class="mb-6 p-4 rounded-md <?php echo $tipo_mensaje === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-green-100 text-green-700 border border-green-300'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Búsqueda simple -->
        <div class="mb-6">
            <form method="GET" action="" class="flex gap-2">
                <input type="text" name="buscar" 
                       value="<?php echo htmlspecialchars($buscar); ?>"
                       placeholder="Buscar por título o descripción..."
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>
                <?php if (!empty($buscar)): ?>
                    <a href="verReservas.php" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabla de reservas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Evento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-clock mr-1"></i>
                                Fecha y Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i>
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user-tie mr-1"></i>
                                Organizador
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-1"></i>
                                Estado
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-1"></i>
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($reservas)): ?>
                            <?php foreach ($reservas as $reserva): ?>
                                <?php
                                // Debug removido
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-calendar-check text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($reserva['titulo']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars(substr($reserva['descripcion'], 0, 50)); ?>
                                                    <?php if (strlen($reserva['descripcion']) > 50): ?>...<?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            <?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?> - 
                                            <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-user mr-1"></i>
                                            <?php 
                                            if (!empty($reserva['cliente_nombres']) && !empty($reserva['cliente_apellidos'])) {
                                                echo htmlspecialchars($reserva['cliente_nombres'] . ' ' . $reserva['cliente_apellidos']);
                                            } else {
                                                echo 'Sin asignar';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            <?php 
                                            if (!empty($reserva['organizador'])) {
                                                echo htmlspecialchars($reserva['organizador']);
                                            } else {
                                                echo 'Sin asignar';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $estado_class = 'bg-gray-100 text-gray-800';
                                        switch ($reserva['estado']) {
                                            case 'confirmado':
                                                $estado_class = 'bg-green-100 text-green-800';
                                                break;
                                            case 'pendiente':
                                                $estado_class = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'cancelado':
                                                $estado_class = 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $estado_class; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="editarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                               title="Editar reserva">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="cancelarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                               class="text-red-600 hover:text-red-900 transition-colors duration-200" 
                                               title="Cancelar reserva"
                                               onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <p class="text-lg">No se encontraron reservas</p>
                                        <?php if (!empty($buscar)): ?>
                                            <p class="text-sm mt-2">Prueba con otros términos de búsqueda</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>
