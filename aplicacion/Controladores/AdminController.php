<?php
    class AdminController {
        private $usuarioModel;
        private $publicacionModel;
        private $categoriaModel;
        private $pagoModel; 
        
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_rol']) || strtolower($_SESSION['usuario_rol']) !== 'admin') {
                header('Location: ' . BASE_URL . 'login');
                exit;
            }

            require_once 'aplicacion/Modelos/Usuario.php';
            require_once 'aplicacion/Modelos/Publicacion.php';
            require_once 'aplicacion/Modelos/Categoria.php';
            require_once 'aplicacion/Modelos/Pago.php'; // Importar modelo Pago
            require_once 'aplicacion/Configuracion/conexion.php';

            $this->usuarioModel = new Usuario();
            $this->publicacionModel = new Publicacion();
            $this->categoriaModel = new Categoria();
            $this->pagoModel = new Pago((new Conexion())->conectar());
        }

        public function index() {
            try {
                // 1. Estadísticas Generales
                $stats_usuarios = $this->usuarioModel->obtenerEstadisticasGenerales();
                $stats_publicaciones = $this->publicacionModel->obtenerEstadisticas();
                
                // 2. Datos Reales para Gráficos
                $raw_usuarios = $this->usuarioModel->obtenerCrecimientoMensual(12);
                $raw_publicaciones = $this->publicacionModel->obtenerCrecimientoMensual(12);
                $raw_ingresos = $this->pagoModel->obtenerIngresosMensuales(12);

                // 3. Procesar datos (rellenar huecos)
                $meses_labels = [];
                $data_usuarios = [];
                $data_publicaciones = [];
                $data_ingresos = [];

                for ($i = 11; $i >= 0; $i--) {
                    $mes_obj = new DateTime("-$i months");
                    $key = $mes_obj->format('Y-m'); 
                    $label = $mes_obj->format('M'); 
                    
                    $meses_labels[] = $label;

                    $u_val = 0;
                    foreach($raw_usuarios as $r) { if($r['mes'] == $key) $u_val = $r['nuevos_usuarios']; }
                    $data_usuarios[] = $u_val;

                    $p_val = 0;
                    foreach($raw_publicaciones as $r) { if($r['mes'] == $key) $p_val = $r['nuevas_publicaciones']; }
                    $data_publicaciones[] = $p_val;

                    $i_val = 0;
                    foreach($raw_ingresos as $r) { if($r['mes'] == $key) $i_val = $r['total']; }
                    $data_ingresos[] = $i_val;
                }

                // 4. Categorías Populares
                $categorias_populares = $this->categoriaModel->obtenerPopulares(5);

                // --- NUEVO: Obtener Información del Sistema ---
                
                // Espacio en disco
                $disk_total = disk_total_space(".");
                $disk_free = disk_free_space(".");
                $disk_used = $disk_total - $disk_free;
                
                // MEJORA: Función anónima para evitar errores de redeclaración
                $formatSize = function($bytes) {
                    return number_format($bytes / (1024 * 1024 * 1024), 2) . ' GB';
                };

                // Información de Base de Datos
                $db_version = "Desconocido";
                try {
                    $conn = (new Conexion())->conectar();
                    // Usamos @@VERSION para SQL Server
                    $stmt = $conn->query("SELECT @@VERSION as ver");
                    if ($stmt) {
                        $ver = $stmt->fetch(PDO::FETCH_ASSOC);
                        // Limpiar string largo
                        $db_version = substr($ver['ver'] ?? 'SQL Server', 0, 25) . '...';
                    }
                } catch (Exception $e) {
                    $db_version = "Error de conexión";
                }

                $info_sistema = [
                    'php_version' => phpversion(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
                    'disk_total' => $formatSize($disk_total), // Usamos la variable $formatSize
                    'disk_free' => $formatSize($disk_free),
                    'disk_used_percent' => ($disk_total > 0) ? round(($disk_used / $disk_total) * 100) : 0,
                    'db_status' => 'Conectado',
                    'db_version' => $db_version,
                    'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'localhost',
                    'memory_limit' => ini_get('memory_limit')
                ];

                $datosVista = [
                    'titulo' => 'Dashboard de Administración',
                    'stats_usuarios' => $stats_usuarios,
                    'stats_publicaciones' => $stats_publicaciones,
                    'chart_labels' => $meses_labels,
                    'chart_data_usuarios' => $data_usuarios,
                    'chart_data_publicaciones' => $data_publicaciones,
                    'chart_data_ingresos' => $data_ingresos,
                    'categorias_populares_chart' => $categorias_populares,
                    'info_sistema' => $info_sistema 
                ];

                extract($datosVista); 
                
            } catch (Exception $e) {
                error_log("Error en AdminController::index: " . $e->getMessage());
                // Asegurar que las variables existan aunque falle algo para no romper la vista
                if (!isset($info_sistema)) $info_sistema = null;
            }
            
            include 'aplicacion/Vistas/admin/index.php';
        }

        // Gestión de Usuarios
        public function usuarios() {
            $pagina = $_GET['pagina'] ?? 1;
            $limite = 1000;
            $estado = $_GET['estado'] ?? null;
            
            try {
                $usuarios = $this->usuarioModel->obtenerTodos($pagina, $limite, $estado);
                $total_usuarios = $this->usuarioModel->contarTodos($estado);
                $total_paginas = ceil($total_usuarios / $limite);
                
                // Obtener estadísticas generales y facultades para filtros/métricas
                $stats_usuarios_general = $this->usuarioModel->obtenerEstadisticasGenerales();
                $facultades = $this->usuarioModel->obtenerFacultades();
                $vendedores_activos = $this->usuarioModel->contarVendedoresActivos();
                $nuevos_este_mes = $this->usuarioModel->contarNuevosEsteMes(); // Asumiendo nuevo método
                
                $datosVista = [
                    'titulo' => 'Gestión de Usuarios',
                    'usuarios' => $usuarios,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $total_paginas,
                    'total_usuarios' => $total_usuarios,
                    'estado_filtro' => $estado,
                    'stats_usuarios_general' => $stats_usuarios_general,
                    'vendedores_activos' => $vendedores_activos,
                    'nuevos_este_mes' => $nuevos_este_mes,
                    'facultades' => $facultades // Se puede usar para filtros en la vista
                ];
                
            } catch (Exception $e) {
                error_log("Error en AdminController::usuarios: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar la lista de usuarios";
            }
            
            include 'aplicacion/Vistas/admin/usuarios.php';
        }

        // Obtener detalles de un usuario para el modal (AJAX)
        public function obtenerUsuario() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); exit;
            }
            header('Content-Type: application/json');

            $id = $_POST['id_usuario'] ?? 0;

            try {
                if ($id <= 0) throw new Exception("ID inválido");

                // 1. Obtener datos básicos
                $usuario = $this->usuarioModel->obtenerPorId($id);
                if (!$usuario) throw new Exception("Usuario no encontrado");

                // 2. Obtener estadísticas (compras, ventas, etc.)
                // Asegúrate de que este método exista en tu modelo Usuario, si no, usa datos dummy o 0
                $stats = $this->usuarioModel->obtenerEstadisticasCompletas($id); 

                echo json_encode([
                    'success' => true,
                    'data' => array_merge($usuario, $stats) // Combinamos info personal + estadísticas
                ]);

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }



        // Acción para eliminar un usuario permanentemente
        public function eliminarUsuario() {
            header('Content-Type: application/json'); // Asegurar cabecera JSON
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
                exit;
            }

            // CORRECCIÓN: Lectura segura de datos
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            if (!is_array($input)) $input = [];

            // Prioridad: JSON -> POST
            $id_usuario = $input['id_usuario'] ?? $_POST['id_usuario'] ?? 0;

            try {
                if ($id_usuario <= 0) {
                    throw new Exception("ID de usuario inválido.");
                }

                if ($id_usuario == ($_SESSION['usuario_id'] ?? 0)) {
                    throw new Exception("No puedes eliminar tu propia cuenta.");
                }

                if ($this->usuarioModel->eliminar($id_usuario)) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("No se pudo eliminar el usuario (posiblemente tiene registros).");
                }

            } catch (Exception $e) {
                http_response_code(400); 
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        // Gestión de Categorías
        public function categorias() {
            try {
                $categorias = $this->categoriaModel->obtenerParaAdmin(); 
                $stats_categorias = $this->categoriaModel->obtenerEstadisticas();
                
                $datosVista = [
                    'titulo' => 'Gestión de Categorías',
                    'categorias' => $categorias, // Corregido nombre variable para coincidir con vista
                    'stats_categorias' => $stats_categorias
                ];
                
            } catch (Exception $e) {
                error_log("Error en AdminController::categorias: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar las categorías";
            }
            
            include 'aplicacion/Vistas/admin/categorias.php';
        }

        // Acción para crear o actualizar una categoría (AJAX)
        public function guardarCategoria() {    
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); exit;
            }
            header('Content-Type: application/json');

            $id = $_POST['id_categoria'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $icono = trim($_POST['icono'] ?? 'fas fa-tag');
            $color = trim($_POST['color'] ?? '#00bcd4');
            $estado = (int)($_POST['estado'] ?? 1);

            // Datos auxiliares para el frontend (si es edición)
            $total_pubs = $_POST['total_publicaciones_hidden'] ?? 0;
            $fecha_creacion = $_POST['fecha_creacion_hidden'] ?? date('Y-m-d');

            try {
                if (empty($nombre)) throw new Exception("El nombre es obligatorio.");

                $success = false;
                $data = [];

                if (!empty($id)) {
                    // Actualizar
                    $success = $this->categoriaModel->actualizar((int)$id, $nombre, $descripcion, $icono, $color);
                    $message = $success ? 'Categoría actualizada.' : 'Error al actualizar.';
                    
                    $data['categoria'] = [
                        'id_categoria' => (int)$id,
                        'nombre_categoria' => $nombre,
                        'descripcion' => $descripcion,
                        'icono' => $icono,
                        'color' => $color,
                        'estado' => $estado, // El estado no cambia al editar info básica aquí
                        'total_publicaciones' => $total_pubs,
                        'fecha_creacion' => $fecha_creacion
                    ];
                } else {
                    // Crear
                    $id_insertado = $this->categoriaModel->crear($nombre, $descripcion, $icono, $color, $estado);
                    $success = (bool)$id_insertado;
                    $message = $success ? 'Categoría creada.' : 'Error al crear.';
                    
                    if ($success) {
                        $data['id_nuevo'] = $id_insertado;
                        $data['categoria'] = [
                            'id_categoria' => $id_insertado,
                            'nombre_categoria' => $nombre,
                            'descripcion' => $descripcion,
                            'icono' => $icono,
                            'color' => $color,
                            'estado' => $estado,
                            'total_publicaciones' => 0,
                            'fecha_creacion' => date('Y-m-d')
                        ];
                    }
                }

                // Recalcular estadísticas para actualizar dashboard en tiempo real
                if ($success) {
                    $stats = $this->categoriaModel->obtenerEstadisticas();
                    $data['stats'] = [
                        'total_categorias' => number_format($stats['total_categorias']),
                        'categorias_activas' => number_format($stats['categorias_activas']),
                        'total_publicaciones' => number_format($stats['total_publicaciones']),
                        'categoria_popular' => $stats['categoria_popular']
                    ];
                }

                echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Acción para eliminar una categoría (AJAX)
        public function eliminarCategoria() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
            header('Content-Type: application/json');

            $id = $_POST['id_categoria'] ?? 0;

            try {
                if ($this->categoriaModel->eliminar((int)$id)) {
                    // Obtener stats actualizadas
                    $stats = $this->categoriaModel->obtenerEstadisticas();
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Categoría eliminada.',
                        'data' => ['stats' => $stats] // Enviamos stats para refrescar
                    ]);
                } else {
                    throw new Exception("No se puede eliminar (posiblemente tiene productos).");
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        // Acción para Cambiar Estado (AJAX)
        public function cambiarEstadoCategoria() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
            header('Content-Type: application/json');

            $id = $_POST['id_categoria'] ?? 0;
            $estado_actual = (int)($_POST['estado_actual'] ?? 0);
            $nuevo_estado = $estado_actual === 1 ? 0 : 1;

            try {
                if ($this->categoriaModel->cambiarEstado((int)$id, $nuevo_estado)) {
                    $stats = $this->categoriaModel->obtenerEstadisticas();
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Estado actualizado.',
                        'data' => [
                            'nuevo_estado' => $nuevo_estado,
                            'stats' => $stats
                        ]
                    ]);
                } else {
                    throw new Exception("Error al cambiar estado.");
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        public function publicaciones() {
            try {
                // Obtener filtros de URL
                $categoria_filtro = $_GET['categoria'] ?? null;
                $estado_filtro = $_GET['estado'] ?? null;
                $tipo_filtro = $_GET['tipo'] ?? null;
                $facultad_filtro = $_GET['facultad'] ?? null;
                
                // Se asume la modificación en el modelo para aceptar filtros
                $publicaciones = $this->publicacionModel->obtenerTodasParaAdmin(
                    $categoria_filtro, 
                    $estado_filtro, 
                    $tipo_filtro,
                    $facultad_filtro
                ); 
                
                // Obtener datos para filtros y métricas
                $stats_publicaciones = $this->publicacionModel->obtenerEstadisticasDetalladas(); 
                $categorias = $this->categoriaModel->obtenerTodas(); // Solo activas para filtros
                $facultades = $this->usuarioModel->obtenerFacultades();
                
                $datosVista = [
                    'titulo' => 'Gestión de Publicaciones',
                    'publicaciones' => $publicaciones,
                    'stats_publicaciones' => $stats_publicaciones,
                    'categorias' => $categorias,
                    'facultades' => $facultades,
                    'categoria_filtro' => $categoria_filtro,
                    'estado_filtro' => $estado_filtro,
                    'tipo_filtro' => $tipo_filtro,
                    'facultad_filtro' => $facultad_filtro,
                ];
                
                // Limpiar mensajes de sesión
                $datosVista['error'] = $_SESSION['admin_error'] ?? '';
                $datosVista['success'] = $_SESSION['admin_success'] ?? '';
                unset($_SESSION['admin_error']);
                unset($_SESSION['admin_success']);

            } catch (Exception $e) {
                error_log("Error en AdminController::publicaciones: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar la lista de publicaciones: " . $e->getMessage();
            }
            
            include 'aplicacion/Vistas/admin/publicaciones.php';
        }

        public function cambiarEstadoPublicacion() {
            header('Content-Type: application/json');
            
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            if (!is_array($input)) $input = []; // <-- ESTA LÍNEA ES CLAVE

            $id_publicacion = $input['id_publicacion'] ?? $_POST['id_publicacion'] ?? null;
            $nuevo_estado = $input['estado'] ?? $_POST['estado'] ?? null;
            
            // ... (resto de tu lógica de validación y modelo igual)
            // Solo asegúrate de envolver en try-catch y devolver JSON válido
             try {
                if (!$this->publicacionModel->cambiarEstado($id_publicacion, $nuevo_estado)) {
                    throw new Exception("Error al actualizar.");
                }
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        
        // Acción para cambiar el estado de un usuario (Activar/Desactivar)
        public function cambiarEstadoUsuario() {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
                exit;
            }

            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            if (!is_array($input)) $input = []; 

            $id_usuario = $input['id_usuario'] ?? $_POST['id_usuario'] ?? 0;
            $nuevo_estado = $input['estado'] ?? $_POST['estado'] ?? null;
            $fecha_fin = $input['fecha_fin'] ?? $_POST['fecha_fin'] ?? null;
            $motivo = $input['motivo'] ?? $_POST['motivo'] ?? null;

            // --- ELIMINA ESTAS LÍNEAS DE AQUÍ (CAUSAN EL ERROR AL ACTIVAR) ---
            /* $dateObj = new DateTime($fecha_fin);
            $ahora = new DateTime();
            if ($dateObj <= $ahora) {
                throw new Exception("La fecha de suspensión debe ser futura.");
            }
            */
            // -----------------------------------------------------------------

            try {
                if ($id_usuario <= 0) throw new Exception("ID inválido.");
                if (!isset($nuevo_estado)) throw new Exception("Estado no recibido.");

                if ($id_usuario == ($_SESSION['usuario_id'] ?? 0) && (int)$nuevo_estado === 0) {
                    throw new Exception("No puedes desactivarte a ti mismo.");
                }

                $resultado = false;
                if ((int)$nuevo_estado === 0) { // Suspender
                    if (empty($fecha_fin) || empty($motivo)) {
                        throw new Exception("Fecha y motivo requeridos.");
                    }
                    
                    // --- MUEVE LA VALIDACIÓN AQUÍ ADENTRO ---
                    try {
                        $dateObj = new DateTime($fecha_fin);
                        $ahora = new DateTime();
                        
                        if ($dateObj <= $ahora) {
                            throw new Exception("La fecha de suspensión debe ser futura.");
                        }

                        $fecha_fin = $dateObj->format('Y-m-d H:i:s');
                    } catch (Exception $e) {
                        // Captura la excepción de fecha futura o formato inválido
                        throw new Exception($e->getMessage()); 
                    }
                    // -----------------------------------------

                    $resultado = $this->usuarioModel->suspenderUsuario($id_usuario, $fecha_fin, $motivo);
                } else { // Activar
                    $resultado = $this->usuarioModel->cambiarEstado($id_usuario, 1);
                }

                if ($resultado) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Error en base de datos.");
                }                

            } catch (Exception $e) {
                http_response_code(400); 
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                error_log("Error cambiarEstadoUsuario: " . $e->getMessage());
            }
            exit; 
        }
    }
?>