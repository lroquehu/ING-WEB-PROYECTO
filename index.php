 
<?php
require_once 'rutas.php';
require_once 'aplicacion/Configuracion/BaseDatos.php';

$controller = $_GET['c'] ?? 'Inicio';
$action = $_GET['a'] ?? 'index';

$controllerClass = $controller . 'Controller';
$controllerFile = "aplicacion/Controladores/{$controllerClass}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerInstance = new $controllerClass();
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        echo "Método no encontrado";
    }
} else {
    echo "Controlador no encontrado";
}
?>