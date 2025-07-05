<?php

require_once "../Modelos/Evento.php";

class EventoController
{
    public function guardarEvento(array $datos)
    {
        // Debug: verificar datos recibidos
        error_log("Datos recibidos en guardarEvento: " . print_r($datos, true));

        $evento = new Evento();
        $resultado = $evento->guardarEvento(
            $datos["titulo"],
            $datos["descripcion"] ?? '',
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $datos["id_usuario"] ?? null
        );

        error_log("Resultado de guardarEvento: " . ($resultado ? "success" : "error"));

        if ($resultado != 0) {
            $_SESSION['mensaje'] = "Evento creado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: ../Vistas/verEventos.php");
            exit();
        } else {
            return "Error al crear el evento o horario no disponible.";
        }
    }

    public function mostrarEventos()
    {
        $evento = new Evento();
        $resultado = $evento->mostrarEventos();

        // Convertir a array para facilitar el manejo en la vista
        $eventos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $eventos[] = $fila;
            }
        }
        return $eventos;
    }

    public function eliminarEvento($id)
    {
        $evento = new Evento();
        $resultado = $evento->eliminarEvento($id);
        if ($resultado != 0) {
            header("Location: ../Vistas/verEventos.php");
            exit();
        } else {
            return "Error al cancelar el evento.";
        }
    }

    public function buscarEvento($id)
    {
        $evento = new Evento();
        return $evento->buscarEvento($id);
    }

    public function actualizarEvento(array $datos)
    {
        $evento = new Evento();
        $resultado = $evento->actualizarEvento(
            $datos["id"],
            $datos["titulo"],
            $datos["descripcion"],
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $datos["id_usuario"]
        );
        if ($resultado != 0) {
            header("Location: ../Vistas/verEventos.php");
            exit();
        } else {
            return "Error al actualizar el evento o horario no disponible.";
        }
    }

    public function verificarDisponibilidadEvento(array $datos)
    {
        $evento = new Evento();
        $id_cliente = $_SESSION['id']; // Usar el ID del usuario actual
        $disponible = $evento->verificarDisponibilidadEvento(
            $datos["fecha_evento"],
            $datos["hora_inicio"],
            $datos["hora_fin"],
            $id_cliente
        );

        if ($disponible) {
            return "Horario disponible. Puede proceder a crear la reserva.";
        } else {
            return "Horario no disponible. Ya existe una reserva en este período.";
        }
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $evento = new Evento();
        $resultado = $evento->obtenerPorUsuario($id_usuario);
        $eventos = [];
        
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $eventos[] = $fila;
            }
        }
        return $eventos;
    }

    public function obtenerTodosEventos()
    {
        $evento = new Evento();
        return $evento->obtenerTodosEventos();
    }

    public function obtenerHistorial($id_usuario = null, $limite = 50)
    {
        $evento = new Evento();
        return $evento->obtenerHistorialEventos($id_usuario, $limite);
    }

    public function cambiarFecha(array $datos)
    {
        $evento = new Evento();
        $resultado = $evento->cambiarFechaEvento(
            $datos["id"],
            $datos["nueva_fecha"],
            $datos["nueva_hora_inicio"],
            $datos["nueva_hora_fin"],
            $datos["motivo"] ?? ''
        );
        if ($resultado != 0) {
            return "Fecha de reserva cambiada correctamente.";
        } else {
            return "Error al cambiar la fecha o horario no disponible.";
        }
    }

    public function obtenerConFiltros(array $filtros = [])
    {
        $evento = new Evento();
        $resultado = $evento->obtenerEventosConFiltros($filtros);

        // Convertir a array para facilitar el manejo en la vista
        $eventos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $eventos[] = $fila;
            }
        }
        return $eventos;
    }

    public function obtenerCalendario($mes, $año, $id_usuario = null)
    {
        $evento = new Evento();
        return $evento->obtenerCalendarioDisponibilidad($mes, $año, $id_usuario);
    }

    public function aceptarEvento($id_evento)
    {
        $evento = new Evento();
        $resultado = $evento->aceptarEvento($id_evento);
        if ($resultado != 0) {
            $_SESSION['mensaje'] = "Evento aceptado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al aceptar el evento.";
            $_SESSION['tipo_mensaje'] = "error";
        }
        header("Location: ../Vistas/verEventos.php");
        exit();
    }

    public function rechazarEvento($id_evento)
    {
        $evento = new Evento();
        $resultado = $evento->rechazarEvento($id_evento);
        if ($resultado != 0) {
            $_SESSION['mensaje'] = "Evento rechazado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al rechazar el evento.";
            $_SESSION['tipo_mensaje'] = "error";
        }
        header("Location: ../Vistas/verEventos.php");
        exit();
    }

    public function obtenerEventosProveedor($id_usuario_proveedor)
    {
        $evento = new Evento();
        $resultado = $evento->obtenerEventosProveedor($id_usuario_proveedor);
        $eventos = [];
        
        if ($resultado) {
            while ($fila = $resultado->fetch()) {
                $eventos[] = $fila;
            }
        }
        return $eventos;
    }
}
