<?php
    class Conexion {
        private $server   = "tcp:uniemprende-server.database.windows.net,1433";
        private $database = "uniemprendeDB";
        private $username = "adminsql";
        private $password = "<Loscapis>";
        private $conn;

        public function conectar() {
            try {
                $dsn = "sqlsrv:Server={$this->server};Database={$this->database}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ];
                return new PDO($dsn, $this->username, $this->password, $options);

            } catch (PDOException $e) {
                error_log("Error SQL Azure: " . $e->getMessage());
                die("Error al conectar a SQL Server Azure");
            }
        }
    }
?>
