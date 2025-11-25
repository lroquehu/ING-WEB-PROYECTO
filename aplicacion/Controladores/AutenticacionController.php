<?php
    class AutenticacionController {
        private $usuarioModel;
        
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            require_once 'aplicacion/Modelos/Usuario.php';
            $this->usuarioModel = new Usuario();
        }
        
        public function login() {
            // Si ya está autenticado, redirigir al inicio
            if (isset($_SESSION['usuario_id'])) {
                header('Location: ' . BASE_URL . 'inicio');
                exit;
            }

            // Inicializar protección contra fuerza bruta
            if (!isset($_SESSION['intentos_login'])) {
                $_SESSION['intentos_login'] = 0;
                $_SESSION['bloqueo_hasta'] = 0;
            }
            
            $ahora = time();
            
            // Verificar si está bloqueado temporalmente 
            if (isset($_SESSION['bloqueo_hasta']) && $_SESSION['bloqueo_hasta'] > $ahora) {
                $tiempo_restante = $_SESSION['bloqueo_hasta'] - $ahora;
                $minutos = ceil($tiempo_restante / 60);
                $error = "Demasiados intentos fallidos. Espera {$minutos} " . ($minutos == 1 ? 'minuto' : 'minutos') . ".";
                include 'aplicacion/Vistas/autenticacion/login.php';
                return;
            }
            
            // Si el bloqueo expiró, resetear contador 
            if (isset($_SESSION['bloqueo_hasta']) && $_SESSION['bloqueo_hasta'] > 0 && $_SESSION['bloqueo_hasta'] <= $ahora) {
                $_SESSION['intentos_login'] = 0;
                $_SESSION['bloqueo_hasta'] = 0;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar token CSRF de forma segura
                if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
                    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $error = "Token de seguridad inválido";
                    include 'aplicacion/Vistas/autenticacion/login.php';
                    return;
                }
                
                // Limpiar token CSRF después de usar
                unset($_SESSION['csrf_token']);
                
                $correo = trim($_POST['correo'] ?? '');
                $contrasenia = $_POST['contrasenia'] ?? '';
                
                // Validaciones básicas
                if (empty($correo) || empty($contrasenia)) {
                    $error = "Por favor ingresa correo y contraseña";
                    include 'aplicacion/Vistas/autenticacion/login.php';
                    return;
                }
                
                $usuario = $this->usuarioModel->login($correo, $contrasenia);
                
                if ($usuario) {
                    // Login exitoso - resetear contadores
                    $_SESSION['intentos_login'] = 0;
                    $_SESSION['bloqueo_hasta'] = 0;
                    
                    // Establecer datos de sesión
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                    $_SESSION['usuario_correo'] = $usuario['correo_institucional'];
                    $_SESSION['usuario_facultad'] = $usuario['facultad'] ?? '';
                    $_SESSION['usuario_escuela'] = $usuario['escuela'] ?? '';
                    
                    // Regenerar ID de sesión por seguridad
                    session_regenerate_id(true);
                    
                    // Redirigir a la página anterior o al inicio
                    $redirect = $this->validarUrlRedireccion($_SESSION['redirect_url'] ?? BASE_URL . 'inicio');
                    unset($_SESSION['redirect_url']);
                    
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    // Login fallido - incrementar contador
                    $_SESSION['intentos_login']++;
                    
                    // Bloquear después de 5 intentos fallidos por 5 minutos
                    if ($_SESSION['intentos_login'] >= 5) {
                        $_SESSION['bloqueo_hasta'] = time() + 300; // 5 minutos
                        $error = "Demasiados intentos fallidos. Tu cuenta ha sido bloqueada por 5 minutos.";
                    } else {
                        $intentos_restantes = 5 - $_SESSION['intentos_login'];
                        $error = "Credenciales incorrectas. Te quedan {$intentos_restantes} intentos.";
                    }
                    
                    include 'aplicacion/Vistas/autenticacion/login.php';
                    return;
                }
            } else {
                // Generar token CSRF para GET requests
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Guardar URL anterior para redirigir después del login
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                if (!empty($referer) && strpos($referer, 'login') === false) {
                    $_SESSION['redirect_url'] = $referer;
                } else {
                    $_SESSION['redirect_url'] = BASE_URL . 'inicio';
                }
                
                // Mostrar formulario de login
                include 'aplicacion/Vistas/autenticacion/login.php';
                return;
            }
        }
        
        public function registro() {
            // Si ya está autenticado, redirigir al inicio
            if (isset($_SESSION['usuario_id'])) {
                header('Location: ' . BASE_URL . 'inicio');
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar token CSRF de forma segura
                if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
                    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $error = "Token de seguridad inválido";
                    include 'aplicacion/Vistas/autenticacion/registro.php';
                    return;
                }
                
                // Limpiar token CSRF después de usar
                unset($_SESSION['csrf_token']);
                
                // Recoger y sanitizar datos para HTML
                $nombres = htmlspecialchars(trim($_POST['nombres'] ?? ''), ENT_QUOTES, 'UTF-8');
                $apellidos = htmlspecialchars(trim($_POST['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
                $dni = trim($_POST['dni'] ?? '');
                $telefono = trim($_POST['telefono'] ?? '');
                $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
                $codigo_univ = trim($_POST['codigo_univ'] ?? '');
                $facultad = htmlspecialchars(trim($_POST['facultad'] ?? ''), ENT_QUOTES, 'UTF-8');
                $escuela = htmlspecialchars(trim($_POST['escuela'] ?? ''), ENT_QUOTES, 'UTF-8');
                $contrasenia = $_POST['contrasenia'] ?? '';
                $confirmar_contrasenia = $_POST['confirmar_contrasenia'] ?? '';
                $terminos = isset($_POST['terminos']);
                
                // Validaciones
                $errores = [];
                
                if (empty($nombres)) $errores[] = "El nombre es obligatorio";
                if (empty($apellidos)) $errores[] = "Los apellidos son obligatorios";
                if (empty($dni) || !preg_match('/^[0-9]{8}$/', $dni)) $errores[] = "El DNI debe tener 8 dígitos";
                if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido";
                if (empty($codigo_univ)) $errores[] = "El código universitario es obligatorio";
                if (empty($contrasenia)) $errores[] = "La contraseña es obligatoria";
                if (!$terminos) $errores[] = "Debes aceptar los términos y condiciones";
                
                // Validación de fortaleza de contraseña
                if (strlen($contrasenia) < 8) {
                    $errores[] = "La contraseña debe tener al menos 8 caracteres";
                }
                if (!preg_match('/[A-Z]/', $contrasenia)) {
                    $errores[] = "La contraseña debe contener al menos una mayúscula";
                }
                if (!preg_match('/[0-9]/', $contrasenia)) {
                    $errores[] = "La contraseña debe contener al menos un número";
                }
                
                // ✅ CORRECCIÓN CRÍTICA: Comparación SEGURA de contraseñas
                if (!hash_equals($contrasenia, $confirmar_contrasenia)) {
                    $errores[] = "Las contraseñas no coinciden";
                }
                
                if (empty($errores)) {
                    // ✅ CORRECCIÓN: Incluir código_univ en el registro
                    $id_usuario_nuevo = $this->usuarioModel->registrar(
                        $nombres, 
                        $apellidos, 
                        $dni, 
                        $telefono, 
                        $correo,
                        $codigo_univ, 
                        $facultad, 
                        $escuela, 
                        $contrasenia
                    );

                    if ($id_usuario_nuevo) {
                        // Procesar foto de perfil si se subió una
                        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
                            $ruta_imagen = $this->procesarFotoPerfil($id_usuario_nuevo, $_FILES['foto_perfil']);
                            if ($ruta_imagen) {
                                $this->usuarioModel->actualizarFoto($id_usuario_nuevo, $ruta_imagen);
                            }
                        }
                        
                        $_SESSION['success'] = "Cuenta creada exitosamente. Ahora puedes iniciar sesión.";
                        header('Location: ' . BASE_URL . 'login');
                        exit;
                    } else {
                        $errores[] = "Error al crear la cuenta. El correo, DNI o código universitario ya están registrados.";
                    }
                }
                
                // Pasar datos al formulario para rellenar
                $datos_formulario = [
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'dni' => $dni,
                    'telefono' => $telefono,
                    'correo' => $correo,
                    'codigo_univ' => $codigo_univ,
                    'facultad' => $facultad,
                    'escuela' => $escuela
                ];
                
                // Si hay errores, mostrar formulario con errores
                $error = $errores;
                include 'aplicacion/Vistas/autenticacion/registro.php';
                return;
            } else {
                // Generar token CSRF para GET requests
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Datos iniciales vacíos
                $datos_formulario = [
                    'nombres' => '', 'apellidos' => '', 'dni' => '', 'telefono' => '',
                    'correo' => '', 'codigo_univ' => '', 'facultad' => '', 'escuela' => ''
                ];
                
                // Mostrar formulario de registro
                include 'aplicacion/Vistas/autenticacion/registro.php';
                return;
            }
        }
        
        public function logout() {
            // Limpiar todas las variables de sesión
            $_SESSION = array();
            
            // Destruir la sesión
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();
            
            // Redirigir al inicio
            header('Location: ' . BASE_URL . 'inicio');
            exit;
        }
        
        /**
         * Valida que la URL de redirección sea del mismo dominio
         */
        private function validarUrlRedireccion($url) {
            $base_domain = parse_url(BASE_URL, PHP_URL_HOST);
            $redirect_domain = parse_url($url, PHP_URL_HOST);
            
            // Si no podemos parsear la URL o los dominios no coinciden, usar inicio
            if ($base_domain === false || $redirect_domain === false || $redirect_domain !== $base_domain) {
                return BASE_URL . 'inicio';
            }
            
            return $url;
        }

        private function procesarFotoPerfil($id_usuario, $archivo_foto) {
            $directorio_uploads = 'assets/uploads/usuarios/' . $id_usuario . '/';

            // Crear directorio si no existe
            if (!is_dir($directorio_uploads)) {
                mkdir($directorio_uploads, 0755, true);
            }

            if ($archivo_foto['error'] === UPLOAD_ERR_OK) {
                $nombre_original = $archivo_foto['name'];
                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($extension, $extensiones_permitidas)) return false;

                $tipo_archivo = mime_content_type($archivo_foto['tmp_name']);
                $tipos_mime_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

                if (!in_array($tipo_archivo, $tipos_mime_permitidos)) return false;

                if (getimagesize($archivo_foto['tmp_name']) === false) return false;

                if ($archivo_foto['size'] > 2 * 1024 * 1024) return false;

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
    }
?>