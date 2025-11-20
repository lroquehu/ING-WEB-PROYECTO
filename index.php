<?php
    // Configuración básica
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    // Definir constante BASE_URL si no existe
    if (!defined('BASE_URL')) {
        //define('BASE_URL', 'http://38.250.161.160/ING-WEB-PROYECTO/');
        /**------------------------------------------- */
        /* SOLO SI QUIEREN VOLVER AL LOCAL HOST */
        /**------------------------------------------- */
        define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/');
    }

    // Incluir archivos necesarios
    require_once 'rutas.php';
    require_once 'aplicacion/Configuracion/conexion.php';

    // Manejo de errores personalizado
    function handleError($errno, $errstr, $errfile, $errline) {
        error_log("Error [$errno]: $errstr en $errfile línea $errline");
        if (ini_get('display_errors')) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>
                    <strong>Error:</strong> $errstr<br>
                    <small>Archivo: $errfile (Línea: $errline)</small>
                </div>";
        }
    }

    set_error_handler('handleError');

    // Función para cargar controladores de forma segura
    function cargarControlador($controllerName) {
        // CORREGIDO: Ruta correcta con "Controladores" en mayúscula
        $controllerFile = "aplicacion/Controladores/{$controllerName}.php";
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Archivo de controlador no encontrado: $controllerFile");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            throw new Exception("Clase controladora no encontrada: $controllerName");
        }
        
        return new $controllerName();
    }

    // Procesar la solicitud
    try {
        // Obtener la URL solicitada
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        /**------------------------------------------- */
        /* SOLO SI QUIEREN VOLVER AL LOCAL HOST */
        /**------------------------------------------- */
        // Remover el base path del proyecto
        $basePath = '/ING-WEB-PROYECTO';
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        

        // Limpiar la URL
        $requestUri = rtrim($requestUri, '/');
        if (empty($requestUri)) {
            $requestUri = '/';
        }

        // USAR EL SISTEMA DE RUTAS
        $route = getRoute($requestUri);
        
        if ($route) {
            // Ruta encontrada en el sistema de rutas
            $controllerName = $route['controller'];
            $action = $route['action'];
            
            // Manejar parámetros de la ruta
            if (isset($route['params'])) {
                foreach ($route['params'] as $key => $value) {
                    $_GET[$key] = $value; // ahora el índice es el nombre real: 'id'
                }
            }
        } else {
            // Fallback al sistema tradicional de parámetros GET
            $controllerName = $_GET['c'] ?? 'Inicio';
            $action = $_GET['a'] ?? 'index';
        }

        // Sanitizar y validar - CORREGIDO: Permitir números en nombres
        $controllerName = htmlspecialchars(trim($controllerName));
        $action = htmlspecialchars(trim($action));
        
        // Validar nombres (letras y números) - CORREGIDO
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $controllerName) || 
            !preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $action)) {
            throw new Exception("Parámetros de URL inválidos");
        }
        
        // Asegurar que el nombre del controlador termine con "Controller"
        if (!str_ends_with($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }
        
        // Cargar y ejecutar el controlador
        $controller = cargarControlador($controllerName);
        
        if (!method_exists($controller, $action)) {
            throw new Exception("Método no encontrado: $action");
        }
        
        // Ejecutar la acción
        if (isset($route['params'])) {
            $controller->$action($route['params']);
        } else {
            $controller->$action();
        }
        
    } catch (Exception $e) {
        // Manejo de errores centralizado
        error_log("Error en aplicación: " . $e->getMessage());
        
        http_response_code(404);
        
        // Mostrar página de error amigable
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Página no encontrada - UniEmprende</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    background: linear-gradient(135deg, #910202 0%, #700101 100%);
                    color: white; 
                    margin: 0; 
                    padding: 0; 
                    display: flex; 
                    justify-content: center; 
                    align-items: center; 
                    min-height: 100vh;
                }
                .error-container { 
                    text-align: center; 
                    background: rgba(255,255,255,0.1); 
                    padding: 3rem; 
                    border-radius: 12px; 
                    backdrop-filter: blur(10px);
                    max-width: 500px;
                }
                .error-code { 
                    font-size: 4rem; 
                    font-weight: bold; 
                    margin-bottom: 1rem;
                    color: #ffd700;
                }
                .error-message { 
                    font-size: 1.5rem; 
                    margin-bottom: 2rem;
                }
                .btn { 
                    display: inline-block; 
                    padding: 0.8rem 1.5rem; 
                    background: #ffd700; 
                    color: #910202; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    font-weight: bold; 
                    transition: all 0.3s;
                }
                .btn:hover { 
                    background: white; 
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <div class='error-code'>404</div>
                <div class='error-message'>Página no encontrada</div>
                <p>La página que buscas no existe o ha sido movida.</p>
                <a href='" . BASE_URL . "' class='btn'>Volver al Inicio</a>
            </div>
        </body>
        </html>";
    }
?>
