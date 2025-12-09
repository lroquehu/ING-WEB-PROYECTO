<?php
// aplicacion/Controladores/api/PerfilController.php

require_once __DIR__ . '/../Modelos/Usuario.php';
require_once __DIR__ . '/../Modelos/Publicacion.php';
require_once __DIR__ . '/../Helpers/imagenes.php';

class PerfilController {
    private $usuarioModel;
    private $publicacionModel;

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        // Permitir métodos específicos
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        $this->usuarioModel = new Usuario();
        $this->publicacionModel = new Publicacion();
    }

    /**
     * Endpoint: /api/perfil
     * Método: GET
     * Params: ?id_usuario=15
     * Descripción: Obtiene datos del perfil y estadísticas.
     */
    public function index() {
        $id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

        if ($id_usuario <= 0) {
            echo json_encode(["status" => "error", "message" => "ID de usuario requerido"]);
            return;
        }

        // 1. Obtener datos del usuario
        $usuario = $this->usuarioModel->obtenerPorId($id_usuario);

        if (!$usuario) {
            echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
            return;
        }

        // Procesar foto de perfil
        if (!empty($usuario['foto_perfil'])) {
            $usuario['foto_perfil'] = obtenerImagenFinal($usuario['foto_perfil']);
        }

        // 2. Obtener estadísticas (usamos el método existente en tu modelo)
        $estadisticas = $this->usuarioModel->obtenerEstadisticasCompletas($id_usuario);

        echo json_encode([
            "status" => "success",
            "data" => [
                "usuario" => $usuario,
                "estadisticas" => $estadisticas
            ]
        ]);
    }

    /**
     * Endpoint: /api/perfil/mis-publicaciones
     * Método: GET
     * Params: ?id_usuario=15
     */
    public function mispublicaciones() {
        $id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

        if ($id_usuario <= 0) {
            echo json_encode(["status" => "error", "message" => "ID de usuario requerido"]);
            return;
        }

        // true = incluir eliminados/pausados si quisieras, o false para solo activos
        // Tu modelo: obtenerPorUsuario($id_usuario, $incluir_eliminados)
        $publicaciones = $this->publicacionModel->obtenerPorUsuario($id_usuario, true);

        // Procesar imágenes
        foreach ($publicaciones as &$pub) {
            // El modelo devuelve un campo 'imagen' (que es la principal)
            if (!empty($pub['imagen'])) {
                $pub['imagen'] = obtenerImagenFinal($pub['imagen']);
            } else {
                $pub['imagen'] = PROD_IMAGE_URL . 'assets/img/no-image.png';
            }
        }

        echo json_encode([
            "status" => "success",
            "count" => count($publicaciones),
            "data" => $publicaciones
        ]);
    }

    /**
     * Endpoint: /api/perfil/editar
     * Método: POST
     * Params (JSON o Form-Data): id_usuario, nombres, apellidos, telefono, facultad, escuela
     */
    public function editar() {
        // Al trabajar con Flutter y posibles archivos, es mejor verificar $_POST primero
        // Si viene json raw, lo decodificamos
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Prioridad: $_POST (Form-Data) > JSON Raw
        $id_usuario = $_POST['id_usuario'] ?? $input['id_usuario'] ?? 0;
        $nombres = $_POST['nombres'] ?? $input['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? $input['apellidos'] ?? '';
        $telefono = $_POST['telefono'] ?? $input['telefono'] ?? '';
        $facultad = $_POST['facultad'] ?? $input['facultad'] ?? '';
        $escuela = $_POST['escuela'] ?? $input['escuela'] ?? '';

        if (!$id_usuario || empty($nombres) || empty($apellidos)) {
            echo json_encode(["status" => "error", "message" => "Datos incompletos (ID, Nombres y Apellidos son obligatorios)"]);
            return;
        }

        try {
            // 1. Actualizar datos de texto
            $resultado = $this->usuarioModel->actualizarPerfil(
                $id_usuario,
                $nombres,
                $apellidos,
                $telefono,
                $facultad,
                $escuela
            );

            if ($resultado) {
                // 2. Manejar subida de foto si existe ($_FILES)
                // Nota: Flutter debe enviar esto como Multipart Request
                if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                    $ruta_imagen = $this->procesarFotoPerfil($id_usuario, $_FILES['foto_perfil']);
                    
                    if ($ruta_imagen) {
                        $this->usuarioModel->actualizarFoto($id_usuario, $ruta_imagen);
                    }
                }

                echo json_encode(["status" => "success", "message" => "Perfil actualizado correctamente"]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo actualizar la base de datos"]);
            }

        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Método auxiliar privado para procesar la imagen (copiado y adaptado de tu PerfilController original)
    private function procesarFotoPerfil($id_usuario, $archivo_foto) {
        // Ajustamos la ruta para que sea relativa desde el root del proyecto, no desde api/
        $directorio_uploads = __DIR__ . '/../../assets/uploads/usuarios/' . $id_usuario . '/';
        
        // Ruta relativa para guardar en BD
        $ruta_bd_base = 'assets/uploads/usuarios/' . $id_usuario . '/';

        if (!is_dir($directorio_uploads)) {
            mkdir($directorio_uploads, 0755, true);
        }

        // Limpiar fotos viejas
        $files = glob($directorio_uploads . '*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }

        $nombre_base = 'perfil_' . uniqid() . '.webp';
        $ruta_destino = $directorio_uploads . $nombre_base;
        
        // Simple move_uploaded_file para simplificar, o usar tu lógica de conversión si el servidor tiene GD habilitado
        // Aquí uso una versión simplificada que intenta convertir a webp si es posible, sino solo mueve
        
        $tipo = mime_content_type($archivo_foto['tmp_name']);
        $imagen = null;

        switch ($tipo) {
            case 'image/jpeg': $imagen = imagecreatefromjpeg($archivo_foto['tmp_name']); break;
            case 'image/png': $imagen = imagecreatefrompng($archivo_foto['tmp_name']); break;
            case 'image/webp': $imagen = imagecreatefromwebp($archivo_foto['tmp_name']); break;
        }

        if ($imagen) {
            imagewebp($imagen, $ruta_destino, 80);
            imagedestroy($imagen);
            return $ruta_bd_base . $nombre_base;
        } else {
            // Fallback si falla la librería GD
            move_uploaded_file($archivo_foto['tmp_name'], $ruta_destino);
            return $ruta_bd_base . $nombre_base;
        }
    }
}
?>
