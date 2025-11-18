<?php
    // Sistema de enrutamiento para UniEmprende
    $routes = [
        // Páginas principales
        '/' => ['controller' => 'Inicio', 'action' => 'index'],
        '/inicio' => ['controller' => 'Inicio', 'action' => 'index'],
        '/acerca-de' => ['controller' => 'Inicio', 'action' => 'acercaDe'],
        '/contacto' => ['controller' => 'Inicio', 'action' => 'contacto'],
        '/buscar' => ['controller' => 'Inicio', 'action' => 'buscar'],
        '/categorias' => ['controller' => 'Inicio', 'action' => 'categorias'],
        
        // Autenticación
        '/login' => ['controller' => 'Autenticacion', 'action' => 'login'],
        '/registro' => ['controller' => 'Autenticacion', 'action' => 'registro'],
        '/logout' => ['controller' => 'Autenticacion', 'action' => 'logout'],
        
        // Perfil de usuario
        '/perfil' => ['controller' => 'Perfil', 'action' => 'index'],
        '/perfil/editar' => ['controller' => 'Perfil', 'action' => 'editar'],
        '/perfil/cambiar-password' => ['controller' => 'Perfil', 'action' => 'cambiarPassword'],
        '/perfil/publicaciones' => ['controller' => 'Perfil', 'action' => 'publicaciones'],
        '/perfil/favoritos' => ['controller' => 'Perfil', 'action' => 'favoritos'],
        '/perfil/eliminar-publicacion' => ['controller' => 'Perfil', 'action' => 'eliminarPublicacion'],
        
        // Publicaciones
        '/publicaciones' => ['controller' => 'Publicacion', 'action' => 'index'],
        '/publicaciones/cambiarestado' => ['controller' => 'Publicacion', 'action' => 'cambiarEstado'],
        '/publicaciones/buscar' => ['controller' => 'Publicacion', 'action' => 'buscar'],
        '/publicaciones/categorias' => ['controller' => 'Publicacion', 'action' => 'categorias'],
        '/publicaciones/crear' => ['controller' => 'Publicacion', 'action' => 'crear'],
        '/publicaciones/eliminar' => ['controller' => 'Publicacion', 'action' => 'eliminar'],
        '/publicaciones/editar/{id}' => ['controller' => 'Publicacion', 'action' => 'editar'],
        '/publicaciones/ver/{id}' => ['controller' => 'Publicacion', 'action' => 'ver'],

        // Chat
        '/chat' => ['controller' => 'Chat', 'action' => 'index'],
        '/chat/ver/{id}' => ['controller' => 'Chat', 'action' => 'ver'],
        '/chat/iniciar' => ['controller' => 'Chat', 'action' => 'iniciar'],
        '/chat/enviar' => ['controller' => 'Chat', 'action' => 'enviar'],
        '/chat/obtenerNuevos' => ['controller' => 'Chat', 'action' => 'obtenerNuevos'],

        '/error/404' => ['controller' => 'Inicio', 'action' => 'error404'],
        '/error/500' => ['controller' => 'Inicio', 'action' => 'error500'],
    ];

    /**
     * Obtener la ruta correspondiente a una URL
     */
    function getRoute($url) {
        global $routes;
        
        // Limpiar la URL
        $url = rtrim($url, '/');
        if (empty($url)) {
            $url = '/';
        }
        
        // Buscar ruta exacta
        if (isset($routes[$url])) {
            return $routes[$url];
        }
        
        // Manejar rutas con parámetros (ej: /publicaciones/ver/123)
        foreach ($routes as $route => $config) {
            // Buscar nombres de parámetros {id}, {slug}, etc.
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route, $paramNames);

            // Convertir ruta a patrón regex
            $pattern = preg_replace('/\//', '\\/', $route);
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^\/]+)', $pattern);
            $pattern = '/^' . $pattern . '$/';
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remover coincidencia completa
                $params = [];
                foreach ($matches as $index => $value) {
                    $params[$paramNames[1][$index]] = $value; // usar el nombre real
                }
                $config['params'] = $params;
                return $config;
            }
        }
        
        return null;
    }

    /**
     * Generar URL para una ruta específica
     */
    function generateUrl($controller, $action, $params = []) {
        global $routes;
        
        // Buscar ruta que coincida con controlador y acción
        foreach ($routes as $route => $config) {
            if ($config['controller'] === $controller && $config['action'] === $action) {
                // Reemplazar parámetros en la ruta
                $url = $route;
                foreach ($params as $key => $value) {
                    $url = str_replace('{' . $key . '}', $value, $url);
                }
                return BASE_URL . ltrim($url, '/');
            }
        }
        
        // Fallback a URL tradicional
        $url = '?c=' . $controller . '&a=' . $action;
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . $value;
        }
        return BASE_URL . $url;
    }
?>