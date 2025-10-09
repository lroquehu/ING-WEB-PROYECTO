<?php
    class AutenticacionController {
        private $usuarioModel;
        
        public function __construct() {
            $this->usuarioModel = new Usuario();
        }
        
        public function login() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $correo = $_POST['correo'] ?? '';
                $contrasenia = $_POST['contrasenia'] ?? '';
                
                $usuario = $this->usuarioModel->login($correo, $contrasenia);
                
                if ($usuario) {
                    session_start();
                    $_SESSION['usuario'] = $usuario;
                    header('Location: ' . BASE_URL);
                    exit;
                } else {
                    $error = "Credenciales incorrectas";
                    include 'aplicacion/Vistas/autenticacion/login.php';
                }
            }
        }
        
        public function registro() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['nombre'] ?? '';
                $correo = $_POST['correo'] ?? '';
                $contrasenia = $_POST['contrasenia'] ?? '';
                
                if ($this->usuarioModel->registrar($nombre, $correo, $contrasenia)) {
                    header('Location: ' . BASE_URL . '?c=Autenticacion&a=login');
                    exit;
                } else {
                    $error = "Error en el registro";
                    include 'aplicacion/Vistas/autenticacion/registro.php';
                }
            }
        }
        
        public function logout() {
            session_start();
            session_destroy();
            header('Location: ' . BASE_URL);
            exit;
        }
    }
?>
