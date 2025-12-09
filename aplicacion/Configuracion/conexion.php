<?php
    class Conexion {
        private $server   = "127.0.0.1,1433";
        private $database = "uniemprendeDB";
        private $username = "sa";
        private $password = "Lorenz119013";
        
        private static $instanciaConexion = null;

        public function conectar() {
            // 1. Singleton: Reutilizar conexión
            if (self::$instanciaConexion !== null) {
                return self::$instanciaConexion;
            }

            try {
                $dsn = "sqlsrv:Server={$this->server};Database={$this->database};TrustServerCertificate=true;LoginTimeout=15";
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
                
                self::$instanciaConexion = new PDO($dsn, $this->username, $this->password, $options);
                
                return self::$instanciaConexion;

            } catch (PDOException $e) {
                die("Error CRÍTICO de Conexión: " . $e->getMessage());
            }
        }
    }
?>