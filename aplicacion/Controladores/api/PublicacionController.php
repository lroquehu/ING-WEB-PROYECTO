<?php
// aplicacion/Controladores/api/PublicacionController.php

require_once __DIR__ . '/../../Modelos/Publicacion.php';
require_once __DIR__ . '/../../Modelos/Categoria.php';
require_once __DIR__ . '/../../Helpers/imagenes.php'; 

class PublicacionController {
    private $publicacionModel;
    private $categoriaModel;

    public function __construct() {
        // Permitir CORS y métodos
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        $this->publicacionModel = new Publicacion();
        $this->categoriaModel = new Categoria();
    }

    // --- MÉTODOS PÚBLICOS (LECTURA) ---

    public function index() {
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
        $orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha_desc';
        $limit = 10; 

        if (!empty($busqueda)) {
            $productos = $this->publicacionModel->buscar($busqueda, $categoria_id, $pagina, $limit);
        } else {
            $productos = $this->publicacionModel->obtenerTodos($pagina, $limit, $categoria_id, '', $orden);
        }

        foreach ($productos as &$prod) {
            if (!empty($prod['imagen_principal'])) {
                $prod['imagen_principal'] = obtenerImagenFinal($prod['imagen_principal']);
            } else {
                $prod['imagen_principal'] = PROD_IMAGE_URL . 'assets/img/no-image.png'; 
            }
            $prod['precio'] = (float)$prod['precio'];
        }

        echo json_encode([
            "status" => "success",
            "page" => $pagina,
            "data" => $productos
        ]);
    }

    public function categorias() {
        $categorias = $this->categoriaModel->obtenerTodas();
        echo json_encode(["status" => "success", "data" => $categorias]);
    }
    
    public function detalle() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { echo json_encode(["status" => "error", "message" => "ID inválido"]); return; }

        $producto = $this->publicacionModel->obtenerPorId($id);

        if ($producto) {
            $imagenes = $this->publicacionModel->obtenerImagenes($id);
            $listaImagenes = [];
            foreach($imagenes as $img) {
                // Incluimos ID de imagen para poder borrarla luego en la edición
                $listaImagenes[] = [
                    'id_imagen' => $img['id_imagen'],
                    'url' => obtenerImagenFinal($img['url_imagen'])
                ];
            }
            $producto['galeria'] = $listaImagenes;
            if (isset($producto['imagen_principal'])) { 
                 $producto['imagen_principal'] = obtenerImagenFinal($producto['imagen_principal']);
            }
            $producto['precio'] = (float)$producto['precio'];

            echo json_encode(["status" => "success", "data" => $producto]);
        } else {
            echo json_encode(["status" => "error", "message" => "Producto no encontrado"]);
        }
    }

    // --- MÉTODOS PRIVADOS (ESCRITURA - REQUIEREN ID_USUARIO) ---

    /**
     * Crear Publicación
     * Método: POST (Multipart/Form-data)
     */
    public function crear() {
        // En POST Multipart, los campos vienen en $_POST directamente
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $categoria_id = $_POST['categoria_id'] ?? 0;
        $precio = $_POST['precio'] ?? 0;
        $tipo = $_POST['tipo'] ?? 'Producto';
        $telefono = $_POST['telefono_contacto'] ?? '';
        $correo = $_POST['correo_contacto'] ?? '';

        if (!$id_usuario || empty($titulo) || empty($descripcion) || !$categoria_id) {
            echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            return;
        }

        try {
            $nuevoId = $this->publicacionModel->crear([
                'id_usuario' => $id_usuario,
                'id_categoria' => $categoria_id,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'precio' => $precio,
                'telefono_contacto' => $telefono,
                'correo_contacto' => $correo
            ]);

            if ($nuevoId) {
                // Procesar imágenes (RF017)
                $imgsProcesadas = 0;
                if (!empty($_FILES['imagenes']['name'][0])) {
                    $imgsProcesadas = $this->procesarImagenes($nuevoId, $_FILES['imagenes']);
                }

                echo json_encode([
                    "status" => "success",
                    "message" => "Publicación creada exitosamente",
                    "id_publicacion" => $nuevoId,
                    "imagenes_guardadas" => $imgsProcesadas
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al guardar en BD"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Editar Publicación
     * Método: POST (Multipart/Form-data)
     */
    public function editar() {
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $id_publicacion = $_POST['id_publicacion'] ?? 0;
        
        if (!$id_usuario || !$id_publicacion) {
            echo json_encode(["status" => "error", "message" => "ID usuario y publicación requeridos"]);
            return;
        }

        // Verificar propiedad
        $pub = $this->publicacionModel->obtenerPorId($id_publicacion);
        if (!$pub || $pub['id_usuario'] != $id_usuario) {
            echo json_encode(["status" => "error", "message" => "No autorizado o no existe"]);
            return;
        }

        $datos = [
            'id_categoria' => $_POST['categoria_id'] ?? $pub['id_categoria'],
            'titulo' => $_POST['titulo'] ?? $pub['titulo'],
            'descripcion' => $_POST['descripcion'] ?? $pub['descripcion'],
            'tipo' => $_POST['tipo'] ?? $pub['tipo'],
            'precio' => $_POST['precio'] ?? $pub['precio'],
            'telefono_contacto' => $_POST['telefono_contacto'] ?? $pub['telefono_contacto'],
            'correo_contacto' => $_POST['correo_contacto'] ?? $pub['correo_contacto'],
            'estado' => $_POST['estado'] ?? $pub['estado']
        ];

        if ($this->publicacionModel->actualizar($id_publicacion, $datos)) {
            // 1. Eliminar imágenes marcadas (si la app envía IDs para borrar)
            // Formato esperado: imagenes_eliminar = "12,15,8" (string separado por comas)
            if (!empty($_POST['imagenes_eliminar'])) {
                $ids = explode(',', $_POST['imagenes_eliminar']);
                foreach ($ids as $imgId) {
                    $this->publicacionModel->eliminarImagen($imgId);
                }
            }

            // 2. Subir nuevas imágenes
            if (!empty($_FILES['imagenes']['name'][0])) {
                $this->procesarImagenes($id_publicacion, $_FILES['imagenes']);
            }

            echo json_encode(["status" => "success", "message" => "Publicación actualizada"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar"]);
        }
    }

    /**
     * Eliminar (Soft Delete)
     * Método: POST
     */
    public function eliminar() {
        // Leemos JSON raw porque delete suele enviar pocos datos
        $input = json_decode(file_get_contents("php://input"), true);
        $id_usuario = $input['id_usuario'] ?? $_POST['id_usuario'] ?? 0;
        $id_publicacion = $input['id_publicacion'] ?? $_POST['id_publicacion'] ?? 0;

        if (!$id_usuario || !$id_publicacion) {
            echo json_encode(["status" => "error", "message" => "Faltan datos"]);
            return;
        }

        $pub = $this->publicacionModel->obtenerPorId($id_publicacion);
        if (!$pub || $pub['id_usuario'] != $id_usuario) {
            echo json_encode(["status" => "error", "message" => "No autorizado"]);
            return;
        }

        // Usamos cambiarEstado a 3 (Eliminado) en lugar de borrar físico, más seguro
        if ($this->publicacionModel->cambiarEstado($id_publicacion, 3)) {
            echo json_encode(["status" => "success", "message" => "Publicación eliminada"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al eliminar"]);
        }
    }

    // --- HELPER INTERNO PARA IMÁGENES ---
    private function procesarImagenes($publicacion_id, $archivos) {
        // Ajustamos ruta al root del proyecto
        $directorio_uploads = __DIR__ . '/../../assets/uploads/publicaciones/' . $publicacion_id . '/';
        $ruta_bd_base = 'assets/uploads/publicaciones/' . $publicacion_id . '/';
        
        if (!is_dir($directorio_uploads)) mkdir($directorio_uploads, 0755, true);
        
        $count = 0;
        // Revisar si ya tiene principal
        $imgsExistentes = $this->publicacionModel->obtenerImagenes($publicacion_id);
        $tienePrincipal = false;
        foreach($imgsExistentes as $img) if($img['es_principal'] == 1) $tienePrincipal = true;

        // Iterar archivos
        foreach ($archivos['tmp_name'] as $index => $tmp_name) {
            if ($archivos['error'][$index] === UPLOAD_ERR_OK) {
                $nombre_base = uniqid() . '_' . bin2hex(random_bytes(4)) . '.webp';
                $ruta_destino = $directorio_uploads . $nombre_base;
                
                // Intento simple de mover o convertir
                // (Para simplificar la API, usaremos move_uploaded_file si no es imagen compleja,
                // pero idealmente deberías usar la misma lógica de conversión de tu controlador web)
                if (move_uploaded_file($tmp_name, $ruta_destino)) {
                    $es_principal = (!$tienePrincipal && $count == 0) ? 1 : 0;
                    $this->publicacionModel->agregarImagen([
                        'id_publicacion' => $publicacion_id,
                        'url_imagen' => $ruta_bd_base . $nombre_base,
                        'es_principal' => $es_principal
                    ]);
                    if($es_principal) $tienePrincipal = true;
                    $count++;
                }
            }
        }
        return $count;
    }
}
?>