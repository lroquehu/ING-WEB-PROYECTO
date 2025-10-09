<?php
    class InicioController {
        public function index() {
            // Lógica para obtener productos destacados
            $productos = []; 
            
            include 'aplicacion/Vistas/inicio/index.php';
        }
    }
?>
