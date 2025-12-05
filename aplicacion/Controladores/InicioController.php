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
                $productosDestacados = $this->publicacionModel->obtenerTodos(); 
                
                $categorias = $this->categoriaModel->obtenerTodas();

                // Si el usuario está logueado, verificar cuáles son sus favoritos
                if (isset($_SESSION['usuario_id']) && !empty($productosDestacados)) {
                    $id_usuario = $_SESSION['usuario_id'];
                    
                    // Obtener solo los IDs de las publicaciones
                    $ids_publicaciones = array_column($productosDestacados, 'id_publicacion');
                    
                    // Consultar a la base de datos cuáles de estos IDs son favoritos
                    $favoritos_ids = $this->publicacionModel->verificarFavoritos($id_usuario, $ids_publicaciones);
                    
                    // Añadir la marca 'es_favorito' a las publicaciones correspondientes
                    foreach ($productosDestacados as &$publicacion) {
                        $publicacion['es_favorito'] = in_array($publicacion['id_publicacion'], $favoritos_ids);
                    }
                    unset($publicacion);
                }
                
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
