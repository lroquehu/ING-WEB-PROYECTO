<?php
// Sistema de enrutamiento básico
$routes = [
    '/' => ['controller' => 'Inicio', 'action' => 'index'],
    '/login' => ['controller' => 'Autenticacion', 'action' => 'login'],
    '/registro' => ['controller' => 'Autenticacion', 'action' => 'registro'],
    '/perfil' => ['controller' => 'Perfil', 'action' => 'index'],
    '/productos' => ['controller' => 'Producto', 'action' => 'index'],
];

function getRoute($url) {
    global $routes;
    return $routes[$url] ?? null;
}
?>