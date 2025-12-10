<?php
    // Importar las clases de PHPMailer al espacio de nombres global
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class AutenticacionController {
        private $usuarioModel;
        
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            require_once 'aplicacion/Modelos/Usuario.php';
            $this->usuarioModel = new Usuario();
            
            // Generar el token CSRF si no existe
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
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
                    // --- Lógica diferenciada para Eliminados vs Suspendidos ---
                    if ((int)$usuario['estado'] === 0) {
                        
                        // CASO 1: USUARIO ELIMINADO (Soft Delete)
                        // Detectamos si tiene la "firma" de eliminación que pusimos en el Modelo
                        if ($usuario['nombres'] === 'Usuario' && $usuario['apellidos'] === 'Eliminado') {
                            $_SESSION['error_login'] = "
                                <div class='text-center'>
                                    <i class='fas fa-user-times fa-2x mb-2'></i><br>
                                    <strong>Cuenta Eliminada</strong><br>
                                    Esta cuenta ha sido eliminada permanentemente y ya no es accesible.
                                </div>";
                        } 
                        // CASO 2: USUARIO SUSPENDIDO (Temporal)
                        else {
                            $fecha_fin_db = $usuario['suspension_fin'] ?? null;
                            $motivo_texto = $usuario['motivo_suspension'] ?? 'Sin motivo especificado';

                            $fecha_mostrar = 'Indefinido';
                            if ($fecha_fin_db) {
                                $fecha_mostrar = date('d/m/Y H:i', strtotime($fecha_fin_db));
                            }
                            
                            $_SESSION['error_login'] = "Tu cuenta está suspendida hasta el: <strong>$fecha_mostrar</strong>.<br>Motivo: " . htmlspecialchars($motivo_texto);
                        }
                        
                        // Redirigir al login para mostrar el mensaje
                        header('Location: ' . BASE_URL . 'login');
                        exit;
                    }
                    // ---------------------------------------------------------------------

                    // Verificar si la cuenta está verificada
                    if ($usuario['verificado'] == 0) {
                        $error = "Tu cuenta aún no ha sido verificada. Por favor, revisa tu correo electrónico y sigue el enlace de verificación.";
                        include 'aplicacion/Vistas/autenticacion/login.php';
                        return;
                    }

                    // Login exitoso - resetear contadores
                    $_SESSION['intentos_login'] = 0;
                    $_SESSION['bloqueo_hasta'] = 0;
                    
                    // Establecer datos de sesión
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                    $_SESSION['usuario_correo'] = $usuario['correo_institucional'];
                    $_SESSION['usuario_facultad'] = $usuario['facultad'] ?? '';
                    $_SESSION['usuario_escuela'] = $usuario['escuela'] ?? '';
                    $_SESSION['usuario_rol'] = $usuario['rol'];
                    $_SESSION['usuario_foto'] = $usuario['foto_perfil'] ?? null;

                    session_regenerate_id(true);

                    // Redirigir según el rol del usuario
                    if (strtolower($usuario['rol']) === 'admin') {
                        header('Location: ' . BASE_URL . 'admin');
                    } else {
                        $redirect = $this->validarUrlRedireccion($_SESSION['redirect_url'] ?? BASE_URL . 'inicio');
                        unset($_SESSION['redirect_url']);
                        header('Location: ' . $redirect);
                    }
                    exit;
                } else {
                    // Login fallido
                    $_SESSION['intentos_login']++;
                    
                    if ($_SESSION['intentos_login'] >= 10) {
                        $_SESSION['bloqueo_hasta'] = time() + 60; 
                        $error = "Demasiados intentos fallidos. Tu cuenta ha sido bloqueada por 1 minuto.";
                    } else {
                        $intentos_restantes = 10 - $_SESSION['intentos_login'];
                        $error = "Credenciales incorrectas. Te quedan {$intentos_restantes} intentos.";
                    }
                    
                    include 'aplicacion/Vistas/autenticacion/login.php';
                    return;
                }
            } else {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                if (!empty($referer) && strpos($referer, 'login') === false) {
                    $_SESSION['redirect_url'] = $referer;
                } else {
                    $_SESSION['redirect_url'] = BASE_URL . 'inicio';
                }
                
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
                //unset($_SESSION['csrf_token']);
                
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
                
                if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "El correo electrónico no es válido.";
                } elseif (!str_ends_with(strtolower($correo), '@unjbg.edu.pe')) {
                    $errores[] = "Solo se permiten correos institucionales con el dominio @unjbg.edu.pe.";
                }
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
                
                if (!hash_equals($contrasenia, $confirmar_contrasenia)) {
                    $errores[] = "Las contraseñas no coinciden";
                }
                
                if (empty($errores)) {
                    // Asignar rol predeterminado si no se pasa
                    $rol = $rol ?? 'estudiante';

                    // 1. Asignar el resultado a la variable correcta
                    $id_usuario_nuevo = $this->usuarioModel->registrar(
                        $nombres, 
                        $apellidos, 
                        $dni, 
                        $telefono, 
                        $correo,
                        $codigo_univ, 
                        $facultad, 
                        $escuela, 
                        $contrasenia,
                        $rol
                    );
                    
                    // 2. Verificar si es un número (ID válido)
                    if (is_numeric($id_usuario_nuevo) && $id_usuario_nuevo > 0) {
                    } else {
                        $errores[] = $id_usuario_nuevo ? $id_usuario_nuevo : "Error al crear la cuenta.";
                    }

                    if ($id_usuario_nuevo) {
                        // Procesar foto de perfil si se subió una
                        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
                            $ruta_imagen = $this->procesarFotoPerfil($id_usuario_nuevo, $_FILES['foto_perfil']);
                            if ($ruta_imagen) {
                                $this->usuarioModel->actualizarFoto($id_usuario_nuevo, $ruta_imagen);
                            }
                        }
                        
                        // --- INICIO: Lógica de Verificación de Correo ---
                        $token_verificacion = bin2hex(random_bytes(32));
                        $expiracion = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

                        if ($this->usuarioModel->guardarTokenVerificacion($id_usuario_nuevo, $token_verificacion, $expiracion)) {
                            // Enviar correo de verificación
                            require_once 'aplicacion/Vendor/PHPMailer/src/Exception.php';
                            require_once 'aplicacion/Vendor/PHPMailer/src/PHPMailer.php';
                            require_once 'aplicacion/Vendor/PHPMailer/src/SMTP.php';
                            require_once 'aplicacion/Configuracion/email.php';

                            $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();
                                $mail->Host       = MAIL_HOST;
                                $mail->SMTPAuth   = true;
                                $mail->Username   = MAIL_USERNAME;
                                $mail->Password   = MAIL_PASSWORD;
                                $mail->SMTPSecure = MAIL_ENCRYPTION;
                                $mail->Port       = MAIL_PORT;
                                $mail->CharSet    = 'UTF-8';

                                $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
                                $mail->addAddress($correo, $nombres);

                                $mail->isHTML(true);
                                $mail->Subject = 'Verifica tu cuenta en UniEmprende';
                                $enlace = BASE_URL . 'verificar-correo/' . $token_verificacion;
                                $mail->Body    = "¡Hola " . htmlspecialchars($nombres) . "!<br><br>Gracias por registrarte en UniEmprende. Para activar tu cuenta, por favor haz clic en el siguiente enlace:<br><br><a href='{$enlace}'>Verificar mi cuenta</a><br><br>Este enlace expirará en 1 hora.<br><br>Saludos,<br>El equipo de UniEmprende";
                                $mail->AltBody = "Hola " . htmlspecialchars($nombres) . ",\n\nPara verificar tu cuenta, copia y pega el siguiente enlace en tu navegador:\n{$enlace}\n\nEste enlace expirará en 1 hora.";

                                $mail->send();

                                $_SESSION['success_registro'] = "¡Registro casi completo! Se ha enviado un enlace de verificación a tu correo <strong>" . htmlspecialchars($correo) . "</strong>. Por favor, revisa tu bandeja de entrada para activar tu cuenta.";
                                header('Location: ' . BASE_URL . 'login');
                                exit;

                            } catch (Exception $e) {
                                $errores[] = "Error al enviar el correo de verificación. Por favor, contacta a soporte. Error: {$mail->ErrorInfo}";
                            }

                            unset($_SESSION['csrf_token']); // Borrar token solo al final
                            $_SESSION['success_registro'] = "...";
                            header('Location: ' . BASE_URL . 'login');
                            exit;
                        } else {
                            $errores[] = "Error al guardar el token de verificación.";
                        }
                        // --- FIN: Lógica de Verificación de Correo ---

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
            $_SESSION = array();
            
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();
            
            header('Location: ' . BASE_URL);
            exit;
        }

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

        // Muestra y procesa el formulario de recuperación de contraseña.
        public function solicitarRecuperacion() {
            // Si ya está autenticado, no tiene sentido estar aquí
            if (isset($_SESSION['usuario_id'])) {
                header('Location: ' . BASE_URL . 'inicio');
                exit;
            }

            $error = '';
            $success = '';

            // NUEVO: Comprobar si hay un mensaje de error desde la redirección de reseteo
            if (isset($_SESSION['error_reset'])) {
                $error = $_SESSION['error_reset'];
                unset($_SESSION['error_reset']); // Limpiar para que no se muestre de nuevo
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validar token CSRF
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $error = "Token de seguridad inválido. Por favor, inténtalo de nuevo.";
                } else {
                    $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);

                    if (!$correo) {
                        $error = "Por favor, introduce una dirección de correo electrónico válida.";
                    } else {
                        // Lógica de recuperación
                        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);

                        if ($usuario) {
                            // Generar un token seguro y único
                            $token = bin2hex(random_bytes(32));

                            // --- CORRECCIÓN DE ZONA HORARIA ---
                            // Obtener la hora actual de la BD para evitar desincronización de zonas horarias.
                            $fecha_actual_db_str = $this->usuarioModel->obtenerFechaActualDB();
                            if (!$fecha_actual_db_str) {
                                throw new Exception("No se pudo obtener la hora del servidor de base de datos.");
                            }
                            $fecha_actual_db = new DateTime($fecha_actual_db_str);
                            $fecha_actual_db->modify('+5 minutes'); // Añadir 5 minutos
                            $expiracion = $fecha_actual_db->format('Y-m-d\TH:i:s');
                            // --- FIN DE CORRECCIÓN ---

                            // Guardar el token en la base de datos para este usuario
                            // NOTA: Esto requiere que la tabla 'Usuarios' tenga las columnas 'token_recuperacion' y 'expiracion_token'.
                            $this->usuarioModel->guardarTokenRecuperacion($usuario['id_usuario'], $token, $expiracion);

                            // --- INICIO: Lógica para enviar el correo ---
                            require_once 'aplicacion/Vendor/PHPMailer/src/Exception.php';
                            require_once 'aplicacion/Vendor/PHPMailer/src/PHPMailer.php';
                            require_once 'aplicacion/Vendor/PHPMailer/src/SMTP.php';
                            require_once 'aplicacion/Configuracion/email.php';

                            $mail = new PHPMailer(true);

                            try {
                                // Configuración del servidor
                                $mail->isSMTP();
                                $mail->Host       = MAIL_HOST;
                                $mail->SMTPAuth   = true;
                                $mail->Username   = MAIL_USERNAME;
                                $mail->Password   = MAIL_PASSWORD;
                                $mail->SMTPSecure = MAIL_ENCRYPTION;
                                $mail->Port       = MAIL_PORT;
                                $mail->CharSet    = 'UTF-8';

                                // Remitente y destinatario
                                $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
                                $mail->addAddress($correo, htmlspecialchars($usuario['nombres']));

                                // Contenido del correo
                                $mail->isHTML(true);
                                $mail->Subject = 'Recuperación de Contraseña - UniEmprende';
                                $enlace = BASE_URL . 'resetear-password/' . $token; // Esta será la próxima página a crear
                                $mail->Body    = "Hola " . htmlspecialchars($usuario['nombres']) . ",<br><br>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace:<br><br><a href='{$enlace}'>Restablecer Contraseña</a><br><br>Si no solicitaste esto, puedes ignorar este correo.<br><br>Saludos,<br>El equipo de UniEmprende";
                                $mail->AltBody = "Hola " . htmlspecialchars($usuario['nombres']) . ",\n\nPara restablecer tu contraseña, copia y pega el siguiente enlace en tu navegador:\n{$enlace}\n\nSi no solicitaste esto, ignora este correo.";

                                $mail->send();
                                $success = "Se ha enviado un enlace de recuperación al correo <strong>" . htmlspecialchars($correo) . "</strong>. Revisa tu bandeja de entrada (y la carpeta de spam).";
                            } catch (Exception $e) {
                                $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
                            }
                            // --- FIN: Lógica para enviar el correo ---
                        } else {
                            // Mensaje de error si el correo no existe
                            $error = "El correo electrónico <strong>" . htmlspecialchars($correo) . "</strong> no se encuentra registrado en nuestra base de datos.";
                        }
                    }
                }
            }

            // Generar un nuevo token CSRF para el formulario
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            include 'aplicacion/Vistas/autenticacion/recuperar.php';
        }

        /**
         * Muestra y procesa el formulario para restablecer la contraseña con un token.
         */
        public function resetearPassword($params) {
            $token = $params['token'] ?? null;

            if (!$token) {
                header('Location: ' . BASE_URL . 'login');
                exit;
            }

            // Verificar si el token es válido
            $tokenData = $this->usuarioModel->obtenerTokenValido($token);

            if (!$tokenData) {
                $_SESSION['error_reset'] = "El enlace de recuperación es inválido o ha expirado. Por favor, solicita uno nuevo.";
                header('Location: ' . BASE_URL . 'recuperar-password');
                exit;
            }

            $error = '';
            $success = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    $error = "Token de seguridad inválido.";
                } else {
                    $nueva_contrasenia = $_POST['nueva_contrasenia'] ?? '';
                    $confirmar_contrasenia = $_POST['confirmar_contrasenia'] ?? '';

                    if (empty($nueva_contrasenia) || empty($confirmar_contrasenia)) {
                        $error = "Ambos campos de contraseña son obligatorios.";
                    } elseif (strlen($nueva_contrasenia) < 8) {
                        $error = "La contraseña debe tener al menos 8 caracteres.";
                    } elseif ($nueva_contrasenia !== $confirmar_contrasenia) {
                        $error = "Las contraseñas no coinciden.";
                    } else {
                        // Todo es válido, proceder a cambiar la contraseña
                        $exito = $this->usuarioModel->restablecerPasswordConToken(
                            $tokenData['id_usuario'],
                            $nueva_contrasenia,
                            $tokenData['id_token']
                        );

                        if ($exito) {
                            $success = "¡Tu contraseña ha sido actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.";
                        } else {
                            $error = "Ocurrió un error inesperado al actualizar tu contraseña. Por favor, inténtalo de nuevo.";
                        }
                    }
                }
            }

            // Generar un nuevo token CSRF para el formulario
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Pasar el token a la vista para incluirlo en el formulario
            $datosVista = ['token' => $token, 'error' => $error, 'success' => $success];
            extract($datosVista);

            include 'aplicacion/Vistas/autenticacion/resetear.php';
        }

        /**
         * Procesa la verificación de correo electrónico a través de un token.
         */
        public function verificarCorreo($params) {
            $token = $params['token'] ?? null;

            if (!$token) {
                header('Location: ' . BASE_URL . 'login');
                exit;
            }

            $usuario = $this->usuarioModel->obtenerUsuarioPorTokenVerificacion($token);

            if ($usuario) {
                // El token es válido y no ha expirado, verificar al usuario
                $this->usuarioModel->marcarUsuarioComoVerificado($usuario['id_usuario']);
                $_SESSION['success'] = "¡Tu cuenta ha sido verificada con éxito! Ahora puedes iniciar sesión.";
            } else {
                // Token inválido o expirado
                $_SESSION['error_login'] = "El enlace de verificación es inválido o ha expirado. Por favor, intenta registrarte de nuevo o contacta a soporte.";
            }

            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }
?>