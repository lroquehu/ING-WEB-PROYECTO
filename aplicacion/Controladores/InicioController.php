<?php
    class InicioController {
        private $publicacionModel; 
        private $categoriaModel;
        
        public function __construct() {
            // Incluir los modelos necesarios
            require_once 'aplicacion/Modelos/Publicacion.php';
            require_once 'aplicacion/Modelos/Categoria.php';
            $this->publicacionModel = new Publicacion(); 
            $this->categoriaModel = new Categoria();
        }
        
        public function index() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            try {
                $productosDestacados = $this->publicacionModel->obtenerDestacados(8); 
                
                $categorias = $this->categoriaModel->obtenerTodas();
                
                $estadisticas = $this->obtenerEstadisticas();
                
                $datosVista = [
                    'productos_destacados' => $productosDestacados,
                    'categorias' => $categorias,
                    'estadisticas' => $estadisticas,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error en InicioController: " . $e->getMessage());
                $datosVista = [
                    'productos_destacados' => [],
                    'categorias' => [],
                    'estadisticas' => [
                        'total_emprendedores' => 0,
                        'total_productos' => 0,
                        'total_servicios' => 0
                    ],
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/inicio/index.php';
        }
        
        public function buscar() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $termino = $_GET['q'] ?? '';
            $categoria_id = $_GET['categoria'] ?? 0;
            $pagina = $_GET['pagina'] ?? 1;
            $limite = 12;
            
            try {
                $resultados = $this->publicacionModel->buscar($termino, $categoria_id, $pagina, $limite);
                $totalResultados = $this->publicacionModel->contarBusqueda($termino, $categoria_id);
                
                $categorias = $this->categoriaModel->obtenerTodas();
                
                $totalPaginas = ceil($totalResultados / $limite);
                
                $datosVista = [
                    'resultados' => $resultados,
                    'termino_busqueda' => $termino,
                    'categoria_seleccionada' => $categoria_id,
                    'categorias' => $categorias,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'total_resultados' => $totalResultados,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error en búsqueda: " . $e->getMessage());
                $datosVista = [
                    'resultados' => [],
                    'termino_busqueda' => $termino,
                    'categoria_seleccionada' => $categoria_id,
                    'categorias' => [],
                    'pagina_actual' => 1,
                    'total_paginas' => 0,
                    'total_resultados' => 0,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/buscar.php';
        }
        
        public function categorias() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $categoria_id = $_GET['id'] ?? 0;
            $pagina = $_GET['pagina'] ?? 1;
            $limite = 12;
            
            try {
                if ($categoria_id > 0) {
                    $productos = $this->publicacionModel->obtenerPorCategoria($categoria_id, $pagina, $limite);
                    $totalProductos = $this->publicacionModel->contarPorCategoria($categoria_id);
                    $categoria = $this->categoriaModel->obtenerPorId($categoria_id);
                } else {
                    $productos = $this->publicacionModel->obtenerTodos($pagina, $limite);
                    $totalProductos = $this->publicacionModel->contarTodos();
                    $categoria = ['nombre_categoria' => 'Todas las categorías'];
                }
                
                $categorias = $this->categoriaModel->obtenerTodas();
                
                $totalPaginas = ceil($totalProductos / $limite);
                
                $datosVista = [
                    'productos' => $productos,
                    'categoria_actual' => $categoria,
                    'categorias' => $categorias,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'total_productos' => $totalProductos,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error al cargar categorías: " . $e->getMessage());
                $datosVista = [
                    'productos' => [],
                    'categoria_actual' => ['nombre_categoria' => 'Error'],
                    'categorias' => [],
                    'pagina_actual' => 1,
                    'total_paginas' => 0,
                    'total_productos' => 0,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/categorias.php'; 
        }
        
        public function contacto() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $mensaje_enviado = false;
            $error = '';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = trim($_POST['nombre'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $asunto = trim($_POST['asunto'] ?? '');
                $mensaje = trim($_POST['mensaje'] ?? '');
                
                // Validaciones básicas
                if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
                    $error = "Por favor completa todos los campos";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "El correo electrónico no es válido";
                } else {
                    // Aquí iría la lógica para enviar el correo
                    // Por ahora, solo marcamos como enviado
                    $mensaje_enviado = true;
                }
            }
            
            $datosVista = [
                'mensaje_enviado' => $mensaje_enviado,
                'error' => $error,
                'usuario_autenticado' => isset($_SESSION['usuario_id'])
            ];
            
            include 'aplicacion/Vistas/inicio/contacto.php';
        }
        
        private function obtenerEstadisticas() {
            // Conectar directamente a la base de datos para obtener estadísticas
            require_once 'aplicacion/Configuracion/conexion.php';
            $conexion = new Conexion();
            $db = $conexion->conectar();
            
            if (!$db) {
                return [
                    'total_emprendedores' => 0,
                    'total_productos' => 0,
                    'total_servicios' => 0
                ];
            }
            
            try {
                // Total de emprendedores únicos con publicaciones activas
                $stmt = $db->query("SELECT COUNT(DISTINCT id_usuario) as total FROM Publicaciones WHERE estado = 1");
                $total_emprendedores = $stmt->fetchColumn();
                
                // Total de productos activos
                $stmt = $db->query("SELECT COUNT(*) as total FROM Publicaciones WHERE estado = 1 AND tipo = 'Producto'");
                $total_productos = $stmt->fetchColumn();
                
                // Total de servicios activos
                $stmt = $db->query("SELECT COUNT(*) as total FROM Publicaciones WHERE estado = 1 AND tipo = 'Servicio'");
                $total_servicios = $stmt->fetchColumn();
                
                return [
                    'total_emprendedores' => $total_emprendedores,
                    'total_productos' => $total_productos,
                    'total_servicios' => $total_servicios
                ];
                
            } catch (PDOException $e) {
                error_log("Error al obtener estadísticas: " . $e->getMessage());
                return [
                    'total_emprendedores' => 0,
                    'total_productos' => 0,
                    'total_servicios' => 0
                ];
            }
        }
        
        public function acercaDe() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $datosVista = [
                'usuario_autenticado' => isset($_SESSION['usuario_id'])
            ];
            
            include 'aplicacion/Vistas/inicio/acerca_de.php';
        }
    }
?>