<?php
    class PerfilController {
        private $usuarioModel;
        private $publicacionModel;
        private $pagoModel;
        
        public function __construct() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Incluir y inicializar modelos
            require_once 'aplicacion/Configuracion/conexion.php'; // <-- 1. Incluir el archivo de conexión
            require_once 'aplicacion/Modelos/Usuario.php';
            require_once 'aplicacion/Modelos/Publicacion.php';
            require_once 'aplicacion/Modelos/Pago.php';
            $this->usuarioModel = new Usuario();
            $this->publicacionModel = new Publicacion();
            $conexion = new Conexion(); // <-- 2. Usar la clase correcta
            $this->pagoModel = new Pago($conexion->conectar());
        }

        /**
         * Verifica si el usuario está autenticado. Si no, redirige al login.
         * @param string $redirect_url La URL a la que redirigir después del login.
         */
        private function verificarAutenticacion($redirect_url = 'perfil') {
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['redirect_url'] = BASE_URL . $redirect_url;
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
        }

        /**
         * Carga una vista específica con los datos proporcionados.
         * @param string $vista El nombre de la vista a cargar.
         * @param array $datosVista Los datos a pasar a la vista.
         */
        private function cargarVista($vista, $datosVista = []) {
            // Extraer los datos para que estén disponibles como variables en la vista
            extract($datosVista);
            
            // Incluir el archivo de la vista
            include "aplicacion/Vistas/{$vista}.php";
        }
        
        public function index() {
            try {
                $this->verificarAutenticacion();

                // Obtener datos actualizados del usuario
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                // Obtener publicaciones del usuario
                $publicaciones = $this->publicacionModel->obtenerPorUsuario($_SESSION['usuario_id']);
                
                // Obtener favoritos del usuario
                $favoritos = $this->publicacionModel->obtenerFavoritos($_SESSION['usuario_id']);
                
                // Obtener estadísticas
                $estadisticas = $this->usuarioModel->obtenerEstadisticasCompletas($_SESSION['usuario_id']);
                
                // Verificar si hay mensajes de sesión o GET
                $mensaje_exito = $_SESSION['mensaje_exito'] ?? '';
                unset($_SESSION['mensaje_exito']);
                
                $error = $_SESSION['error'] ?? '';
                unset($_SESSION['error']);

                if (empty($mensaje_exito)) {
                    $success = $_GET['success'] ?? '';
                    switch ($success) {
                        case '1':
                            $mensaje_exito = "Perfil actualizado exitosamente";
                            break;
                        case '2':
                            $mensaje_exito = "Contraseña cambiada exitosamente";
                            break;
                        case '3':
                            $mensaje_exito = "Publicación creada exitosamente";
                            break;
                        case '4':
                            $mensaje_exito = "Publicación actualizada exitosamente";
                            break;
                    }
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'publicaciones' => $publicaciones,
                    'favoritos' => $favoritos,
                    'estadisticas' => $estadisticas,
                    'mensaje_exito' => $mensaje_exito,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::index: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar el perfil",
                    'usuario' => [],
                    'publicaciones' => [],
                    'estadisticas' => [
                        'total_productos' => 0,
                        'total_vistas' => 0,
                        'total_contactos' => 0,
                        'total_favoritos' => 0,
                    ]
                ];
            }
            
            include 'aplicacion/Vistas/perfil/index.php';
        }

        public function ver($params = []) {
            try {
                // Extraemos el ID del usuario de los parámetros de la URL.
                // El router de tu aplicación pasa un array (ej: ['id' => 123]),
                // por lo que debemos obtener el valor de la clave 'id'.
                $id_usuario = (int)($params['id'] ?? 0);

                if (!$id_usuario) {
                    throw new Exception("No se ha especificado un perfil de usuario.");
                }

                // Si el usuario intenta ver su propio perfil público, redirigir a su panel
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
                    header('Location: ' . BASE_URL . 'perfil');
                    exit;
                }
        
                // Obtener datos del usuario público
                $usuario = $this->usuarioModel->obtenerPorId($id_usuario);
                
                if (!$usuario) {
                    $this->cargarVista('perfil/verperfil', ['error' => 'El usuario que buscas no existe.', 'usuario' => null]);
                    return;
                }
                
                // Obtener todas las publicaciones del usuario y filtrar solo las activas (estado = 1)
                $todas_las_publicaciones = $this->publicacionModel->obtenerPorUsuario($id_usuario);
                $publicaciones_activas = array_filter($todas_las_publicaciones, function($p) {
                    return isset($p['estado']) && $p['estado'] == 1;
                });
                
                // Cargar la vista del perfil público
                $this->cargarVista('perfil/verperfil', [
                    'usuario' => $usuario,
                    'publicaciones' => $publicaciones_activas,
                ]);
        
            } catch (Exception $e) {
                error_log("Error en PerfilController::ver: " . $e->getMessage());
                $this->cargarVista('perfil/verperfil', [
                    'error' => 'Ocurrió un error al cargar el perfil.', 
                    'usuario' => null
                ]);
            }
        }
        
        public function editar() {
            try {
                $this->verificarAutenticacion();
        
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                $error = '';
                // Pre-fill form with existing data
                $datos_formulario = [
                    'nombres' => $usuario['nombres'],
                    'apellidos' => $usuario['apellidos'],
                    'telefono' => $usuario['telefono'],
                    'facultad' => $usuario['facultad'],
                    'escuela' => $usuario['escuela']
                ];
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Repopulate form with submitted data in case of error
                    $datos_formulario = $_POST;

                    // --- 1. Recoger y sanitizar datos ---
                    $nombres = trim($_POST['nombres'] ?? '');
                    $apellidos = trim($_POST['apellidos'] ?? '');
                    $telefono = trim($_POST['telefono'] ?? '');
                    $facultad = trim($_POST['facultad'] ?? '');
                    $escuela = trim($_POST['escuela'] ?? '');
                    
                    $password_actual = $_POST['password_actual'] ?? '';
                    $nuevo_password = $_POST['nuevo_password'] ?? '';
                    $confirmar_password = $_POST['confirmar_password'] ?? '';

                    $cambiosRealizados = false;
                    $passwordCambiado = false;

                    // --- 2. Actualizar datos del perfil ---
                    if (empty($nombres) || empty($apellidos)) {
                        $error = "Los nombres y apellidos son obligatorios";
                    } else {
                        if ($nombres != $usuario['nombres'] || $apellidos != $usuario['apellidos'] || $telefono != $usuario['telefono'] || $facultad != $usuario['facultad'] || $escuela != $usuario['escuela']) {
                            $perfilActualizado = $this->usuarioModel->actualizarPerfil(
                                $_SESSION['usuario_id'], $nombres, $apellidos, $telefono, $facultad, $escuela
                            );
                            if ($perfilActualizado) {
                                $cambiosRealizados = true;
                                $_SESSION['usuario_nombre'] = $nombres . ' ' . $apellidos;
                                $_SESSION['usuario_facultad'] = $facultad;
                                $_SESSION['usuario_escuela'] = $escuela;
                            } else {
                                $error = "Error al actualizar los datos del perfil.";
                            }
                        }
                    }

                    // --- 3. Procesar foto de perfil ---
                    if (empty($error) && isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
                        $ruta_imagen = $this->procesarFotoPerfil($_SESSION['usuario_id'], $_FILES['foto_perfil']);
                        if ($ruta_imagen) {
                            if ($this->usuarioModel->actualizarFoto($_SESSION['usuario_id'], $ruta_imagen)) {
                                $cambiosRealizados = true;
                            }
                        } else {
                            $error = "Error al procesar la imagen. Asegúrate de que es un formato válido (JPG, PNG, WebP) y no excede 2MB.";
                        }
                    }
                    
                    // --- 4. Procesar cambio de contraseña ---
                    if (empty($error) && (!empty($password_actual) || !empty($nuevo_password) || !empty($confirmar_password))) {
                        if (empty($password_actual) || empty($nuevo_password) || empty($confirmar_password)) {
                            $error = "Para cambiar la contraseña, debes rellenar los tres campos.";
                        } elseif (strlen($nuevo_password) < 8) {
                            $error = "La nueva contraseña debe tener al menos 8 caracteres.";
                        } elseif ($nuevo_password !== $confirmar_password) {
                            $error = "Las nuevas contraseñas no coinciden.";
                        } else {
                            if ($this->usuarioModel->cambiarPassword($_SESSION['usuario_id'], $password_actual, $nuevo_password)) {
                                $passwordCambiado = true;
                            } else {
                                $error = "La contraseña actual que ingresaste es incorrecta.";
                            }
                        }
                    }

                    // --- 5. Redireccionar o mostrar vista con errores ---
                    if (empty($error)) {
                        if ($passwordCambiado) {
                            header('Location: ' . BASE_URL . 'perfil?success=2'); // Contraseña cambiada
                            exit;
                        } elseif ($cambiosRealizados) {
                            header('Location: ' . BASE_URL . 'perfil?success=1'); // Perfil actualizado
                            exit;
                        } else {
                            // No se hicieron cambios, redirigir de vuelta al perfil
                            header('Location: ' . BASE_URL . 'perfil');
                            exit;
                        }
                    }
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'datos_formulario' => $datos_formulario,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::editar: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar el formulario de edición",
                    'usuario' => [],
                    'datos_formulario' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/editar.php';
        }
        
        public function cambiarPassword() {
            try {
                $this->verificarAutenticacion();

                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                $error = '';
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $password_actual = $_POST['password_actual'] ?? '';
                    $nuevo_password = $_POST['nuevo_password'] ?? '';
                    $confirmar_password = $_POST['confirmar_password'] ?? '';
                    
                    // VALIDACIONES MEJORADAS:
                    if (empty($password_actual) || empty($nuevo_password) || empty($confirmar_password)) {
                        $error = "Todos los campos son obligatorios";
                    } elseif ($nuevo_password !== $confirmar_password) {
                        $error = "Las contraseñas no coinciden";
                    } elseif (strlen($nuevo_password) < 8) {
                        $error = "La nueva contraseña debe tener al menos 8 caracteres";
                    } elseif (!preg_match('/[A-Z]/', $nuevo_password)) {
                        $error = "La contraseña debe contener al menos una letra mayúscula";
                    } elseif (!preg_match('/[0-9]/', $nuevo_password)) {
                        $error = "La contraseña debe contener al menos un número";
                    } elseif ($nuevo_password === $password_actual) {
                        $error = "La nueva contraseña debe ser diferente a la actual";
                    } else {
                        if ($this->usuarioModel->cambiarPassword(
                            $_SESSION['usuario_id'],
                            $password_actual,
                            $nuevo_password
                        )) {
                            header('Location: ' . BASE_URL . 'perfil?success=2');
                            exit;
                        } else {
                            $error = "La contraseña actual es incorrecta";
                        }
                    }
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::cambiarPassword: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al procesar la solicitud",
                    'usuario' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/cambiar-password.php';
        }
        
        public function publicaciones() {
            try {
                $this->verificarAutenticacion();

                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                // 1. Obtener TODAS las publicaciones del usuario
                $todas_las_publicaciones = $this->publicacionModel->obtenerPorUsuario($_SESSION['usuario_id']);
                
                // 2. Calcular estadísticas sobre la lista COMPLETA, sin importar el filtro
                $estadisticas = $this->obtenerEstadisticasPublicaciones($todas_las_publicaciones);
                
                // 3. Filtrar la lista de publicaciones para mostrar en la página
                $estado_filtro = $_GET['estado'] ?? 'all';
                $publicaciones_a_mostrar = $todas_las_publicaciones; // Por defecto, mostrar todas
                if ($estado_filtro !== 'all') {
                    $publicaciones_a_mostrar = array_filter($todas_las_publicaciones, function($pub) use ($estado_filtro) {
                        return $pub['estado'] == $estado_filtro;
                    });
                }
                
                // 4. Pasar los datos correctos a la vista
                $datosVista = [
                    'usuario' => $usuario,
                    'publicaciones' => $publicaciones_a_mostrar, // La lista filtrada
                    'estado_filtro' => $estado_filtro,
                    'estadisticas' => $estadisticas // Las estadísticas completas
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::publicaciones: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar las publicaciones",
                    'usuario' => [],
                    'publicaciones' => [],
                    'estado_filtro' => 'all',
                    'estadisticas' => [
                        'total' => 0, 'activas' => 0, 
                        'pausadas' => 0, 'eliminadas' => 0
                    ]
                ];
            }
            
            $this->cargarVista('perfil/publicaciones', $datosVista);
        }
        
        public function favoritos() {
            try {
                $this->verificarAutenticacion();

                // Obtener datos del usuario y sus publicaciones favoritas
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                $favoritos = $this->publicacionModel->obtenerFavoritos($_SESSION['usuario_id']);
                
                // Variable para marcar la vista activa en el sidebar
                $vistaActual = 'favoritos';
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::favoritos: " . $e->getMessage());
                $error = "Error al cargar los favoritos";
                $usuario = [];
                $favoritos = [];
                $vistaActual = 'favoritos';
            }
            
            // Cargar la vista. Las variables $usuario, $favoritos, $vistaActual y $error estarán disponibles.
            $this->cargarVista('perfil/favoritos', get_defined_vars());
        }
        
        public function eliminarPublicacion() {
            $this->verificarAutenticacion('perfil/publicaciones');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'perfil/publicaciones');
                exit;
            }
            
            $publicacion_id = $_POST['publicacion_id'] ?? 0;
            
            if (!$publicacion_id) {
                $_SESSION['error'] = "ID de publicación no válido";
                header('Location: ' . BASE_URL . 'perfil/publicaciones');
                exit;
            }
            
            try {
                // Verificar que la publicación pertenece al usuario
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                
                if (!$publicacion || $publicacion['id_usuario'] != $_SESSION['usuario_id']) {
                    $_SESSION['error'] = "No tienes permisos para eliminar esta publicación";
                    header('Location: ' . BASE_URL . 'perfil/publicaciones');
                    exit;
                }
                
                // Eliminar publicación (cambiar estado a eliminado)
                if ($this->publicacionModel->eliminar($publicacion_id)) {
                    $_SESSION['success'] = "Publicación eliminada exitosamente";
                } else {
                    $_SESSION['error'] = "Error al eliminar la publicación";
                }
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::eliminarPublicacion: " . $e->getMessage());
                $_SESSION['error'] = "Error al procesar la solicitud";
            }
            
            header('Location: ' . BASE_URL . 'perfil/publicaciones');
            exit;
        }
        
        
        private function obtenerEstadisticasPublicaciones($publicaciones) {
            $total = count($publicaciones);
            $activas = 0;
            $pausadas = 0;
            $eliminadas = 0;
            
            foreach ($publicaciones as $publicacion) {
                switch ($publicacion['estado']) {
                    case 1: $activas++; break;
                    case 2: $pausadas++; break;
                    case 3: $eliminadas++; break;
                }
            }
            
            return [
                'total' => $total,
                'activas' => $activas,
                'pausadas' => $pausadas,
                'eliminadas' => $eliminadas
            ];
        }

        private function procesarFotoPerfil($id_usuario, $archivo_foto) {
            $directorio_uploads = 'assets/uploads/usuarios/' . $id_usuario . '/';

            // Crear directorio si no existe
            if (!is_dir($directorio_uploads)) {
                mkdir($directorio_uploads, 0755, true);
            }

            // Limpiar directorio de fotos antiguas
            $files = glob($directorio_uploads . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            if ($archivo_foto['error'] === UPLOAD_ERR_OK) {
                $nombre_original = $archivo_foto['name'];
                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($extension, $extensiones_permitidas)) {
                    return false; // Extensión no permitida
                }

                $tipo_archivo = mime_content_type($archivo_foto['tmp_name']);
                $tipos_mime_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

                if (!in_array($tipo_archivo, $tipos_mime_permitidos)) {
                    return false; // Tipo de archivo no permitido
                }

                if (getimagesize($archivo_foto['tmp_name']) === false) {
                    return false; // No es una imagen válida
                }

                if ($archivo_foto['size'] > 2 * 1024 * 1024) { // Límite de 2MB
                    return false;
                }

                $nombre_base = 'perfil_' . uniqid();
                $nombre_archivo_webp = $nombre_base . '.webp';
                $ruta_destino = $directorio_uploads . $nombre_archivo_webp;

                $imagen_origen = null;
                switch ($tipo_archivo) {
                    case 'image/jpeg':
                        $imagen_origen = imagecreatefromjpeg($archivo_foto['tmp_name']);
                        break;
                    case 'image/png':
                        $imagen_origen = imagecreatefrompng($archivo_foto['tmp_name']);
                        imagepalettetotruecolor($imagen_origen);
                        imagealphablending($imagen_origen, true);
                        imagesavealpha($imagen_origen, true);
                        break;
                    case 'image/webp':
                        move_uploaded_file($archivo_foto['tmp_name'], $ruta_destino);
                        break;
                }

                if ($imagen_origen !== null) {
                    imagewebp($imagen_origen, $ruta_destino, 80);
                    imagedestroy($imagen_origen);
                }

                return $ruta_destino;
            }

            return false;
        }

        public function toggleFavorito() {
            header('Content-Type: application/json');
            
            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión', 'redirect' => BASE_URL . 'login']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $id_publicacion = $input['id_publicacion'] ?? $_POST['id_publicacion'] ?? 0;
            
            if (!$id_publicacion) {
                echo json_encode(['success' => false, 'error' => 'ID inválido']);
                exit;
            }
            
            $id_usuario = $_SESSION['usuario_id'];
            $esFavorito = $this->publicacionModel->esFavorito($id_usuario, $id_publicacion);
            
            if ($esFavorito) {
                $resultado = $this->publicacionModel->eliminarFavorito($id_usuario, $id_publicacion);
                $accion = 'eliminado';
            } else {
                // Al ejecutar esto, el Trigger de la BD saltará automáticamente y creará la notificación
                $resultado = $this->publicacionModel->agregarFavorito($id_usuario, $id_publicacion);
                $accion = 'agregado';
            }
            
            echo json_encode(['success' => $resultado, 'accion' => $accion]);
            exit;
        }

        public function eliminarFavorito() {
            $this->verificarAutenticacion('perfil/favoritos');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id_publicacion = $_POST['publicacion_id'] ?? 0;
                if ($id_publicacion) {
                    $this->publicacionModel->eliminarFavorito($_SESSION['usuario_id'], $id_publicacion);
                    $_SESSION['success'] = "Eliminado de favoritos correctamente.";
                }
            }
            header('Location: ' . BASE_URL . 'perfil/favoritos');
            exit;
        }

        public function ventas() {
            $this->verificarAutenticacion();
            // Obtenemos las ventas
            $ventas = $this->pagoModel->obtenerVentasPorUsuario($_SESSION['usuario_id']);
            $page_title = "Mis Ventas";
            require_once 'aplicacion/Vistas/perfil/ventas.php';
        }

        public function misCompras() {
            $this->verificarAutenticacion();
            $compras = $this->pagoModel->obtenerComprasPorUsuario($_SESSION['usuario_id']);
            $page_title = "Mis Compras";
            require_once 'aplicacion/Vistas/perfil/mis-compras.php';
        }
        // En aplicacion/Controladores/PerfilController.php

        public function configuracion() {
            $this->verificarAutenticacion();
            
            // Obtenemos los datos frescos del usuario
            $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
            
            $mensaje_exito = $_SESSION['success'] ?? '';
            $error = $_SESSION['error'] ?? '';
            unset($_SESSION['success'], $_SESSION['error']); // Limpiar flash messages
            
            $this->cargarVista('perfil/configuracion', [
                'usuario' => $usuario,
                'mensaje_exito' => $mensaje_exito,
                'error' => $error
            ]);
        }

        public function guardarConfiguracionYape() {
            $this->verificarAutenticacion();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $activo = isset($_POST['yape_activo']) ? 1 : 0; // Si el checkbox viene, es 1
                $numero = trim($_POST['yape_numero'] ?? '');
                $nombre = trim($_POST['yape_nombre'] ?? '');
                
                // Validaciones básicas si se activa
                if ($activo) {
                    if (empty($numero) || empty($nombre)) {
                        $_SESSION['error'] = "Si activas Yape, debes ingresar el número y el nombre del titular.";
                        header('Location: ' . BASE_URL . 'perfil/configuracion');
                        exit;
                    }
                }
                
                $ruta_qr = null;
                
                // Procesar imagen QR si se subió
                if (isset($_FILES['yape_qr']) && $_FILES['yape_qr']['error'] === UPLOAD_ERR_OK) {
                    // Reutilizamos o adaptamos la lógica de subida de imágenes
                    $ruta_qr = $this->procesarImagenQR($_SESSION['usuario_id'], $_FILES['yape_qr']);
                    if (!$ruta_qr) {
                        $_SESSION['error'] = "Error al subir el código QR. Formato no válido.";
                        header('Location: ' . BASE_URL . 'perfil/configuracion');
                        exit;
                    }
                }
                
                // Guardar en BD
                if ($this->usuarioModel->actualizarConfiguracionYape($_SESSION['usuario_id'], $activo, $numero, $nombre, $ruta_qr)) {
                    $_SESSION['success'] = "Configuración de pago actualizada correctamente.";
                } else {
                    $_SESSION['error'] = "Error al guardar la configuración.";
                }
            }
            
            header('Location: ' . BASE_URL . 'perfil/configuracion');
            exit;
        }

        // Helper privado para subir el QR
        private function procesarImagenQR($id_usuario, $archivo) {
            $directorio = 'assets/uploads/usuarios/' . $id_usuario . '/qr/';
            
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }
            
            $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return false;
            
            $nombre_archivo = 'yape_' . uniqid() . '.' . $ext;
            $ruta_destino = $directorio . $nombre_archivo;
            
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                return $ruta_destino;
            }
            return false;
        }
    }
?>