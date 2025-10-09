<?php
class PerfilController {
    private $usuarioModel;
    private $productoModel;
    
    public function __construct() {
        session_start();
        $this->usuarioModel = new Usuario();
        $this->productoModel = new Producto();
        
        // Verifica si el usuario está logueado
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . '?c=Autenticacion&a=login');
            exit;
        }
    }
    
    public function index() {
        $usuario = $_SESSION['usuario'];
        $productosUsuario = $this->productoModel->obtenerPorUsuario($usuario['id']);
        
        include 'aplicacion/Vistas/perfil/index.php';
    }
    
    public function editar() {
        $usuario = $_SESSION['usuario'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $universidad = $_POST['universidad'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $bio = $_POST['bio'] ?? '';
            
            if ($this->usuarioModel->actualizarPerfil($usuario['id'], $nombre, $correo, $universidad, $telefono, $bio)) {
                $_SESSION['usuario'] = $this->usuarioModel->obtenerPorId($usuario['id']);
                header('Location: ' . BASE_URL . '?c=Perfil&a=index&success=1');
                exit;
            } else {
                $error = "Error al actualizar el perfil";
            }
        }
        
        include 'aplicacion/Vistas/perfil/editar.php';
    }
    
    public function cambiarPassword() {
        $usuario = $_SESSION['usuario'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $passwordActual = $_POST['password_actual'] ?? '';
            $nuevoPassword = $_POST['nuevo_password'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';
            
            if ($nuevoPassword !== $confirmarPassword) {
                $error = "Las contraseñas no coinciden";
            } else {
                if ($this->usuarioModel->cambiarPassword($usuario['id'], $passwordActual, $nuevoPassword)) {
                    header('Location: ' . BASE_URL . '?c=Perfil&a=index&success=2');
                    exit;
                } else {
                    $error = "La contraseña actual es incorrecta";
                }
            }
        }
        
        include 'aplicacion/Vistas/perfil/cambiar-password.php';
    }
    
    public function misProductos() {
        $usuario = $_SESSION['usuario'];
        $productos = $this->productoModel->obtenerPorUsuario($usuario['id']);
        
        include 'aplicacion/Vistas/perfil/mis-productos.php';
    }
}
?>
