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
<?php 
    $page_title = 'UniEmprende - Plataforma Universitaria de Emprendimiento';
    require_once 'aplicacion/Vistas/plantillas/header.php'; 
?>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #510200;
            --primary-light: #b30303;
            --secondary-color: #2c3e50;
            --accent-color: #ffc107;
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
            /* Variable para la red */
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

        /* Overlay para mejorar legibilidad */
        body::before { /* Corrección para eliminar fondo transparente del header */
            display: none;
        }
        
        .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header */
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
            background: rgba(81, 2, 0, 0.95);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .nav-btn:hover::before {
            left: 100%;
        }

        .nav-btn i {
            position: relative;
            z-index: 2;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .nav-btn .btn-text {
            position: relative;
            z-index: 2;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.3s ease;
            margin-left: 0;
            font-weight: 500;
        }

        .nav-btn:hover .btn-text {
            max-width: 200px;
            opacity: 1;
            margin-left: 0.5rem;
        }

        .nav-btn:hover i {
            transform: scale(1.1);
        }

        /* Variantes de botones */
        .nav-btn-primary {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }


        .nav-btn-primary:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.2);
        }

        .nav-btn-outline {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.4);
        }

        .nav-btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
        }

        .nav-btn-secondary {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
        }

        .nav-btn-secondary:hover {
            background: var(--accent-color);
            color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Botón de búsqueda */
        .search-container {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 0.5rem 1rem;
            margin-right: 1rem;
            transition: var(--transition);
        }

        .search-container:focus-within {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        }

        .search-input {
            background: transparent;
            border: none;
            color: var(--bg-white);
            padding: 0.5rem;
            width: 200px;
            outline: none;
            font-size: 0.9rem;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-btn {
            background: transparent;
            border: none;
            color: var(--bg-white);
            cursor: pointer;
            padding: 0.5rem;
            transition: var(--transition);
        }

        .search-btn:hover {
            color: var(--accent-color);
            transform: scale(1.1);
        }

        /* Botón de desplazamiento hacia arriba */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: var(--bg-white);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(145, 2, 2, 0.4);
        }

        /* Estados de hover mejorados */
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Botones */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: var(--bg-white);
        }
        
        .btn-primary:hover {
            background: var(--primary-color);
            box-shadow: 0 6px 20px rgba(145, 2, 2, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--bg-white);
            border: 2px solid rgba(255, 255, 255, 0.4);
        }
        
        .btn-secondary:hover {
            background: var(--primary-color);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-dark);
        }
        
        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-primary-solid {
            background: var(--primary-color);
            color: var(--bg-white);
            border: 2px solid var(--primary-color);
        }

        .btn-primary-solid:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        .btn-icon {
            background: transparent;
            border: none;
            color: var(--text-lighter);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
        }
        
        .btn-icon:hover {
            color: var(--primary-color);
            background: rgba(145, 2, 2, 0.1);
        }
        
        /* * ==========================================
         * ESTILOS DEL GRAFO Y HERO (MODIFICADOS) 
         * ==========================================
         */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--bg-white);
            min-height: 85vh; 
            padding: 6rem 0 4rem; /* Reducido el padding superior para que no se vea tan abajo */
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        /* FONDO DE RED INTERACTIVO */
        #network-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1; /* Nivel 1: Fondo */
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
            z-index: 100;
        }
        .show-tooltip { opacity: 1 !important; }

        /* Contenedor del contenido hero para que flote sobre el grafo */
        .hero .container {
            position: relative;
            z-index: 2; /* Por encima del grafo */
            width: 100%;
            pointer-events: none; /* Dejar pasar clics en zonas vacías */
        }

        .hero-content {
            display: flex; /* Cambiado de grid a flex para manejo libre */
            justify-content: flex-start;
            align-items: center;
            position: relative;
        }
        
        .hero-text {
            max-width: 600px;
            pointer-events: none; /* El texto en sí no bloquea, pero sus hijos sí */
        }
        
        /* Reactivar clics en elementos interactivos dentro del hero */
        .hero-buttons, .hero-stats {
            pointer-events: auto; 
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
        
        /* ========================================== */
        
        /* Secciones */
        section:not(.hero) {
            padding: 5rem 0;
            position: relative;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 2.75rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
            font-weight: 700;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Categorías */
        .categories {
            background: rgba(248, 249, 250, 0.9);
        }
        
        .category-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .category-filter {
            padding: 0.75rem 1.5rem;
            background: var(--bg-white);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .category-filter:hover,
        .category-filter.active {
            background: var(--primary-color);
            color: var(--bg-white);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .category-icon {
            font-size: 1.1rem;
        }
        
        /* Productos/Publicaciones */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 4fr));
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background: var(--bg-white);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }
        
        .product-card:hover {
            box-shadow: var(--shadow-hover);
        }
        
        .product-image {
            position: relative;
            height: 220px;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .no-image {
            text-align: center;
            color: var(--text-lighter);
            padding: 2rem;
        }
        
        .no-image i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .product-badges {
            position: absolute;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            display: flex;
            justify-content: space-between;
        }
        
        .product-type {
            background: var(--primary-color);
            color: var(--bg-white);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .product-favorite {
            background: rgba(255,255,255,0.9);
            color: var(--text-lighter);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }
        
        .product-favorite:hover,
        .product-favorite.favorited {
            color: var(--primary-color);
            background: var(--bg-white);
        }
        
        .product-info {
            padding: 1.75rem;
        }
        
        .product-title {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            color: var(--secondary-color);
            line-height: 1.3;
        }
        
        .product-description {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 0.95rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        
        .product-category {
            background: var(--bg-light);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-light);
        }
        
        .product-price {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.25rem;
        }
        
        .product-vendor {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            color: var(--border-color);
            opacity: 0.7;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .empty-state p {
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        /* About Section */
        .about {
            background: rgba(248, 249, 250, 0.9);
        }
        
        .about-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }
        
        .about-content h2 {
            font-size: 2.75rem;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
        }
        
        .about-content p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: var(--text-light);
            line-height: 1.7;
        }
        
        .about-features {
            margin: 2.5rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon {
            background: var(--primary-color);
            color: var(--bg-white);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }
        
        .feature-text h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }
        
        .feature-text p {
            margin: 0;
            font-size: 0.95rem;
        }
        
        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .stat {
            text-align: center;
            padding: 1.5rem;
            background: var(--bg-white);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-text {
            color: var(--text-light);
            font-weight: 500;
        }
        
        .about-visual {
            text-align: center;
        }
        
        .visual-container {
            background: var(--bg-white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        
        .visual-placeholder-large {
            font-size: 8rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            opacity: 0.7;
        }
        
        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .alert i {
            font-size: 1.25rem;
        }
        
        /* Footer */
        .main-footer {
            background: var(--secondary-color);
            color: var(--bg-white);
            padding: 3rem 0 1rem;
            position: relative; 
            z-index: 2;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-description {
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-link {
            color: var(--bg-white);
            text-decoration: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .social-link:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .footer-column h4 {
            color: var(--bg-white);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-links a:hover {
            color: var(--bg-white);
            transform: translateX(5px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero { padding-top: 6rem; text-align: center; }
            .hero-content { justify-content: center; }
            .hero-text { max-width: 100%; background: rgba(81, 2, 0, 0.6); border-radius: 15px; padding: 1rem;} /* Más oscuro en móvil para leer mejor */
            .about-container { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 2.5rem 2px 3rem;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .hero-stats {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2.25rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            .category-filters {
                justify-content: flex-start;
            }
            section {
                margin-top: -0.5rem;
                padding: 0rem 0;
            }

        }
        
        @media (max-width: 480px) {
            .hero-text h1 {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .category-filters {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 1rem;
            }

            /* --- NUEVO: Responsive para Sidebar --- */
            .page-layout {
                grid-template-columns: 1fr; /* Apila las columnas en móvil */
            }
            .sidebar {
                position: static; /* El sidebar ya no es pegajoso */
            }
        }

        /* --- NUEVO: Estilos para el layout y el nuevo sidebar/dropdown --- */
        .page-layout {
            display: grid;
            grid-template-columns: 280px 1fr; /* Columna para sidebar y contenido */
            gap: 2.5rem;
            align-items: flex-start;
        }

        .main-content {
            margin-top: 4.5rem; /* Ajuste para bajar el contenido principal */
        }

        .sidebar {
            position: static; /* Se cambió a static para que no flote */
            background: var(--bg-white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-top: 4.5rem; /* Ajuste para bajar el sidebar */
            padding: 1.5rem;
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .category-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .category-list-item a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            transition: var(--transition);
        }

        .category-list-item a:hover,
        .category-list-item a.active {
            background: var(--primary-color);
            color: var(--bg-white);
            transform: translateX(5px);
        }

        .category-count {
            background: rgba(0,0,0,0.08);
            color: var(--text-dark);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* --- NUEVO: Estilos para el filtro de precio --- */
        .price-filter-container {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .price-filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        input[type="range"] {
            width: 100%;
            -webkit-appearance: none;
            appearance: none;
            height: 8px;
            background: var(--bg-light);
            border-radius: 5px;
            outline: none;
            cursor: pointer;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: var(--primary-color);
            border-radius: 50%;
            cursor: pointer;
        }

        .price-label {
            display: flex;
            justify-content: space-between;
            margin-top: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        /* --- NUEVO: Estilos para el filtro de búsqueda por texto --- */
        .search-filter-container {
            margin-bottom: 1.5rem;
        }

        .search-filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .search-input-wrapper {
            position: relative;
        }

        #search-filter {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 2.5rem; /* Espacio para el ícono */
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        #search-filter:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
        }

        .search-input-wrapper i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-lighter);
        }

        /* --- NUEVO: Modal de "Inicio de Sesión Requerido" (copiado de ver.php) --- */
        .login-required-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .login-required-modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .login-required-modal-box {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 480px;
            text-align: center;
        }

        .login-required-modal-box i {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        .custom-modal-buttons {
            display: flex; justify-content: center; gap: 1rem;
        }

        /* NUEVO: Efecto hover para el botón cancelar del modal de login */
        #login-modal-cancel:hover {
            background-color: #f0f0f0;
            border-color: #bbb;
        }
    </style>

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
                                <a href="<?php echo BASE_URL; ?>registro" class="btn btn-primary">
                                    <i class="fas fa-rocket"></i> Comenzar Ahora
                                </a>
                                <a href="<?php echo BASE_URL; ?>login" class="btn btn-secondary">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Publicar Producto
                                </a>
                                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Explorar Productos
                                </a>
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
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($mensaje_exito); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="container page-layout">
            
            <aside class="sidebar">
                <div class="search-filter-container">
                    <h4 class="search-filter-title">Buscar en la página</h4>
                    <div class="search-input-wrapper">
                        <input type="text" id="search-filter" placeholder="Escribe para filtrar...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>


                <h3 class="sidebar-title">
                    <i class="fas fa-tags"></i>
                    Categorías
                </h3>
                <ul class="category-list">
                    <li class="category-list-item" role="presentation">
                        <a href="#" class="category-filter active" data-categoria="all" role="menuitem">
                            <span>Todas</span>
                        </a>
                    </li>
                    <?php foreach ($categorias as $categoria): ?>
                    <li class="category-list-item" role="presentation">
                        <a href="#" class="category-filter" data-categoria="<?php echo $categoria['id_categoria']; ?>" role="menuitem">
                            <span><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></span>
                            <?php if (isset($categoria['total_publicaciones'])): ?>
                                <span class="category-count"><?php echo $categoria['total_publicaciones']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="price-filter-container">
                    <h4 class="price-filter-title">Filtrar por Precio</h4>
                    <div class="price-slider">
                        <input type="range" id="price-range" min="0" max="1000" value="1000" step="10">
                        <div class="price-label">
                            <span>S/ 0</span>
                            <span id="price-value">S/ 1000</span>
                        </div>
                    </div>
                </div>
            </aside>
            
            <div class="main-content">
                <section class="products" id="destacados" style="padding-top: 0;">
                    <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
                        <h2 class="section-title" style="font-size: 2.25rem;">Publicaciones Recientes</h2>
                    </div>
                    
                    <?php if (empty($publicaciones_destacadas)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>Aún no hay publicaciones destacadas</h3>
                            <p>Sé el primero en publicar y destacar tu producto o servicio</p>
                            <?php if (!$usuario_autenticado): ?>
                                <a href="<?php echo BASE_URL; ?>registro" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Regístrate para publicar
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Crear primera publicación
                                </a>
                            <?php endif; ?>
                        </div>
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
                            <article class="product-card" 
                                        data-price="<?php echo $publicacion['precio']; ?>"
                                        data-categoria="<?php echo $publicacion['id_categoria']; ?>"
                                        role="article" 
                                        aria-labelledby="product-<?php echo $publicacion['id_publicacion']; ?>">
                                    
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>">
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
                                                        data-producto="<?php echo $publicacion['id_publicacion']; ?>"
                                                        data-logged-in="<?php echo $usuario_autenticado ? 'true' : 'false'; ?>">
                                                    <i class="fa-heart <?php echo $es_favorito ? 'fas' : 'far'; ?>"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                    
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
                                            <img src="<?php echo !empty($publicacion['foto_perfil']) ? obtenerImagenFinal($publicacion['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Vendedor" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 8px; border: 1px solid var(--border-color);">
                                            <a href="<?php echo BASE_URL; ?>perfil/ver/<?php echo $publicacion['id_usuario']; ?>" style="color: inherit; text-decoration: none;" title="Ver perfil de <?php echo htmlspecialchars($publicacion['nombres']); ?>">
                                                <?php echo htmlspecialchars($publicacion['nombres'] . ' ' . $publicacion['apellidos']); ?>
                                            </a>
                                        </div>
                                        
                                        <div class="product-actions">
                                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" 
                                               class="btn btn-outline btn-sm">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        
                                            <?php if ($usuario_autenticado): ?>
                                                <?php if ($_SESSION['usuario_id'] == $publicacion['id_usuario']): ?>
                                                    <span class="btn-icon" style="opacity: 0.5; cursor: not-allowed;" title="Es tu publicación">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <a href="<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $publicacion['id_usuario']; ?>" 
                                                       class="btn-icon" 
                                                       title="Contactar vendedor"
                                                       aria-label="Contactar vendedor"
                                                       style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?php echo BASE_URL; ?>login" 
                                                   class="btn-icon" 
                                                   title="Inicia sesión para contactar"
                                                   aria-label="Inicia sesión para contactar"
                                                   style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="text-align: center; margin-top: 3rem;">
                            <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary-solid">
                                <i class="fas fa-search"></i> Explorar Todas las Publicaciones
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>

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
                        </div>
                </div>
            </div>
        </section>
    </main>

    <!-- NUEVO: Modal de "Inicio de Sesión Requerido" -->
    <div id="login-required-modal" class="login-required-modal-overlay">
        <div class="login-required-modal-box">
            <i class="fas fa-sign-in-alt"></i>
            <h3 style="font-size: 1.5rem; color: #333; margin-bottom: 1rem;">Inicio de Sesión Requerido</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 2rem;">Necesitas iniciar sesión para poder agregar publicaciones a tus favoritos.</p>
            <div class="custom-modal-buttons">
                <button id="login-modal-cancel" class="btn btn-outline" style="border-color: #ccc; color: #333;">Cancelar</button>
                <a href="<?php echo BASE_URL; ?>login" id="login-modal-confirm" class="btn btn-primary-solid">
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </div>

    <button class="scroll-to-top" id="scrollToTop" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script>
        // SCRIPT DEL GRAFO D3.JS
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
                .attr("r",12);

            const sim = d3.forceSimulation(data.nodes)
                .force("link", d3.forceLink(data.links).id(d=>d.id).distance(100).strength(0.5))
                .force("charge", d3.forceManyBody().strength(-300))
                // POSICIONAMIENTO INICIAL: 75% del ancho (Derecha) donde estaban los emojis
                .force("center", d3.forceCenter(width * 0.75, height / 2))
                .force("center", d3.forceCenter(width * 0.75, height / 1.7))
                .force("center", d3.forceCenter(width * 0.7, height / 1.7))
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
                // Deshabilitar el paneo (arrastrar el fondo)
                // Deshabilitar paneo (botón primario) y zoom (rueda del ratón)
                .filter((event) => event.type !== 'wheel' && event.button !== 0)
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

        // Header scroll effect
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
            const priceRange = document.getElementById('price-range');
            const priceValue = document.getElementById('price-value');
            const searchFilter = document.getElementById('search-filter');

            function applyFilters() {
                const selectedCategory = document.querySelector('.category-filter.active').getAttribute('data-categoria');
                const maxPrice = parseFloat(priceRange.value);
                const searchTerm = searchFilter.value.toLowerCase().trim();
                let visibleCount = 0;

                productCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-categoria');
                    const cardPrice = parseFloat(card.getAttribute('data-price'));
                    const cardTitle = card.querySelector('.product-title').textContent.toLowerCase();
                    const cardDescription = card.querySelector('.product-description').textContent.toLowerCase();

                    const categoryMatch = selectedCategory === 'all' || cardCategory === selectedCategory;
                    const priceMatch = cardPrice <= maxPrice;
                    const searchMatch = (searchTerm === '' || cardTitle.includes(searchTerm) || cardDescription.includes(searchTerm));

                    if (categoryMatch && priceMatch && searchMatch) {
                        card.style.display = 'block';
                        card.style.animation = 'fadeIn 0.5s ease';
                        visibleCount++;
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
                            <p>Intenta ajustar los filtros de búsqueda, categoría o precio.</p>
                        `;
                        productGrid.appendChild(noResults);
                    }
                } else if (noResults) {
                    noResults.remove();
                }
            } 

            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function(e) {
                    e.preventDefault();
                    categoryFilters.forEach(f => f.classList.remove('active'));
                    this.classList.add('active');
                    applyFilters();
                });
            });

            priceRange.addEventListener('input', function() {
                priceValue.textContent = `S/ ${this.value}`;
                applyFilters();
            });

            searchFilter.addEventListener('input', function() {
                applyFilters();
            });

            // --- Funcionalidad de Favoritos ---
            const favoriteButtons = document.querySelectorAll('.product-favorite');
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Evitar que el clic vaya a la tarjeta
                    
                    const isLoggedIn = this.getAttribute('data-logged-in') === 'true';

                    if (!isLoggedIn) {
                        const modal = document.getElementById('login-required-modal');
                        modal.classList.add('visible');
                        return;
                    }

                    const productId = this.getAttribute('data-producto');
                    const icon = this.querySelector('i');
                    const btn = this;
                    
                    // Llamada AJAX
                    fetch('<?php echo BASE_URL; ?>publicaciones/toggle-favorito', {
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

            // --- NUEVO: Lógica para el modal de "Inicio de Sesión Requerido" ---
            const loginModal = document.getElementById('login-required-modal');
            if (loginModal) {
                const btnLoginCancel = document.getElementById('login-modal-cancel');
                
                btnLoginCancel.addEventListener('click', () => {
                    loginModal.classList.remove('visible');
                });

                loginModal.addEventListener('click', function(e) {
                    if (e.target === this) { loginModal.classList.remove('visible'); }
                });
            }
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

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>