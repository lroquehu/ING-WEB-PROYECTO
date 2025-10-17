<?php
    require_once 'rutas.php';
    require_once 'aplicacion/Configuracion/conexion.php';

    // Sanitizar y obtener parámetros
    $controller= isset($_GET['c']) ? htmlspecialchars($_GET['c']) : 'Inicio';
    $action = isset($_GET['a']) ? htmlspecialchars($_GET['a']) : 'index';

    // Validar nombres seguros (solo letras)
    if (!preg_match('/^[a-zA-Z]+$/', $controller) || !preg_match('/^[a-zA-Z]+$/', $action)) {
        die("Parámetros inválidos");
    }

    $controllerClass = $controller . 'Controller';
    $controllerFile = "aplicacion/Controladores/{$controllerClass}.php";

    if (file_exists($controllerFile)) {
        require_once $controllerFile;

        if(class_exists($controllerClass)){
            $controllerInstance = new $controllerClass();

            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
            } else {
                http_response_code(404);
                echo "Método no encontrado";
            }
        } else {
            http_response_code(404);
            echo "Clase controladora no encontrada";
        }
    } else {
        http_response_code(404);
        echo "Controlador no encontrado";
    }
?>  
