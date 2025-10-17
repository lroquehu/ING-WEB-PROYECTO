<?php
    class Conexion {
        private $host = 'localhost';
        private $dbname = 'uniemprende';
        private $username = 'root';
        private $password = '';
        private $conn;
        
        public function conectar() {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8", 
                    $this->username, 
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                return $this->conn;
            } catch(PDOException $e) {
                // En producción, loggear el error en lugar de mostrarlo
                error_log("Error de conexión: " . $e->getMessage());
                return null;
            }
        }
    }

    // Constante para URLs
    define('BASE_URL', 'http://localhost:81/ing-web-proyecto/');
?>