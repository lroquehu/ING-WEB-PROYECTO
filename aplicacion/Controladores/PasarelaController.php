<?php
// Importamos la conexión y el modelo
require_once 'aplicacion/Configuracion/conexion.php'; 
require_once 'aplicacion/Modelos/Pago.php';
// Asegúrate de importar el modelo de Publicación arriba
require_once 'aplicacion/Modelos/Publicacion.php';
require_once 'aplicacion/Modelos/Notificacion.php'; // <--- NUEVO
require_once 'aplicacion/Modelos/Conversacion.php'; // <--- NUEVO
require_once 'aplicacion/Modelos/Mensaje.php';      // <--- NUEVO
require_once 'aplicacion/Modelos/Usuario.php';
class PasarelaController {
    
    private $db;
    private $pagoModelo;
    private $publicacionModelo;
    private $notificacionModelo;
    private $conversacionModelo;
    private $mensajeModelo;
    private $usuarioModelo;

    public function __construct() {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inicializamos conexión y modelo
        $conexion = new Conexion(); // Ajusta según cómo se llame tu clase en conexion.php
        $this->db = $conexion->conectar(); // O getConexion(), ajusta según tu archivo
        $this->pagoModelo = new Pago($this->db);
        $this->publicacionModelo = new Publicacion();
        $this->notificacionModelo = new Notificacion($this->db);
        $this->conversacionModelo = new Conversacion($this->db);
        $this->mensajeModelo = new Mensaje($this->db);
        $this->usuarioModelo = new Usuario();
    }

    public function index() {
        // 1. Verificamos que nos pasen un ID
        if (!isset($_GET['id'])) {
            // Si no hay ID, redirigimos al inicio o mostramos error
            header('Location: /');
            exit;
        }

        $id_publicacion = $_GET['id'];

        // 2. Buscamos los datos REALES del producto
        // Usamos la misma conexión que ya tienes
        $publicacionModelo = new Publicacion($this->db);
        $producto = $publicacionModelo->obtenerPorId($id_publicacion);

        if (!$producto) {
            echo "El producto no existe.";
            exit;
        }

        // 3. Pasamos los datos a la vista (precio, titulo, id)
        // Convertimos el precio a float para asegurarnos
        $monto = (float) $producto['precio'];
        $titulo = $producto['titulo'];
        $usuario_logueado = $_SESSION['id_usuario'] ?? 0; // Asegúrate de manejar la sesión

        require_once 'aplicacion/Vistas/pasarela/checkout.php';
    }

    public function procesar() {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if (!$datos) {
            echo json_encode(['status' => 'error', 'message' => 'Sin datos']);
            return;
        }

        // --- DATOS DEL SISTEMA ---
        // Obtenemos el ID del usuario real de la sesión
        $id_usuario = $_SESSION['usuario_id'] ?? null;
        
        // Aquí deberías recibir qué publicación se está pagando
        $id_publicacion = isset($datos['id_publicacion']) ? $datos['id_publicacion'] : null; 
        // -------------------------

        $accessToken = 'TEST-7726067468222223-112818-b85da260d5b3da201a39a1651f927ab7-3024933477'; // <--- PONGAN SU TOKEN

        if (!$id_usuario || !$id_publicacion) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'detalle' => 'Falta información del usuario o de la publicación.']);
            return;
        }
        // Preparamos datos para MP
        $paymentData = [
            "transaction_amount" => (float) $datos['transaction_amount'],
            "token" => $datos['token'],
            "description" => $datos['description'],
            "installments" => (int) $datos['installments'],
            "payment_method_id" => $datos['payment_method_id'],
            "issuer_id" => (int) $datos['issuer_id'],
            "payer" => [
                "email" => $datos['payer']['email'],
                "identification" => [
                    "type" => $datos['payer']['identification']['type'],
                    "number" => $datos['payer']['identification']['number']
                ]
            ]
        ];

        // Llamada cURL a Mercado Pago
        $ch = curl_init('https://api.mercadopago.com/v1/payments');
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . uniqid()
        ];

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $mpResponse = json_decode($response, true);

        // --- LÓGICA DE GUARDADO EN BASE DE DATOS ---
        if ($httpCode == 201 && $mpResponse['status'] == 'approved') {
            
            // Guardamos en la tabla Pagos y obtenemos el ID interno
            $id_pago_interno = $this->pagoModelo->registrarPago(
                $id_usuario,
                $id_publicacion, 
                $mpResponse['id'],           // ID de Mercado Pago
                $mpResponse['transaction_amount'],
                $mpResponse['payment_method_id'],
                $mpResponse['status'],
                $mpResponse['status_detail']
            );

            if ($id_pago_interno) {
                // --- LÓGICA NUEVA (AHORA DENTRO DEL IF) ---
                
                // A. Obtener datos necesarios
                $publicacion = $this->publicacionModelo->obtenerPorId($id_publicacion);
                $id_vendedor = $publicacion['id_usuario'];
                $comprador = $this->usuarioModelo->obtenerPorId($id_usuario);
                $nombre_comprador = $comprador['nombres'];

                // B. Crear Notificación para el Vendedor
                $mensajeNotif = "¡{$nombre_comprador} compró tu producto '{$publicacion['titulo']}'!";
                $enlaceRecibo = "pago/recibo/" . $id_pago_interno;
                
                // Asegúrate que tu modelo Notificacion tenga método crear($id_usuario, $tipo, $mensaje, $enlace)
                $this->notificacionModelo->crear($id_vendedor, 'venta', $mensajeNotif, $enlaceRecibo);

                // C. Insertar Mensaje en el Chat
                // 1. Verificar o crear conversación
                $conversacion = $this->conversacionModelo->iniciarOObtener($id_usuario, $id_vendedor);

                // 2. Insertar mensaje automático
                if ($conversacion && isset($conversacion['id_conversacion'])) {
                    $id_conversacion = $conversacion['id_conversacion'];
                    $mensajeChat = "¡" . htmlspecialchars($nombre_comprador) . " ha realizado la compra del producto '{$publicacion['titulo']}' por S/ " . number_format($mpResponse['transaction_amount'], 2) . "!";
                    // Pasamos los IDs de los participantes y el flag es_sistema = 1
                    $this->mensajeModelo->crear($id_conversacion, $id_usuario, $id_vendedor, $mensajeChat, 1);
                }

                // --- FIN LÓGICA NUEVA ---

                echo json_encode([
                    'status' => 'approved', 
                    'id_pago' => $mpResponse['id'],
                    'mensaje' => 'Pago registrado en BD exitosamente'
                ]);

            } else {
                // El pago se hizo en MP, pero falló nuestra BD (Caso raro pero crítico)
                echo json_encode([
                    'status' => 'approved_but_db_error', 
                    'id_pago' => $mpResponse['id'],
                    'mensaje' => 'Pago aprobado en MP pero error al guardar en sistema local'
                ]);
            }

        } else {
            // Pago rechazado
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'detalle' => $mpResponse['status_detail'] ?? 'Error desconocido',
                'raw' => $mpResponse
            ]);
        }
    }

    public function recibo($params) {
        $id_pago = $params['id'] ?? 0;

        if (!$id_pago) {
            header("Location: /error/404");
            exit;
        }

        $datos_recibo = $this->pagoModelo->obtenerPagoPorId($id_pago);

        if (!$datos_recibo) {
            // El pago no existe o el usuario no tiene permiso para verlo
            // (puedes añadir una validación de permisos aquí)
            header("Location: /error/404");
            exit;
        }

        require_once 'aplicacion/Vistas/pasarela/recibo.php';
    }
}
?>