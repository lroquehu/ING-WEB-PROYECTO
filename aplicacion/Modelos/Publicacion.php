<?php
class Publicacion {
    private $db;
    private $table = 'Publicaciones';
    private $table_imagenes = 'ImagenesPublicacion';
    private $table_movimientos = 'Movimientos';
    
    public function __construct() {
        require_once 'aplicacion/Configuracion/conexion.php';
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }
    
    /**
     * Verificar si la conexión a la base de datos está activa
     */
    private function verificarConexion() {
        if (!$this->db) {
            throw new Exception("Error de conexión a la base de datos");
        }
    }
    
    /**
     * Obtener todos los productos con paginación y filtros
     */
    /**
     * Obtener todos los productos con paginación y filtros
     * MODIFICADO: Acepta $id_usuario_target para verificar favoritos
     */
    public function obtenerTodos($pagina = 1, $limite = 12, $categoria_id = 0, $tipo = '', $orden = 'fecha_desc', $id_usuario_target = '')
    {
        try {
            $this->verificarConexion();
            $offset = ($pagina - 1) * $limite;

            // --- LÓGICA FAVORITOS ---
            // Si nos pasan un ID de usuario, verificamos si le dio like
            $check_favorito = !empty($id_usuario_target) && $id_usuario_target != '0';
            
            // Columna dinámica: Devuelve 1 si existe en la tabla Favoritos, sino 0
            $campo_favorito = $check_favorito ? 
                ", (CASE WHEN F.id_publicacion IS NOT NULL THEN 1 ELSE 0 END) as es_favorito" : 
                ", 0 as es_favorito";
            
            // Join condicional solo si hay usuario logueado
            $join_favoritos = $check_favorito ? 
                "LEFT JOIN Favoritos F ON F.id_publicacion = p.id_publicacion AND F.id_usuario = :id_usuario_target" : 
                "";
            // ------------------------

            $query = "SELECT 
                        p.id_publicacion, p.titulo, p.descripcion, p.precio, p.tipo,
                        p.estado, p.fecha_publicacion, p.fecha_actualizacion,
                        p.telefono_contacto, p.correo_contacto,
                        u.id_usuario, u.nombres, u.apellidos, u.facultad, u.escuela, u.foto_perfil,
                        c.id_categoria, c.nombre_categoria
                        
                        $campo_favorito  /* <-- AQUÍ SE AGREGA LA COLUMNA MÁGICA */

                        , (SELECT TOP 1 ip.url_imagen
                        FROM {$this->table_imagenes} ip
                        WHERE ip.id_publicacion = p.id_publicacion 
                        AND ip.es_principal = 1) AS imagen_principal,

                        (SELECT COUNT(*) 
                        FROM {$this->table_movimientos} m 
                        WHERE m.id_publicacion = p.id_publicacion) AS total_vistas

                    FROM {$this->table} p
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    $join_favoritos  /* <-- AQUÍ SE HACE EL JOIN */
                    WHERE p.estado = 1";

            $params = [];

            // Filtros existentes
            if ($categoria_id > 0) {
                $query .= " AND p.id_categoria = :categoria_id";
                $params[':categoria_id'] = $categoria_id;
            }

            if (!empty($tipo) && in_array($tipo, ['Producto', 'Servicio'])) {
                $query .= " AND p.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }

            // Ordenamiento
            $ordenes_validos = [
                'fecha_desc'   => 'p.fecha_publicacion DESC',
                'fecha_asc'    => 'p.fecha_publicacion ASC',
                'precio_asc'   => 'p.precio ASC',
                'precio_desc'  => 'p.precio DESC',
                'titulo_asc'   => 'p.titulo ASC',
                'titulo_desc'  => 'p.titulo DESC'
            ];
            $orden_sql = $ordenes_validos[$orden] ?? 'p.fecha_publicacion DESC';
            $query .= " ORDER BY {$orden_sql}";

            // Paginación
            $query .= " OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY";

            $stmt = $this->db->prepare($query);

            // Bindings normales
            foreach ($params as $key => $value) {
                $tipoParam = PDO::PARAM_STR;
                if ($key === ':categoria_id') $tipoParam = PDO::PARAM_INT;
                $stmt->bindValue($key, $value, $tipoParam);
            }

            // Binding especial para favoritos
            if ($check_favorito) {
                $stmt->bindValue(':id_usuario_target', $id_usuario_target, PDO::PARAM_INT);
            }

            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    
    /**
     * Contar todos los productos con filtros
     */
    public function contarTodos($categoria_id = 0, $tipo = '') {
        try {
            $this->verificarConexion();
            
            $query = "SELECT COUNT(*) as total 
                    FROM {$this->table} p
                    WHERE p.estado = 1";
            
            $params = [];
            
            if ($categoria_id > 0) {
                $query .= " AND p.id_categoria = :categoria_id";
                $params[':categoria_id'] = $categoria_id;
            }
            
            if (!empty($tipo) && in_array($tipo, ['Producto', 'Servicio'])) {
                $query .= " AND p.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }
            
            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $tipo = PDO::PARAM_STR;
                if ($key === ':categoria_id') {
                    $tipo = PDO::PARAM_INT;
                }
                $stmt->bindValue($key, $value, $tipo);
            }
            
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::contarTodos: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id_publicacion) {
        try {
            $this->verificarConexion();
            
            // MODIFICADO: Se añade subconsulta para el rating del vendedor
            $query = "SELECT p.*,
                            u.nombres, u.apellidos, u.telefono, u.correo_institucional,
                            u.facultad, u.escuela, u.fecha_registro, u.foto_perfil,
                            c.nombre_categoria,
                            (SELECT COUNT(*) FROM {$this->table_movimientos} 
                            WHERE id_publicacion = p.id_publicacion AND tipo_movimiento = 'Vista') as total_vistas,
                            (SELECT ISNULL(AVG(CAST(v.puntuacion AS FLOAT)), 0) 
                             FROM Valoraciones v 
                             JOIN Publicaciones p_v ON v.id_publicacion = p_v.id_publicacion 
                             WHERE p_v.id_usuario = p.id_usuario) as vendedor_rating
                    FROM {$this->table} p
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.id_publicacion = :id_publicacion";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener productos por usuario
     */
    public function obtenerPorUsuario($id_usuario, $incluir_eliminados = false) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT 
                        p.*, 
                        c.nombre_categoria,
                        (
                            SELECT TOP 1 url_imagen 
                            FROM {$this->table_imagenes}
                            WHERE id_publicacion = p.id_publicacion
                            AND es_principal = 1
                            ORDER BY id_imagen ASC
                        ) AS imagen,
                        (
                            SELECT COUNT(*) 
                            FROM {$this->table_movimientos}
                            WHERE id_publicacion = p.id_publicacion AND tipo_movimiento = 'Vista'
                        ) AS total_vistas
                    FROM {$this->table} p
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.id_usuario = :id_usuario";
            
            if (!$incluir_eliminados) {
                $query .= " AND p.estado != 3";
            }
            
            $query .= " ORDER BY p.fecha_publicacion DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerPorUsuario: " . $e->getMessage());
            return [];
        }
    }

    
    /**
     * Contar productos por usuario
     */
    public function contarPorUsuario($id_usuario) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT COUNT(*) as total 
                    FROM {$this->table} 
                    WHERE id_usuario = :id_usuario AND estado != 3";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::contarPorUsuario: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Contar productos por usuario y estado
     */
    public function contarPorUsuarioYEstado($id_usuario, $estado) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT COUNT(*) as total 
                    FROM {$this->table} 
                    WHERE id_usuario = :id_usuario AND estado = :estado";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::contarPorUsuarioYEstado: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener productos por categoría
     */
    public function obtenerPorCategoria($id_categoria, $pagina = 1, $limite = 12, $orden = 'fecha_desc') {
        try {
            $this->verificarConexion();
            $offset = ($pagina - 1) * $limite;
            
            $query = "SELECT p.id_publicacion, p.titulo, p.descripcion, p.precio, p.tipo,
                            p.fecha_publicacion, u.nombres, u.apellidos, u.facultad, u.foto_perfil,
                            c.nombre_categoria,
                            (SELECT TOP 1 url_imagen FROM {$this->table_imagenes} 
                            WHERE id_publicacion = p.id_publicacion 
                            AND es_principal = 1) as imagen_principal
                    FROM {$this->table} p
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.id_categoria = :id_categoria AND p.estado = 1";
            
            // Aplicar ordenamiento
            $ordenes_validos = [
                'fecha_desc' => 'p.fecha_publicacion DESC',
                'fecha_asc' => 'p.fecha_publicacion ASC',
                'precio_asc' => 'p.precio ASC',
                'precio_desc' => 'p.precio DESC'
            ];
            
            $orden_sql = $ordenes_validos[$orden] ?? 'p.fecha_publicacion DESC';
            $query .= " ORDER BY {$orden_sql}";
            
            $query .= " OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerPorCategoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar productos por categoría
     */
    public function contarPorCategoria($id_categoria) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT COUNT(*) as total 
                    FROM {$this->table} 
                    WHERE id_categoria = :id_categoria AND estado = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::contarPorCategoria: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener productos destacados
     */
    public function obtenerDestacados($limite = 8) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT p.id_publicacion, p.titulo, p.descripcion, p.precio, p.tipo,
                            p.fecha_publicacion, u.nombres, u.apellidos, u.foto_perfil,
                            c.nombre_categoria,
                            (SELECT TOP 1 url_imagen FROM {$this->table_imagenes} 
                            WHERE id_publicacion = p.id_publicacion 
                            AND es_principal = 1) as imagen_principal
                    FROM {$this->table} p
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.estado = 1
                    ORDER BY p.fecha_publicacion DESC
                    OFFSET 0 ROWS FETCH NEXT :limite ROWS ONLY";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerDestacados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener productos similares
     */
    public function obtenerSimilares($id_publicacion, $id_categoria, $limite = 4) {
        try {
            $this->verificarConexion();

            $query = "SELECT 
                        p.id_publicacion, 
                        p.titulo, 
                        p.precio, 
                        p.tipo,
                        (
                            SELECT TOP 1 url_imagen 
                            FROM {$this->table_imagenes} 
                            WHERE id_publicacion = p.id_publicacion 
                            AND es_principal = 1
                            ORDER BY id_imagen ASC
                        ) AS imagen_principal
                    FROM {$this->table} p
                    WHERE p.id_categoria = :id_categoria
                        AND p.id_publicacion != :id_publicacion
                        AND p.estado = 1
                    ORDER BY p.fecha_publicacion DESC
                    OFFSET 0 ROWS FETCH NEXT :limite ROWS ONLY";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerSimilares: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crear nueva publicación
     */
    public function crear($datos) {
        try {
            $this->verificarConexion();
            $this->db->beginTransaction();
            
            // SQL Server: Cuando hay triggers, se debe usar OUTPUT...INTO
            // 1. Declarar una tabla temporal para guardar el ID
            // 2. Insertar y usar OUTPUT...INTO para guardar el ID en la tabla temporal
            // 3. Seleccionar el ID desde la tabla temporal
            $query = "DECLARE @OutputTbl TABLE (ID INT);
                      INSERT INTO {$this->table} (id_usuario, id_categoria, titulo, descripcion, tipo, precio, telefono_contacto, correo_contacto, estado, fecha_publicacion)
                      OUTPUT INSERTED.id_publicacion INTO @OutputTbl(ID)
                      VALUES (:id_usuario, :id_categoria, :titulo, :descripcion, :tipo, :precio, :telefono_contacto, :correo_contacto, 1, GETDATE());
                      SELECT ID FROM @OutputTbl;";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':telefono_contacto', $datos['telefono_contacto']);
            $stmt->bindParam(':correo_contacto', $datos['correo_contacto']);
            
            $stmt->execute();
            // Para SQL Server, cuando se ejecutan múltiples sentencias en un lote,
            // es necesario avanzar al siguiente conjunto de resultados para obtener el SELECT.
            $stmt->nextRowset();
            $id_publicacion = $stmt->fetchColumn();

            if ($id_publicacion) {
                
                // Registrar movimiento
                $this->registrarMovimiento($id_publicacion, $datos['id_usuario'], 'Alta');
                
                $this->db->commit();
                return $id_publicacion;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en Publicacion::crear: " . $e->getMessage());
            // Propagar el error para que el controlador pueda manejarlo
            throw $e;
            return false;
        }
    }
    
    /**
     * Actualizar publicación
     */
    public function actualizar($id_publicacion, $datos) {
        try {
            $this->verificarConexion();
            $this->db->beginTransaction();
            
            // OBTENER id_usuario de forma segura
            $id_usuario = $datos['id_usuario'] ?? null;
            if (!$id_usuario) {
                // Si no viene en los datos, obtenerlo de la publicación actual
                $publicacion_actual = $this->obtenerPorId($id_publicacion);
                $id_usuario = $publicacion_actual['id_usuario'] ?? null;
            }
            
            if (!$id_usuario) {
                throw new Exception("No se pudo determinar el usuario para registrar el movimiento");
            }
            
            $query = "UPDATE {$this->table} 
                    SET id_categoria = :id_categoria,
                        titulo = :titulo,
                        descripcion = :descripcion,
                        tipo = :tipo,
                        precio = :precio,
                        telefono_contacto = :telefono_contacto,
                        correo_contacto = :correo_contacto,
                        estado = :estado,
                        fecha_actualizacion = GETDATE()
                    WHERE id_publicacion = :id_publicacion";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':precio', $datos['precio']);
            $stmt->bindParam(':telefono_contacto', $datos['telefono_contacto']);
            $stmt->bindParam(':correo_contacto', $datos['correo_contacto']);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Registrar movimiento de edición
                $this->registrarMovimiento($id_publicacion, $id_usuario, 'Edición');
                
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en Publicacion::actualizar: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en Publicacion::actualizar: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar publicación (cambiar estado a eliminado)
     */
    public function eliminar($id_publicacion) {
        try {
            $this->verificarConexion();
            $this->db->beginTransaction();
            
            // Primero obtener y eliminar archivos físicos de las imágenes asociadas (si existen)
            $queryGetImgs = "SELECT url_imagen FROM {$this->table_imagenes} WHERE id_publicacion = :id_publicacion";
            $stmtGetImgs = $this->db->prepare($queryGetImgs);
            $stmtGetImgs->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmtGetImgs->execute();
            $imagenes = $stmtGetImgs->fetchAll(PDO::FETCH_ASSOC);

            foreach ($imagenes as $img) {
                if (empty($img['url_imagen'])) continue;
                $path = __DIR__ . '/../../' . ltrim($img['url_imagen'], '/\\');
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            // Intentar borrar directorio de la publicación si existe (puede contener subdirectorios)
            // Suponiendo estructura assets/uploads/publicaciones/{id}
            $pubDir = __DIR__ . '/../../assets/uploads/publicaciones/' . $id_publicacion;
            if (is_dir($pubDir)) {
                // eliminar archivos residuales
                $files = glob($pubDir . '/*');
                foreach ($files as $f) { @unlink($f); }
                @rmdir($pubDir);
            }

            // Luego eliminar registros de imágenes en BD
            $queryImagenes = "DELETE FROM {$this->table_imagenes} WHERE id_publicacion = :id_publicacion";
            $stmtImg = $this->db->prepare($queryImagenes);
            $stmtImg->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmtImg->execute();

            // Luego eliminar la publicación principal
            $query = "DELETE FROM {$this->table} WHERE id_publicacion = :id_publicacion";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
                
            if ($stmt->execute()) {
                // Registrar movimiento de eliminación
                $publicacion = $this->obtenerPorId($id_publicacion);
                if ($publicacion) {
                    $this->registrarMovimiento($id_publicacion, $publicacion['id_usuario'], 'Eliminación');
                }
                
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en Publicacion::eliminar: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cambiar estado de publicación
     */
    public function cambiarEstado($id_publicacion, $nuevo_estado) {
        try {
            $this->verificarConexion();
            $this->db->beginTransaction();
            
            $query = "UPDATE {$this->table} 
                    SET estado = :estado, fecha_actualizacion = GETDATE()
                    WHERE id_publicacion = :id_publicacion";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Obtener usuario para el movimiento
                $publicacion = $this->obtenerPorId($id_publicacion);
                if ($publicacion) {
                    $tipo_movimiento = match($nuevo_estado) {
                        1 => 'Reactivación',
                        2 => 'Pausa', 
                        3 => 'Eliminación',
                        default => 'Edición'
                    };
                    $this->registrarMovimiento($id_publicacion, $publicacion['id_usuario'], $tipo_movimiento);
                }
                
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en Publicacion::cambiarEstado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener imágenes de una publicación
     */
    public function obtenerImagenes($id_publicacion) {
        try {
            $this->verificarConexion();
            
            $query = "SELECT id_imagen, url_imagen, es_principal 
                    FROM {$this->table_imagenes} 
                    WHERE id_publicacion = :id_publicacion
                    ORDER BY es_principal DESC, id_imagen ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerImagenes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Agregar imagen a publicación
     */
    public function agregarImagen($datos_imagen) {
        try {
            $this->verificarConexion();
            // Asegurar que es_principal tenga un valor por defecto (0)
            $es_principal = isset($datos_imagen['es_principal']) ? (int)$datos_imagen['es_principal'] : 0;

            $query = "INSERT INTO {$this->table_imagenes} 
                (id_publicacion, url_imagen, es_principal)
                VALUES (:id_publicacion, :url_imagen, :es_principal)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $datos_imagen['id_publicacion'], PDO::PARAM_INT);
            $stmt->bindParam(':url_imagen', $datos_imagen['url_imagen']);
            $stmt->bindParam(':es_principal', $es_principal, PDO::PARAM_INT);

            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::agregarImagen: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error en Publicacion::agregarImagen: " . $e->getMessage());
        }
    }
    
    /**
     * Eliminar imagen
     */
    public function eliminarImagen($id_imagen) {
        try {
            $this->verificarConexion();
            // Obtener la URL antes de borrar para eliminar el archivo físico
            $queryGet = "SELECT url_imagen FROM {$this->table_imagenes} WHERE id_imagen = :id_imagen";
            $stmtGet = $this->db->prepare($queryGet);
            $stmtGet->bindParam(':id_imagen', $id_imagen, PDO::PARAM_INT);
            $stmtGet->execute();
            $row = $stmtGet->fetch(PDO::FETCH_ASSOC);
            $url = $row['url_imagen'] ?? null;

            if ($url) {
                $path = __DIR__ . '/../../' . ltrim($url, '/\\');
                if (is_file($path)) {
                    @unlink($path);
                }
                // Intentar borrar directorio si quedó vacío
                $dir = dirname($path);
                if (is_dir($dir)) {
                    // Escanear el directorio para ver si quedan archivos
                    // scandir devuelve array con '.' y '..' y los archivos
                    $archivos = array_diff(scandir($dir), array('.', '..'));
                    
                    // Si el array está vacío, significa que no hay archivos
                    if (count($archivos) === 0) {
                        @rmdir($dir);
                    }
                }
            }

            $query = "DELETE FROM {$this->table_imagenes} 
                    WHERE id_imagen = :id_imagen";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_imagen', $id_imagen, PDO::PARAM_INT);

            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::eliminarImagen: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Incrementar contador de vistas
     */
    public function incrementarVistas($id_publicacion) {
        try {
            $this->verificarConexion();
            
            // Manejar sesión de forma segura
            $id_usuario = null;
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['usuario_id'])) {
                $id_usuario = $_SESSION['usuario_id'];
            }
            
            // Usar la tabla de movimientos para registrar vistas
            $query = "INSERT INTO {$this->table_movimientos} 
                    (id_publicacion, id_usuario, tipo_movimiento)
                    VALUES (:id_publicacion, :id_usuario, 'Vista')";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            
            if ($id_usuario) {
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':id_usuario', null, PDO::PARAM_NULL);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::incrementarVistas: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar movimiento en el historial
     */
    /**
     * Registrar movimiento en el historial
     */
    public function registrarMovimiento($id_publicacion, $id_usuario, $tipo_movimiento, $descripcion = null) {
        try {
            $this->verificarConexion();

            // --- BLOQUE NUEVO: Verificar si ya existe (solo para Contactos) ---
            if ($tipo_movimiento === 'Contacto') {
                $checkSql = "SELECT COUNT(*) FROM {$this->table_movimientos} 
                             WHERE id_publicacion = :id_pub 
                             AND id_usuario = :id_user 
                             AND tipo_movimiento = 'Contacto'";
                $checkStmt = $this->db->prepare($checkSql);
                $checkStmt->bindParam(':id_pub', $id_publicacion, PDO::PARAM_INT);
                $checkStmt->bindParam(':id_user', $id_usuario, PDO::PARAM_INT);
                $checkStmt->execute();
                
                if ($checkStmt->fetchColumn() > 0) {
                    return true; // Ya existe, no hacemos nada pero devolvemos "éxito"
                }
            }
            // -----------------------------------------------------------------
            
            // Si no existe (o es otro tipo de movimiento), insertamos normal
            $query = "INSERT INTO {$this->table_movimientos} 
                    (id_publicacion, id_usuario, tipo_movimiento, descripcion, fecha)
                    VALUES (:id_publicacion, :id_usuario, :tipo_movimiento, :descripcion, GETDATE())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_movimiento', $tipo_movimiento);
            $stmt->bindParam(':descripcion', $descripcion);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error SQL en registrarMovimiento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener productos favoritos del usuario
     */
    public function obtenerFavoritos($id_usuario) {
        try {
            $this->verificarConexion();
            
            // Se agregó p.id_usuario para que el controlador tenga el ID del vendedor
            $query = "SELECT p.id_publicacion, p.id_usuario, p.titulo, p.precio, p.tipo, p.descripcion,
                            p.fecha_publicacion, c.nombre_categoria, u.nombres, u.apellidos, u.foto_perfil,
                            f.fecha as fecha_agregado,
                            (SELECT TOP 1 url_imagen FROM {$this->table_imagenes} 
                            WHERE id_publicacion = p.id_publicacion 
                            AND es_principal = 1) as imagen_principal
                    FROM {$this->table} p
                    INNER JOIN Favoritos f ON p.id_publicacion = f.id_publicacion
                    INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario
                    WHERE f.id_usuario = :id_usuario AND p.estado = 1
                    ORDER BY f.fecha DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerFavoritos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si un usuario ya dió favorito a una publicación
     */
    public function esFavorito($id_usuario, $id_publicacion) {
        try {
            $this->verificarConexion();
            $query = "SELECT COUNT(*) FROM Favoritos WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verifica qué publicaciones de una lista son favoritas para un usuario.
     * @param int $id_usuario
     * @param array $ids_publicaciones
     * @return array Un array con los IDs de las publicaciones que son favoritas.
     */
    public function verificarFavoritos($id_usuario, $ids_publicaciones) {
        if (empty($ids_publicaciones) || !$id_usuario) {
            return [];
        }
        try {
            $this->verificarConexion();
            $placeholders = implode(',', array_fill(0, count($ids_publicaciones), '?'));
            $query = "SELECT id_publicacion FROM Favoritos WHERE id_usuario = ? AND id_publicacion IN ($placeholders)";
            $stmt = $this->db->prepare($query);
            $params = array_merge([$id_usuario], $ids_publicaciones);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Agregar a favoritos
     */
    public function agregarFavorito($id_usuario, $id_publicacion) {
        try {
            $this->verificarConexion();
            if ($this->esFavorito($id_usuario, $id_publicacion)) return true;

            $query = "INSERT INTO Favoritos (id_usuario, id_publicacion) VALUES (:id_usuario, :id_publicacion)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Eliminar de favoritos
     */
    public function eliminarFavorito($id_usuario, $id_publicacion) {
        try {
            $this->verificarConexion();
            $query = "DELETE FROM Favoritos WHERE id_usuario = :id_usuario AND id_publicacion = :id_publicacion";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_publicacion', $id_publicacion, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Alterna el estado de favorito de una publicación para un usuario.
     * Agrega el favorito si no existe, o lo elimina si ya existe.
     * @param int $id_usuario
     * @param int $id_publicacion
     * @return bool Retorna el nuevo estado de favorito (true si fue agregado, false si fue eliminado).
     */
    public function toggleFavorito($id_usuario, $id_publicacion) {
        try {
            $this->verificarConexion();
            if ($this->esFavorito($id_usuario, $id_publicacion)) {
                $this->eliminarFavorito($id_usuario, $id_publicacion);
                return false; // Se eliminó
            } else {
                $this->agregarFavorito($id_usuario, $id_publicacion);
                return true; // Se agregó
            }
        } catch (Exception $e) {
            error_log("Error en Publicacion::toggleFavorito: " . $e->getMessage());
            // En caso de error, es más seguro asumir que no se cambió el estado
            // y devolver el estado original.
            return $this->esFavorito($id_usuario, $id_publicacion);
        }
    }
    
    /**
     * Obtener estadísticas de productos
     */
    public function obtenerEstadisticas() {
        try {
            $this->verificarConexion();
            
            $query = "SELECT 
                        COUNT(*) as total_publicaciones,
                        SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as publicaciones_activas,
                        SUM(CASE WHEN estado = 2 THEN 1 ELSE 0 END) as publicaciones_pausadas,
                        SUM(CASE WHEN estado = 3 THEN 1 ELSE 0 END) as publicaciones_eliminadas,
                        SUM(CASE WHEN tipo = 'Producto' THEN 1 ELSE 0 END) as total_productos,
                        SUM(CASE WHEN tipo = 'Servicio' THEN 1 ELSE 0 END) as total_servicios,
                        COUNT(DISTINCT id_usuario) as total_vendedores
                    FROM {$this->table}";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Publicacion::obtenerEstadisticas: " . $e->getMessage());
            return [
                'total_publicaciones' => 0,
                'publicaciones_activas' => 0,
                'publicaciones_pausadas' => 0,
                'publicaciones_eliminadas' => 0,
                'total_productos' => 0,
                'total_servicios' => 0,
                'total_vendedores' => 0
            ];
        }
    }
    
    /**
     * Validar datos de publicación antes de insertar/actualizar
     */
    private function validarDatosPublicacion($datos) {
        $errores = [];
        
        if (empty(trim($datos['titulo']))) {
            $errores[] = "El título es obligatorio";
        }
        
        if (strlen(trim($datos['titulo'])) < 5) {
            $errores[] = "El título debe tener al menos 5 caracteres";
        }
        
        if (empty(trim($datos['descripcion']))) {
            $errores[] = "La descripción es obligatoria";
        }
        
        if ($datos['precio'] < 0) {
            $errores[] = "El precio no puede ser negativo";
        }
        
        return $errores;
    }

    /**
     * Obtiene las estadísticas de valoración (promedio y total) para una publicación.
     * @param int $id_publicacion
     * @return array ['promedio' => float, 'total' => int]
     */
    public function obtenerEstadisticasValoracion($id_publicacion) {
        // Usamos ISNULL para evitar resultados nulos si no hay valoraciones
        $sql = "SELECT 
                    ISNULL(AVG(CAST(puntuacion AS FLOAT)), 0) as promedio, 
                    COUNT(id_valoracion) as total 
                FROM Valoraciones 
                WHERE id_publicacion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_publicacion]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado;
    }

    /**
     * Verifica si un usuario ya ha valorado una publicación específica.
     * @param int $id_usuario
     * @param int $id_publicacion
     * @return bool
     */
    public function usuarioYaValoro($id_usuario, $id_publicacion) {
        $sql = "SELECT 1 FROM Valoraciones WHERE id_usuario_valorador = ? AND id_publicacion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario, $id_publicacion]);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Agrega una nueva valoración a la base de datos.
     * @param int $id_publicacion
     * @param int $id_usuario
     * @param int $puntuacion
     * @return bool
     */
    public function agregarValoracion($id_publicacion, $id_usuario, $puntuacion, $comentario = null) {
        // 1. Verificar que el usuario no sea el dueño de la publicación
        $pubStmt = $this->db->prepare("SELECT id_usuario FROM Publicaciones WHERE id_publicacion = ?");
        $pubStmt->execute([$id_publicacion]);
        $publicacion = $pubStmt->fetch(PDO::FETCH_ASSOC);

        if ($publicacion && $publicacion['id_usuario'] == $id_usuario) {
            $_SESSION['error_valoracion'] = "No puedes valorar tu propia publicación.";
            return false;
        }

        // 2. Verificar que el usuario no haya valorado antes (la BD también lo previene con UNIQUE)
        if ($this->usuarioYaValoro($id_usuario, $id_publicacion)) {
            $_SESSION['error_valoracion'] = "Ya has valorado esta publicación anteriormente.";
            return false;
        }

        // 3. Insertar la valoración
        $sql = "INSERT INTO Valoraciones (id_publicacion, id_usuario_valorador, puntuacion, comentario) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$id_publicacion, $id_usuario, $puntuacion, $comentario]);
        } catch (PDOException $e) {
            error_log("Error al agregar valoración: " . $e->getMessage());
            $_SESSION['error_valoracion'] = "Ocurrió un error al guardar tu valoración.";
            return false;
        }
    }

    /**
     * Obtiene la valoración específica de un usuario para una publicación.
     * @param int $id_usuario
     * @param int $id_publicacion
     * @return array|false
     */
    public function obtenerValoracionUsuario($id_usuario, $id_publicacion) {
        $sql = "SELECT * FROM Valoraciones WHERE id_usuario_valorador = ? AND id_publicacion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario, $id_publicacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza una valoración existente.
     * @param int $id_valoracion
     * @param int $puntuacion
     * @return bool
     */
    public function actualizarValoracion($id_valoracion, $puntuacion, $comentario = null) {
        // Validar que la puntuación esté en el rango correcto
        if ($puntuacion < 1 || $puntuacion > 5) {
            $_SESSION['error_valoracion'] = "La puntuación debe estar entre 1 y 5.";
            return false;
        }

        $sql = "UPDATE Valoraciones SET puntuacion = ?, comentario = ?, fecha_valoracion = GETDATE() WHERE id_valoracion = ?";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([$puntuacion, $comentario, $id_valoracion]);
        } catch (PDOException $e) {
            error_log("Error al actualizar valoración: " . $e->getMessage());
            $_SESSION['error_valoracion'] = "Ocurrió un error al actualizar tu valoración.";
            return false;
        }
    }

    /**
     * Elimina una valoración específica, verificando que pertenezca al usuario.
     * @param int $id_valoracion
     * @param int $id_usuario
     * @return bool
     */
    public function eliminarValoracion($id_valoracion, $id_usuario) {
        $sql = "DELETE FROM Valoraciones WHERE id_valoracion = ? AND id_usuario_valorador = ?";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute([$id_valoracion, $id_usuario]);
            // execute() para un DELETE devuelve true, pero rowCount() nos dice si realmente se borró algo.
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar valoración: " . $e->getMessage());
            $_SESSION['error_valoracion'] = "Ocurrió un error al eliminar tu valoración.";
            return false;
        }
    }

    /**
     * Obtiene todas las valoraciones de una publicación, incluyendo datos del usuario.
     * @param int $id_publicacion
     * @return array
     */
    public function obtenerValoracionesPublicacion($id_publicacion) {
        $sql = "SELECT 
                    v.id_valoracion,
                    v.puntuacion,
                    v.comentario,
                    v.fecha_valoracion,
                    u.nombres,
                    u.apellidos,
                    u.foto_perfil,
                    u.id_usuario AS id_usuario_valorador
                FROM Valoraciones v
                JOIN Usuarios u ON v.id_usuario_valorador = u.id_usuario
                WHERE v.id_publicacion = ?
                ORDER BY v.fecha_valoracion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_publicacion]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
