<?php
// aplicacion/Controladores/api/FavoritosController.php

require_once __DIR__ . '/../Modelos/Publicacion.php';
require_once __DIR__ . '/../Modelos/Notificacion.php';
require_once __DIR__ . '/../Modelos/Usuario.php';
require_once __DIR__ . '/../Helpers/imagenes.php';

class FavoritosController {
    private $publicacionModel;
    private $notificacionModel;
    private $usuarioModel;

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        $this->publicacionModel = new Publicacion();
        $this->notificacionModel = new Notificacion();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Endpoint: /api/favoritos/toggle
     * Método: POST
     * Body: { "id_usuario": 16, "id_publicacion": 65 }
     * Descripción: Si no es favorito, lo agrega. Si ya lo es, lo quita.
     */
    public function toggle() {
        $input = json_decode(file_get_contents("php://input"), true);
        $id_usuario = $input['id_usuario'] ?? 0;
        $id_publicacion = $input['id_publicacion'] ?? 0;

        if (!$id_usuario || !$id_publicacion) {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
            return;
        }

        try {
            // Verificar estado actual
            $esFavorito = $this->publicacionModel->esFavorito($id_usuario, $id_publicacion);
            $accion = '';

            if ($esFavorito) {
                $this->publicacionModel->eliminarFavorito($id_usuario, $id_publicacion);
                $accion = 'eliminado';
                $mensaje = "Eliminado de favoritos";
            } else {
                $this->publicacionModel->agregarFavorito($id_usuario, $id_publicacion);
                $accion = 'agregado';
                $mensaje = "Agregado a favoritos";

                // --- Lógica de Notificación al Vendedor ---
                // Obtenemos datos para notificar al dueño del producto
                $publicacion = $this->publicacionModel->obtenerPorId($id_publicacion);
                $dueño_id = $publicacion['id_usuario'];

                // No notificar si uno se da like a sí mismo
                if ($id_usuario != $dueño_id) {
                    $usuario_actual = $this->usuarioModel->obtenerPorId($id_usuario);
                    $textoNotif = $usuario_actual['nombres'] . " le dio me gusta a tu producto: " . $publicacion['titulo'];
                    // enlace referencial para la web (opcional en app)
                    $enlace = 'publicaciones/ver/' . $id_publicacion; 
                    
                    $this->notificacionModel->crear($dueño_id, 'favorito', $textoNotif, $enlace);
                }
            }

            echo json_encode([
                "status" => "success",
                "message" => $mensaje,
                "accion" => $accion, // 'agregado' o 'eliminado' para que la app sepa qué ícono poner
                "es_favorito" => ($accion === 'agregado')
            ]);

        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Endpoint: /api/favoritos
     * Método: GET
     * Params: ?id_usuario=16
     */
    public function index() {
        $id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

        if ($id_usuario <= 0) {
            echo json_encode(["status" => "error", "message" => "ID usuario requerido"]);
            return;
        }

        $favoritos = $this->publicacionModel->obtenerFavoritos($id_usuario);

        // Procesar imágenes
        foreach ($favoritos as &$fav) {
            if (!empty($fav['imagen_principal'])) {
                $fav['imagen_principal'] = obtenerImagenFinal($fav['imagen_principal']);
            } else {
                $fav['imagen_principal'] = PROD_IMAGE_URL . 'assets/img/no-image.png';
            }
            $fav['precio'] = (float)$fav['precio'];
        }

        echo json_encode([
            "status" => "success",
            "count" => count($favoritos),
            "data" => $favoritos
        ]);
    }
}
?>