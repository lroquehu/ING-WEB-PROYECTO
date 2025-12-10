<?php
    class Usuario {
        private $db;
        private $table = 'Usuarios';
        private $publicacionModel;
        
        public function __construct($publicacionModel = null) {
            require_once 'aplicacion/Configuracion/conexion.php';
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->publicacionModel = $publicacionModel;
        }
        
        //Verificar si la conexión a la base de datos está activa
        private function verificarConexion() {
            if (!$this->db) {
                throw new Exception("Error de conexión a la base de datos");
            }
        }
        
        public function setPublicacionModel($publicacionModel) {
            $this->publicacionModel = $publicacionModel;
        }
        
        public function login($correo, $contrasenia) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                            correo_institucional, codigo_univ, facultad, escuela, 
                            contrasena, estado, fecha_registro, verificado, rol, suspension_fin, motivo_suspension, foto_perfil 
                        FROM {$this->table} 
                        WHERE correo_institucional = :correo"; 
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($usuario && password_verify($contrasenia, $usuario['contrasena'])) {
                    unset($usuario['contrasena']);
                    return $usuario; 
                }
                
                return false;
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::login: " . $e->getMessage());
                return false;
            }
        }
        
        public function registrar($nombres, $apellidos, $dni, $telefono, $correo, 
            $codigo_univ, $facultad, $escuela, $contrasenia, $foto_perfil = null, $rol = 'estudiante') {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();

                // --- MEJORA: Unificar la validación de existencia en una sola consulta ---
                $this->verificarExistencia($correo, $dni, $codigo_univ);
                
                // Validar datos
                $errores = $this->validarDatosRegistro($nombres, $apellidos, $dni, $correo, $codigo_univ, $contrasenia);
                if (!empty($errores)) {
                    throw new Exception(implode(', ', $errores));
                }
                
                // Hash de la contraseña
                $contrasenia_hash = password_hash($contrasenia, PASSWORD_DEFAULT);
                
                // 'verificado' se inserta como 0 (no verificado) por defecto.
                $query = "INSERT INTO {$this->table} 
                    (nombres, apellidos, dni, telefono, correo_institucional, 
                    codigo_univ, facultad, escuela, contrasena, foto_perfil, estado, verificado, rol)
                    VALUES (:nombres, :apellidos, :dni, :telefono, :correo,
                    :codigo_univ, :facultad, :escuela, :contrasena, :foto_perfil, 1, 0, :rol)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':codigo_univ', $codigo_univ);
                $stmt->bindParam(':facultad', $facultad);
                $stmt->bindParam(':escuela', $escuela);
                $stmt->bindParam(':contrasena', $contrasenia_hash);
                $stmt->bindParam(':rol', $rol);
                $stmt->bindParam(':foto_perfil', $foto_perfil);
                
                if ($stmt->execute()) {
                    $id_usuario = $this->db->lastInsertId();
                    $this->db->commit();
                    return $id_usuario;
                }
                
                $this->db->rollBack();
                return false;
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::registrar (DB): " . $e->getMessage());
                return "Error de Base de Datos: " . $e->getMessage();
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::registrar (Validación): " . $e->getMessage());
                return $e->getMessage();
            }
        }
        
        public function suspenderUsuario($id_usuario, $fecha_fin, $motivo) {
            try {
                $this->verificarConexion();
                
                $query = "UPDATE {$this->table} 
                        SET estado = 0, suspension_fin = :fecha, motivo_suspension = :motivo 
                        WHERE id_usuario = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':fecha', $fecha_fin);
                $stmt->bindParam(':motivo', $motivo);
                $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    return true;
                } else {
                    // Capturar error si execute devuelve false sin lanzar excepción
                    $err = $stmt->errorInfo();
                    throw new Exception("Error SQL: " . $err[2]);
                }

            } catch (PDOException $e) {
                // CAMBIO IMPORTANTE: No retornar false, sino lanzar el error real
                // Esto enviará el mensaje detallado de SQL Server al controlador
                error_log("Error DB: " . $e->getMessage());
                throw new Exception("Fallo en BD: " . $e->getMessage());
            }
        }

        public function obtenerPorId($id_usuario) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT * FROM {$this->table} 
                    WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerPorId: " . $e->getMessage());
                return null;
            }
        }
        
        public function obtenerPorCorreo($correo) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                        correo_institucional, codigo_univ, facultad, escuela, 
                        estado, fecha_registro, rol
                        FROM {$this->table} 
                        WHERE correo_institucional = :correo";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerPorCorreo: " . $e->getMessage());
                return null;
            }
        }
        
        public function actualizarPerfil($id_usuario, $nombres, $apellidos, $telefono, $facultad, $escuela) {
            try {
                $this->verificarConexion();
                
                $errores = [];
                if (empty(trim($nombres))) $errores[] = "El nombre es obligatorio";
                if (empty(trim($apellidos))) $errores[] = "Los apellidos son obligatorios";
                
                if (!empty($errores)) {
                    throw new Exception(implode(', ', $errores));
                }
                
                $query = "UPDATE {$this->table} 
                        SET nombres = :nombres,
                            apellidos = :apellidos,
                            telefono = :telefono,
                            facultad = :facultad,
                            escuela = :escuela
                        WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':facultad', $facultad);
                $stmt->bindParam(':escuela', $escuela);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::actualizarPerfil: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                error_log("Error en Usuario::actualizarPerfil: " . $e->getMessage());
                return false;
            }
        }
        
        public function actualizarFoto($id_usuario, $ruta_imagen) {
            try {
                $this->verificarConexion();
                
                $query = "UPDATE {$this->table} 
                        SET foto_perfil = :foto_perfil 
                        WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':foto_perfil', $ruta_imagen);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::actualizarFoto: " . $e->getMessage());
                return false;
            }
        }
        
        public function cambiarPassword($id_usuario, $contrasenia_actual, $nueva_contrasenia) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                $query_actual = "SELECT contrasena FROM {$this->table} WHERE id_usuario = :id_usuario";
                $stmt_actual = $this->db->prepare($query_actual);
                $stmt_actual->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt_actual->execute();
                
                $resultado = $stmt_actual->fetch(PDO::FETCH_ASSOC);
                
                if (!$resultado || !password_verify($contrasenia_actual, $resultado['contrasena'])) {
                    $this->db->rollBack();
                    return false;
                }
                
                $errores = $this->validarContrasenia($nueva_contrasenia);
                if (!empty($errores)) {
                    $this->db->rollBack();
                    throw new Exception(implode(', ', $errores));
                }
                
                $nueva_contrasenia_hash = password_hash($nueva_contrasenia, PASSWORD_DEFAULT);
                
                $query = "UPDATE {$this->table} SET contrasena = :contrasena WHERE id_usuario = :id_usuario";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':contrasena', $nueva_contrasenia_hash);
                
                if ($stmt->execute()) {
                    $this->db->commit();
                    return true;
                }
                
                $this->db->rollBack();
                return false;
                
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::cambiarPassword: " . $e->getMessage());
                return false;
            }
        }
        
        public function existeCorreo($correo) {
            try {
                $this->verificarConexion();
                $query = "SELECT id_usuario FROM {$this->table} WHERE correo_institucional = :correo";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                return (bool) $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeCorreo: " . $e->getMessage());
                return false;
            }
        }
        
        public function existeDni($dni) {
            try {
                $this->verificarConexion();
                $query = "SELECT id_usuario FROM {$this->table} WHERE dni = :dni";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':dni', $dni);
                $stmt->execute();
                return (bool) $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeDni: " . $e->getMessage());
                return false;
            }
        }
        
        public function existeCodigoUniv($codigo_univ) {
            try {
                $this->verificarConexion();
                $query = "SELECT id_usuario FROM {$this->table} WHERE codigo_univ = :codigo_univ";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':codigo_univ', $codigo_univ);
                $stmt->execute();
                return (bool) $stmt->fetch();
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeCodigoUniv: " . $e->getMessage());
                return false;
            }
        }

        public function verificarExistencia($correo, $dni, $codigo_univ) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT 
                    (SELECT TOP 1 1 FROM {$this->table} WHERE correo_institucional = :correo) as correo_existe,
                    (SELECT TOP 1 1 FROM {$this->table} WHERE dni = :dni) as dni_existe,
                    (SELECT TOP 1 1 FROM {$this->table} WHERE codigo_univ = :codigo_univ) as codigo_existe";

                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':codigo_univ', $codigo_univ);
                $stmt->execute();

                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($resultado['correo_existe']) {
                    throw new Exception("El correo electrónico ya está registrado.");
                }
                if ($resultado['dni_existe']) {
                    throw new Exception("El DNI ya está registrado.");
                }
                if ($resultado['codigo_existe']) {
                    throw new Exception("El código universitario ya está registrado.");
                }
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::verificarExistencia: " . $e->getMessage());
                throw new Exception("Error al verificar los datos en la base de datos.");
            }
        }

        public function cambiarEstado($id_usuario, $estado) {
            try {
                $this->verificarConexion();
                
                // 1. CORRECCIÓN: Convertir a enteros explícitamente para evitar conflictos con SQL Server
                $id_usuario = (int)$id_usuario;
                $estado = (int)$estado;

                if ($estado === 1) {
                    // Al activar, borramos la fecha y motivo de suspensión
                    $query = "UPDATE {$this->table} 
                            SET estado = :estado, suspension_fin = NULL, motivo_suspension = NULL 
                            WHERE id_usuario = :id_usuario";
                } else {
                    // Cambio de estado normal
                    $query = "UPDATE {$this->table} SET estado = :estado WHERE id_usuario = :id_usuario";
                }

                $stmt = $this->db->prepare($query);
                
                // 2. CORRECCIÓN: Usar bindValue en lugar de bindParam
                $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    return true;
                } else {
                    // Capturar error específico de SQL si falla
                    $errorInfo = $stmt->errorInfo();
                    throw new Exception("Error SQL: " . $errorInfo[2]);
                }

            } catch (Exception $e) { // Captura PDOException y Exception
                error_log("Error en Usuario::cambiarEstado: " . $e->getMessage());
                // Esto hará que el controlador devuelva 'false' y el JS muestre el error
                return false;
            }
        }

        public function eliminar($id_usuario) {
            try {
                $this->verificarConexion();
                
                // En lugar de DELETE, hacemos UPDATE para anonimizar
                // Usamos CAST para convertir el ID a texto y concatenarlo en SQL Server
                $query = "UPDATE {$this->table} SET 
                            estado = 0,
                            nombres = 'Usuario',
                            apellidos = 'Eliminado',
                            correo_institucional = CONCAT('del_', CAST(id_usuario AS VARCHAR), '_', correo_institucional),
                            dni = CONCAT('DEL', CAST(id_usuario AS VARCHAR)), 
                            telefono = NULL,
                            foto_perfil = NULL,
                            contrasena = 'CUENTA_ELIMINADA',
                            verificado = 0
                          WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                
                return $stmt->execute();

            } catch (PDOException $e) {
                error_log("Error en Usuario::eliminar (Soft Delete): " . $e->getMessage());
                // Si falla el soft delete, lanzamos excepción
                throw new Exception("Error al procesar la eliminación del usuario.");
            }
        }
        
        public function obtenerEstadisticasCompletas($id_usuario) {
            try {
                $this->verificarConexion();

                $query = "
                    SELECT 
                        (SELECT COUNT(p.id_publicacion) FROM Publicaciones p WHERE p.id_usuario = :id_usuario AND p.estado = 1) AS total_productos,
                        (SELECT COUNT(m.id_movimiento) FROM Movimientos m JOIN Publicaciones p ON m.id_publicacion = p.id_publicacion WHERE p.id_usuario = :id_usuario_vistas AND m.tipo_movimiento = 'Vista') AS total_vistas,
                        (SELECT COUNT(m.id_movimiento) FROM Movimientos m JOIN Publicaciones p ON m.id_publicacion = p.id_publicacion WHERE p.id_usuario = :id_usuario_contactos AND m.tipo_movimiento = 'Contacto') AS total_contactos,
                        (SELECT COUNT(f.id_favorito) FROM Favoritos f JOIN Publicaciones p ON f.id_publicacion = p.id_publicacion WHERE p.id_usuario = :id_usuario_favoritos) AS total_favoritos
                ";

                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_usuario_vistas', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_usuario_contactos', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_usuario_favoritos', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();

                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$resultado) {
                    return ['total_productos' => 0, 'total_vistas' => 0, 'total_contactos' => 0, 'total_favoritos' => 0];
                }
                return $resultado;

            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerEstadisticasCompletas: " . $e->getMessage());
                return ['total_productos' => 0, 'total_vistas' => 0, 'total_contactos' => 0, 'total_favoritos' => 0];
            }
        }

        public function obtenerTodos($pagina = 1, $limite = 20, $estado = null) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                // Excluimos usuarios eliminados lógicamente (nombres='Usuario' AND apellidos='Eliminado')
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, correo_institucional, codigo_univ, facultad, escuela, estado, fecha_registro, foto_perfil FROM {$this->table} WHERE (nombres != 'Usuario' OR apellidos != 'Eliminado')";
                $params = [];
                if ($estado !== null) {
                    $query .= " AND estado = :estado";
                    $params[':estado'] = $estado;
                }
                $query .= " ORDER BY fecha_registro DESC OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY";
                $params[':limite'] = $limite;
                $params[':offset'] = $offset;
                $stmt = $this->db->prepare($query);
                foreach ($params as $key => $value) {
                    $tipo = PDO::PARAM_STR;
                    if ($key === ':estado' || $key === ':limite' || $key === ':offset') $tipo = PDO::PARAM_INT;
                    $stmt->bindValue($key, $value, $tipo);
                }
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
        
        public function contarTodos($estado = null) {
            try {
                $this->verificarConexion();
                $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE (nombres != 'Usuario' OR apellidos != 'Eliminado')";
                $params = [];
                
                if ($estado !== null) {
                    $query .= " WHERE estado = :estado";
                    $params[':estado'] = $estado;
                }
                
                $stmt = $this->db->prepare($query);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                }
                
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['total'] ?? 0;
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::contarTodos: " . $e->getMessage());
                return 0;
            }
        }
        
        public function buscar($termino, $pagina = 1, $limite = 20) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, correo_institucional, codigo_univ, facultad, escuela, estado, fecha_registro, foto_perfil
                        FROM {$this->table}
                        WHERE nombres LIKE :termino OR apellidos LIKE :termino OR correo_institucional LIKE :termino OR codigo_univ LIKE :termino OR dni LIKE :termino
                        WHERE (nombres != 'Usuario' OR apellidos != 'Eliminado')
                        ORDER BY fecha_registro DESC 
                        OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY"; 
                
                $stmt = $this->db->prepare($query);
                $termino_like = "%" . $termino . "%";
                $stmt->bindParam(':termino', $termino_like);
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::buscar: " . $e->getMessage());
                return [];
            }
        }
        
        public function obtenerPorFacultad($facultad, $pagina = 1, $limite = 20) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, correo_institucional, codigo_univ, facultad, escuela, estado, fecha_registro
                    FROM {$this->table} WHERE facultad = :facultad
                    WHERE (nombres != 'Usuario' OR apellidos != 'Eliminado')
                    ORDER BY nombres ASC, apellidos ASC 
                    OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY"; 
            
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':facultad', $facultad);
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerPorFacultad: " . $e->getMessage());
                return [];
            }
        }
        
        public function obtenerFacultades() {
            try {
                $this->verificarConexion();
                $query = "SELECT DISTINCT facultad FROM {$this->table} WHERE facultad IS NOT NULL AND facultad != '' ORDER BY facultad ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerFacultades: " . $e->getMessage());
                return [];
            }
        }
        
        public function obtenerEscuelas() {
            try {
                $this->verificarConexion();
                $query = "SELECT DISTINCT escuela FROM {$this->table} WHERE escuela IS NOT NULL AND escuela != '' ORDER BY escuela ASC";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerEscuelas: " . $e->getMessage());
                return [];
            }
        }
        
        public function obtenerEstadisticasGenerales() {
            try {
                $this->verificarConexion();
                // Excluir usuarios eliminados de las estadísticas
                $query = "SELECT 
                            COUNT(*) as total_usuarios,
                            SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as usuarios_activos,
                            SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as usuarios_inactivos,
                            COUNT(DISTINCT facultad) as total_facultades,
                            COUNT(DISTINCT escuela) as total_escuelas,
                            (SELECT COUNT(*) FROM Publicaciones WHERE estado = 1) as total_publicaciones_activas
                        FROM {$this->table}
                        WHERE nombres != 'Usuario' OR apellidos != 'Eliminado'";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return ['total_usuarios' => 0, 'usuarios_activos' => 0, 'usuarios_inactivos' => 0, 'total_facultades' => 0, 'total_escuelas' => 0, 'total_publicaciones_activas' => 0];
            }
        }

        public function obtenerCrecimientoMensual($meses = 12) {
            try {
                $this->verificarConexion();
                $sql = "SELECT FORMAT(fecha_registro, 'yyyy-MM') as mes, COUNT(id_usuario) as nuevos_usuarios
                        FROM Usuarios WHERE fecha_registro >= DATEADD(month, -:meses, GETDATE())
                        GROUP BY FORMAT(fecha_registro, 'yyyy-MM') ORDER BY mes ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':meses', $meses, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerCrecimientoMensual: " . $e->getMessage());
                return [];
            }
        }

        public function contarVendedoresActivos() {
            try {
                $this->verificarConexion();
                $query = "SELECT COUNT(DISTINCT id_usuario) as total FROM Publicaciones WHERE estado = 1";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['total'] ?? 0;
            } catch (PDOException $e) {
                error_log("Error en Usuario::contarVendedoresActivos: " . $e->getMessage());
                return 0;
            }
        }

        public function contarNuevosEsteMes() {
            try {
                $this->verificarConexion();
                $query = "SELECT COUNT(id_usuario) as total FROM Usuarios WHERE fecha_registro >= DATEADD(month, DATEDIFF(month, 0, GETDATE()), 0)";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['total'] ?? 0;
            } catch (PDOException $e) {
                error_log("Error en Usuario::contarNuevosEsteMes: " . $e->getMessage());
                return 0;
            }
        }

        public function generarTokenRecuperacion($correo) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                $usuario = $this->obtenerPorCorreo($correo);
                if (!$usuario) return false;
                
                $queryInvalidar = "UPDATE TokensRecuperacion SET utilizado = 1 WHERE id_usuario = :id_usuario AND utilizado = 0";
                $stmtInvalidar = $this->db->prepare($queryInvalidar);
                $stmtInvalidar->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                $stmtInvalidar->execute();
                
                $token = bin2hex(random_bytes(32));
                $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $query = "INSERT INTO TokensRecuperacion (id_usuario, token, expiracion) VALUES (:id_usuario, :token, :expiracion)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expiracion', $expiracion);
                
                if ($stmt->execute()) {
                    $this->db->commit();
                    return ['token' => $token, 'expiracion' => $expiracion, 'id_usuario' => $usuario['id_usuario']];
                }
                
                $this->db->rollBack();
                return false;
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::generarTokenRecuperacion: " . $e->getMessage());
                return false;
            }
        }
        
        // Restablecer contraseña con token
        public function restablecerPassword($token, $nueva_contrasenia) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                // Verificar token válido
                $query = "SELECT tr.*, u.id_usuario 
                        FROM TokensRecuperacion tr
                        INNER JOIN Usuarios u ON tr.id_usuario = u.id_usuario
                        WHERE tr.token = :token 
                        AND tr.utilizado = 0 
                        AND tr.expiracion > GETDATE()";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':token', $token);
                $stmt->execute();
                
                $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$tokenData) {
                    return false;
                }
                
                // Validar nueva contraseña
                $errores = $this->validarContrasenia($nueva_contrasenia);
                if (!empty($errores)) {
                    throw new Exception(implode(', ', $errores));
                }
                
                // Actualizar contraseña
                $nueva_contrasenia_hash = password_hash($nueva_contrasenia, PASSWORD_DEFAULT);
                
                $queryUpdate = "UPDATE Usuarios 
                            SET contrasena = :contrasena 
                            WHERE id_usuario = :id_usuario";
                
                $stmtUpdate = $this->db->prepare($queryUpdate);
                $stmtUpdate->bindParam(':contrasena', $nueva_contrasenia_hash);
                $stmtUpdate->bindParam(':id_usuario', $tokenData['id_usuario'], PDO::PARAM_INT);
                
                if ($stmtUpdate->execute()) {
                    // Marcar token como utilizado
                    $queryMarkUsed = "UPDATE TokensRecuperacion 
                                    SET utilizado = 1 
                                    WHERE id_token = :id_token";
                    
                    $stmtMark = $this->db->prepare($queryMarkUsed);
                    $stmtMark->bindParam(':id_token', $tokenData['id_token'], PDO::PARAM_INT);
                    $stmtMark->execute();
                    
                    $this->db->commit();
                    return true;
                }
                
                $this->db->rollBack();
                return false;
                
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::restablecerPassword: " . $e->getMessage());
                return false;
            }
        }
        
        // Verificar si el usuario puede publicar (límites, restricciones, etc.)
        public function puedePublicar($id_usuario) {
            try {
                $this->verificarConexion();
                
                // Verificar si el usuario está activo
                $usuario = $this->obtenerPorId($id_usuario);
                if (!$usuario || $usuario['estado'] != 1) {
                    return false;
                }
                
                // Aquí puedes agregar más validaciones como:
                // - Límite de publicaciones activas
                // - Antigüedad de la cuenta
                // - Calificación del usuario
                // - etc.
                
                return true;
                
            } catch (Exception $e) {
                error_log("Error en Usuario::puedePublicar: " . $e->getMessage());
                return false;
            }
        }
        
        // Validar datos de registro
        private function validarDatosRegistro($nombres, $apellidos, $dni, $correo, $codigo_univ, $contrasenia) {
            $errores = [];
            
            if (empty(trim($nombres))) $errores[] = "El nombre es obligatorio";
            if (empty(trim($apellidos))) $errores[] = "Los apellidos son obligatorios";
            if (empty($dni) || !preg_match('/^[0-9]{8}$/', $dni)) $errores[] = "El DNI debe tener 8 dígitos";
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido";
            if (empty($codigo_univ)) $errores[] = "El código universitario es obligatorio";
            
            $errores_contrasenia = $this->validarContrasenia($contrasenia);
            $errores = array_merge($errores, $errores_contrasenia);
            
            return $errores;
        }
        
        // Validar contraseña
        private function validarContrasenia($contrasenia) {
            $errores = [];
            
            if (empty($contrasenia)) $errores[] = "La contraseña es obligatoria";
            if (strlen($contrasenia) < 8) $errores[] = "La contraseña debe tener al menos 8 caracteres";
            if (!preg_match('/[A-Z]/', $contrasenia)) $errores[] = "La contraseña debe contener al menos una letra mayúscula";
            if (!preg_match('/[0-9]/', $contrasenia)) $errores[] = "La contraseña debe contener al menos un número";

            return $errores;
        }

        public function obtenerTokenValido($token) {
            try {
                $sql = "SELECT * FROM TokensRecuperacion WHERE token = :token AND utilizado = 0 AND expiracion > GETDATE()";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':token', $token);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error al obtener token válido: " . $e->getMessage());
                return false;
            }
        }

        public function restablecerPasswordConToken($id_usuario, $nueva_contrasenia, $id_token) {
            try {
                $this->db->beginTransaction();
                $contrasenia_hash = password_hash($nueva_contrasenia, PASSWORD_DEFAULT);
                $sql_update_pass = "UPDATE Usuarios SET contrasena = :contrasena WHERE id_usuario = :id_usuario";
                $stmt_pass = $this->db->prepare($sql_update_pass);
                $stmt_pass->bindParam(':contrasena', $contrasenia_hash);
                $stmt_pass->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt_pass->execute();

                $sql_update_token = "UPDATE TokensRecuperacion SET utilizado = 1 WHERE id_token = :id_token";
                $stmt_token = $this->db->prepare($sql_update_token);
                $stmt_token->bindParam(':id_token', $id_token, PDO::PARAM_INT);
                $stmt_token->execute();

                $this->db->commit();
                return true;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error al restablecer contraseña con token: " . $e->getMessage());
                return false;
            }
        }

        public function obtenerFechaActualDB() {
            try {
                $stmt = $this->db->query("SELECT GETDATE() as fecha_actual");
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['fecha_actual'] ?? false;
            } catch (PDOException $e) {
                error_log("Error al obtener fecha de la BD: " . $e->getMessage());
                return false;
            }
        }

        public function guardarTokenVerificacion($id_usuario, $token, $expiracion) {
            $sql = "UPDATE Usuarios SET token_verificacion = ?, expiracion_token_verificacion = ? WHERE id_usuario = ?";
            try {
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$token, $expiracion, $id_usuario]);
            } catch (PDOException $e) {
                error_log("Error en Usuario::guardarTokenVerificacion: " . $e->getMessage());
                return false;
            }
        }

        public function obtenerUsuarioPorTokenVerificacion($token) {
            $sql = "SELECT id_usuario FROM Usuarios WHERE token_verificacion = ? AND expiracion_token_verificacion > GETDATE()";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$token]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerUsuarioPorTokenVerificacion: " . $e->getMessage());
                return false;
            }
        }

        public function marcarUsuarioComoVerificado($id_usuario) {
            $sql = "UPDATE Usuarios SET verificado = 1, token_verificacion = NULL, expiracion_token_verificacion = NULL WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id_usuario]);
        }


    }
?>