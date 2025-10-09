<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plataforma de emprendimiento universitario">
    <meta name="keywords" content="unjbg, universidad, emprendimiento universitario, emprendimiento, estudiantes, productos">
    <title>UniEmprende</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
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
                <input type="text" class="search-input" placeholder="Buscar productos..." id="searchInput">
            </div>
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                <li><a href="<?php echo BASE_URL; ?>?c=Producto&a=categorias">Categorías</a></li>
                <li><a href="<?php echo BASE_URL; ?>?c=Producto&a=index">Productos</a></li>
                <li><a href="#contact">Contacto</a></li>
                <li><a href="#about">Sobre Nosotros</a></li>
            </ul>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="<?php echo BASE_URL; ?>?c=Perfil&a=index" class="btn btn-filled">Mi Perfil</a>
                    <a href="<?php echo BASE_URL; ?>?c=Autenticacion&a=logout" class="btn btn-outline">Cerrar Sesión</a>
                <?php else: ?>
                    <button class="btn btn-filled" onclick="openModal('login-modal')">Iniciar Sesión</button>
                    <button class="btn btn-outline" onclick="openModal('register-modal')">Registrarse</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>