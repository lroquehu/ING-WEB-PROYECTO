<?php
// Asegúrate de que las rutas relativas a los modelos sean correctas desde esta subcarpeta
require_once __DIR__ . '/../../Modelos/Usuario.php';

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
                // IMPORTANTE: Para una API profesional, aquí deberíamos generar un JWT (JSON Web Token).
                // Por ahora, devolveremos el ID del usuario para que la App lo guarde.
                
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
     * Body JSON: { "nombres": "...", "apellidos": "...", "correo": "...", ... }
     */
    public function registro() {
        $data = json_decode(file_get_contents("php://input"));

        // Validación básica de campos requeridos
        if (
            !empty($data->nombres) && 
            !empty($data->apellidos) && 
            !empty($data->correo) && 
            !empty($data->password) &&
            !empty($data->dni) &&
            !empty($data->codigo_univ)
        ) {
            // Validar dominio correo (Lógica de tu negocio)
            if (!str_ends_with(strtolower($data->correo), '@unjbg.edu.pe')) {
                echo json_encode(["status" => "error", "message" => "Solo correos @unjbg.edu.pe"]);
                return;
            }

            try {
                // Llamamos a tu método registrar existente
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
                    // AQUÍ deberías disparar la lógica de envío de correo de verificación
                    // Puedes reutilizar la lógica de PHPMailer que tienes en AutenticacionController web
                    // encapsulándola en un método privado o Helper.
                    
                    http_response_code(201); // Created
                    echo json_encode([
                        "status" => "success",
                        "message" => "Usuario registrado. Verifique su correo.",
                        "user_id" => $id_nuevo
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error",
                        "message" => "No se pudo registrar el usuario. Correo o DNI ya existen."
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
}
?>