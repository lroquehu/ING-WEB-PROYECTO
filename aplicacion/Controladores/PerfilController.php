<?php
    class PerfilController {
        private $usuarioModel;
        private $publicacionModel;
        
        public function __construct() {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['redirect_url'] = BASE_URL . 'perfil';
                header('Location: ' . BASE_URL . 'login');
                exit;
            }
            
            // Incluir y inicializar modelos
            require_once 'aplicacion/Modelos/Usuario.php';
            require_once 'aplicacion/Modelos/Publicacion.php';
            $this->usuarioModel = new Usuario();
            $this->publicacionModel = new Publicacion();
        }
        
        public function index() {
            try {
                // Obtener datos actualizados del usuario
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                // Obtener publicaciones del usuario
                $publicaciones = $this->publicacionModel->obtenerPorUsuario($_SESSION['usuario_id']);
                
                // Obtener estadísticas
                $estadisticas = $this->obtenerEstadisticasUsuario($_SESSION['usuario_id']);
                
                // Verificar si hay mensajes de sesión o GET
                $mensaje_exito = $_SESSION['mensaje_exito'] ?? '';
                unset($_SESSION['mensaje_exito']);
                
                $error = $_SESSION['error'] ?? '';
                unset($_SESSION['error']);

                if (empty($mensaje_exito)) {
                    $success = $_GET['success'] ?? '';
                    switch ($success) {
                        case '1':
                            $mensaje_exito = "Perfil actualizado exitosamente";
                            break;
                        case '2':
                            $mensaje_exito = "Contraseña cambiada exitosamente";
                            break;
                        case '3':
                            $mensaje_exito = "Publicación creada exitosamente";
                            break;
                        case '4':
                            $mensaje_exito = "Publicación actualizada exitosamente";
                            break;
                    }
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'publicaciones' => $publicaciones,
                    'estadisticas' => $estadisticas,
                    'mensaje_exito' => $mensaje_exito,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::index: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar el perfil",
                    'usuario' => [],
                    'publicaciones' => [],
                    'estadisticas' => [
                        'total_publicaciones' => 0,
                        'publicaciones_activas' => 0,
                        'publicaciones_pausadas' => 0
                    ]
                ];
            }
            
            include 'aplicacion/Vistas/perfil/index.php';
        }
        
        public function editar() {
            try {
                // Obtener datos actuales del usuario
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                $error = '';
                $datos_formulario = [
                    'nombres' => $usuario['nombres'],
                    'apellidos' => $usuario['apellidos'],
                    'telefono' => $usuario['telefono'],
                    'facultad' => $usuario['facultad'],
                    'escuela' => $usuario['escuela']
                ];
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Recoger y sanitizar datos
                    $nombres = trim($_POST['nombres'] ?? '');
                    $apellidos = trim($_POST['apellidos'] ?? '');
                    $telefono = trim($_POST['telefono'] ?? '');
                    $facultad = trim($_POST['facultad'] ?? '');
                    $escuela = trim($_POST['escuela'] ?? '');
                    
                    // Validaciones
                    if (empty($nombres) || empty($apellidos)) {
                        $error = "Los nombres y apellidos son obligatorios";
                    } else {
                        // Actualizar perfil
                        if ($this->usuarioModel->actualizarPerfil(
                            $_SESSION['usuario_id'],
                            $nombres,
                            $apellidos,
                            $telefono,
                            $facultad,
                            $escuela
                        )) {
                            // Actualizar datos en sesión
                            $_SESSION['usuario_nombre'] = $nombres . ' ' . $apellidos;
                            $_SESSION['usuario_facultad'] = $facultad;
                            $_SESSION['usuario_escuela'] = $escuela;
                            
                            header('Location: ' . BASE_URL . 'perfil?success=1');
                            exit;
                        } else {
                            $error = "Error al actualizar el perfil";
                        }
                    }
                    
                    // Mantener datos del formulario en caso de error
                    $datos_formulario = [
                        'nombres' => $nombres,
                        'apellidos' => $apellidos,
                        'telefono' => $telefono,
                        'facultad' => $facultad,
                        'escuela' => $escuela
                    ];
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'datos_formulario' => $datos_formulario,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::editar: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar el formulario de edición",
                    'usuario' => [],
                    'datos_formulario' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/editar.php';
        }
        
        public function cambiarPassword() {
            try {
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                $error = '';
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $password_actual = $_POST['password_actual'] ?? '';
                    $nuevo_password = $_POST['nuevo_password'] ?? '';
                    $confirmar_password = $_POST['confirmar_password'] ?? '';
                    
                    // VALIDACIONES MEJORADAS:
                    if (empty($password_actual) || empty($nuevo_password) || empty($confirmar_password)) {
                        $error = "Todos los campos son obligatorios";
                    } elseif ($nuevo_password !== $confirmar_password) {
                        $error = "Las contraseñas no coinciden";
                    } elseif (strlen($nuevo_password) < 8) {
                        $error = "La nueva contraseña debe tener al menos 8 caracteres";
                    } elseif (!preg_match('/[A-Z]/', $nuevo_password)) {
                        $error = "La contraseña debe contener al menos una letra mayúscula";
                    } elseif (!preg_match('/[0-9]/', $nuevo_password)) {
                        $error = "La contraseña debe contener al menos un número";
                    } elseif ($nuevo_password === $password_actual) {
                        $error = "La nueva contraseña debe ser diferente a la actual";
                    } else {
                        if ($this->usuarioModel->cambiarPassword(
                            $_SESSION['usuario_id'],
                            $password_actual,
                            $nuevo_password
                        )) {
                            header('Location: ' . BASE_URL . 'perfil?success=2');
                            exit;
                        } else {
                            $error = "La contraseña actual es incorrecta";
                        }
                    }
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'error' => $error
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::cambiarPassword: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al procesar la solicitud",
                    'usuario' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/cambiar-password.php';
        }
        
        public function publicaciones() {
            try {
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                // Obtener todas las publicaciones del usuario
                $publicaciones = $this->publicacionModel->obtenerPorUsuario($_SESSION['usuario_id']);
                
                // Filtrar por estado si se especifica
                $estado_filtro = $_GET['estado'] ?? 'all';
                if ($estado_filtro !== 'all') {
                    $publicaciones = array_filter($publicaciones, function($pub) use ($estado_filtro) {
                        return $pub['estado'] == $estado_filtro;
                    });
                }
                
                $datosVista = [
                    'usuario' => $usuario,
                    'publicaciones' => $publicaciones,
                    'estado_filtro' => $estado_filtro,
                    'estadisticas' => $this->obtenerEstadisticasPublicaciones($publicaciones)
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::publicaciones: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar las publicaciones",
                    'usuario' => [],
                    'publicaciones' => [],
                    'estadisticas' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/publicaciones.php';
        }
        
        public function favoritos() {
            try {
                $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                
                if (!$usuario) {
                    throw new Exception("Usuario no encontrado");
                }
                
                // Obtener publicaciones favoritas (necesitarías implementar este método)
                $favoritos = $this->publicacionModel->obtenerFavoritos($_SESSION['usuario_id']);
                
                $datosVista = [
                    'usuario' => $usuario,
                    'favoritos' => $favoritos
                ];
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::favoritos: " . $e->getMessage());
                $datosVista = [
                    'error' => "Error al cargar los favoritos",
                    'usuario' => [],
                    'favoritos' => []
                ];
            }
            
            include 'aplicacion/Vistas/perfil/favoritos.php';
        }
        
        public function eliminarPublicacion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'perfil/publicaciones');
                exit;
            }
            
            $publicacion_id = $_POST['publicacion_id'] ?? 0;
            
            if (!$publicacion_id) {
                $_SESSION['error'] = "ID de publicación no válido";
                header('Location: ' . BASE_URL . 'perfil/publicaciones');
                exit;
            }
            
            try {
                // Verificar que la publicación pertenece al usuario
                $publicacion = $this->publicacionModel->obtenerPorId($publicacion_id);
                
                if (!$publicacion || $publicacion['id_usuario'] != $_SESSION['usuario_id']) {
                    $_SESSION['error'] = "No tienes permisos para eliminar esta publicación";
                    header('Location: ' . BASE_URL . 'perfil/publicaciones');
                    exit;
                }
                
                // Eliminar publicación (cambiar estado a eliminado)
                if ($this->publicacionModel->eliminar($publicacion_id)) {
                    $_SESSION['success'] = "Publicación eliminada exitosamente";
                } else {
                    $_SESSION['error'] = "Error al eliminar la publicación";
                }
                
            } catch (Exception $e) {
                error_log("Error en PerfilController::eliminarPublicacion: " . $e->getMessage());
                $_SESSION['error'] = "Error al procesar la solicitud";
            }
            
            header('Location: ' . BASE_URL . 'perfil/publicaciones');
            exit;
        }
        
        private function obtenerEstadisticasUsuario($usuario_id) {
            try {
                $total_publicaciones = $this->publicacionModel->contarPorUsuario($usuario_id);
                $publicaciones_activas = $this->publicacionModel->contarPorUsuarioYEstado($usuario_id, 1);
                $publicaciones_pausadas = $this->publicacionModel->contarPorUsuarioYEstado($usuario_id, 2);
                
                return [
                    'total_publicaciones' => $total_publicaciones,
                    'publicaciones_activas' => $publicaciones_activas,
                    'publicaciones_pausadas' => $publicaciones_pausadas
                ];
                
            } catch (Exception $e) {
                error_log("Error al obtener estadísticas de usuario: " . $e->getMessage());
                return [
                    'total_publicaciones' => 0,
                    'publicaciones_activas' => 0,
                    'publicaciones_pausadas' => 0
                ];
            }
        }
        
        private function obtenerEstadisticasPublicaciones($publicaciones) {
            $total = count($publicaciones);
            $activas = 0;
            $pausadas = 0;
            $eliminadas = 0;
            
            foreach ($publicaciones as $publicacion) {
                switch ($publicacion['estado']) {
                    case 1: $activas++; break;
                    case 2: $pausadas++; break;
                    case 3: $eliminadas++; break;
                }
            }
            
            return [
                'total' => $total,
                'activas' => $activas,
                'pausadas' => $pausadas,
                'eliminadas' => $eliminadas
            ];
        }
    }
?>