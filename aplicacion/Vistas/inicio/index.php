//index.php

<?php 
    // aplicacion/Vistas/inicio/index.php

    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Datos que vienen del controlador
    $publicaciones_destacadas = $productosDestacados ?? [];
    $categorias = $categorias ?? [];
    $estadisticas = $estadisticas ?? [
        'total_emprendedores' => 0,
        'total_productos' => 0,
        'total_servicios' => 0,
        'total_categorias' => 0
    ];

    $usuario_autenticado = isset($_SESSION['usuario_id']);
    $usuario_info = $usuario_info ?? null;

    // Mensajes de éxito
    $mensaje_exito = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniEmprende</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <meta name="description" content="Plataforma de compra y venta para la comunidad universitaria. Conecta con estudiantes emprendedores de tu universidad.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #510200;
            --primary-light: #b30303;
            --secondary-color: #2c3e50;
            --accent-color: #ffd700;
            --text-dark: #333;
            --text-light: #666;
            --text-lighter: #888;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-color: #e1e1e1;
            --shadow: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.2);
            --transition: all 0.3s ease;
            
            /* Variables específicas para la red */
            --network-text: rgba(255,255,255,0.35);
        }
            
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }

        /* --- HERO SECTION --- */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--bg-white);
            /* Altura mínima para asegurar que haya espacio para la red y el texto */
            min-height: 100vh; /* Ocupar al menos toda la pantalla visible */
            padding: 8rem 0 4rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center; /* Centrar verticalmente el contenido */
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="rgba(255,255,255,0.05)"><circle cx="50" cy="50" r="2"/></svg>') repeat;
            pointer-events: none;
        }

        /* --- RED GRÁFICA (Fondo Interactivo) --- */
        #network-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1; /* Nivel 1: Detrás del texto pero interactuable */
            overflow: hidden;
            cursor: grab;
        }

        #network-background:active {
            cursor: grabbing;
        }

        svg {
            width: 100%;
            height: 100%;
            display: block;
            margin-top: 3%;
        }

        .link {
            stroke: var(--network-text);
            stroke-width: 1.4px;
            stroke-linecap: round;
        }

        .node circle {
            fill: #ffffff;
            stroke: var(--accent-color);
            stroke-width: 4px;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.8));
            transition: transform 160ms ease, stroke-width 160ms ease, fill 160ms ease;
            cursor: pointer;
        }

        .node:hover circle {
            fill: var(--accent-color);
            stroke: #ffffff;
            stroke-width: 2px;
            transform: scale(1.35);
        }

        #tooltip {
            position: absolute;
            pointer-events: none;
            padding: 6px 10px;
            background: rgba(255,255,255,0.95);
            color: #222;
            border-radius: 6px;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            opacity: 0;
            transition: opacity 140ms ease;
            white-space: nowrap;
            z-index: 100; /* Por encima de todo */
        }
        .show-tooltip { opacity: 1 !important; }

        /* --- CONTENIDO (Texto) --- */
        .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 0 1rem;
            position: relative;
            z-index: 2; /* Nivel 2: Por encima de la red */
            width: 100%;
        }

        /* REGLA CLAVE: Contenedor Hero más angosto y transparente a clics */
        .hero .container {
            max-width: 1200px; 
            pointer-events: none; /* Permite que los clics en áreas vacías pasen a la red */
        }

        .hero-content {
            display: flex;
            justify-content: flex-start; /* Alinear contenido a la izquierda */
            align-items: center;
            position: relative;
        }
        
        .hero-text {
            max-width: 600px; 
            pointer-events: none; 
        }

        .hero-text h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .hero-text p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            line-height: 1.6;
            text-shadow: 0 1px 5px rgba(0,0,0,0.2);
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.2);
            pointer-events: auto; /* Permitir selección en stats */
        }
        
        .hero-stat {
            text-align: center;
        }
        
        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }
        
        .hero-stat-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Header & Nav */
        .main-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            padding: 1rem 0;
            box-shadow: var(--shadow-lg);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }
        
        .header-scrolled {
            padding: 0.8rem 0;
            background: rgba(81, 2, 0, 0.95);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            pointer-events: auto; /* Asegurar que el header sea interactivo */
        }
        
        .logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--bg-white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--accent-color), #ffed4a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-buttons {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .nav-btn {
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            background: transparent;
            color: var(--bg-white);
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .nav-btn:hover::before { left: 100%; }
        .nav-btn i { position: relative; z-index: 2; font-size: 1.1rem; transition: var(--transition); }
        .nav-btn .btn-text { position: relative; z-index: 2; max-width: 0; opacity: 0; overflow: hidden; white-space: nowrap; transition: all 0.3s ease; margin-left: 0; font-weight: 500; }
        .nav-btn:hover .btn-text { max-width: 200px; opacity: 1; margin-left: 0.5rem; }
        .nav-btn:hover i { transform: scale(1.1); }

        .nav-btn-primary { background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.3); }
        .nav-btn-primary:hover { background: rgba(255, 255, 255, 0.25); border-color: rgba(255, 255, 255, 0.5); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 255, 255, 0.2); }
        .nav-btn-outline { background: transparent; border: 2px solid rgba(255, 255, 255, 0.4); }
        .nav-btn-outline:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.8); transform: translateY(-2px); }

        .search-container { display: flex; align-items: center; background: rgba(255, 255, 255, 0.1); border-radius: 25px; padding: 0.5rem 1rem; margin-right: 1rem; transition: var(--transition); }
        .search-container:focus-within { background: rgba(255, 255, 255, 0.2); box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3); }
        .search-input { background: transparent; border: none; color: var(--bg-white); padding: 0.5rem; width: 200px; outline: none; font-size: 0.9rem; }
        .search-input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .search-btn { background: transparent; border: none; color: var(--bg-white); cursor: pointer; padding: 0.5rem; transition: var(--transition); }
        .search-btn:hover { color: var(--accent-color); transform: scale(1.1); }

        /* Botones Generales */
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer; font-size: 0.95rem; }
        .btn-primary { background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.4); color: var(--bg-white); }
        .btn-primary:hover { background: var(--primary-color); box-shadow: 0 6px 20px rgba(145, 2, 2, 0.3); }
        .btn-secondary { background: transparent; color: var(--bg-white); border: 2px solid rgba(255, 255, 255, 0.4); }
        .btn-secondary:hover { background: var(--primary-color); }
        
        /* Otras Secciones */
        section:not(.hero) { padding: 5rem 0; position: relative; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); }
        .section-header { text-align: center; margin-bottom: 3rem; }
        .section-title { font-size: 2.75rem; margin-bottom: 1rem; color: var(--secondary-color); font-weight: 700; }
        .section-subtitle { font-size: 1.2rem; color: var(--text-light); max-width: 600px; margin: 0 auto; }
        
        /* Categorias */
        .category-filters { display: flex; justify-content: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 3rem; }
        .category-filter { padding: 0.75rem 1.5rem; background: var(--bg-white); border: 2px solid var(--border-color); border-radius: 50px; cursor: pointer; transition: var(--transition); font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
        .category-filter.active { background: var(--primary-color); color: var(--bg-white); border-color: var(--primary-color); }
        .category-icon { font-size: 1.1rem; }
        
        /* Productos */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 4fr)); gap: 2rem; }
        .product-card { background: var(--bg-white); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow); transition: var(--transition); position: relative; }
        .product-image { height: 220px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 1.75rem; }
        .product-title { font-size: 1.3rem; margin-bottom: 0.75rem; color: var(--secondary-color); }
        .product-price { font-weight: 700; color: var(--primary-color); font-size: 1.25rem; }
        .btn-outline { background: transparent; border: 2px solid var(--border-color); color: var(--text-dark); }
        .btn-outline:hover { border-color: var(--primary-color); color: var(--primary-color); }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; }
        .btn-icon { background: transparent; border: none; color: var(--text-lighter); font-size: 1.2rem; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: var(--transition); }
        .btn-icon:hover { color: var(--primary-color); background: rgba(145, 2, 2, 0.1); }
        .product-badges { position: absolute; top: 1rem; left: 1rem; right: 1rem; display: flex; justify-content: space-between; }
        .product-type { background: var(--primary-color); color: var(--bg-white); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .product-favorite { background: rgba(255,255,255,0.9); color: var(--text-lighter); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); border: none; }
        .product-favorite:hover, .product-favorite.favorited { color: var(--primary-color); background: var(--bg-white); }
        .product-description { color: var(--text-light); margin-bottom: 1rem; font-size: 0.95rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .product-category { background: var(--bg-light); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; color: var(--text-light); }
        .product-vendor { color: var(--text-light); font-size: 0.9rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .product-actions { display: flex; justify-content: space-between; align-items: center; }
        .no-image { text-align: center; color: var(--text-lighter); padding: 2rem; }
        .no-image i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .product-card:hover { box-shadow: var(--shadow-hover); }
        .product-card:hover .product-image img { transform: scale(1.05); }

        /* Empty State */
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); }
        .empty-state i { font-size: 5rem; margin-bottom: 1.5rem; color: var(--border-color); opacity: 0.7; }
        .empty-state h3 { font-size: 1.5rem; margin-bottom: 1rem; color: var(--text-dark); }
        .empty-state p { margin-bottom: 2rem; font-size: 1.1rem; }

        /* About */
        .about-container { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
        .about-content h2 { font-size: 2.75rem; margin-bottom: 1.5rem; color: var(--secondary-color); }
        .about-content p { font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.7; }
        .about-features { margin: 2.5rem 0; }
        .feature-item { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; }
        .feature-icon { background: var(--primary-color); color: var(--bg-white); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem; }
        .feature-text h4 { font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--secondary-color); }
        .feature-text p { margin: 0; font-size: 0.95rem; }
        .about-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-top: 2rem; }
        .stat { text-align: center; padding: 1.5rem; background: var(--bg-white); border-radius: 12px; box-shadow: var(--shadow); }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.5rem; }
        .stat-text { color: var(--text-light); font-weight: 500; }
        .about-visual { text-align: center; }
        .visual-container { background: var(--bg-white); padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); }
        .visual-placeholder-large { font-size: 8rem; margin-bottom: 1rem; color: var(--primary-color); opacity: 0.7; }

        /* Alert */
        .alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; border-left: 4px solid; display: flex; align-items: center; gap: 1rem; }
        .alert-success { background: #d4edda; color: #155724; border-color: #28a745; }
        .alert i { font-size: 1.25rem; }

        /* Footer */
        .main-footer { background: var(--secondary-color); color: var(--bg-white); padding: 3rem 0 1rem; position: relative; z-index: 2; }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem; margin-bottom: 2rem; }
        .footer-links { list-style: none; }
        .footer-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); }
        .scroll-to-top { position: fixed; bottom: 2rem; right: 2rem; width: 50px; height: 50px; background: var(--primary-color); color: var(--bg-white); border: none; border-radius: 50%; cursor: pointer; transition: var(--transition); z-index: 1000; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: var(--shadow-lg); opacity: 0; visibility: hidden; }
        .scroll-to-top.visible { opacity: 1; visibility: visible; }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero { padding-top: 6rem; text-align: center; }
            .hero-content { justify-content: center; }
            .hero-text { max-width: 100%; background: rgba(81, 2, 0, 0.6); } /* Más oscuro en móvil para leer mejor */
            .about-container { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .hero-text h1 { font-size: 2.5rem; }
            .nav-buttons { flex-direction: column; gap: 0.5rem; }
            .nav-btn .btn-text { display: none; }
            .hero-buttons { justify-content: center; }
            .product-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="main-header" id="mainHeader">
        <div class="container" style="pointer-events: auto;">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>" class="logo"><i class="fas fa-graduation-cap"></i>UniEmprende</a>
                <div class="nav-buttons">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Buscar productos...">
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                    <?php if (!$usuario_autenticado): ?>
                        <a href="<?php echo BASE_URL; ?>login" class="nav-btn nav-btn-outline"><i class="fas fa-sign-in-alt"></i><span class="btn-text">Iniciar Sesión</span></a>
                        <a href="<?php echo BASE_URL; ?>registro" class="nav-btn nav-btn-primary"><i class="fas fa-user-plus"></i><span class="btn-text">Registrarse</span></a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="nav-btn nav-btn-primary"><i class="fas fa-plus-circle"></i><span class="btn-text">Publicar</span></a>
                        <a href="<?php echo BASE_URL; ?>perfil" class="nav-btn nav-btn-outline"><i class="fas fa-user"></i><span class="btn-text">Mi Perfil</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="hero">
            <div id="network-background">
                <div id="tooltip"></div>
            </div>

            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>Conectando Emprendedores Universitarios</h1>
                        <p>Descubre productos y servicios creados por estudiantes emprendedores de todas las universidades. Compra, vende y emprende en un entorno seguro y confiable.</p>
                        
                        <div class="hero-buttons">
                            <?php if (!$usuario_autenticado): ?>
                                <a href="<?php echo BASE_URL; ?>registro" class="btn btn-primary"><i class="fas fa-rocket"></i> Comenzar Ahora</a>
                                <a href="<?php echo BASE_URL; ?>login" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Publicar Producto</a>
                                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-secondary"><i class="fas fa-search"></i> Explorar Productos</a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hero-stats">
                            <div class="hero-stat">
                                <div class="hero-stat-number"><?php echo number_format($estadisticas['total_emprendedores']); ?></div>
                                <div class="hero-stat-text">Emprendedores Activos</div>
                            </div>
                            <div class="hero-stat">
                                <div class="hero-stat-number"><?php echo number_format($estadisticas['total_productos'] + $estadisticas['total_servicios']); ?></div>
                                <div class="hero-stat-text">Publicaciones</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!empty($mensaje_exito)): ?>
        <div class="container">
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <span><?php echo htmlspecialchars($mensaje_exito); ?></span></div>
        </div>
        <?php endif; ?>

        <section class="categories" id="categorias">
            <div class="container">
                <div class="section-header"><h2 class="section-title">Explora por Categorías</h2></div>
                <div class="category-filters">
                    <div class="category-filter active" data-categoria="all"><i class="fas fa-th-large category-icon"></i> Todas las Categorías</div>
                    <?php foreach ($categorias as $categoria): ?>
                        <div class="category-filter" data-categoria="<?php echo $categoria['id_categoria']; ?>">
                            <i class="fas fa-tag category-icon"></i> <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                            <?php if (isset($categoria['total_publicaciones'])): ?>
                                <span class="badge">(<?php echo $categoria['total_publicaciones']; ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="products" id="destacados">
            <div class="container">
                <?php if (empty($publicaciones_destacadas)): ?>
                    <div class="empty-state"><i class="fas fa-box-open"></i><h3>Aún no hay publicaciones destacadas</h3></div>
                <?php else: ?>
                    <div class="product-grid" id="product-grid">
                        <?php foreach ($publicaciones_destacadas as $publicacion): ?>
                            <?php
                                // Verificar si la publicación es favorita
                                $es_favorito = false;
                                if (isset($publicacion['es_favorito'])) {
                                    $es_favorito = $publicacion['es_favorito'];
                                }
                            ?>
                            <article class="product-card" data-categoria="<?php echo $publicacion['id_categoria']; ?>">
                                <div class="product-image">
                                    <?php 
                                    // Obtener URL final (local si existe, producción si no)
                                    $imgPrincipal = obtenerImagenFinal($publicacion['imagen_principal'] ?? null);
                                    ?>
                                    <?php if (!empty($imgPrincipal)): ?>
                                        <img src="<?php echo htmlspecialchars($imgPrincipal); ?>" 
                                            alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>"
                                            loading="lazy">
                                    <?php else: ?>
                                        <div class="no-image" role="img" aria-label="Producto sin imagen disponible">
                                            <i class="fas fa-image"></i>
                                            <span>Imagen no disponible</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-badges">
                                        <div class="product-type"><?php echo $publicacion['tipo']; ?></div>
                                        <button class="product-favorite <?php echo $es_favorito ? 'favorited' : ''; ?>" 
                                                title="Agregar a favoritos"
                                                aria-label="Agregar a favoritos"
                                                data-producto="<?php echo $publicacion['id_publicacion']; ?>">
                                            <i class="fa-heart <?php echo $es_favorito ? 'fas' : 'far'; ?>"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <h3 class="product-title" id="product-<?php echo $publicacion['id_publicacion']; ?>">
                                        <?php echo htmlspecialchars($publicacion['titulo']); ?>
                                    </h3>
                                    
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(mb_substr($publicacion['descripcion'], 0, 120)); ?>
                                        <?php echo mb_strlen($publicacion['descripcion']) > 120 ? '...' : ''; ?>
                                    </p>
                                    
                                    <div class="product-meta">
                                        <span class="product-category">
                                            <?php echo htmlspecialchars($publicacion['nombre_categoria']); ?>
                                        </span>
                                        <span class="product-price">
                                            S/ <?php echo number_format($publicacion['precio'], 2); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="product-vendor">
                                        <i class="fas fa-user-graduate"></i>
                                        <?php echo htmlspecialchars($publicacion['nombres'] . ' ' . $publicacion['apellidos']); ?>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" 
                                        class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                        <button class="btn-icon" 
                                                title="Contactar vendedor"
                                                aria-label="Contactar vendedor">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="text-align: center; margin-top: 3rem;">
                        <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                            <i class="fas fa-search"></i> Explorar Todas las Publicaciones
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="about" id="sobre-nosotros">
            <div class="container">
                <div class="about-container">
                    <div class="about-content">
                        <h2>Impulsando el Talento Universitario</h2>
                        <p>UniEmprende nació con la visión de crear un ecosistema vibrante donde los estudiantes universitarios puedan mostrar y comercializar sus creaciones, productos y servicios. Somos la plataforma líder para el emprendimiento universitario en Latinoamérica.</p>
                        <p>Nuestra misión es impulsar el talento joven y fomentar el espíritu emprendedor en el ámbito universitario, proporcionando las herramientas necesarias para que los estudiantes puedan convertir sus ideas en proyectos reales y sostenibles.</p>
                        
                        <div class="about-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Comercio Seguro</h4>
                                    <p>Transacciones protegidas y verificación de usuarios para tu tranquilidad.</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Comunidad Verificada</h4>
                                    <p>Todos nuestros usuarios son estudiantes universitarios verificados.</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Crecimiento Constante</h4>
                                    <p>Herramientas diseñadas para el crecimiento de tu emprendimiento.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="about-stats">
                            <div class="stat">
                                <div class="stat-number">100%</div>
                                <div class="stat-text">Estudiantes Verificados</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">24/7</div>
                                <div class="stat-text">Soporte Activo</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="about-visual">
                        <div class="visual-container">
                            <div class="visual-placeholder-large">
                                🎯
                            </div>
                            <h3>Tu Éxito es Nuestra Misión</h3>
                            <p style="color: var(--text-light); margin-top: 1rem;">
                                Conectamos talento universitario con oportunidades reales
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <button class="scroll-to-top" id="scrollToTop" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <i class="fas fa-graduation-cap"></i>
                        UniEmprende
                    </div>
                    <p class="footer-description">
                        La plataforma líder para el emprendimiento universitario. Conectamos estudiantes emprendedores y facilitamos el comercio dentro de la comunidad universitaria.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h4>Enlaces Rápidos</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="<?php echo BASE_URL; ?>publicaciones"><i class="fas fa-box"></i> Publicaciones</a></li>
                        <li><a href="<?php echo BASE_URL; ?>categorias"><i class="fas fa-tags"></i> Categorías</a></li>
                        <li><a href="<?php echo BASE_URL; ?>sobre-nosotros"><i class="fas fa-info-circle"></i> Sobre Nosotros</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Para Emprendedores</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>registro"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                        <li><a href="<?php echo BASE_URL; ?>publicaciones/crear"><i class="fas fa-plus-circle"></i> Publicar Producto</a></li>
                        <li><a href="<?php echo BASE_URL; ?>guia"><i class="fas fa-book"></i> Guía de Uso</a></li>
                        <li><a href="<?php echo BASE_URL; ?>faq"><i class="fas fa-question-circle"></i> Preguntas Frecuentes</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>terminos"><i class="fas fa-file-contract"></i> Términos de Uso</a></li>
                        <li><a href="<?php echo BASE_URL; ?>privacidad"><i class="fas fa-shield-alt"></i> Política de Privacidad</a></li>
                        <li><a href="<?php echo BASE_URL; ?>cookies"><i class="fas fa-cookie"></i> Política de Cookies</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contacto"><i class="fas fa-envelope"></i> Contacto</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 UniEmprende. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        (function(){
            const nodes = Array.from({length: 16}, (_, i) => ({ id: "" + (i+1) }));
            let links = [
                {source:"1",target:"2"},{source:"1",target:"3"}, {source:"2",target:"4"},{source:"3",target:"4"},
                {source:"1",target:"5"},{source:"2",target:"6"}, {source:"3",target:"7"},{source:"4",target:"8"},
                {source:"1",target:"9"},{source:"2",target:"10"}, {source:"3",target:"11"},{source:"4",target:"12"},
                {source:"13",target:"1"},{source:"13",target:"7"}, {source:"14",target:"7"},
                {source:"15",target:"9"}, {source:"16",target:"6"}
            ];
            for(let i=0;i<4;i++){ links.push({source:(5+i).toString(),target:(9 + ((i+1)%4)).toString()}); }
            const seen=new Set();
            links = links.filter(l=>{
                const a=l.source,b=l.target;
                const key=a<b?`${a}_${b}`:`${b}_${a}`;
                if(seen.has(key)) return false;
                seen.add(key);
                return true;
            });

            const data={nodes:nodes.map(n=>({...n})),links:links.map(l=>({...l}))};

            // SELECCIONAR EL FONDO DE RED
            const container = d3.select("#network-background");
            const width = container.node().clientWidth;
            const height = container.node().clientHeight;
            const tooltip = d3.select("#tooltip");

            const svg = container.append("svg")
                .attr("viewBox", `0 0 ${width} ${height}`)
                .attr("preserveAspectRatio", "xMidYMid slice");

            const layer = svg.append("g");

            const link = layer.append("g")
                .selectAll("line")
                .data(data.links)
                .enter().append("line")
                .attr("class","link");

            const node = layer.append("g")
                .selectAll("g")
                .data(data.nodes)
                .enter().append("g")
                .attr("class","node")
                .call(d3.drag()
                    .on("start",dragstart)
                    .on("drag",drag)
                    .on("end",dragend));

            node.append("circle")
                .attr("r",12)
                .on("mouseenter",(e,d)=>{
                    tooltip.html("Nodo " + d.id)
                    .style("left",(e.pageX+15)+"px")
                    .style("top",(e.pageY+15)+"px")
                    .classed("show-tooltip",true);
                })
                .on("mousemove",(e)=>{
                    tooltip.style("left",(e.pageX+15)+"px")
                        .style("top",(e.pageY+15)+"px");
                })
                .on("mouseleave",()=>{
                    tooltip.classed("show-tooltip",false);
                });

            const sim = d3.forceSimulation(data.nodes)
                .force("link", d3.forceLink(data.links).id(d=>d.id).distance(100).strength(0.5))
                .force("charge", d3.forceManyBody().strength(-300))
                // POSICIONAMIENTO INICIAL: 75% del ancho (Derecha)
                .force("center", d3.forceCenter(width * 0.75, height / 2))
                .force("collide", d3.forceCollide(25))
                .on("tick",()=>{
                    link.attr("x1",d=>d.source.x).attr("y1",d=>d.source.y)
                        .attr("x2",d=>d.target.x).attr("y2",d=>d.target.y);
                    node.attr("transform",d=>`translate(${d.x},${d.y})`);
                });

            // Zoom y Pan activados (con scroll de página permitido filtrando evento 'wheel')
            svg.call(
                d3.zoom()
                .scaleExtent([0.1, 4])
                .filter((event) => !event.type.includes('wheel'))
                .on("zoom",(e)=>layer.attr("transform",e.transform))
            );

            function dragstart(e,d){
                if(!e.active) sim.alphaTarget(0.3).restart();
                d.fx=d.x; d.fy=d.y;
            }
            function drag(e,d){
                d.fx=e.x;
                d.fy=e.y;
            }
            function dragend(e,d){
                if(!e.active) sim.alphaTarget(0);
                d.fx=null; d.fy=null;
            }
        })();

        // Scripts generales
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            const scrollToTop = document.getElementById('scrollToTop');
            
            if (window.scrollY > 100) {
                header.classList.add('header-scrolled');
                scrollToTop.classList.add('visible');
            } else {
                header.classList.remove('header-scrolled');
                scrollToTop.classList.remove('visible');
            }
        });

        // Back to top functionality
        document.getElementById('scrollToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });



        // Filtrado de productos por categoría
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            const productCards = document.querySelectorAll('.product-card');
            
            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    // Remover clase active de todos los filtros
                    categoryFilters.forEach(f => f.classList.remove('active'));
                    // Agregar clase active al filtro clickeado
                    this.classList.add('active');
                    
                    const categoria = this.getAttribute('data-categoria');
                    
                    // Mostrar/ocultar productos según categoría
                    let visibleCount = 0;
                    productCards.forEach(card => {
                        if (categoria === 'all' || card.getAttribute('data-categoria') === categoria) {
                            card.style.display = 'block';
                            visibleCount++;
                            // Animación de aparición
                            card.style.animation = 'fadeIn 0.5s ease';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Mostrar mensaje si no hay resultados
                    const productGrid = document.getElementById('product-grid');
                    let noResults = productGrid.querySelector('.no-results');
                    
                    if (visibleCount === 0) {
                        if (!noResults) {
                            noResults = document.createElement('div');
                            noResults.className = 'empty-state no-results';
                            noResults.innerHTML = `
                                <i class="fas fa-search"></i>
                                <h3>No se encontraron publicaciones</h3>
                                <p>No hay publicaciones en esta categoría en este momento.</p>
                            `;
                            productGrid.appendChild(noResults);
                        }
                    } else if (noResults) {
                        noResults.remove();
                    }
                });
            });

            // Favoritos functionality
            const favoriteButtons = document.querySelectorAll('.product-favorite');
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Evitar que el clic vaya a la tarjeta
                    
                    const productId = this.getAttribute('data-producto');
                    const icon = this.querySelector('i');
                    const btn = this;
                    
                    // Llamada AJAX
                    fetch('<?php echo BASE_URL; ?>favoritos/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id_publicacion: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error && data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        
                        if (data.success) {
                            // Toggle visual state
                            if (data.accion === 'agregado') {
                                btn.classList.add('favorited');
                                icon.className = 'fas fa-heart'; // Corazón lleno
                            } else {
                                btn.classList.remove('favorited');
                                icon.className = 'far fa-heart'; // Corazón vacío
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            // Smooth scroll para enlaces internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>