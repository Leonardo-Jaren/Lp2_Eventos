<?php

class ConexionDB {
    private $dns;
    private $usuario;
    private $contrasena;
    private $conexion;

    public function __construct() {
        $this->dns = "mysql:host=localhost;dbname=lp2_eventos02";
        $this->usuario = "root";
        $this->contrasena = "";
    }

    public function conectar() {
        try {
            $this->conexion = new PDO($this->dns, $this->usuario, $this->contrasena);
            return $this->conexion;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }

    public function desconectar() {
        $this->conexion = null;
    }
}
