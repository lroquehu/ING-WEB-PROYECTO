<?php
    class AdminController {
        private $usuarioModel;
        private $publicacionModel;
        private $categoriaModel;

        public function __construct() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // 1. Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['redirect_url'] = BASE_URL . 'admin';
                header('Location: ' . BASE_URL . 'login');
                exit;
            }

            if (!isset($_SESSION['usuario_rol']) || strtolower($_SESSION['usuario_rol']) !== 'admin') {
                // Redirigir si no es el administrador
                header('Location: ' . BASE_URL . 'admin');
                exit;
            }

            // 3. Incluir Modelos
            require_once 'aplicacion/Modelos/Usuario.php';
            require_once 'aplicacion/Modelos/Publicacion.php';
            require_once 'aplicacion/Modelos/Categoria.php';
            
            $this->usuarioModel = new Usuario();
            $this->publicacionModel = new Publicacion();
            $this->categoriaModel = new Categoria();
        }

        /**
         * Vista principal del Panel de Administración (Dashboard)
         */
        public function index() {
            try {
                // Obtener estadísticas generales de la plataforma
                $stats_usuarios = $this->usuarioModel->obtenerEstadisticasGenerales();
                $stats_publicaciones = $this->publicacionModel->obtenerEstadisticas();
                
                $datosVista = [
                    'titulo' => 'Dashboard de Administración',
                    'stats_usuarios' => $stats_usuarios,
                    'stats_publicaciones' => $stats_publicaciones
                ];
                
            } catch (Exception $e) {
                error_log("Error en AdminController::index: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar las estadísticas";
            }
            
            include 'aplicacion/Vistas/admin/index.php';
        }

        /**
         * Gestión de Usuarios
         */
        public function usuarios() {
            $pagina = $_GET['pagina'] ?? 1;
            $limite = 20;
            $estado = $_GET['estado'] ?? null;
            
            try {
                $usuarios = $this->usuarioModel->obtenerTodos($pagina, $limite, $estado);
                $total_usuarios = $this->usuarioModel->contarTodos($estado);
                $total_paginas = ceil($total_usuarios / $limite);
                
                $datosVista = [
                    'titulo' => 'Gestión de Usuarios',
                    'usuarios' => $usuarios,
                    'pagina_actual' => $pagina,
                    'total_paginas' => $total_paginas,
                    'total_usuarios' => $total_usuarios,
                    'estado_filtro' => $estado
                ];
                
            } catch (Exception $e) {
                error_log("Error en AdminController::usuarios: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar la lista de usuarios";
            }
            
            include 'aplicacion/Vistas/admin/usuarios.php';
        }

        /**
         * Acción para cambiar el estado de un usuario (Activar/Desactivar)
         */
        public function cambiarEstadoUsuario() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'admin/usuarios');
                exit;
            }

            $id_usuario = $_POST['id_usuario'] ?? 0;
            $nuevo_estado = $_POST['estado'] ?? 0; // 1: Activo, 0: Inactivo

            try {
                // Validación básica de datos
                if ($id_usuario <= 0 || !in_array($nuevo_estado, [0, 1])) {
                    throw new Exception("Datos de usuario o estado inválidos");
                }
                
                // El administrador no puede desactivarse a sí mismo
                if ($id_usuario == $_SESSION['usuario_id']) {
                    throw new Exception("No puedes modificar tu propio estado desde este panel");
                }

                if ($this->usuarioModel->cambiarEstado($id_usuario, $nuevo_estado)) {
                    $_SESSION['admin_success'] = "Estado de usuario actualizado correctamente.";
                } else {
                    $_SESSION['admin_error'] = "Error al actualizar el estado del usuario.";
                }

            } catch (Exception $e) {
                error_log("Error en AdminController::cambiarEstadoUsuario: " . $e->getMessage());
                $_SESSION['admin_error'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . 'admin/usuarios');
            exit;
        }

        /**
         * Gestión de Categorías
         */
        public function categorias() {
            try {
                $categorias = $this->categoriaModel->obtenerTodas();
                $stats_categorias = $this->categoriaModel->obtenerEstadisticas();
                
                $datosVista = [
                    'titulo' => 'Gestión de Categorías',
                    'categorias' => $categorias,
                    'stats_categorias' => $stats_categorias,
                    'error' => $_SESSION['admin_error'] ?? '',
                    'success' => $_SESSION['admin_success'] ?? ''
                ];
                unset($_SESSION['admin_error']);
                unset($_SESSION['admin_success']);
                
            } catch (Exception $e) {
                error_log("Error en AdminController::categorias: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar las categorías";
            }
            
            require_once 'aplicacion/Vistas/admin/categorias.php';
        }

        /**
         * Acción para crear o actualizar una categoría
         */
        public function guardarCategoria() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'admin/categorias');
                exit;
            }

            $id_categoria = $_POST['id_categoria'] ?? 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            try {
                if (empty($nombre)) {
                    throw new Exception("El nombre de la categoría es obligatorio.");
                }

                if ($id_categoria > 0) {
                    // Actualizar
                    if ($this->categoriaModel->actualizar($id_categoria, $nombre, $descripcion)) {
                        $_SESSION['admin_success'] = "Categoría actualizada correctamente.";
                    } else {
                        throw new Exception("Error al actualizar la categoría.");
                    }
                } else {
                    // Crear
                    if ($this->categoriaModel->crear($nombre, $descripcion)) {
                        $_SESSION['admin_success'] = "Categoría creada correctamente.";
                    } else {
                        throw new Exception("Error al crear la categoría.");
                    }
                }

            } catch (Exception $e) {
                error_log("Error en AdminController::guardarCategoria: " . $e->getMessage());
                $_SESSION['admin_error'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . 'admin/categorias');
            exit;
        }
        
        /**
         * Acción para eliminar una categoría
         */
        public function eliminarCategoria() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'admin/categorias');
                exit;
            }

            $id_categoria = $_POST['id_categoria'] ?? 0;

            try {
                if ($id_categoria <= 0) {
                    throw new Exception("ID de categoría inválido.");
                }

                if ($this->categoriaModel->eliminar($id_categoria)) {
                    $_SESSION['admin_success'] = "Categoría eliminada correctamente.";
                } else {
                    // El modelo ya registra el error si hay productos asociados
                    throw new Exception("No se pudo eliminar la categoría. Revise el log de errores.");
                }

            } catch (Exception $e) {
                error_log("Error en AdminController::eliminarCategoria: " . $e->getMessage());
                $_SESSION['admin_error'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . 'admin/categorias');
            exit;
        }

        public function publicaciones() {
            try {
                // Usar el método que obtiene TODAS las publicaciones, incluyendo inactivas
                $publicaciones = $this->publicacionModel->obtenerTodasParaAdmin(); 

                $datosVista = [
                    'titulo' => 'Gestión de Publicaciones',
                    'publicaciones' => $publicaciones,
                ];
                
                // Limpiar mensajes de sesión
                $datosVista['error'] = $_SESSION['admin_error'] ?? '';
                $datosVista['success'] = $_SESSION['admin_success'] ?? '';
                unset($_SESSION['admin_error']);
                unset($_SESSION['admin_success']);

            } catch (Exception $e) {
                error_log("Error en AdminController::publicaciones: " . $e->getMessage());
                $datosVista['error'] = "Error al cargar la lista de publicaciones: " . $e->getMessage();
            }
            
            include 'aplicacion/Vistas/admin/publicaciones.php';
        }

        public function cambiarEstadoPublicacion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . 'admin/publicaciones');
                exit;
            }

            $id_publicacion = $_POST['id_publicacion'] ?? 0;
            $nuevo_estado = $_POST['estado'] ?? 0; // 1: Activo, 0: Inactivo

            try {
                if ($id_publicacion <= 0 || !in_array($nuevo_estado, [0, 1])) {
                    throw new Exception("Datos de publicación o estado inválidos");
                }

                if ($this->publicacionModel->cambiarEstado($id_publicacion, $nuevo_estado)) {
                    $mensaje = ($nuevo_estado == 1) ? "Publicación activada correctamente." : "Publicación desactivada correctamente.";
                    $_SESSION['admin_success'] = $mensaje;
                } else {
                    $_SESSION['admin_error'] = "Error al actualizar el estado de la publicación.";
                }

            } catch (Exception $e) {
                error_log("Error en AdminController::cambiarEstadoPublicacion: " . $e->getMessage());
                $_SESSION['admin_error'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . 'admin/publicaciones');
            exit;
        }
    }
?>