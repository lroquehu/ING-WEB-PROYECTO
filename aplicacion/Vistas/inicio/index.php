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
    <title>UniEmprende - Plataforma Universitaria de Emprendimiento</title>
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
            background-image: url('wilas.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }

        /* Overlay para mejorar legibilidad */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            z-index: -1;
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
            padding: 0.8rem 0;
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

        /* Responsive */
        @media (max-width: 768px) {
            .nav-buttons {
                gap: 0.25rem;
            }
            
            .nav-btn {
                min-width: 44px;
                height: 44px;
                padding: 0.6rem;
            }
            
            .nav-btn .btn-text {
                display: none;
            }
            
            .nav-btn:hover .btn-text {
                display: none;
            }
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
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--bg-white);
            padding: 8rem 0 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="rgba(255,255,255,0.05)"><circle cx="50" cy="50" r="2"/></svg>') repeat;
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .hero-text h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            font-weight: 700;
        }
        
        .hero-text p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            line-height: 1.6;
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
        
        .hero-visual {
            text-align: center;
            position: relative;
        }
        
        .hero-visual-content {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .visual-placeholder {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .visual-text {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Secciones */
        section {
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
            .hero-content,
            .about-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 6rem 0 3rem;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: flex-start;
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
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .nav-buttons {
                flex-direction: column;
                gap: 0.5rem;
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
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    UniEmprende
                </a>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Formulario de Búsqueda -->
                    <form action="<?php echo BASE_URL; ?>publicaciones/buscar" method="GET" class="search-container">
                        <input type="search" name="q" class="search-input" placeholder="Buscar productos o servicios..." aria-label="Buscar">
                        <button type="submit" class="search-btn" aria-label="Realizar búsqueda">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <div class="nav-buttons">
                    <?php if ($usuario_autenticado): ?>
                        <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="nav-btn nav-btn-primary">
                            <i class="fas fa-plus"></i>
                            <span class="btn-text">Publicar</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>perfil" class="nav-btn nav-btn-outline">
                            <i class="fas fa-user"></i>
                            <span class="btn-text">Mi Perfil</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>logout" class="nav-btn nav-btn-secondary">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="btn-text">Salir</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login" class="nav-btn nav-btn-outline">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="btn-text">Ingresar</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>registro" class="nav-btn nav-btn-primary">
                            <i class="fas fa-user-plus"></i>
                            <span class="btn-text">Registrarse</span>
                        </a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>



    <main>
        <!-- Hero Section -->
        <section class="hero" id="hero">
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
                    
                    <div class="hero-visual">
                        <div class="hero-visual-content">
                            <div class="visual-placeholder">
                                🎓🚀
                            </div>
                            <div class="visual-text">
                                Tu plataforma universitaria<br>para emprender y conectar
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alertas de éxito -->
        <?php if (!empty($mensaje_exito)): ?>
        <div class="container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($mensaje_exito); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Categorías -->
        <section class="categories" id="categorias">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Explora por Categorías</h2>
                </div>
                
                <div class="category-filters">
                    <div class="category-filter active" data-categoria="all">
                        <i class="fas fa-th-large category-icon"></i>
                        Todas las Categorías
                    </div>
                    <?php foreach ($categorias as $categoria): ?>
                        <div class="category-filter" data-categoria="<?php echo $categoria['id_categoria']; ?>">
                            <i class="fas fa-tag category-icon"></i>
                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                            <?php if (isset($categoria['total_publicaciones'])): ?>
                                <span class="badge">(<?php echo $categoria['total_publicaciones']; ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Publicaciones Destacadas -->
        <section class="products" id="destacados">
            <div class="container">
                
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
                                    data-categoria="<?php echo $publicacion['id_categoria']; ?>"
                                    role="article" 
                                    aria-labelledby="product-<?php echo $publicacion['id_publicacion']; ?>">
                                
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
                        <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                            <i class="fas fa-search"></i> Explorar Todas las Publicaciones
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sobre Nosotros -->
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

    <!-- Botón Back to Top -->
    <button class="scroll-to-top" id="scrollToTop" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-graduation-cap"></i>
                        UniEmprende
                    </div>
                    <p class="footer-description">
                        La plataforma líder para el emprendimiento universitario. 
                        Conectamos estudiantes emprendedores y facilitamos el comercio 
                        dentro de la comunidad universitaria.
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
                    <h4>Explorar</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>publicaciones"><i class="fas fa-chevron-right"></i> Productos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>publicaciones?tipo=Servicio"><i class="fas fa-chevron-right"></i> Servicios</a></li>
                        <li><a href="<?php echo BASE_URL; ?>categorias"><i class="fas fa-chevron-right"></i> Categorías</a></li>
                        <li><a href="<?php echo BASE_URL; ?>buscar"><i class="fas fa-chevron-right"></i> Búsqueda</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Cuenta</h4>
                    <ul class="footer-links">
                        <?php if ($usuario_autenticado): ?>
                            <li><a href="<?php echo BASE_URL; ?>perfil"><i class="fas fa-chevron-right"></i> Mi Perfil</a></li>
                            <li><a href="<?php echo BASE_URL; ?>perfil/publicaciones"><i class="fas fa-chevron-right"></i> Mis Publicaciones</a></li>
                            <li><a href="<?php echo BASE_URL; ?>perfil/favoritos"><i class="fas fa-chevron-right"></i> Favoritos</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>login"><i class="fas fa-chevron-right"></i> Iniciar Sesión</a></li>
                            <li><a href="<?php echo BASE_URL; ?>registro"><i class="fas fa-chevron-right"></i> Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Ayuda</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>acerca-de"><i class="fas fa-chevron-right"></i> Acerca de</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contacto"><i class="fas fa-chevron-right"></i> Contacto</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Preguntas Frecuentes</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Términos de Uso</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 UniEmprende. Todos los derechos reservados. | Desarrollado para la comunidad universitaria</p>
            </div>
        </div>
    </footer>

    <script>
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

        // CSS para animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .badge {
                background: rgba(255,255,255,0.2);
                padding: 0.2rem 0.5rem;
                border-radius: 10px;
                font-size: 0.7rem;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>