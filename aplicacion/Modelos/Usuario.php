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
                
                // CORREGIDO: usar 'contrasena' en lugar de 'contraseña'
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                contrasena, estado, fecha_registro
                        FROM {$this->table} 
                        WHERE correo_institucional = :correo 
                        AND estado = 1";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // CORREGIDO: verificar contra 'contrasena'
                if ($usuario && password_verify($contrasenia, $usuario['contrasena'])) {
                    // Eliminar la contraseña del array antes de retornar
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
            $codigo_univ, $facultad, $escuela, $contrasenia, $foto_perfil = null) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                // Verificar si el correo, DNI o código ya existen
                if ($this->existeCorreo($correo)) {
                    throw new Exception("El correo electrónico ya está registrado");
                }
                
                if ($this->existeDni($dni)) {
                    throw new Exception("El DNI ya está registrado");
                }
                
                if ($this->existeCodigoUniv($codigo_univ)) {
                    throw new Exception("El código universitario ya está registrado");
                }
                
                // Validar datos
                $errores = $this->validarDatosRegistro($nombres, $apellidos, $dni, $correo, $codigo_univ, $contrasenia);
                if (!empty($errores)) {
                    throw new Exception(implode(', ', $errores));
                }
                
                // Hash de la contraseña
                $contrasenia_hash = password_hash($contrasenia, PASSWORD_DEFAULT);
                
                // CORREGIDO: usar 'contrasena' en lugar de 'contraseña'
                $query = "INSERT INTO {$this->table} 
                        (nombres, apellidos, dni, telefono, correo_institucional, 
                        codigo_univ, facultad, escuela, contrasena, foto_perfil, estado)
                        VALUES (:nombres, :apellidos, :dni, :telefono, :correo, 
                        :codigo_univ, :facultad, :escuela, :contrasena, :foto_perfil, 1)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':codigo_univ', $codigo_univ);
                $stmt->bindParam(':facultad', $facultad);
                $stmt->bindParam(':escuela', $escuela);
                $stmt->bindParam(':contrasena', $contrasenia_hash); // CORREGIDO
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
                error_log("Error en Usuario::registrar: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::registrar: " . $e->getMessage());
                return false;
            }
        }
        
        // Obtener usuario por ID
        public function obtenerPorId($id_usuario) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                foto_perfil, estado, fecha_registro
                        FROM {$this->table} 
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
        
        // Obtener usuario por correo
        public function obtenerPorCorreo($correo) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                estado, fecha_registro
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
        
        // Actualizar perfil de usuario
        public function actualizarPerfil($id_usuario, $nombres, $apellidos, $telefono, $facultad, $escuela) {
            try {
                $this->verificarConexion();
                
                // Validar datos
                $errores = [];
                if (empty(trim($nombres))) {
                    $errores[] = "El nombre es obligatorio";
                }
                if (empty(trim($apellidos))) {
                    $errores[] = "Los apellidos son obligatorios";
                }
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
        
        // Actualizar foto de perfil
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
        
        //Cambiar contraseña - CORREGIDO: usar 'contrasena'
        public function cambiarPassword($id_usuario, $contrasenia_actual, $nueva_contrasenia) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                // Verificar contraseña actual - CORREGIDO
                $query_actual = "SELECT contrasena FROM {$this->table} 
                                WHERE id_usuario = :id_usuario";
                
                $stmt_actual = $this->db->prepare($query_actual);
                $stmt_actual->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt_actual->execute();
                
                $resultado = $stmt_actual->fetch(PDO::FETCH_ASSOC);
                
                // CORREGIDO: verificar contra 'contrasena'
                if (!$resultado || !password_verify($contrasenia_actual, $resultado['contrasena'])) {
                    $this->db->rollBack();
                    return false;
                }
                
                // Validar nueva contraseña
                $errores = $this->validarContrasenia($nueva_contrasenia);
                if (!empty($errores)) {
                    $this->db->rollBack();
                    throw new Exception(implode(', ', $errores));
                }
                
                // Actualizar contraseña - CORREGIDO
                $nueva_contrasenia_hash = password_hash($nueva_contrasenia, PASSWORD_DEFAULT);
                
                $query = "UPDATE {$this->table} 
                        SET contrasena = :contrasena
                        WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':contrasena', $nueva_contrasenia_hash); // CORREGIDO
                
                if ($stmt->execute()) {
                    $this->db->commit();
                    return true;
                }
                
                $this->db->rollBack();
                return false;
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::cambiarPassword: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error en Usuario::cambiarPassword: " . $e->getMessage());
                return false;
            }
        }
        
        // Verificar si un correo existe
        public function existeCorreo($correo) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario FROM {$this->table} 
                        WHERE correo_institucional = :correo";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                
                return (bool) $stmt->fetch();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeCorreo: " . $e->getMessage());
                return false;
            }
        }
        
        // Verificar si un DNI existe
        public function existeDni($dni) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario FROM {$this->table} 
                        WHERE dni = :dni";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':dni', $dni);
                $stmt->execute();
                
                return (bool) $stmt->fetch();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeDni: " . $e->getMessage());
                return false;
            }
        }
        
        // Verificar si un código universitario existe
        public function existeCodigoUniv($codigo_univ) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT id_usuario FROM {$this->table} 
                        WHERE codigo_univ = :codigo_univ";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':codigo_univ', $codigo_univ);
                $stmt->execute();
                
                return (bool) $stmt->fetch();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::existeCodigoUniv: " . $e->getMessage());
                return false;
            }
        }
        
        // Cambiar estado de usuario (activar/desactivar)
        public function cambiarEstado($id_usuario, $estado) {
            try {
                $this->verificarConexion();
                
                $query = "UPDATE {$this->table} 
                        SET estado = :estado
                        WHERE id_usuario = :id_usuario";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
                
                return $stmt->execute();
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::cambiarEstado: " . $e->getMessage());
                return false;
            }
        }
        
        // Obtener estadísticas del usuario
        public function obtenerEstadisticas($id_usuario) {
            try {
                if (!$this->publicacionModel) {
                    require_once 'aplicacion/Modelos/Publicacion.php';
                    $this->publicacionModel = new Publicacion();
                }
                
                $total_publicaciones = $this->publicacionModel->contarPorUsuario($id_usuario);
                $publicaciones_activas = $this->publicacionModel->contarPorUsuarioYEstado($id_usuario, 1);
                $publicaciones_pausadas = $this->publicacionModel->contarPorUsuarioYEstado($id_usuario, 2);
                
                return [
                    'total_publicaciones' => $total_publicaciones,
                    'publicaciones_activas' => $publicaciones_activas,
                    'publicaciones_pausadas' => $publicaciones_pausadas,
                    'total_ventas' => 0, // Por implementar
                    'calificacion_promedio' => 0, // Por implementar
                    'vistas_totales' => 0 // Por implementar
                ];
                
            } catch (Exception $e) {
                error_log("Error en Usuario::obtenerEstadisticas: " . $e->getMessage());
                return [
                    'total_publicaciones' => 0,
                    'publicaciones_activas' => 0,
                    'publicaciones_pausadas' => 0,
                    'total_ventas' => 0,
                    'calificacion_promedio' => 0,
                    'vistas_totales' => 0
                ];
            }
        }
        
        // Obtener todos los usuarios (para administración)
        public function obtenerTodos($pagina = 1, $limite = 20, $estado = null) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                estado, fecha_registro
                        FROM {$this->table}";
                
                $params = [];
                
                if ($estado !== null) {
                    $query .= " WHERE estado = :estado";
                    $params[':estado'] = $estado;
                }
                
                $query .= " ORDER BY fecha_registro DESC 
                        LIMIT :limite OFFSET :offset";
                
                $params[':limite'] = $limite;
                $params[':offset'] = $offset;
                
                $stmt = $this->db->prepare($query);
                
                foreach ($params as $key => $value) {
                    $tipo = PDO::PARAM_STR;
                    if ($key === ':estado' || $key === ':limite' || $key === ':offset') {
                        $tipo = PDO::PARAM_INT;
                    }
                    $stmt->bindValue($key, $value, $tipo);
                }
                
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerTodos: " . $e->getMessage());
                return [];
            }
        }
        
        // Contar total de usuarios
        public function contarTodos($estado = null) {
            try {
                $this->verificarConexion();
                
                $query = "SELECT COUNT(*) as total FROM {$this->table}";
                
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
        
        // Buscar usuarios
        public function buscar($termino, $pagina = 1, $limite = 20) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                estado, fecha_registro
                        FROM {$this->table}
                        WHERE nombres LIKE :termino 
                            OR apellidos LIKE :termino
                            OR correo_institucional LIKE :termino
                            OR codigo_univ LIKE :termino
                            OR dni LIKE :termino
                        ORDER BY fecha_registro DESC
                        LIMIT :limite OFFSET :offset";
                
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
        
        // Obtener usuarios por facultad
        public function obtenerPorFacultad($facultad, $pagina = 1, $limite = 20) {
            try {
                $this->verificarConexion();
                $offset = ($pagina - 1) * $limite;
                
                $query = "SELECT id_usuario, nombres, apellidos, dni, telefono, 
                                correo_institucional, codigo_univ, facultad, escuela, 
                                estado, fecha_registro
                        FROM {$this->table}
                        WHERE facultad = :facultad
                        ORDER BY nombres ASC, apellidos ASC
                        LIMIT :limite OFFSET :offset";
                
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
        
        // Obtener lista de facultades únicas
        public function obtenerFacultades() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT DISTINCT facultad 
                        FROM {$this->table} 
                        WHERE facultad IS NOT NULL 
                        AND facultad != ''
                        ORDER BY facultad ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerFacultades: " . $e->getMessage());
                return [];
            }
        }
        
        // Obtener lista de escuelas únicas
        public function obtenerEscuelas() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT DISTINCT escuela 
                        FROM {$this->table} 
                        WHERE escuela IS NOT NULL 
                        AND escuela != ''
                        ORDER BY escuela ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerEscuelas: " . $e->getMessage());
                return [];
            }
        }
        
        // Obtener estadísticas generales de usuarios
        public function obtenerEstadisticasGenerales() {
            try {
                $this->verificarConexion();
                
                $query = "SELECT 
                            COUNT(*) as total_usuarios,
                            SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as usuarios_activos,
                            SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as usuarios_inactivos,
                            COUNT(DISTINCT facultad) as total_facultades,
                            COUNT(DISTINCT escuela) as total_escuelas,
                            (SELECT COUNT(*) FROM Publicaciones WHERE estado = 1) as total_publicaciones_activas
                        FROM {$this->table}";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
                return $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en Usuario::obtenerEstadisticasGenerales: " . $e->getMessage());
                return [
                    'total_usuarios' => 0,
                    'usuarios_activos' => 0,
                    'usuarios_inactivos' => 0,
                    'total_facultades' => 0,
                    'total_escuelas' => 0,
                    'total_publicaciones_activas' => 0
                ];
            }
        }
        
        // Generar token de recuperación de contraseña
        public function generarTokenRecuperacion($correo) {
            try {
                $this->verificarConexion();
                $this->db->beginTransaction();
                
                $usuario = $this->obtenerPorCorreo($correo);
                if (!$usuario) {
                    return false;
                }
                
                // Invalidar tokens anteriores
                $queryInvalidar = "UPDATE TokensRecuperacion 
                                SET utilizado = 1 
                                WHERE id_usuario = :id_usuario AND utilizado = 0";
                $stmtInvalidar = $this->db->prepare($queryInvalidar);
                $stmtInvalidar->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                $stmtInvalidar->execute();
                
                // Generar nuevo token
                $token = bin2hex(random_bytes(32));
                $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $query = "INSERT INTO TokensRecuperacion (id_usuario, token, expiracion)
                        VALUES (:id_usuario, :token, :expiracion)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expiracion', $expiracion);
                
                if ($stmt->execute()) {
                    $this->db->commit();
                    return [
                        'token' => $token,
                        'expiracion' => $expiracion,
                        'id_usuario' => $usuario['id_usuario']
                    ];
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
                        AND tr.expiracion > NOW()";
                
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
            
            if (empty(trim($nombres))) {
                $errores[] = "El nombre es obligatorio";
            }
            
            if (empty(trim($apellidos))) {
                $errores[] = "Los apellidos son obligatorios";
            }
            
            if (empty($dni) || !preg_match('/^[0-9]{8}$/', $dni)) {
                $errores[] = "El DNI debe tener 8 dígitos";
            }
            
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El correo electrónico no es válido";
            }
            
            if (empty($codigo_univ)) {
                $errores[] = "El código universitario es obligatorio";
            }
            
            $errores_contrasenia = $this->validarContrasenia($contrasenia);
            $errores = array_merge($errores, $errores_contrasenia);
            
            return $errores;
        }
        
        // Validar contraseña
        private function validarContrasenia($contrasenia) {
            $errores = [];
            
            if (empty($contrasenia)) {
                $errores[] = "La contraseña es obligatoria";
            }
            
            if (strlen($contrasenia) < 8) {
                $errores[] = "La contraseña debe tener al menos 8 caracteres";
            }
            
            if (!preg_match('/[A-Z]/', $contrasenia)) {
                $errores[] = "La contraseña debe contener al menos una letra mayúscula";
            }
            
            if (!preg_match('/[0-9]/', $contrasenia)) {
                $errores[] = "La contraseña debe contener al menos un número";
            }
            
            return $errores;
        }
    }
?>