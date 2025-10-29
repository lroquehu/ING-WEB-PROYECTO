<?php
    // encabezado.php - PARTIAL CORREGIDO
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://localhost/ING-WEB-PROYECTO/');
    }

    $usuario_autenticado = isset($_SESSION['usuario_id']);
?>

<!-- Header con Navegación -->
<header>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <span>UniEmprende</span>
            </div>
        </a>
        
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <form method="GET" action="<?php echo BASE_URL; ?>buscar" style="display: contents;">
                <input type="text" class="search-input" name="q" placeholder="Buscar productos/servicios" 
                       id="searchInput" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            </form>
        </div>
        
        <ul class="nav-links">
            <li><a href="<?php echo BASE_URL; ?>" class="<?php echo ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/ING-WEB-PROYECTO/') ? 'active' : ''; ?>">Inicio</a></li>
            <li><a href="<?php echo BASE_URL; ?>categorias" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'categorias') !== false ? 'active' : ''; ?>">Categorías</a></li>
            <li><a href="<?php echo BASE_URL; ?>publicaciones" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'publicaciones') !== false ? 'active' : ''; ?>">Productos</a></li>
            <li><a href="<?php echo BASE_URL; ?>contacto">Contacto</a></li>
            <li><a href="<?php echo BASE_URL; ?>acerca-de">Sobre Nosotros</a></li>
        </ul>
        
        <div class="auth-buttons">
            <?php if ($usuario_autenticado): ?>
                <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-filled">Mi Perfil</a>
                <a href="<?php echo BASE_URL; ?>logout" class="btn btn-outline">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login" class="btn btn-filled">Iniciar Sesión</a>
                <a href="<?php echo BASE_URL; ?>registro" class="btn btn-outline">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>
</header>