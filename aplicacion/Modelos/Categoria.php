<?php
    class Categoria {
        private $db;
        private $table = 'Categorias';
        
        public function __construct() {
            require_once 'aplicacion/Configuracion/conexion.php';
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
        }
        
        private function verificarConexion() {
            if (!$this->db) {
                throw new Exception("Error de conexión a la base de datos");
            }
        }

        public function obtenerParaAdmin() {
            try {
                $this->verificarConexion();
                $query = "SELECT 
                    c.id_categoria, 
                    c.nombre_categoria, 
                    c.descripcion, 
                    c.estado,
                    ISNULL(c.icono, 'fas fa-tag') as icono,        
                    ISNULL(c.color, '#00bcd4') as color,        
                    c.fecha_creacion, 
                    COUNT(p.id_publicacion) as total_publicaciones
                FROM {$this->table} c
                LEFT JOIN Publicaciones p ON c.id_categoria = p.id_categoria AND p.estado = 1
                GROUP BY 
                    c.id_categoria, c.nombre_categoria, c.descripcion, c.estado, 
                    c.icono, c.color, c.fecha_creacion
                ORDER BY c.id_categoria DESC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { return []; }
        }
        
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
        
        public function crear($nombre_categoria, $descripcion = '',$icono = 'fas fa-tag', $color = '#00bcd4', $estado=1) {
             try {
                $this->verificarConexion();
                $query = "INSERT INTO {$this->table} (nombre_categoria, descripcion, estado, icono, color, fecha_creacion) 
                VALUES (:nombre_categoria, :descripcion, :estado, :icono, :color, GETDATE())";
                $stmt = $this->db->prepare($query);
                $stmt->execute([':nombre_categoria'=>$nombre_categoria, ':descripcion'=>$descripcion, ':estado'=>$estado, ':icono'=>$icono, ':color'=>$color]);
                return $this->db->lastInsertId();
            } catch (Exception $e) { return false; }
        }

        public function actualizar($id_categoria, $nombre_categoria, $descripcion = '', $icono = 'fas fa-tag', $color = '#00bcd4') {
             try {
                $this->verificarConexion();
                $query = "UPDATE {$this->table} SET nombre_categoria = :nombre_categoria, descripcion = :descripcion, icono = :icono, color = :color WHERE id_categoria = :id_categoria";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([':id_categoria'=>$id_categoria, ':nombre_categoria'=>$nombre_categoria, ':descripcion'=>$descripcion, ':icono'=>$icono, ':color'=>$color]);
            } catch (Exception $e) { return false; }
        }
        
        public function eliminar($id_categoria) {
             try {
                $this->verificarConexion();
                $query = "DELETE FROM {$this->table} WHERE id_categoria = :id_categoria";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([':id_categoria'=>$id_categoria]);
            } catch (Exception $e) { return false; }
        }
        
        public function cambiarEstado($id_categoria, $estado) {
             try {
                $this->verificarConexion();
                $query = "UPDATE {$this->table} SET estado = :estado WHERE id_categoria = :id_categoria";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([':id_categoria'=>$id_categoria, ':estado'=>$estado]);
            } catch (Exception $e) { return false; }
        }
        
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
        
        public function obtenerPopulares($limite = 10) {
            try {
                $this->verificarConexion();
                
                // SE AGREGO c.color y c.icono al SELECT y al GROUP BY
                $query = "SELECT c.id_categoria, c.nombre_categoria, c.descripcion, c.color, c.icono,
                        COUNT(p.id_publicacion) as total_productos
                        FROM {$this->table} c
                        LEFT JOIN Publicaciones p ON c.id_categoria = p.id_categoria 
                        AND p.estado = 1
                        WHERE c.estado = 1
                        GROUP BY c.id_categoria, c.nombre_categoria, c.descripcion, c.color, c.icono
                        ORDER BY total_productos DESC, c.nombre_categoria ASC
                        OFFSET 0 ROWS FETCH NEXT :limite ROWS ONLY"; 
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Categoria::obtenerPopulares: " . $e->getMessage());
                return [];
            }
        }
        
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
        
        public function obtenerEstadisticas() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT 
                            COUNT(c.id_categoria) as total_categorias,
                            SUM(CASE WHEN c.estado = 1 THEN 1 ELSE 0 END) as categorias_activas,
                            (SELECT COUNT(p.id_publicacion) FROM Publicaciones p WHERE p.estado = 1) as total_publicaciones
                        FROM {$this->table} c";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $populares = $this->obtenerPopulares(1);
                $stats['categoria_popular'] = !empty($populares) ? $populares[0]['nombre_categoria'] : 'N/A';

                return $stats;
                
            } catch (Exception $e) {
                return ['total_categorias' => 0, 'categorias_activas' => 0, 'total_publicaciones' => 0, 'categoria_popular' => 'N/A'];
            }
        }
        
        public function obtenerConProductosRecientes($limite_categorias = 8, $limite_productos = 3) {
            // Se mantiene el código original, pero se corrige el LIMIT
            try {
                $this->verificarConexion();
                
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
                OFFSET 0 ROWS FETCH NEXT :limite_categorias ROWS ONLY";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':limite_categorias', $limite_categorias, PDO::PARAM_INT);
                $stmt->execute();
                
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Para cada categoría, obtener productos recientes
                foreach ($categorias as &$categoria) {
                    $queryProductos = "SELECT TOP (:limite_productos) p.id_publicacion, p.titulo, p.precio, p.tipo,
                                            p.fecha_publicacion,
                                            (SELECT url_imagen FROM ImagenesPublicacion 
                                            WHERE id_publicacion = p.id_publicacion 
                                            AND es_principal = 1) as imagen_principal
                                    FROM Publicaciones p
                                    WHERE p.id_categoria = :id_categoria 
                                    AND p.estado = 1
                                    ORDER BY p.fecha_publicacion DESC";
                    
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