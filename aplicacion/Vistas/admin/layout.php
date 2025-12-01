<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/');
    }

    $titulo = $titulo ?? 'Panel de Administración';
    $vista_actual = $vista_actual ?? 'index';

    function esActivo($link, $vista_actual) {
        return ($link === $vista_actual) ? 'active' : '';
    }

    $tema_actual = $_COOKIE['admin_theme'] ?? 'light';
    $menu_estado = $_COOKIE['menu_state'] ?? 'expanded';
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $tema_actual; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?> - Panel de Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #0A3D62;
            --primary-dark: #062A44;
            --primary-light: #145A8D;

            --bg-body: #f8f9fa;
            --bg-sidebar: #343a40;
            --bg-navbar: var(--primary-color);
            --bg-card: #ffffff;
            --bg-overlay: rgba(0, 0, 0, 0.5);
            --text-primary: #212529;
            --text-secondary: #495057;
            --text-muted: #6c757d;
            --admin-primary: var(--primary-color);
            --admin-secondary: #b8860b;
            --status-success: #28a745;
            --status-warning: #ffc107;
            --status-danger: #dc3545;
            --border-light: #dee2e6;
            --border-dark: #ced4da;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
        }

        body.dark-mode {
            --bg-body: #0F1114;
            --bg-sidebar: #15191D;
            --bg-navbar: var(--primary-color);
            --bg-card: #1A1E23;
            --bg-overlay: rgba(0, 0, 0, 0.7);
            --text-primary: #F0F4F8;
            --text-secondary: #D1D9E6;
            --text-muted: #A0AEC0;
            --admin-primary: var(--primary-color);
            --admin-secondary: #C0A968;
            --status-success: #2EAF7D;
            --status-warning: #D5A544;
            --status-danger: #C44C4C;
            --border-light: #2A2F36;
            --border-dark: #394049;
            --shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.55);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        .admin-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .top-navbar {
            background: var(--primary-color);
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mobile-menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            font-weight: 500;
        }

        /* ===== CONTENIDO PRINCIPAL CON SIDEBAR ===== */
        .main-wrapper {
            display: flex;
            flex: 1;
            position: relative;
        }

        /* ===== SIDEBAR DESPLEGABLE ===== */
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            z-index: 900;
            transition: all 0.3s ease;
            box-shadow: 2px 0 15px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        /* Header del Sidebar */
        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
            text-align: center;
            position: relative;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 1.5rem 1rem 1rem;
        }

        .admin-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .sidebar.collapsed .admin-profile {
            gap: 0.75rem;
        }

        .admin-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid var(--admin-secondary);
            object-fit: cover;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .sidebar.collapsed .admin-avatar {
            width: 60px;
            height: 60px;
            border-width: 3px;
        }

        .admin-info {
            text-align: center;
        }

        .sidebar.collapsed .admin-info {
            display: none;
        }

        .admin-name {
            font-weight: 700;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }

        /* Navegación del Sidebar */
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .sidebar.collapsed .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar-nav li {
            position: relative;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            border-left: 4px solid transparent;
        }

        .sidebar.collapsed .sidebar-nav li a {
            padding: 1rem;
            justify-content: center;
        }

        .sidebar-nav li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border-left-color: var(--admin-secondary);
        }

        .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
            border-left-color: var(--admin-secondary);
        }

        .sidebar.collapsed .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: all 0.3s ease;
            color: #fff;
        }

        .sidebar.collapsed .nav-icon {
            font-size: 1.3rem;
        }

        .nav-text {
            font-weight: 500;
            transition: all 0.3s ease;
            color: #fff;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        /* Tooltips para menú colapsado */
        .sidebar.collapsed .sidebar-nav li:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: var(--bg-card);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
        }

        /* Footer del Sidebar */
        .sidebar-footer {
            padding: 1rem 0rem;
            border-top: 1px solid var(--border-light);
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 1rem 0rem;
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-footer a {
            justify-content: center;
            padding: 0.75rem;
        }

        .sidebar-footer a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .footer-text {
            font-weight: 500;
        }

        .sidebar.collapsed .footer-text {
            display: none;
        }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .main-content {
            flex: 1;
            transition: all 0.3s ease;
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
            background: var(--bg-body);
        }

        .sidebar.collapsed ~ .main-content {
            width: calc(100% - 80px);
        }

        /* Contenido */
        .content {
            flex: 1;
            padding: 1rem;
            background: var(--bg-body);
            width: 100%;
        }

        /* Overlay para móvil */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-overlay);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* ===== COMPONENTES ===== */
        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: var(--status-success);
            color: var(--status-success);
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border-left-color: var(--status-danger);
            color: var(--status-danger);
        }

        .alert-content {
            flex: 1;
        }

        .alert-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-overlay);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-spinner.active {
            opacity: 1;
            visibility: visible;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-light);
            border-left: 4px solid var(--admin-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* ===== ANIMACIONES ===== */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }


        @media (min-width: 769px) {
            .mobile-menu-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 0.75rem 1rem;
                height: 60px;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                height: calc(100vh - 60px);
                z-index: 1001;
                transform: translateX(-100%);
                transform: transform 0.3s ease;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                width: 100%;
            }
            
            .content {
                padding: 1rem;
            }

            .admin-avatar {
                width: 80px;
                height: 80px;
            }

            .sidebar.collapsed .admin-avatar {
                width: 50px;
                height: 50px;
            }
        
            .sidebar.active + .sidebar-overlay {
                display: block;
            }
        }

        .card {
            border: none;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background-color: var(--primary-color); 
            color: var(--border-light);
        }

        body.dark-mode .card-header {
            background-color: #15191D; 
            color: var(--text-secondary);
        }
        
        .card-body {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        .btn {
            font-weight: 600;
            transition: all 0.3s ease;
        }

        body.dark-mode .table {
            color: var(--text-primary);
            border-color: var(--border-light);
        }

        body.dark-mode .table th {
            background-color: var(--bg-sidebar);
            color: var(--text-primary);
        }

        body.dark-mode .table td {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-light);
        }

        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        .modal-header {
            border-bottom-color: var(--border-light);
        }

        .modal-footer {
            border-top-color: var(--border-light);
        }

        .nav-tabs .nav-link {
            color: var(--text-secondary);
        }

        .nav-tabs .nav-link.active {
            background-color: var(--bg-card);
            border-color: var(--border-light) var(--border-light) var(--bg-card);
            color: var(--text-primary);
        }

        body.dark-mode .pagination .page-link {
            background-color: var(--bg-sidebar);
            border-color: var(--border-light);
            color: var(--text-primary);
        }

        .pagination .page-link:hover {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        /* Mejoras específicas para el contenido del dashboard */
        .content h1, .content h2, .content h3, .content h4, .content h6 {
            color: var(--text-primary);
            font-weight: 600;
        }

        .content small, .content .text-muted {
            color: var(--text-muted) !important;
        }
    </style>
</head>
<body id="admin-body" class="<?php echo $tema_actual === 'dark' ? 'dark-mode' : ''; ?>">
    <div class="admin-container">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="navbar-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title"><?php echo htmlspecialchars($titulo); ?></h1>
            </div>
            
            <div class="user-menu">
                <button id="theme-toggle" class="theme-toggle" title="Cambiar Tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </div>

        <div class="main-wrapper">
            <!-- Sidebar Desplegable -->
            <div class="sidebar <?php echo $menu_estado === 'collapsed' ? 'collapsed' : ''; ?>" id="sidebar">
                <div class="sidebar-header">
                    <div class="admin-profile">
                        <img src="<?php echo BASE_URL; ?>assets/images/admin-avatar.jpg" 
                            alt="Admin" 
                            class="admin-avatar"
                            onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=0A3D62&color=fff&size=200'">
                        <div class="admin-info">
                            <div class="admin-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador'); ?></div>
                        </div>
                    </div>
                </div>
                
                <nav class="sidebar-nav">
                    <ul>
                        <li data-tooltip="Dashboard">
                            <a href="<?php echo BASE_URL; ?>admin" class="<?php echo esActivo('index', $vista_actual); ?>">
                                <i class="fas fa-tachometer-alt nav-icon"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li data-tooltip="Gestión de Usuarios">
                            <a href="<?php echo BASE_URL; ?>admin/usuarios" class="<?php echo esActivo('usuarios', $vista_actual); ?>">
                                <i class="fas fa-users nav-icon"></i>
                                <span class="nav-text">Gestión de Usuarios</span>
                            </a>
                        </li>
                        <li data-tooltip="Publicaciones">
                            <a href="<?php echo BASE_URL; ?>admin/publicaciones" class="<?php echo esActivo('publicaciones', $vista_actual); ?>">
                                <i class="fas fa-box-open nav-icon"></i>
                                <span class="nav-text">Publicaciones</span>
                            </a>
                        </li>
                        <li data-tooltip="Categorías">
                            <a href="<?php echo BASE_URL; ?>admin/categorias" class="<?php echo esActivo('categorias', $vista_actual); ?>">
                                <i class="fas fa-tags nav-icon"></i>
                                <span class="nav-text">Categorías</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <div class="sidebar-footer">
                    <a href="<?php echo BASE_URL; ?>" target="_blank">
                        <i class="fas fa-external-link-alt nav-icon"></i>
                        <span class="footer-text">Ver Sitio Web</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>logout">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <span class="footer-text">Cerrar Sesión</span>
                    </a>
                </div>
            </div>

            <!-- Overlay para móvil -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            <!-- Contenido Principal -->
            <div class="main-content">
                <!-- Contenido -->
                <div class="content">
                    <!-- System Messages -->
                    <?php if (!empty($_SESSION['admin_success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <div class="alert-content">
                                <strong>Éxito!</strong>
                                <p><?php echo htmlspecialchars($_SESSION['admin_success']); ?></p>
                            </div>
                            <button class="alert-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <?php unset($_SESSION['admin_success']); ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($_SESSION['admin_error'])): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="alert-content">
                                <strong>Error!</strong>
                                <p><?php echo htmlspecialchars($_SESSION['admin_error']); ?></p>
                            </div>
                            <button class="alert-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <?php unset($_SESSION['admin_error']); ?>
                    <?php endif; ?>

                    <!-- Main Content -->
                    <main class="main-content">
                        <?php echo $contenido ?? '<div class="empty-state">No hay contenido para mostrar</div>'; ?>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>
    <script>
        // Nuevo: Función de utilidad para manejar cookies.
        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            // Aseguramos que la cookie esté disponible en todo el path de admin.
            document.cookie = name + "=" + (value || "")  + expires + "; path=/"; 
        }

        document.addEventListener('DOMContentLoaded', function() {
            const body = document.getElementById('admin-body');
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const sidebar = document.getElementById('sidebar');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Gestión del Tema (Ajustada para que el JS también actualice la cookie que lee PHP)
            function initTheme() {
                // La clase del body ya está correctamente establecida por PHP. 
                // Solo necesitamos asegurar que el icono es correcto.
                if (body.classList.contains('dark-mode')) {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                } else {
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                }
            }

            function toggleTheme() {
                body.classList.toggle('dark-mode');
                
                const isDarkMode = body.classList.contains('dark-mode');
                const themeState = isDarkMode ? 'dark' : 'light';
                
                if (isDarkMode) {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                } else {
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                }
                
                // CORRECCIÓN Adicional: Usamos la función de cookie para que PHP lo detecte.
                setCookie('admin_theme', themeState, 365);
                // Se mantiene localStorage por compatibilidad, aunque ya no es la fuente de PHP.
                localStorage.setItem('admin_theme', themeState);
                
                window.dispatchEvent(new Event('themechange'));
            }

            // Gestión del Menú - MODIFICADA
            function toggleMobileMenu() {
                if (window.innerWidth <= 768) {
                    // En móvil: toggle del menú lateral
                    sidebar.classList.toggle('active');
                } else {
                    // En desktop: toggle de colapsar/expandir
                    sidebar.classList.toggle('collapsed');
                    
                    // *** ESTA ES LA CLAVE ***
                    // Guardar estado en COOKIE (que es la fuente de verdad de PHP)
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    const newState = isCollapsed ? 'collapsed' : 'expanded';
                    setCookie('menu_state', newState, 365); 
                    // Se elimina localStorage.setItem('menu_state', ...) 
                }
            }

            // *** initMenuState() FUE ELIMINADA *** // El estado inicial ahora es gestionado 100% por el PHP/Cookie para evitar el flicker.

            // Cerrar menú móvil al hacer clic en el overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });

            // Cerrar menú móvil al hacer clic fuera (mejorado)
            document.addEventListener('click', function(event) {
                const isMobile = window.innerWidth <= 768;
                const clickedOnSidebar = sidebar.contains(event.target);
                const clickedOnMenuToggle = mobileMenuToggle.contains(event.target);
                
                if (isMobile && 
                    !clickedOnSidebar && 
                    !clickedOnMenuToggle &&
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });

            // Event Listeners
            themeToggle.addEventListener('click', toggleTheme);
            mobileMenuToggle.addEventListener('click', toggleMobileMenu);

            // Responsive helper
            window.addEventListener('resize', function() {
                const isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // En móvil: asegurarse que no esté colapsado
                    sidebar.classList.remove('collapsed');
                } else {
                    // En desktop: cerrar menú móvil si está abierto
                    sidebar.classList.remove('active');
                }
            });

            // Inicialización
            initTheme();
            // initMenuState() ELIMINADA para corregir el flicker.
            
            // Utilidades
            window.showLoading = function() {
                document.getElementById('loadingSpinner').classList.add('active');
            };

            window.hideLoading = function() {
                document.getElementById('loadingSpinner').classList.remove('active');
            };

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        });
    </script>
</body>
</html>