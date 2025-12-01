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
            
            include 'aplicacion/Vistas/publicacion/index.php';
        }
        
        public function ver($params = []){
            // CORRECCIÓN: El router pasa los parámetros como un array numérico.
            // CORRECCIÓN DEFINITIVA: Unificar la obtención del ID.
            // El router pasa los parámetros de la URL (ej: /ver/123) como un array numérico.
            // El ID será el primer elemento del array $params.
            // Hacemos el método más robusto para aceptar el ID desde los parámetros de la ruta (`publicaciones/ver/123`)
            // o desde un parámetro GET (`publicaciones/ver?id=123`).
            // También se contempla el caso de que venga como un parámetro GET (ej: ?id=123).
            $id = 0;
            if (!empty($params) && is_numeric($params[0])) $id = $params[0];
            if (!$id) $id = $_GET['id'] ?? 0;
            if (!empty($params) && isset($params[0]) && is_numeric($params[0])) {
                $id = $params[0];
            }
            
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
                    $full = obtenerImagenFinal($url);
                    $imagenes[] = $full;
                }
                
                // Obtener información del vendedor
                $vendedor = $this->usuarioModel->obtenerPorId($publicacion['id_usuario']);
                
                // Obtener publicaciones similares
                $publicacionesSimilares = $this->publicacionModel->obtenerSimilares($publicacion_id, $publicacion['id_categoria'], 4);
                
                // Incrementar contador de vistas
                $this->publicacionModel->incrementarVistas($publicacion_id);
                
                // --- INICIO LÓGICA DE VALORACIÓN Y FAVORITOS ---
                $es_favorito = false;
                $usuario_ya_valoro = true; // Por defecto, no puede valorar
                $valoracion_usuario = null;

                if (isset($_SESSION['usuario_id'])) {
                    $id_usuario_actual = $_SESSION['usuario_id'];
                    $es_favorito = $this->publicacionModel->esFavorito($id_usuario_actual, $publicacion_id);
                    // Verificamos si el usuario actual ya ha valorado esta publicación
                    $usuario_ya_valoro = $this->publicacionModel->usuarioYaValoro($id_usuario_actual, $publicacion_id);
                }
                // Si ya valoró, obtenemos su valoración para permitir la edición
                if ($usuario_ya_valoro && isset($id_usuario_actual)) {
                    $valoracion_usuario = $this->publicacionModel->obtenerValoracionUsuario($id_usuario_actual, $publicacion_id);
                }

                // Obtener estadísticas de valoración de la publicación
                $stats_valoracion = $this->publicacionModel->obtenerEstadisticasValoracion($publicacion_id);

                // Obtener todas las valoraciones para mostrarlas públicamente
                $valoraciones = $this->publicacionModel->obtenerValoracionesPublicacion($publicacion_id);
                // --- FIN LÓGICA DE VALORACIÓN Y FAVORITOS ---

                $datosVista = [
                    'publicacion' => $publicacion,
                    'imagenes' => $imagenes,
                    'vendedor' => $vendedor,
                    'publicaciones_similares' => $publicacionesSimilares,
                    'usuario_autenticado' => isset($_SESSION['usuario_id']),
                    'es_propietario' => isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $publicacion['id_usuario'],
                    'es_favorito' => $es_favorito,
                    'valoracion_promedio' => $stats_valoracion['promedio'],
                    'total_valoraciones' => $stats_valoracion['total'],
                    'usuario_ya_valoro' => $usuario_ya_valoro,
                    'valoracion_usuario' => $valoracion_usuario,
                    'valoraciones' => $valoraciones // <-- NUEVO: Pasamos las valoraciones a la vista
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

        public function valorar() {
            // Verificar que el usuario esté autenticado y que el método sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
                // Redirigir si no cumple las condiciones
                header('Location: ' . BASE_URL);
                exit;
            }
        
            $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);
            $puntuacion = filter_input(INPUT_POST, 'puntuacion', FILTER_VALIDATE_INT);
            $id_usuario = $_SESSION['usuario_id'];
            $comentario_raw = trim($_POST['comentario'] ?? '');
            $comentario = !empty($comentario_raw) ? htmlspecialchars($comentario_raw, ENT_QUOTES, 'UTF-8') : null;
        
            // Validaciones
            if (!$id_publicacion || !$puntuacion || $puntuacion < 1 || $puntuacion > 5) {
                // Si los datos son inválidos, redirigir a la publicación con un mensaje de error (opcional)
                $_SESSION['error_valoracion'] = "La puntuación debe estar entre 1 y 5.";
                header('Location: ' . BASE_URL . 'publicaciones/ver/' . $id_publicacion);
                exit;
            }
        
            // Intentar agregar la valoración a través del modelo
            $exito = $this->publicacionModel->agregarValoracion($id_publicacion, $id_usuario, $puntuacion, $comentario);
        
            if ($exito) {
                $_SESSION['exito_valoracion'] = "¡Gracias por tu valoración!";
            }
            
            header('Location: ' . BASE_URL . 'publicaciones/ver/' . $id_publicacion);
            exit;
        }

        public function editarValoracion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
                header('Location: ' . BASE_URL);
                exit;
            }

            $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);
            $id_valoracion = filter_input(INPUT_POST, 'id_valoracion', FILTER_VALIDATE_INT);
            $puntuacion = filter_input(INPUT_POST, 'puntuacion', FILTER_VALIDATE_INT);
            $id_usuario = $_SESSION['usuario_id'];
            $comentario_raw = trim($_POST['comentario'] ?? '');
            $comentario = !empty($comentario_raw) ? htmlspecialchars($comentario_raw, ENT_QUOTES, 'UTF-8') : null;

            // Validaciones
            if (!$id_publicacion || !$id_valoracion || !$puntuacion) {
                $_SESSION['error_valoracion'] = "Datos inválidos para editar la valoración.";
                header('Location: ' . BASE_URL . 'publicaciones/ver/' . $id_publicacion);
                exit;
            }

            // Aquí podrías añadir una capa extra de seguridad para verificar que el id_valoracion pertenece al usuario actual.

            $exito = $this->publicacionModel->actualizarValoracion($id_valoracion, $puntuacion, $comentario);

            if ($exito) {
                $_SESSION['exito_valoracion'] = "¡Tu valoración ha sido actualizada!";
            }

            header('Location: ' . BASE_URL . 'publicaciones/ver/' . $id_publicacion);
            exit;
        }

        public function eliminarValoracion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
                header('Location: ' . BASE_URL);
                exit;
            }

            $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);
            $id_valoracion = filter_input(INPUT_POST, 'id_valoracion', FILTER_VALIDATE_INT);
            $id_usuario = $_SESSION['usuario_id'];

            if (!$id_publicacion || !$id_valoracion) {
                $_SESSION['error_valoracion'] = "Datos inválidos para eliminar la valoración.";
                // Si no tenemos id_publicacion, redirigimos a la página principal o al perfil.
                header('Location: ' . ($id_publicacion ? BASE_URL . 'publicaciones/ver/' . $id_publicacion : BASE_URL));
                exit;
            }

            // Llamar al modelo para eliminar la valoración, pasando el id del usuario para seguridad.
            $exito = $this->publicacionModel->eliminarValoracion($id_valoracion, $id_usuario);

            if ($exito) {
                $_SESSION['exito_valoracion'] = "Tu valoración ha sido eliminada.";
            }
            // El modelo ya establece el mensaje de error si falla.

            header('Location: ' . BASE_URL . 'publicaciones/ver/' . $id_publicacion);
            exit;
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
                    $nombre_base = uniqid() . '_' . bin2hex(random_bytes(8));
                    $nombre_archivo_webp = $nombre_base . '.webp';
                    $ruta_destino = $directorio_uploads . $nombre_archivo_webp;

                    // 5. Convertir a WebP
                    $imagen_origen = null;
                    switch ($tipo_archivo) {
                        case 'image/jpeg':
                        case 'image/jpg':
                            $imagen_origen = imagecreatefromjpeg($tmp_name);
                            break;
                        case 'image/png':
                            $imagen_origen = imagecreatefrompng($tmp_name);
                            // Conservar transparencia para PNG
                            imagepalettetotruecolor($imagen_origen);
                            imagealphablending($imagen_origen, true);
                            imagesavealpha($imagen_origen, true);
                            break;
                        case 'image/gif':
                            $imagen_origen = imagecreatefromgif($tmp_name);
                            break;
                        case 'image/webp':
                            // Si ya es WebP, solo lo movemos
                            move_uploaded_file($tmp_name, $ruta_destino);
                            $imagen_origen = null; // Marcar para saltar la conversión
                            break;
                    }

                    if ($imagen_origen !== null) {
                        // Guardar la imagen como WebP con una calidad del 80%
                        if (imagewebp($imagen_origen, $ruta_destino, 80)) {
                            // La conversión fue exitosa
                        } else {
                            // Si la conversión falla, saltar esta imagen
                            imagedestroy($imagen_origen);
                            continue;
                        }
                        imagedestroy($imagen_origen);
                    }

                    // Asignar es_principal = 1 solo si NO existe ya una principal
                    $es_principal = 0;
                    if (!$tienePrincipal && count($imagenes_procesadas) === 0) {
                        $es_principal = 1;
                        $tienePrincipal = true; // asegurar que solo una quede marcada
                    }

                    $imagenes_procesadas[] = [
                        'id_publicacion' => $publicacion_id,
                        'url_imagen' => $ruta_destino, // Se guarda la ruta del archivo .webp
                        'es_principal' => $es_principal
                    ];
                }
            }
            
            // Guardar en base de datos
            foreach ($imagenes_procesadas as $imagen) {
                $this->publicacionModel->agregarImagen($imagen);
            }
            
            return count($imagenes_procesadas);
        }

    /**
     * Endpoint para AJAX para marcar/desmarcar una publicación como favorita.
     */
    public function toggleFavorito() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"));
        $publicacion_id = $datos->publicacion_id ?? 0;
        
        if (!$publicacion_id) {
            echo json_encode(['success' => false, 'error' => 'ID de publicación no válido']);
            exit;
        }

        try {
            $id_usuario_actual = $_SESSION['usuario_id'];
            
            // Alternar el estado de favorito
            $esFavoritoAhora = $this->publicacionModel->toggleFavorito($id_usuario_actual, $publicacion_id);

            // Si se agregó a favoritos, crear notificación para el dueño
            if ($esFavoritoAhora) {
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                $dueño_id = $publicacion['id_usuario'];

                // Solo notificar si el que da favorito no es el mismo dueño
                if ($id_usuario_actual != $dueño_id) {
                    $usuario_actual = $this->usuarioModel->obtenerPorId($id_usuario_actual);
                    
                    require_once 'aplicacion/Modelos/Notificacion.php';
                    $notificacionModel = new Notificacion();
                    
                    $mensaje = htmlspecialchars($usuario_actual['nombres']) . " está interesado en tu producto: " . htmlspecialchars($publicacion['titulo']);
                    $enlace = 'publicaciones/ver/' . $publicacion_id;
                    
                    $notificacionModel->crear($dueño_id, 'favorito', $mensaje, $enlace);
                }
            }

            echo json_encode(['success' => true, 'esFavorito' => $esFavoritoAhora]);
            exit;

        } catch (Exception $e) {
            error_log("Error en PublicacionController::toggleFavorito: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud.']);
            exit;
        }
    }

    public function registrarContacto() {
        header('Content-Type: application/json');

        // 1. Validaciones básicas
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
            exit;
        }

        $datos = json_decode(file_get_contents("php://input"));
        $publicacion_id = $datos->id_publicacion ?? 0;

        if (!$publicacion_id) {
            echo json_encode(['success' => false, 'error' => 'ID de publicación no válido']);
            exit;
        }

        try {
            // --- CORRECCIÓN AQUÍ ---
            // Guardamos el resultado en una variable
            $resultado = $this->publicacionModel->registrarMovimiento($publicacion_id, $_SESSION['usuario_id'], 'Contacto');
            
            // Verificamos si fue TRUE (se guardó) o FALSE (falló)
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                // Si es false, avisamos al frontend
                echo json_encode(['success' => false, 'error' => 'La base de datos rechazó el registro. Revisa si el ID de usuario es válido.']);
            }
            exit;
            // -----------------------

        } catch (Exception $e) {
            error_log("Error en PublicacionController::registrarContacto: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
            exit;
        }
    }
}
?>