<?php
    class Conexion {
        private $server   = "tcp:38.250.161.160,1433";
        private $database = "uniemprendeDB";
        private $username = "sa";
        private $password = "Lorenz119013";
        private $conn;

        public function conectar() {
            try {
                $dsn = "sqlsrv:Server={$this->server};Database={$this->database};TrustServerCertificate=true";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
                return new PDO($dsn, $this->username, $this->password, $options);

            } catch (PDOException $e) {
                error_log("Error SQL: " . $e->getMessage());
                die($e->getMessage());
            }
        }
    }
?>