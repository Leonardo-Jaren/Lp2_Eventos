<?php

class Calendario {
    
    public static function generarCalendarioMes($mes, $año, $eventos = []) {
        $primerDia = mktime(0, 0, 0, $mes, 1, $año);
        $nombreMes = date('F Y', $primerDia);
        $diasEnMes = date('t', $primerDia);
        $diaSemana = date('w', $primerDia);
        
        $calendario = [
            'mes' => $nombreMes,
            'año' => $año,
            'mes_numero' => $mes,
            'dias' => []
        ];
        
        $eventosPorFecha = [];
        foreach ($eventos as $evento) {
            $fecha = $evento['fecha'];
            if (!isset($eventosPorFecha[$fecha])) {
                $eventosPorFecha[$fecha] = [];
            }
            $eventosPorFecha[$fecha][] = $evento;
        }
        
        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $fechaActual = sprintf('%04d-%02d-%02d', $año, $mes, $dia);
            $esHoy = ($fechaActual == date('Y-m-d'));
            $esPasado = ($fechaActual < date('Y-m-d'));
            
            $calendario['dias'][] = [
                'numero' => $dia,
                'fecha' => $fechaActual,
                'es_hoy' => $esHoy,
                'es_pasado' => $esPasado,
                'eventos' => isset($eventosPorFecha[$fechaActual]) ? $eventosPorFecha[$fechaActual] : [],
                'tiene_eventos' => isset($eventosPorFecha[$fechaActual]) && count($eventosPorFecha[$fechaActual]) > 0
            ];
        }
        
        return $calendario;
    }
    
    public static function obtenerDiasSemana() {
        return ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    }
    
    public static function esFinDeSemana($fecha) {
        $diaSemana = date('w', strtotime($fecha));
        return ($diaSemana == 0 || $diaSemana == 6);
    }

    public static function mesAnterior($mes, $año) {
        if ($mes == 1) {
            return ['mes' => 12, 'año' => $año - 1];
        }
        return ['mes' => $mes - 1, 'año' => $año];
    }
    
    public static function mesSiguiente($mes, $año) {
        if ($mes == 12) {
            return ['mes' => 1, 'año' => $año + 1];
        }
        return ['mes' => $mes + 1, 'año' => $año];
    }
    
    public static function formatearFecha($fecha, $formato = 'd/m/Y') {
        return date($formato, strtotime($fecha));
    }
    
    public static function formatearHora($hora, $formato = 'H:i') {
        return date($formato, strtotime($hora));
    }
    
    public static function nombreMesEspanol($mes) {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[$mes];
    }
}

?>