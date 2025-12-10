<?php
// Asegúrate de que las rutas relativas a los modelos sean correctas desde esta subcarpeta
require_once __DIR__ . '/../Modelos/Usuario.php';

// Importar clases de PHPMailer necesarias para la recuperación
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $usuarioModel;

    public function __construct() {
        // Cabeceras OBLIGATORIAS para API REST
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Manejo de preflight request (CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $this->usuarioModel = new Usuario();
    }

    /**
     * Endpoint: /api/auth/login
     * Método: POST
     * Body JSON: { "email": "correo@uni.pe", "password": "123" }
     */
    public function login() {
        // Leer el JSON raw que envía Flutter
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->email) && !empty($data->password)) {
            // Usamos tu modelo existente. 
            // NOTA: Tu modelo devuelve un array si es exitoso o false si falla.
            $usuario = $this->usuarioModel->login($data->email, $data->password);

            if ($usuario) {
                // Verificar si la cuenta está verificada (según tu lógica actual)
                if ($usuario['verificado'] == 0) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Cuenta no verificada. Revisa tu correo."
                    ]);
                    return;
                }

                // ÉXITO: Devolvemos los datos del usuario.
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Login exitoso",
                    "data" => [
                        "id_usuario" => $usuario['id_usuario'],
                        "nombres" => $usuario['nombres'],
                        "apellidos" => $usuario['apellidos'],
                        "foto_perfil" => $usuario['foto_perfil'] ? PROD_IMAGE_URL . $usuario['foto_perfil'] : null,
                        "correo" => $usuario['correo_institucional']
                    ]
                ]);
            } else {
                http_response_code(401); // Unauthorized
                echo json_encode([
                    "status" => "error",
                    "message" => "Credenciales incorrectas"
                ]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos (email o password)"
            ]);
        }
    }

    /**
     * Endpoint: /api/auth/registro
     * Método: POST
     */
    public function registro() {
        $data = json_decode(file_get_contents("php://input"));

        // Validación básica
        if (
            !empty($data->nombres) && 
            !empty($data->apellidos) && 
            !empty($data->correo) && 
            !empty($data->password) &&
            !empty($data->dni) &&
            !empty($data->codigo_univ)
        ) {
            // Validar dominio
            if (!str_ends_with(strtolower($data->correo), '@unjbg.edu.pe')) {
                echo json_encode(["status" => "error", "message" => "Solo correos @unjbg.edu.pe"]);
                return;
            }

            try {
                // Registrar usuario (estado=1, verificado=0)
                $id_nuevo = $this->usuarioModel->registrar(
                    $data->nombres,
                    $data->apellidos,
                    $data->dni,
                    $data->telefono ?? '',
                    $data->correo,
                    $data->codigo_univ,
                    $data->facultad ?? '',
                    $data->escuela ?? '',
                    $data->password
                );

                if ($id_nuevo) {
                    // --- LÓGICA DE VERIFICACIÓN (NUEVO) ---
                    $token = bin2hex(random_bytes(32));
                    // Usar fecha DB o PHP (aquí usaremos PHP para simplificar, asegúrate que la zona horaria coincida)
                    $expiracion = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

                    // Guardar token en BD
                    if ($this->usuarioModel->guardarTokenVerificacion($id_nuevo, $token, $expiracion)) {
                        
                        // Enviar Correo
                        require_once __DIR__ . '/../../Vendor/PHPMailer/src/Exception.php';
                        require_once __DIR__ . '/../../Vendor/PHPMailer/src/PHPMailer.php';
                        require_once __DIR__ . '/../../Vendor/PHPMailer/src/SMTP.php';
                        require_once __DIR__ . '/../../Configuracion/email.php';

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
                            $mail->addAddress($data->correo, $data->nombres);

                            $mail->isHTML(true);
                            $mail->Subject = 'Verifica tu cuenta - UniEmprende';
                            
                            // Enviamos el Token para que lo ingresen en la App
                            $mail->Body    = "¡Hola " . htmlspecialchars($data->nombres) . "!<br><br>
                                              Gracias por registrarte. Para activar tu cuenta desde la App, usa este código:<br><br>
                                              <h1 style='color:#004b8d'>{$token}</h1><br><br>
                                              O haz clic aquí: <a href='" . BASE_URL . "verificar-correo/{$token}'>Verificar cuenta</a>";
                            
                            $mail->send();

                        } catch (Exception $e) {
                            // Log error pero no detener el registro
                            error_log("Error enviando correo API: " . $e->getMessage());
                        }
                    }
                    // --- FIN LÓGICA VERIFICACIÓN ---
                    
                    http_response_code(201); // Created
                    echo json_encode([
                        "status" => "success",
                        "message" => "Usuario registrado. Revisa tu correo para el código de verificación.",
                        "user_id" => $id_nuevo
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error",
                        "message" => "No se pudo registrar. Correo o DNI ya existen."
                    ]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        }
    }

    /**
     * Endpoint: /api/auth/recuperar-password
     * Método: POST
     * Body JSON: { "correo": "usuario@unjbg.edu.pe" }
     */
    public function solicitarRecuperacion() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->correo)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El correo es obligatorio"]);
            return;
        }

        $usuario = $this->usuarioModel->obtenerPorCorreo($data->correo);

        if ($usuario) {
            try {
                // 1. Generar token
                $token = bin2hex(random_bytes(32));

                // 2. Calcular expiración (Sincronizado con hora DB)
                $fecha_actual_db_str = $this->usuarioModel->obtenerFechaActualDB();
                if (!$fecha_actual_db_str) {
                    throw new Exception("Error interno de fecha.");
                }
                $fecha_actual_db = new DateTime($fecha_actual_db_str);
                $fecha_actual_db->modify('+1 hour'); // Expiración en 1 hora
                $expiracion = $fecha_actual_db->format('Y-m-d\TH:i:s');

                // 3. Guardar token
                $this->usuarioModel->guardarTokenRecuperacion($usuario['id_usuario'], $token, $expiracion);

                // 4. Configurar y enviar correo
                require_once __DIR__ . '/../Vendor/PHPMailer/src/Exception.php';
                require_once __DIR__ . '/../Vendor/PHPMailer/src/PHPMailer.php';
                require_once __DIR__ . '/../Vendor/PHPMailer/src/SMTP.php';
                require_once __DIR__ . '/../Configuracion/email.php';

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = MAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = MAIL_USERNAME;
                $mail->Password   = MAIL_PASSWORD;
                $mail->SMTPSecure = MAIL_ENCRYPTION;
                $mail->Port       = MAIL_PORT;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
                $mail->addAddress($data->correo, $usuario['nombres']);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña - UniEmprende (App)';
                
                // NOTA: Enviamos el token para que la App pueda manejar la vista de "Nueva Contraseña"
                // O un enlace web si prefieres redireccionar al navegador.
                $token_code = $token; 
                
                $mail->Body    = "Hola " . htmlspecialchars($usuario['nombres']) . ",<br><br>Has solicitado restablecer tu contraseña desde la aplicación móvil.<br><br>Usa el siguiente Token en la App:<br><h1>{$token_code}</h1><br>O si estás en web, usa este enlace: <a href='" . BASE_URL . "resetear-password/{$token}'>Restablecer aquí</a><br><br>Si no solicitaste esto, ignora este correo.<br><br>Saludos,<br>UniEmprende";
                
                $mail->send();

                http_response_code(200);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Correo de recuperación enviado",
                    "data" => ["token_hint" => "Token enviado al correo"] 
                ]);

            } catch (Exception $e) {
                error_log("API Error Mail: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "No se pudo enviar el correo. Inténtalo más tarde."]);
            }
        } else {
            // Por seguridad, a veces se responde success aunque el correo no exista
            // para no revelar usuarios, pero aquí indicaremos error para la UI.
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "El correo no está registrado"]);
        }
    }

    /**
     * Endpoint: /api/auth/resetear-password
     * Método: POST
     * Body JSON: { "token": "...", "password": "...", "confirm_password": "..." }
     */
    public function resetearPassword() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->token) || empty($data->password) || empty($data->confirm_password)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Faltan datos requeridos"]);
            return;
        }

        if ($data->password !== $data->confirm_password) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Las contraseñas no coinciden"]);
            return;
        }

        // Verificar token
        $tokenData = $this->usuarioModel->obtenerTokenValido($data->token);

        if (!$tokenData) {
            http_response_code(400); // Bad Request o 401
            echo json_encode(["status" => "error", "message" => "El token es inválido o ha expirado"]);
            return;
        }

        // Cambiar contraseña
        $exito = $this->usuarioModel->restablecerPasswordConToken(
            $tokenData['id_usuario'],
            $data->password,
            $tokenData['id_token']
        );

        if ($exito) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Contraseña actualizada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Error al actualizar la contraseña"]);
        }
    }

    /**
     * Endpoint: /api/auth/verificar-cuenta
     * Método: POST
     * Body JSON: { "token": "..." }
     */
    public function verificarCuenta() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->token)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El token es obligatorio"]);
            return;
        }

        // Buscar usuario con ese token válido
        $usuario = $this->usuarioModel->obtenerUsuarioPorTokenVerificacion($data->token);

        if ($usuario) {
            // Marcar como verificado y limpiar token
            $this->usuarioModel->marcarUsuarioComoVerificado($usuario['id_usuario']);
            
            http_response_code(200);
            echo json_encode([
                "status" => "success", 
                "message" => "¡Cuenta verificada con éxito! Ahora puedes iniciar sesión."
            ]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => "error", 
                "message" => "El código de verificación es inválido o ha expirado."
            ]);
        }
    }
}
?>