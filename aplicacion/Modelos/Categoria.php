<?php
    class Categoria {
        private $db;
        private $table = 'Categorias';
        
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
         * Obtener todas las categorías activas
         */
        public function obtenerTodas() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_categoria, nombre_categoria, descripcion 
                        FROM {$this->table} 
                        WHERE estado = 1 
                        ORDER BY nombre_categoria ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerTodas: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerTodas: " . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Obtener categoría por ID
         */
        public function obtenerPorId($id_categoria) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_categoria, nombre_categoria, descripcion, estado 
                        FROM {$this->table} 
                        WHERE id_categoria = :id_categoria";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerPorId: " . $e->getMessage());
                return null;
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerPorId: " . $e->getMessage());
                return null;
            }
        }
        
        /**
         * Obtener categorías con conteo de productos
         */
        public function obtenerConConteo() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT c.id_categoria, c.nombre_categoria, c.descripcion,
                                COUNT(p.id_publicacion) as total_productos
                        FROM {$this->table} c
                        LEFT JOIN Publicaciones p ON c.id_categoria = p.id_categoria 
                            AND p.estado = 1
                        WHERE c.estado = 1
                        GROUP BY c.id_categoria, c.nombre_categoria, c.descripcion
                        ORDER BY c.nombre_categoria ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerConConteo: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerConConteo: " . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Crear nueva categoría (solo administradores)
         */
        public function crear($nombre_categoria, $descripcion = '') {
            try {
                $this->verificarConexion();
                
                // Validar parámetros
                if (empty(trim($nombre_categoria))) {
                    throw new Exception("El nombre de categoría es obligatorio");
                }
                
                // Verificar si la categoría ya existe
                $queryCheck = "SELECT id_categoria FROM {$this->table} 
                            WHERE nombre_categoria = :nombre_categoria";
                $stmtCheck = $this->db->prepare($queryCheck);
                $stmtCheck->bindParam(':nombre_categoria', $nombre_categoria);
                $stmtCheck->execute();
                
                if ($stmtCheck->fetch()) {
                    throw new Exception("La categoría '{$nombre_categoria}' ya existe");
                }
                
                $query = "INSERT INTO {$this->table} (nombre_categoria, descripcion, estado) 
                        VALUES (:nombre_categoria, :descripcion, 1)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':nombre_categoria', $nombre_categoria);
                $stmt->bindParam(':descripcion', $descripcion);
                
                if ($stmt->execute()) {
                    return $this->db->lastInsertId();
                }
                
                return false;
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::crear: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Categoria::crear: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Actualizar categoría (solo administradores)
         */
        public function actualizar($id_categoria, $nombre_categoria, $descripcion = '') {
            try {
                $this->verificarConexion();
                
                // Validar parámetros
                if (empty(trim($nombre_categoria))) {
                    throw new Exception("El nombre de categoría es obligatorio");
                }
                
                // Verificar si el nombre ya existe en otra categoría
                $queryCheck = "SELECT id_categoria FROM {$this->table} 
                            WHERE nombre_categoria = :nombre_categoria 
                            AND id_categoria != :id_categoria";
                $stmtCheck = $this->db->prepare($queryCheck);
                $stmtCheck->bindParam(':nombre_categoria', $nombre_categoria);
                $stmtCheck->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmtCheck->execute();
                
                if ($stmtCheck->fetch()) {
                    throw new Exception("El nombre de categoría '{$nombre_categoria}' ya existe");
                }
                
                $query = "UPDATE {$this->table} 
                        SET nombre_categoria = :nombre_categoria, 
                            descripcion = :descripcion
                        WHERE id_categoria = :id_categoria";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmt->bindParam(':nombre_categoria', $nombre_categoria);
                $stmt->bindParam(':descripcion', $descripcion);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::actualizar: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Categoria::actualizar: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Cambiar estado de categoría (activar/desactivar)
         */
        public function cambiarEstado($id_categoria, $estado) {
            try {
                $this->verificarConexion();
                
                $query = "UPDATE {$this->table} 
                        SET estado = :estado 
                        WHERE id_categoria = :id_categoria";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::cambiarEstado: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Categoria::cambiarEstado: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Eliminar categoría (solo si no tiene productos)
         */
        public function eliminar($id_categoria) {
            try {
                $this->verificarConexion();
                
                // Verificar si la categoría tiene productos asociados
                $queryCheck = "SELECT COUNT(*) as total 
                            FROM Publicaciones 
                            WHERE id_categoria = :id_categoria 
                            AND estado != 3"; // Excluir eliminados
                $stmtCheck = $this->db->prepare($queryCheck);
                $stmtCheck->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmtCheck->execute();
                
                $resultado = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                
                if ($resultado['total'] > 0) {
                    throw new Exception("No se puede eliminar la categoría porque tiene {$resultado['total']} productos asociados");
                }
                
                $query = "DELETE FROM {$this->table} 
                        WHERE id_categoria = :id_categoria";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::eliminar: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Categoria::eliminar: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Buscar categorías por nombre
         */
        public function buscar($termino) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_categoria, nombre_categoria, descripcion 
                        FROM {$this->table} 
                        WHERE (nombre_categoria LIKE :termino 
                            OR descripcion LIKE :termino)
                        AND estado = 1
                        ORDER BY nombre_categoria ASC";
                
                $stmt = $this->db->prepare($query);
                $termino = "%" . $termino . "%";
                $stmt->bindParam(':termino', $termino);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::buscar: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                error_log("Error en Categoria::buscar: " . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Obtener categorías populares (con más productos)
         */
        public function obtenerPopulares($limite = 10) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT c.id_categoria, c.nombre_categoria, c.descripcion,
                                COUNT(p.id_publicacion) as total_productos
                        FROM {$this->table} c
                        LEFT JOIN Publicaciones p ON c.id_categoria = p.id_categoria 
                            AND p.estado = 1
                        WHERE c.estado = 1
                        GROUP BY c.id_categoria, c.nombre_categoria, c.descripcion
                        ORDER BY total_productos DESC, c.nombre_categoria ASC
                        LIMIT :limite";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerPopulares: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerPopulares: " . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Verificar si una categoría existe y está activa
         */
        public function existe($id_categoria) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_categoria FROM {$this->table} 
                        WHERE id_categoria = :id_categoria 
                        AND estado = 1";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmt->execute();
                
                return (bool) $stmt->fetch();
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::existe: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Categoria::existe: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Obtener estadísticas de categorías
         */
        public function obtenerEstadisticas() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT 
                            COUNT(*) as total_categorias,
                            SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as categorias_activas,
                            SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as categorias_inactivas
                        FROM {$this->table}";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerEstadisticas: " . $e->getMessage());
                return [
                    'total_categorias' => 0,
                    'categorias_activas' => 0,
                    'categorias_inactivas' => 0
                ];
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerEstadisticas: " . $e->getMessage());
                return [
                    'total_categorias' => 0,
                    'categorias_activas' => 0,
                    'categorias_inactivas' => 0
                ];
            }
        }
        
        /**
         * Obtener categorías con productos recientes (OPTIMIZADO)
         */
        public function obtenerConProductosRecientes($limite_categorias = 8, $limite_productos = 3) {
            try {
                $this->verificarConexion();
                
                // Consulta optimizada usando JOIN en lugar de subconsultas
                $query = "SELECT 
                    c.id_categoria, 
                    c.nombre_categoria, 
                    c.descripcion,
                    COUNT(p.id_publicacion) as total_productos,
                    MAX(p.fecha_publicacion) as ultima_publicacion
                FROM {$this->table} c
                LEFT JOIN Publicaciones p ON c.id_categoria = p.id_categoria AND p.estado = 1
                WHERE c.estado = 1
                GROUP BY c.id_categoria, c.nombre_categoria, c.descripcion
                ORDER BY 
                    CASE WHEN MAX(p.fecha_publicacion) IS NULL THEN 1 ELSE 0 END, 
                    MAX(p.fecha_publicacion) DESC,
                    c.nombre_categoria ASC
                LIMIT :limite_categorias";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':limite_categorias', $limite_categorias, PDO::PARAM_INT);
                $stmt->execute();
                
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Para cada categoría, obtener productos recientes
                foreach ($categorias as &$categoria) {
                    $queryProductos = "SELECT p.id_publicacion, p.titulo, p.precio, p.tipo,
                                            p.fecha_publicacion,
                                            (SELECT url_imagen FROM ImagenesPublicacion 
                                            WHERE id_publicacion = p.id_publicacion 
                                            AND es_principal = 1 LIMIT 1) as imagen_principal
                                    FROM Publicaciones p
                                    WHERE p.id_categoria = :id_categoria 
                                    AND p.estado = 1
                                    ORDER BY p.fecha_publicacion DESC
                                    LIMIT :limite_productos";
                    
                    $stmtProductos = $this->db->prepare($queryProductos);
                    $stmtProductos->bindParam(':id_categoria', $categoria['id_categoria'], PDO::PARAM_INT);
                    $stmtProductos->bindParam(':limite_productos', $limite_productos, PDO::PARAM_INT);
                    $stmtProductos->execute();
                    
                    $categoria['productos_recientes'] = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
                }
                
                return $categorias;
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerConProductosRecientes: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerConProductosRecientes: " . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Obtener categorías para formularios (select options)
         */
        public function obtenerParaSelect() {
            try {
                $this->verificarConexion();
                
                $categorias = $this->obtenerTodas();
                $options = [];
                
                foreach ($categorias as $categoria) {
                    $options[$categoria['id_categoria']] = $categoria['nombre_categoria'];
                }
                
                return $options;
                
            } catch (Exception $e) {
                error_log("Error en Categoria::obtenerParaSelect: " . $e->getMessage());
                return [];
            }
        }
    }
?>