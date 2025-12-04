<?php
// aplicacion/Controladores/api/ChatController.php

require_once __DIR__ . '/../../Modelos/Conversacion.php';
require_once __DIR__ . '/../../Modelos/Mensaje.php';
require_once __DIR__ . '/../../Modelos/Usuario.php';
require_once __DIR__ . '/../../Helpers/imagenes.php';

class ChatController {
    private $conversacionModel;
    private $mensajeModel;
    private $usuarioModel;

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        $this->conversacionModel = new Conversacion();
        $this->mensajeModel = new Mensaje();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Endpoint: /api/chat
     * Método: GET
     * Params: ?id_usuario=16
     * Descripción: Lista todas las conversaciones del usuario
     */
    public function index() {
        $id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

        if ($id_usuario <= 0) {
            echo json_encode(["status" => "error", "message" => "ID usuario requerido"]);
            return;
        }

        $conversaciones = $this->conversacionModel->obtenerPorUsuario($id_usuario);

        // Procesamos para mejorar la data que recibe la App (ej. fotos)
        foreach ($conversaciones as &$conv) {
            // Buscamos la foto del "otro usuario" para mostrarla en la lista
            // Nota: Tu modelo ya devuelve 'id_otro_usuario', pero no su foto.
            // Si tu consulta SQL en Conversacion::obtenerPorUsuario no trae foto,
            // podemos obtenerla extra o mejorar el modelo. 
            // Por simplicidad y rendimiento, aquí asumiremos que la App puede cargarla
            // o hacemos una consulta rápida si es crítico.
            
            // Para asegurar, vamos a obtener datos básicos del otro usuario si faltan
            if (isset($conv['id_otro_usuario'])) {
                $otro = $this->usuarioModel->obtenerPorId($conv['id_otro_usuario']);
                if ($otro && !empty($otro['foto_perfil'])) {
                    $conv['foto_otro_usuario'] = obtenerImagenFinal($otro['foto_perfil']);
                } else {
                    $conv['foto_otro_usuario'] = PROD_IMAGE_URL . 'assets/iconos/user.webp';
                }
            }
        }

        echo json_encode([
            "status" => "success",
            "data" => $conversaciones
        ]);
    }

    /**
     * Endpoint: /api/chat/mensajes
     * Método: GET
     * Params: ?id_conversacion=10&id_usuario=16
     * Descripción: Obtiene los mensajes de una charla y los marca como leídos
     */
    public function mensajes() {
        $id_conversacion = isset($_GET['id_conversacion']) ? (int)$_GET['id_conversacion'] : 0;
        $id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0; // El que está leyendo (yo)

        if (!$id_conversacion || !$id_usuario) {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
            return;
        }

        // Verificar acceso (opcional pero recomendado)
        $conversacion = $this->conversacionModel->obtenerPorId($id_conversacion, $id_usuario);
        if (!$conversacion) {
            echo json_encode(["status" => "error", "message" => "Conversación no encontrada o acceso denegado"]);
            return;
        }

        // Marcar como leídos (porque los estoy viendo)
        $this->mensajeModel->marcarComoLeidos($id_conversacion, $id_usuario);

        // Obtener mensajes
        $mensajes = $this->mensajeModel->obtenerPorConversacion($id_conversacion);

        echo json_encode([
            "status" => "success",
            "data" => $mensajes
        ]);
    }

    /**
     * Endpoint: /api/chat/iniciar
     * Método: POST
     * Body: { "id_usuario": 16, "id_otro_usuario": 20 }
     * Descripción: Crea una conversación nueva o devuelve la existente
     */
    public function iniciar() {
        $input = json_decode(file_get_contents("php://input"), true);
        $id_usuario = $input['id_usuario'] ?? 0; // Yo
        $id_otro_usuario = $input['id_otro_usuario'] ?? 0; // El vendedor

        if (!$id_usuario || !$id_otro_usuario || $id_usuario == $id_otro_usuario) {
            echo json_encode(["status" => "error", "message" => "IDs inválidos"]);
            return;
        }

        $conversacion = $this->conversacionModel->iniciarOObtener($id_usuario, $id_otro_usuario);

        if ($conversacion) {
            echo json_encode([
                "status" => "success",
                "id_conversacion" => $conversacion['id_conversacion'],
                "message" => "Conversación lista"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al iniciar conversación"]);
        }
    }

    /**
     * Endpoint: /api/chat/enviar
     * Método: POST
     * Body: { "id_conversacion": 10, "id_usuario": 16, "contenido": "Hola, sigue disponible?" }
     */
    public function enviar() {
        $input = json_decode(file_get_contents("php://input"), true);
        $id_conversacion = $input['id_conversacion'] ?? 0;
        $id_usuario = $input['id_usuario'] ?? 0; // Remitente
        $contenido = trim($input['contenido'] ?? '');

        if (!$id_conversacion || !$id_usuario || empty($contenido)) {
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
            return;
        }

        // Obtener datos de la conversación para saber quién es el destinatario
        $conversacion = $this->conversacionModel->obtenerPorId($id_conversacion, $id_usuario);
        
        if (!$conversacion) {
            echo json_encode(["status" => "error", "message" => "No tienes permiso en esta conversación"]);
            return;
        }

        // Calcular destinatario
        $id_destinatario = ($conversacion['id_usuario1'] == $id_usuario) 
            ? $conversacion['id_usuario2'] 
            : $conversacion['id_usuario1'];

        // Guardar mensaje
        $mensaje = $this->mensajeModel->crear($id_conversacion, $id_usuario, $id_destinatario, $contenido);

        if ($mensaje) {
            echo json_encode([
                "status" => "success",
                "data" => $mensaje
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al enviar mensaje"]);
        }
    }
}
?>