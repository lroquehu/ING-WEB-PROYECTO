<?php
    // index.php - VERSIÓN CORREGIDA PARA SOPORTE API REST
    
    // Configuración básica
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    // Definir constante BASE_URL si no existe
    if (!defined('BASE_URL')) {
        // URL base del servidor de producción (VPS)
        define('PROD_IMAGE_URL', 'https://sv-fhj9pa34z7eatkdstwlm.cloud.elastika.pe/');
        define('LOCAL_IMAGE_URL', 'http://localhost:8000/');
        
        // URL base de tu entorno (Ajusta según corresponda: Local o VPS)
        //define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/'); 
        define('BASE_URL', 'https://sv-fhj9pa34z7eatkdstwlm.cloud.elastika.pe/ING-WEB-PROYECTO/');
    }

    // Incluir archivos necesarios
    require_once 'rutas.php';
    require_once 'aplicacion/Configuracion/conexion.php';
    require_once 'aplicacion/Helpers/imagenes.php';

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

    // --- CORRECCIÓN API: Función mejorada para cargar controladores y subcarpetas ---
    function cargarControlador($controllerName) {
        $className = $controllerName;
        
        // Detectar si el controlador está en una subcarpeta (ej: "api/Auth")
        if (strpos($controllerName, '/') !== false) {
            $parts = explode('/', $controllerName);
            $folder = $parts[0]; // "api"
            $class = $parts[1];  // "Auth"
            
            // Ruta para subcarpetas: aplicacion/Controladores/api/AuthController.php
            $controllerFile = "aplicacion/Controladores/{$folder}/{$class}Controller.php";
            $className = $class . "Controller"; // El nombre de la clase
        } else {
            // Comportamiento normal para la web (ej: "Inicio")
            if (!str_ends_with($controllerName, 'Controller')) {
                $controllerName .= 'Controller';
            }
            $controllerFile = "aplicacion/Controladores/{$controllerName}.php";
            $className = $controllerName;
        }
        
        if (!file_exists($controllerFile)) {
            // DEBUG: Descomenta esto si sigues teniendo problemas para ver qué archivo busca
            // throw new Exception("Archivo no encontrado: $controllerFile");
            throw new Exception("Controlador no encontrado.");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($className)) {
            throw new Exception("Clase no encontrada: $className");
        }
        
        return new $className();
    }

    // Procesar la solicitud
    try {
        // Obtener la URL solicitada
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remover el base path del proyecto si existe
        $basePath = '/ING-WEB-PROYECTO';
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // Limpiar la URL
        $requestUri = rtrim($requestUri, '/');
        if (empty($requestUri)) {
            $requestUri = '/';
        }

        // Rutas estáticas especiales
        if ($requestUri === '/terminos') {
            require_once 'aplicacion/Vistas/autenticacion/terminos.php';
            exit();
        }
        if ($requestUri === '/privacidad') {
            require_once 'aplicacion/Vistas/autenticacion/privacidad.php';
            exit();
        }

        // USAR EL SISTEMA DE RUTAS
        $route = getRoute($requestUri);
        
        if ($route) {
            // Ruta encontrada
            $controllerName = $route['controller'];
            $action = $route['action'];
            
            if (isset($route['params'])) {
                foreach ($route['params'] as $key => $value) {
                    $_GET[$key] = $value;
                }
            }
        } else {
            // Fallback tradicional
            $controllerName = $_GET['c'] ?? 'Inicio';
            $action = $_GET['a'] ?? 'index';
        }

        // Sanitizar
        $controllerName = htmlspecialchars(trim($controllerName));
        $action = htmlspecialchars(trim($action));
        
        // --- CORRECCIÓN API: Permitir barra '/' en la validación del nombre ---
        // Antes solo permitía a-z y 0-9. Ahora permite '/' para rutas como "api/Auth"
        if (!preg_match('/^[a-zA-Z0-9\/]+$/', $controllerName) || 
            !preg_match('/^[a-zA-Z0-9_]+$/', $action)) {
            throw new Exception("Parámetros de URL inválidos: Controlador ($controllerName) o Acción ($action)");
        }
        
        // Cargar y ejecutar el controlador
        $controller = cargarControlador($controllerName);
        
        if (!method_exists($controller, $action)) {
            throw new Exception("Método '$action' no encontrado en el controlador.");
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
        
        // Si es una petición API (espera JSON), devolver error JSON
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header("Content-Type: application/json");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage() // Muestra el error real para depurar
            ]);
            exit;
        }

        // Mostrar página de error HTML (tu diseño original)
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Página no encontrada</title>
            <style>
                body { font-family: sans-serif; background: #910202; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;}
                .box { text-align: center; background: rgba(255,255,255,0.1); padding: 2rem; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='box'>
                <h1>Error 404 / 500</h1>
                <p>" . $e->getMessage() . "</p>
                <a href='" . BASE_URL . "' style='color: #ffd700;'>Volver al Inicio</a>
            </div>
        </body>
        </html>";
    }
?>
