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
        '/recuperar-password' => ['controller' => 'Autenticacion', 'action' => 'solicitarRecuperacion'],
        '/resetear-password/{token}' => ['controller' => 'Autenticacion', 'action' => 'resetearPassword'],
        '/verificar-correo/{token}' => ['controller' => 'Autenticacion', 'action' => 'verificarCorreo'],
        
        // Perfil de usuario
        '/perfil' => ['controller' => 'Perfil', 'action' => 'index'],
        '/perfil/editar' => ['controller' => 'Perfil', 'action' => 'editar'],
        '/perfil/cambiar-password' => ['controller' => 'Perfil', 'action' => 'cambiarPassword'],
        '/perfil/publicaciones' => ['controller' => 'Perfil', 'action' => 'publicaciones'],
        '/perfil/favoritos' => ['controller' => 'Perfil', 'action' => 'favoritos'],
        '/perfil/eliminar-publicacion' => ['controller' => 'Perfil', 'action' => 'eliminarPublicacion'],
        '/perfil/ver/{id}' => ['controller' => 'Perfil', 'action' => 'ver'], // NUEVA RUTA para perfiles públicos

        // ---------------------------------------------------------
        // Pasarela de Pago (TEST - Mercado Pago)
        // ---------------------------------------------------------
        '/test-pasarela' => ['controller' => 'Pasarela', 'action' => 'index'],
        '/test-pasarela/procesar' => ['controller' => 'Pasarela', 'action' => 'procesar'],
        // ---------------------------------------------------------

        // ... (otras rutas de perfil) ...
        '/perfil/ventas' => ['controller' => 'Perfil', 'action' => 'ventas'], // Nueva vista de mis ventas
        '/perfil/mis-compras' => ['controller' => 'Perfil', 'action' => 'misCompras'], // Nueva vista de mis compras

        // ... (otras rutas de pasarela) ...
        '/pago/recibo/{id}' => ['controller' => 'Pasarela', 'action' => 'recibo'], // Ver el recibo

        // Rutas para FAVORITOS
        '/favoritos/toggle' => ['controller' => 'Perfil', 'action' => 'toggleFavorito'], // Para AJAX (corazón)
        '/perfil/eliminar-favorito' => ['controller' => 'Perfil', 'action' => 'eliminarFavorito'], // Para el listado
        
        // Publicaciones
        '/publicaciones' => ['controller' => 'Publicacion', 'action' => 'index'],
        '/publicaciones/cambiarestado' => ['controller' => 'Publicacion', 'action' => 'cambiarEstado'],
        '/publicaciones/buscar' => ['controller' => 'Publicacion', 'action' => 'buscar'],
        '/publicaciones/categorias' => ['controller' => 'Publicacion', 'action' => 'categorias'],
        '/publicaciones/crear' => ['controller' => 'Publicacion', 'action' => 'crear'],
        '/publicaciones/eliminar' => ['controller' => 'Publicacion', 'action' => 'eliminar'],
        '/publicaciones/editar/{id}' => ['controller' => 'Publicacion', 'action' => 'editar'],
        '/publicaciones/ver/{id}' => ['controller' => 'Publicacion', 'action' => 'ver'],
        '/publicaciones/toggle-favorito' => ['controller' => 'Publicacion', 'action' => 'toggleFavorito'], // Para AJAX
        '/publicaciones/valorar' => ['controller' => 'Publicacion', 'action' => 'valorar'],
        '/publicaciones/editar-valoracion' => ['controller' => 'Publicacion', 'action' => 'editarValoracion'],
        '/publicaciones/eliminar-valoracion' => ['controller' => 'Publicacion', 'action' => 'eliminarValoracion'],
        '/publicaciones/registrarContacto' => ['controller' => 'Publicacion', 'action' => 'registrarContacto'],

        // Notificaciones (NUEVO)
        '/notificaciones' => ['controller' => 'Notificacion', 'action' => 'listar'],
        '/notificaciones/verificarestado' => ['controller' => 'Notificacion', 'action' => 'verificarEstado'],
        '/notificaciones/leer/{id_notificacion}' => ['controller' => 'Notificacion', 'action' => 'leer'],
        '/notificaciones/obtenerrecientes' => ['controller' => 'Notificacion', 'action' => 'obtenerRecientes'],

        // Chat
        '/chat' => ['controller' => 'Chat', 'action' => 'index'],
        '/chat/ver/{id}' => ['controller' => 'Chat', 'action' => 'ver'],
        '/chat/iniciar' => ['controller' => 'Chat', 'action' => 'iniciar'],
        '/chat/enviar' => ['controller' => 'Chat', 'action' => 'enviar'],
        '/chat/obtenerNuevos' => ['controller' => 'Chat', 'action' => 'obtenerNuevos'],
        '/chat/eliminarMensaje' => ['controller' => 'Chat', 'action' => 'eliminarMensaje'],

        '/error/404' => ['controller' => 'Inicio', 'action' => 'error404'],
        '/error/500' => ['controller' => 'Inicio', 'action' => 'error500'],

        //------------------------
        // --- API ROUTES ---
        //------------------------
        '/api/auth/login'    => ['controller' => 'api/Auth', 'action' => 'login'],
        '/api/auth/registro' => ['controller' => 'api/Auth', 'action' => 'registro'],
        // API - Catálogo Público
        '/api/publicaciones'         => ['controller' => 'api/Publicacion', 'action' => 'index'],
        '/api/publicaciones/detalle' => ['controller' => 'api/Publicacion', 'action' => 'detalle'],
        '/api/categorias'            => ['controller' => 'api/Publicacion', 'action' => 'categorias'],
        // API - Perfil
        '/api/perfil'             => ['controller' => 'api/Perfil', 'action' => 'index'],
        '/api/perfil/editar'      => ['controller' => 'api/Perfil', 'action' => 'editar'],
        '/api/perfil/publicaciones' => ['controller' => 'api/Perfil', 'action' => 'mis_publicaciones'],
        // API - Gestión de Publicaciones (CRUD)
        '/api/publicaciones/crear'    => ['controller' => 'api/Publicacion', 'action' => 'crear'],
        '/api/publicaciones/editar'   => ['controller' => 'api/Publicacion', 'action' => 'editar'],
        '/api/publicaciones/eliminar' => ['controller' => 'api/Publicacion', 'action' => 'eliminar'],
        // API - Favoritos
        '/api/favoritos'        => ['controller' => 'api/Favoritos', 'action' => 'index'],
        '/api/favoritos/toggle' => ['controller' => 'api/Favoritos', 'action' => 'toggle'],
        // API - Chat
        '/api/chat'          => ['controller' => 'api/Chat', 'action' => 'index'],    // Listar mis chats
        '/api/chat/mensajes' => ['controller' => 'api/Chat', 'action' => 'mensajes'], // Ver mensajes de un chat
        '/api/chat/iniciar'  => ['controller' => 'api/Chat', 'action' => 'iniciar'],  // Crear chat con vendedor
        '/api/chat/enviar'   => ['controller' => 'api/Chat', 'action' => 'enviar'],   // Enviar texto
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
