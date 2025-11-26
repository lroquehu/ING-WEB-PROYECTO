<?php
class NotificacionController {

    private $notificacionModel;
    private $mensajeModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Este constructor no requiere autenticación obligatoria para todos los métodos
        // porque `verificarEstado` podría ser llamado incluso por usuarios no logueados (aunque devolverá 0)
        // La protección se hará en cada método que lo requiera.
        require_once 'aplicacion/Modelos/Notificacion.php';
        require_once 'aplicacion/Modelos/Mensaje.php';
        $this->notificacionModel = new Notificacion();
        $this->mensajeModel = new Mensaje();
    }

    /**
     * Endpoint AJAX para verificar el estado de nuevas notificaciones y mensajes.
     * Devuelve un JSON con los contadores de elementos no leídos.
     */
    public function verificarEstado() {
        header('Content-Type: application/json');
        
        $id_usuario = $_SESSION['usuario_id'] ?? null;
        $response = [
            'alertas' => 0,
            'mensajes' => 0
        ];

        if ($id_usuario) {
            $response['alertas'] = $this->notificacionModel->contarNoLeidas($id_usuario);
            $response['mensajes'] = $this->mensajeModel->contarNoLeidosGlobal($id_usuario);
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Muestra la lista de notificaciones del usuario.
     */
    public function listar() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id_usuario = $_SESSION['usuario_id'];
        $notificaciones = $this->notificacionModel->obtenerUltimas($id_usuario, 25);

        $datosVista = [
            'titulo' => 'Mis Notificaciones',
            'notificaciones' => $notificaciones
        ];

        // Se necesitará una vista para esto, que se creará más adelante
        include 'aplicacion/Vistas/notificacion/index.php';
    }

    /**
     * Marca una notificación como leída y redirige al enlace asociado.
     * Espera el ID de la notificación como parte de la URL.
     */
    public function leer($params) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        $id_notificacion = $params['id_notificacion'] ?? null;
        $id_usuario = $_SESSION['usuario_id'];

        if (!$id_notificacion) {
            header('Location: ' . BASE_URL); // Redirigir al inicio si no hay ID
            exit;
        }

        // Obtenemos la notificación para verificar que pertenece al usuario y obtener el enlace
        $notificacion = $this->notificacionModel->obtenerPorId($id_notificacion);

        if ($notificacion && $notificacion['id_usuario'] == $id_usuario) {
            // Marcar como leída
            $this->notificacionModel->marcarLeida($id_notificacion, $id_usuario);
            // Redirigir al enlace
            header('Location: ' . BASE_URL . $notificacion['enlace']);
            exit;
        } else {
            // Si la notificación no existe o no pertenece al usuario, redirigir sin hacer nada
            header('Location: ' . BASE_URL . 'notificaciones');
            exit;
        }
    }

    /**
     * Endpoint AJAX para obtener las últimas 5 notificaciones para el menú desplegable.
     */
    public function obtenerRecientes() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([]);
            exit;
        }

        $id_usuario = $_SESSION['usuario_id'];
        // Obtenemos las últimas 5 para el menú rápido
        $notificaciones = $this->notificacionModel->obtenerUltimas($id_usuario, 5);
        
        echo json_encode($notificaciones);
        exit;
    }
}
?>