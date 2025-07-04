<?php

require_once "../Modelos/Reserva.php";

class ReservaController{
    public function guardar(array $datos){
        $reserva = new Reserva();
        $resultado = $reserva->guardar(
            $datos["titulo"],
            $datos["descripcion"],
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $datos["id_usuario"] ?? null // Este será el organizador, puede ser NULL
        );
        if($resultado != 0){
            header("Location: ../Vistas/verReservas.php");
            exit();
        } else {
            return "Error al crear la reserva o horario no disponible.";
        }
    }

    public function mostrar(){
        $reserva = new Reserva();
        $resultado = $reserva->mostrar();
        
        // Convertir a array para facilitar el manejo en la vista
        $reservas = [];
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $reservas[] = $fila;
            }
        }
        return $reservas;
    }

    public function eliminar($id){
        $reserva = new Reserva();
        $resultado = $reserva->eliminar($id);
        if($resultado != 0){
            header("Location: ../Vistas/verReservas.php");
            exit();
        } else {
            return "Error al cancelar la reserva.";
        }
    }

    public function buscar($id){
        $reserva = new Reserva();
        return $reserva->buscar($id);
    }

    public function actualizar(array $datos){
        $reserva = new Reserva();
        $resultado = $reserva->actualizar(
            $datos["id"],
            $datos["titulo"],
            $datos["descripcion"],
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $datos["id_usuario"]
        );
        if($resultado != 0){
            header("Location: ../Vistas/verReservas.php");
            exit();
        } else {
            return "Error al actualizar la reserva o horario no disponible.";
        }
    }

    public function verificarDisponibilidad(array $datos){
        $reserva = new Reserva();
        $disponible = $reserva->verificarDisponibilidad(
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $datos["id_usuario"]
        );
        
        if($disponible){
            return "Horario disponible. Puede proceder a crear la reserva.";
        } else {
            return "Horario no disponible. Ya existe una reserva en este período.";
        }
    }

    public function obtenerPorUsuario($id_usuario){
        $reserva = new Reserva();
        return $reserva->obtenerPorUsuario($id_usuario);
    }

    public function obtenerTodasReservas(){
        $reserva = new Reserva();
        return $reserva->obtenerTodasReservas();
    }

    public function obtenerHistorial($id_usuario = null, $limite = 50){
        $reserva = new Reserva();
        return $reserva->obtenerHistorialReservas($id_usuario, $limite);
    }

    public function cambiarFecha(array $datos){
        $reserva = new Reserva();
        $resultado = $reserva->cambiarFechaReserva(
            $datos["id"],
            $datos["nueva_fecha"],
            $datos["nueva_hora_inicio"],
            $datos["nueva_hora_fin"],
            $datos["motivo"] ?? ''
        );
        if($resultado != 0){
            return "Fecha de reserva cambiada correctamente.";
        } else {
            return "Error al cambiar la fecha o horario no disponible.";
        }
    }

    public function obtenerConFiltros(array $filtros = []){
        $reserva = new Reserva();
        $resultado = $reserva->obtenerReservasConFiltros($filtros);
        
        // Convertir a array para facilitar el manejo en la vista
        $reservas = [];
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $reservas[] = $fila;
            }
        }
        return $reservas;
    }

    public function obtenerCalendario($mes, $año, $id_usuario = null){
        $reserva = new Reserva();
        return $reserva->obtenerCalendarioDisponibilidad($mes, $año, $id_usuario);
    }
}

?>
