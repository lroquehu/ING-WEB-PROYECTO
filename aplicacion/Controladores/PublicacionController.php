<?php
    class PublicacionController {
        private $publicacionModel;
        private $categoriaModel;
        private $usuarioModel;
        
        public function __construct() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            require_once 'aplicacion/Modelos/Publicacion.php';
            require_once 'aplicacion/Modelos/Categoria.php';
            require_once 'aplicacion/Modelos/Usuario.php';
            
            $this->publicacionModel = new Publicacion();
            $this->categoriaModel = new Categoria();
            $this->usuarioModel = new Usuario();
        }
        
        public function index() {
            try {
                $pagina = $_GET['pagina'] ?? 1;
                $limite = 12;
                $categoria_id = $_GET['categoria'] ?? 0;
                $tipo = $_GET['tipo'] ?? '';
                $orden = $_GET['orden'] ?? 'fecha_desc';
                
                // Obtener publicaciones con filtros
                $publicaciones = $this->publicacionModel->obtenerTodos($pagina, $limite, $categoria_id, $tipo, $orden);
                $totalPublicaciones = $this->publicacionModel->contarTodos($categoria_id, $tipo);
                
                // Obtener categorías para filtros
                $categorias = $this->categoriaModel->obtenerTodas();
                
                // Calcular paginación
                $totalPaginas = ceil($totalPublicaciones / $limite);
                
                $datosVista = [
                    'publicaciones' => $publicaciones,
                    'categorias' => $categorias,
                    'categoria_actual' => $categoria_id,
                    'tipo_actual' => $tipo,
                    'orden_actual' => $orden,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'total_publicaciones' => $totalPublicaciones,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::index: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar las publicaciones",
                    'publicaciones' => [],
                    'categorias' => [],
                    'pagina_actual' => 1,
                    'total_paginas' => 0,
                    'total_publicaciones' => 0,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/ver.php';
        }
        
        public function ver(){
            $id = $_GET['id'] ?? 0;
            $publicacion_id = (int)$id; 
            if (!$publicacion_id) {
                header('Location: ' . BASE_URL . 'publicaciones');
                exit;
            }
            
            try {
                // Obtener información de la publicación
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);

                if (!$publicacion || $publicacion['estado'] != 1) {
                    throw new Exception("Publicación no encontrada o no disponible");
                }
                
                // Obtener imágenes de la publicación (devuelve filas: id_imagen, url_imagen, es_principal)
                $imagenesRows = $this->publicacionModel->obtenerImagenes($publicacion_id);
                // Transformar a array de URLs públicas que la vista espera (strings)
                $imagenes = [];
                foreach ($imagenesRows as $row) {
                    $url = $row['url_imagen'] ?? '';
                    if (empty($url)) continue;
                    // Si ya es URL absoluta, usarla; si es relativa, prefijar BASE_URL
                    if (preg_match('#^https?://#i', $url)) {
                        $full = $url;
                    } else {
                        $full = '/' . ltrim($url, '/\\');
                    }
                    $imagenes[] = $full;
                }
                
                // Obtener información del vendedor
                $vendedor = $this->usuarioModel->obtenerPorId($publicacion['id_usuario']);
                
                // Obtener publicaciones similares
                $publicacionesSimilares = $this->publicacionModel->obtenerSimilares($publicacion_id, $publicacion['id_categoria'], 4);
                
                // Incrementar contador de vistas
                $this->publicacionModel->incrementarVistas($publicacion_id);
                
                $datosVista = [
                    'publicacion' => $publicacion,
                    'imagenes' => $imagenes,
                    'vendedor' => $vendedor,
                    'publicaciones_similares' => $publicacionesSimilares,
                    'usuario_autenticado' => isset($_SESSION['usuario_id']),
                    'es_propietario' => isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $publicacion['id_usuario']
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::ver: " . $e->getMessage());
                $datosVista = [
                    'error' => $e->getMessage(),
                    'publicacion' => null,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/ver.php';
        }
        
        public function crear() {
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['redirect_url'] = BASE_URL . 'publicaciones/crear';
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
            
            try {
                // Obtener categorías para el formulario
                $categorias = $this->categoriaModel->obtenerTodas();
                
                $error = '';
                $datos_formulario = [
                    'titulo' => '',
                    'descripcion' => '',
                    'categoria_id' => '',
                    'tipo' => 'Producto',
                    'precio' => '',
                    'telefono_contacto' => '',
                    'correo_contacto' => ''
                ];
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Recoger y sanitizar datos
                    $titulo = trim($_POST['titulo'] ?? '');
                    $descripcion = trim($_POST['descripcion'] ?? '');
                    $categoria_id = intval($_POST['categoria_id'] ?? 0);
                    $tipo = $_POST['tipo'] ?? 'Producto';
                    $precio = floatval($_POST['precio'] ?? 0);
                    $telefono_contacto = trim($_POST['telefono_contacto'] ?? '');
                    $correo_contacto = trim($_POST['correo_contacto'] ?? '');
                    
                    // Validaciones
                    if (empty($titulo) || empty($descripcion) || $categoria_id === 0) {
                        $error = "Por favor completa todos los campos obligatorios";
                    } elseif (strlen($titulo) < 5) {
                        $error = "El título debe tener al menos 5 caracteres";
                    } elseif (strlen($descripcion) < 10) {
                        $error = "La descripción debe tener al menos 10 caracteres";
                    } elseif ($precio < 0) {
                        $error = "El precio no puede ser negativo";
                    } else {
                        // Crear publicación
                        $publicacion_id = $this->publicacionModel->crear([
                            'id_usuario' => $_SESSION['usuario_id'],
                            'id_categoria' => $categoria_id,
                            'titulo' => $titulo,
                            'descripcion' => $descripcion,
                            'tipo' => $tipo,
                            'precio' => $precio,
                            'telefono_contacto' => $telefono_contacto,
                            'correo_contacto' => $correo_contacto
                        ]);
                        
                        if ($publicacion_id) {
                            // Procesar imágenes si se subieron
                            if (!empty($_FILES['imagenes']['name'][0])) {
                                $this->procesarImagenes($publicacion_id, $_FILES['imagenes']);
                            }
                            
                            header('Location: ' . BASE_URL . 'publicaciones/ver/' . $publicacion_id . '?success=1');
                            exit;
                        } else {
                            $error = "Error al crear la publicación";
                        }
                    }
                    
                    // Mantener datos del formulario en caso de error
                    $datos_formulario = [
                        'titulo' => $titulo,
                        'descripcion' => $descripcion,
                        'categoria_id' => $categoria_id,
                        'tipo' => $tipo,
                        'precio' => $precio,
                        'telefono_contacto' => $telefono_contacto,
                        'correo_contacto' => $correo_contacto
                    ];
                }
                
                $datosVista = [
                    'categorias' => $categorias,
                    'datos_formulario' => $datos_formulario,
                    'error' => $error,
                    'usuario_autenticado' => true
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::crear: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al crear la publicación",
                    'categorias' => [],
                    'datos_formulario' => [],
                    'usuario_autenticado' => true
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/crear.php';
        }
        
        public function editar() {
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['redirect_url'] = BASE_URL . 'publicaciones/editar/' . ($_GET['id'] ?? '');
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
            
            $publicacion_id = $_GET['id'] ?? 0;
            
            if (!$publicacion_id) {
                header('Location: ' . BASE_URL . 'perfil/publicaciones');
                exit;
            }
            
            try {
                // Verificar que la publicación pertenece al usuario
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                
                if (!$publicacion || $publicacion['id_usuario'] != $_SESSION['usuario_id']) {
                    throw new Exception("No tienes permisos para editar esta publicación");
                }
                
                // Obtener categorías para el formulario
                $categorias = $this->categoriaModel->obtenerTodas();
                $publicacion['imagenes'] = $this->publicacionModel->obtenerImagenes($publicacion_id);
                
                $error = '';
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Recoger y sanitizar datos
                    $titulo = trim($_POST['titulo'] ?? '');
                    $descripcion = trim($_POST['descripcion'] ?? '');
                    $categoria_id = intval($_POST['categoria_id'] ?? 0);
                    $tipo = $_POST['tipo'] ?? 'Producto';
                    $precio = floatval($_POST['precio'] ?? 0);
                    $telefono_contacto = trim($_POST['telefono_contacto'] ?? '');
                    $correo_contacto = trim($_POST['correo_contacto'] ?? '');
                    $estado = intval($_POST['estado'] ?? 1);
                    
                    // Validaciones
                    if (empty($titulo) || empty($descripcion) || $categoria_id === 0) {
                        $error = "Por favor completa todos los campos obligatorios";
                    } elseif (strlen($titulo) < 5) {
                        $error = "El título debe tener al menos 5 caracteres";
                    } elseif (strlen($descripcion) < 10) {
                        $error = "La descripción debe tener al menos 10 caracteres";
                    } elseif ($precio < 0) {
                        $error = "El precio no puede ser negativo";
                    } else {
                        // Actualizar publicación
                        if ($this->publicacionModel->actualizar($publicacion_id, [
                            'id_categoria' => $categoria_id,
                            'titulo' => $titulo,
                            'descripcion' => $descripcion,
                            'tipo' => $tipo,
                            'precio' => $precio,
                            'telefono_contacto' => $telefono_contacto,
                            'correo_contacto' => $correo_contacto,
                            'estado' => $estado
                        ])) {
                            // Procesar nuevas imágenes si se subieron
                            if (!empty($_FILES['imagenes']['name'][0])) {
                                $this->procesarImagenes($publicacion_id, $_FILES['imagenes']);
                            }
                            
                            // Eliminar imágenes marcadas para borrar
                            if (!empty($_POST['eliminar_imagenes'])) {
                                foreach ($_POST['eliminar_imagenes'] as $imagen_id) {
                                    $this->publicacionModel->eliminarImagen($imagen_id);
                                }
                            }
                            
                            header('Location: ' . BASE_URL . 'publicaciones/ver/' . $publicacion_id . '?success=2');
                            exit;
                        } else {
                            $error = "Error al actualizar la publicación";
                        }
                    }
                    
                    // Mantener datos del formulario en caso de error
                    $publicacion['titulo'] = $titulo;
                    $publicacion['descripcion'] = $descripcion;
                    $publicacion['id_categoria'] = $categoria_id;
                    $publicacion['tipo'] = $tipo;
                    $publicacion['precio'] = $precio;
                    $publicacion['telefono_contacto'] = $telefono_contacto;
                    $publicacion['correo_contacto'] = $correo_contacto;
                    $publicacion['estado'] = $estado;
                }
                
                $datosVista = [
                    'publicacion' => $publicacion,
                    'categorias' => $categorias,
                    'error' => $error,
                    'usuario_autenticado' => true
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::editar: " . $e->getMessage());
                $datosVista = [
                    'error' => $e->getMessage(),
                    'publicacion' => null,
                    'categorias' => [],
                    'usuario_autenticado' => true
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/editar.php';
        }
        
        public function eliminar() {
            // Verificar autenticación y método POST
            if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
            
            $publicacion_id = $_POST['publicacion_id'] ?? 0;
            
            if (!$publicacion_id) {
                $_SESSION['error'] = "ID de publicación no válido";
                header('Location: ' . BASE_URL . 'perfil');
                exit;
            }
            
            try {
                // Verificar que la publicación pertenece al usuario
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                
                if (!$publicacion || $publicacion['id_usuario'] != $_SESSION['usuario_id']) {
                    $_SESSION['error'] = "No tienes permisos para eliminar esta publicación";
                    header('Location: ' . BASE_URL . 'perfil');
                    exit;
                }
                
                // Eliminar publicación (cambiar estado a eliminado)
                if ($this->publicacionModel->eliminar($publicacion_id)) {
                    $_SESSION['mensaje_exito'] = "Publicación eliminada exitosamente";
                } else {
                    $_SESSION['error'] = "Error al eliminar la publicación";
                }
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::eliminar: " . $e->getMessage());
                $_SESSION['error'] = "Error al procesar la solicitud";
            }
            
            header('Location: ' . BASE_URL . 'perfil');
            exit;
        }
        
        public function cambiarEstado() {
            // Verificar autenticación y método POST
            if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
            
            $publicacion_id = $_POST['publicacion_id'] ?? 0;
            $nuevo_estado = $_POST['nuevo_estado'] ?? 0;
            
            if (!$publicacion_id || !in_array($nuevo_estado, [1, 2])) { // 1: Activo, 2: Pausado
                $_SESSION['error'] = "Datos no válidos para cambiar el estado.";
                header('Location: ' . BASE_URL . 'perfil');
                exit;
            }
            
            try {
                // Verificar que la publicación pertenece al usuario
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                
                if (!$publicacion || $publicacion['id_usuario'] != $_SESSION['usuario_id']) {
                    $_SESSION['error'] = "No tienes permisos para cambiar el estado de esta publicación.";
                    header('Location: ' . BASE_URL . 'perfil');
                    exit;
                }
                
                // Cambiar estado de la publicación
                if ($this->publicacionModel->cambiarEstado($publicacion_id, $nuevo_estado)) {
                    $mensaje = $nuevo_estado == 1 ? "reactivada" : "pausada";
                    $_SESSION['mensaje_exito'] = "Publicación $mensaje exitosamente.";
                } else {
                    $_SESSION['error'] = "Error al cambiar el estado de la publicación.";
                }
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::cambiar_estado: " . $e->getMessage());
                $_SESSION['error'] = "Error al procesar la solicitud.";
            }
            
            // Redirigir siempre a la página de perfil para ver los cambios.
            header('Location: ' . BASE_URL . 'perfil');
            exit;
        }
        
        public function buscar() {
            try {
                $termino = $_GET['q'] ?? '';
                $categoria_id = $_GET['categoria'] ?? 0;
                $tipo = $_GET['tipo'] ?? '';
                $orden = $_GET['orden'] ?? 'relevancia';
                $pagina = $_GET['pagina'] ?? 1;
                $limite = 12;
                
                // Realizar búsqueda
                $resultados = $this->publicacionModel->buscar($termino, $categoria_id, $tipo, $orden, $pagina, $limite);
                $totalResultados = $this->publicacionModel->contarBusqueda($termino, $categoria_id, $tipo);
                
                // Obtener categorías para filtros
                $categorias = $this->categoriaModel->obtenerTodas();
                
                // Calcular paginación
                $totalPaginas = ceil($totalResultados / $limite);
                
                $datosVista = [
                    'resultados' => $resultados,
                    'termino_busqueda' => $termino,
                    'categoria_seleccionada' => $categoria_id,
                    'tipo_seleccionado' => $tipo,
                    'orden_seleccionado' => $orden,
                    'categorias' => $categorias,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'total_resultados' => $totalResultados,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::buscar: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al realizar la búsqueda",
                    'resultados' => [],
                    'termino_busqueda' => $termino,
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
            try {
                $categoria_id = $_GET['id'] ?? 0;
                $pagina = $_GET['pagina'] ?? 1;
                $limite = 12;
                $orden = $_GET['orden'] ?? 'fecha_desc';
                
                if ($categoria_id > 0) {
                    // Obtener publicaciones por categoría específica
                    $publicaciones = $this->publicacionModel->obtenerPorCategoria($categoria_id, $pagina, $limite, $orden);
                    $totalPublicaciones = $this->publicacionModel->contarPorCategoria($categoria_id);
                    $categoria = $this->categoriaModel->obtenerPorId($categoria_id);
                } else {
                    // Obtener todas las publicaciones
                    $publicaciones = $this->publicacionModel->obtenerTodos($pagina, $limite, 0, '', $orden);
                    $totalPublicaciones = $this->publicacionModel->contarTodos();
                    $categoria = ['nombre_categoria' => 'Todas las categorías'];
                }
                
                // Obtener todas las categorías para el menú
                $categorias = $this->categoriaModel->obtenerTodas();
                
                // Calcular paginación
                $totalPaginas = ceil($totalPublicaciones / $limite);
                
                $datosVista = [
                    'publicaciones' => $publicaciones,
                    'categoria_actual' => $categoria,
                    'categorias' => $categorias,
                    'orden_actual' => $orden,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $totalPaginas,
                    'total_publicaciones' => $totalPublicaciones,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
                
            } catch (Exception $e) {
                error_log("Error en PublicacionController::categorias: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar la categoría",
                    'publicaciones' => [],
                    'categoria_actual' => ['nombre_categoria' => 'Error'],
                    'categorias' => [],
                    'pagina_actual' => 1,
                    'total_paginas' => 0,
                    'total_publicaciones' => 0,
                    'usuario_autenticado' => isset($_SESSION['usuario_id'])
                ];
            }
            
            include 'aplicacion/Vistas/publicacion/categorias.php';
        }
        
        private function procesarImagenes($publicacion_id, $archivos_imagenes) {
            $directorio_uploads = 'assets/uploads/publicaciones/' . $publicacion_id . '/';
            
            // Crear directorio si no existe
            if (!is_dir($directorio_uploads)) {
                mkdir($directorio_uploads, 0755, true);
            }
            
            $imagenes_procesadas = [];
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            // Comprobar si ya existe una imagen principal para esta publicación
            $imagenesExistentes = $this->publicacionModel->obtenerImagenes($publicacion_id);
            $tienePrincipal = false;
            foreach ($imagenesExistentes as $r) {
                if (!empty($r['es_principal']) && (int)$r['es_principal'] === 1) {
                    $tienePrincipal = true;
                    break;
                }
            }

            foreach ($archivos_imagenes['tmp_name'] as $index => $tmp_name) {
                if ($archivos_imagenes['error'][$index] === UPLOAD_ERR_OK) {

                    $nombre_original = $archivos_imagenes['name'][$index];
                    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

                    // 1. Validar extensión
                    if (!in_array($extension, $extensiones_permitidas)) {
                        continue;
                    }

                    // 2. Validar tipo MIME real
                    $tipo_archivo = mime_content_type($tmp_name);
                    $tipos_mime_permitidos = [
                        'image/jpeg','image/jpg', 'image/png', 'image/gif', 'image/webp'
                    ];

                    if (!in_array($tipo_archivo, $tipos_mime_permitidos)) {
                        continue;
                    }

                    // 3. Validar que sea realmente una imagen
                    $tamanio = getimagesize($tmp_name);
                    if ($tamanio === false) {
                        continue;
                    }

                    // 4. Validar tamaño máximo (ej: 5MB)
                    if ($archivos_imagenes['size'][$index] > 5 * 1024 * 1024) {
                        continue;
                    }

                    // Generar nombre seguro
                    $nombre_archivo = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                    $ruta_destino = $directorio_uploads . $nombre_archivo;

                    // Mover archivo
                    if (move_uploaded_file($tmp_name, $ruta_destino)) { // Ahora $ruta_destino es relativa, ej: "assets/uploads/..."
                        // Asignar es_principal = 1 solo si NO existe ya una principal
                        $es_principal = 0;
                        if (!$tienePrincipal && count($imagenes_procesadas) === 0) {
                            $es_principal = 1;
                            $tienePrincipal = true; // asegurar que solo una quede marcada
                        }

                        $imagenes_procesadas[] = [
                            'id_publicacion' => $publicacion_id,
                            'url_imagen' => $ruta_destino, // Se guarda la misma ruta relativa en la BD
                            'es_principal' => $es_principal
                        ];
                    }
                }
            }
            
            // Guardar en base de datos
            foreach ($imagenes_procesadas as $imagen) {
                $this->publicacionModel->agregarImagen($imagen);
            }
            
            return count($imagenes_procesadas);
        }
    }
?>