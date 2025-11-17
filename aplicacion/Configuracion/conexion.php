<?php
    class Conexion {
        private $host = 'bvxxdhyfy7pc9ng4j96s-mysql.services.clever-cloud.com';
        private $port = '20163';
        private $dbname = 'bvxxdhyfy7pc9ng4j96s';
        private $username = 'uqrqtnpuyqomumwl';
        private $password = 'QigAII7L0ZfwGXyIKEq';
        private $conn;

        public function conectar() {
            try {

                $this->conn = new PDO(
                    "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4", 
                    $this->username, 
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" 
                    ]
                );
                return $this->conn;
            } catch(PDOException $e) {
                // En producción, loggear el error en lugar de mostrarlo
                error_log("Error de conexión: " . $e->getMessage());
                
                // Mostrar error genérico
                if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                    die("Error de conexión: " . $e->getMessage());
                } else {
                    die("Error de conexión con la base de datos");
                }
            }
        }
    }

    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/');
    }
?>
