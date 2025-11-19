<?php
    class Conexion {
        private $server   = "127.0.0.1,1433";
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
                error_log("Error SQL Azure: " . $e->getMessage());
                die($e->getMessage());
            }
        }
    }
?>
