<?php
class ChatController {
    private $conversacionModel;
    private $mensajeModel;
    private $usuarioModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        require_once 'aplicacion/Modelos/Conversacion.php';
        require_once 'aplicacion/Modelos/Mensaje.php';
        require_once 'aplicacion/Modelos/Usuario.php';
        $this->conversacionModel = new Conversacion();
        $this->mensajeModel = new Mensaje();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra la lista de conversaciones del usuario.
     */
    public function index() {
        $id_usuario_actual = $_SESSION['usuario_id'];
        $conversaciones = $this->conversacionModel->obtenerPorUsuario($id_usuario_actual);

        $datosVista = [
            'titulo' => 'Mis Mensajes',
            'conversaciones' => $conversaciones
        ];

        include 'aplicacion/Vistas/chat/index.php';
    }

    /**
     * Muestra una conversación específica y sus mensajes.
     */
    public function ver($params) {
        $id_conversacion = $params['id'] ?? null;
        if (!$id_conversacion) {
            header('Location: ' . BASE_URL . 'chat');
            exit;
        }

        $id_usuario_actual = $_SESSION['usuario_id'];
        $conversacion = $this->conversacionModel->obtenerPorId($id_conversacion, $id_usuario_actual);

        if (!$conversacion) {
            // Si no tiene permiso o no existe, redirigir
            $_SESSION['error_chat'] = "La conversación no existe o no tienes acceso.";
            header('Location: ' . BASE_URL . 'chat');
            exit;
        }

        // Determinar el ID del otro usuario
        $id_otro_usuario = ($conversacion['id_usuario1'] == $id_usuario_actual) 
            ? $conversacion['id_usuario2'] 
            : $conversacion['id_usuario1'];
        
        $otro_usuario = $this->usuarioModel->obtenerPorId($id_otro_usuario);

        // Marcar mensajes como leídos
        $this->mensajeModel->marcarComoLeidos($id_conversacion, $id_usuario_actual);

        $mensajes = $this->mensajeModel->obtenerPorConversacion($id_conversacion);

        $datosVista = [
            'titulo' => 'Chat con ' . htmlspecialchars($otro_usuario['nombres']),
            'conversacion' => $conversacion,
            'otro_usuario' => $otro_usuario,
            'mensajes' => $mensajes,
            'id_usuario_actual' => $id_usuario_actual
        ];

        include 'aplicacion/Vistas/chat/ver.php';
    }

    /**
     * Inicia una conversación con un usuario.
     * Si ya existe, redirige a la conversación. Si no, la crea.
     */
    public function iniciar() {
        $id_destinatario = $_GET['destinatario'] ?? null;
        $id_usuario_actual = $_SESSION['usuario_id'];

        if (!$id_destinatario || $id_destinatario == $id_usuario_actual) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $conversacion = $this->conversacionModel->iniciarOObtener($id_usuario_actual, $id_destinatario);

        if ($conversacion) {
            header('Location: ' . BASE_URL . 'chat/ver/' . $conversacion['id_conversacion']);
        } else {
            $_SESSION['error_chat'] = "No se pudo iniciar la conversación.";
            header('Location: ' . BASE_URL . 'chat');
        }
        exit;
    }

    /**
     * Endpoint para enviar un mensaje (usado por AJAX).
     */
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id_conversacion = $_POST['id_conversacion'] ?? null;
        $contenido = trim($_POST['contenido'] ?? '');
        $id_usuario_actual = $_SESSION['usuario_id'];

        if (!$id_conversacion || empty($contenido)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Faltan datos']);
            exit;
        }

        $conversacion = $this->conversacionModel->obtenerPorId($id_conversacion, $id_usuario_actual);
        if (!$conversacion) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'No tienes permiso para enviar mensajes en esta conversación']);
            exit;
        }

        $id_destinatario = ($conversacion['id_usuario1'] == $id_usuario_actual) 
            ? $conversacion['id_usuario2'] 
            : $conversacion['id_usuario1'];

        $mensaje = $this->mensajeModel->crear($id_conversacion, $id_usuario_actual, $id_destinatario, $contenido);

        if ($mensaje) {
            echo json_encode(['success' => true, 'mensaje' => $mensaje]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'No se pudo enviar el mensaje']);
        }
        exit;
    }

    /**
     * Endpoint para obtener nuevos mensajes (usado por AJAX para polling).
     */
    public function obtenerNuevos() {
        $id_conversacion = $_GET['id_conversacion'] ?? null;
        $id_usuario_actual = $_SESSION['usuario_id'];

        if (!$id_conversacion) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta el ID de la conversación']);
            exit;
        }

        // Verificar permiso
        $conversacion = $this->conversacionModel->obtenerPorId($id_conversacion, $id_usuario_actual);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        $nuevos_mensajes = $this->mensajeModel->obtenerNuevos($id_conversacion, $id_usuario_actual);

        if (!empty($nuevos_mensajes)) {
            // Marcar como leídos después de obtenerlos
            $this->mensajeModel->marcarComoLeidos($id_conversacion, $id_usuario_actual);
        }

        echo json_encode(['success' => true, 'mensajes' => $nuevos_mensajes]);
        exit;
    }

    /**
     * Endpoint para eliminar un mensaje (usado por AJAX).
     */
    public function eliminarMensaje() {
        // Solo procesar peticiones POST y con sesión activa
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
            http_response_code(405);
            echo json_encode(['error' => 'Acceso no permitido']);
            exit;
        }

        // Obtener el cuerpo de la petición JSON
        $datos = json_decode(file_get_contents("php://input"));

        $id_mensaje = $datos->id_mensaje ?? null;
        $id_usuario_actual = $_SESSION['usuario_id'];

        header('Content-Type: application/json');
        $respuesta = ['success' => false];

        if (!$id_mensaje) {
            http_response_code(400);
            $respuesta['error'] = 'ID de mensaje no proporcionado.';
            echo json_encode($respuesta);
            exit;
        }

        // Intentar eliminar el mensaje usando el modelo
        if ($this->mensajeModel->eliminarMensaje($id_mensaje, $id_usuario_actual)) {
            $respuesta['success'] = true;
            $respuesta['id_mensaje'] = $id_mensaje;
        } else {
            http_response_code(403); // Forbidden
            $respuesta['error'] = 'No se pudo eliminar el mensaje o no tienes permiso.';
        }

        echo json_encode($respuesta);
        exit;
    }
}
?>