<?php
    // aplicacion/Vistas/partials/encabezado.php

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
    <title>header</title>
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

        /* User Actions & Notifications */
        .user-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-btn-icon {
            position: relative;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bg-white);
            font-size: 1.3rem;
            text-decoration: none;
            border-radius: 50%;
            transition: var(--transition);
        }
        .nav-btn-icon:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: var(--accent-color);
            color: var(--primary-dark);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
            display: none; /* Oculto por defecto */
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-color);
        }
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: var(--bg-white);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            width: 320px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
            z-index: 1001;
        }
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-header {
            padding: 1rem;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }
        #notif-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .no-notif {
            padding: 2rem;
            text-align: center;
            color: var(--text-light);
        }
        .dropdown-footer {
            padding: 0.75rem;
            text-align: center;
            border-top: 1px solid var(--border-color);
        }
        .dropdown-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
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
            border-radius: 16px;
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
            cursor: pointer;
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
                flex-direction: row;
                gap: 0.5rem;
                justify-content: center;
            }
            .logo{
                justify-content: center;
            }
            .header-content{
                display:flex;
                flex-direction:column;
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

        /* Responsive styles for mobile */
        @media (max-width: 768px) {

            .main-header{
                padding: 1rem 0 0 0;
                position: unset;
            }

            .nav-buttons {
                display: flex;
                position: unset;
                top: 100%;
                left: 0;
                width: 100%;
                background: none;
                padding: 1rem;
            }

            .dropdown:hover .dropdown-menu{
                transform: translateX(60%);
            }

            .nav-buttons.active {
                display: flex;
            }

            /* Product grid to 1 column on mobile */
            .product-grid {
                grid-template-columns: 1fr;
            }

            /* Product detail page to 1 column */
            .product-main-layout {
                grid-template-columns: 1fr;
            }

            .product-gallery, .product-sidebar {
                position: static;
            }

            /* Forms on mobile */
            input, select, textarea {
                width: 100%;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            /* Further reduce product grid to 1 column if needed */
            .product-grid {
                grid-template-columns: 1fr;
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

                <div class="nav-buttons">
                    <?php if ($usuario_autenticado): ?>
                        <div class="user-actions">
                            <a href="<?php echo BASE_URL; ?>chat" class="nav-btn-icon" id="chat-link">
                                <i class="fas fa-comments"></i>
                                <span class="badge" id="chat-badge">0</span>
                            </a>
                            <div class="dropdown">
                                <a href="#" class="nav-btn-icon" id="notif-link">
                                    <i class="fas fa-bell"></i>
                                    <span class="badge" id="notif-badge">0</span>
                                </a>
                                <div class="dropdown-menu" id="notif-dropdown">
                                    <div class="dropdown-header">Notificaciones</div>
                                    <div id="notif-list">
                                        <!-- Las notificaciones se cargarán aquí -->
                                        <p class="no-notif">No tienes notificaciones nuevas.</p>
                                    </div>
                                    <div class="dropdown-footer">
                                        <a href="<?php echo BASE_URL; ?>notificaciones">Ver todas</a>
                                    </div>
                                </div>
                            </div>
                        </div>

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
    </header>

<script>
    const base_url = "<?php echo BASE_URL; ?>";
    
    document.addEventListener('DOMContentLoaded', function() {

        <?php if ($usuario_autenticado): ?>
        function verificarNotificaciones() {
            fetch(base_url + 'notificaciones/verificarestado')
                .then(response => response.json())
                .then(data => {
                    // Actualizar burbuja de notificaciones (alertas)
                    const notifBadge = document.getElementById('notif-badge');
                    if (data.alertas > 0) {
                        notifBadge.textContent = data.alertas > 9 ? '9+' : data.alertas;
                        notifBadge.style.display = 'flex';
                    } else {
                        notifBadge.style.display = 'none';
                    }

                    // Actualizar burbuja de mensajes
                    const chatBadge = document.getElementById('chat-badge');
                    if (data.mensajes > 0) {
                        chatBadge.textContent = data.mensajes > 9 ? '9+' : data.mensajes;
                        chatBadge.style.display = 'flex';
                    } else {
                        chatBadge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error al verificar notificaciones:', error));
        }

        // Verificar al cargar la página
        verificarNotificaciones();

        // ... código anterior del numerito ...

    // Lógica para cargar la lista de notificaciones al pasar el mouse
        const notifLink = document.getElementById('notif-link');
        const notifList = document.getElementById('notif-list');

        if (notifLink && notifList) {
            // Usamos 'mouseenter' para detectar cuando el usuario pone el mouse sobre la campana
            notifLink.parentElement.addEventListener('mouseenter', function() {
                
                fetch(base_url + 'notificaciones/obtenerrecientes')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(notif => {
                                // Definir icono según tipo
                                let iconClass = notif.tipo === 'favorito' ? 'fa-heart' : 'fa-info-circle';
                                let iconColor = notif.tipo === 'favorito' ? '#e74c3c' : '#3498db'; // Rojo o Azul
                                
                                // Estilo para no leídas
                                let bgStyle = notif.leido == 0 ? 'background-color: #f8f9fa; font-weight: bold;' : '';

                                html += `
                                    <a href="${base_url}notificaciones/leer/${notif.id}" 
                                    style="display: flex; gap: 10px; padding: 10px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; align-items: center; ${bgStyle}">
                                        <div style="color: ${iconColor}; font-size: 1.2rem;">
                                            <i class="fas ${iconClass}"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <p style="margin: 0; font-size: 0.9rem; line-height: 1.3;">${notif.mensaje}</p>
                                            <small style="color: #888; font-size: 0.75rem;">${tiempoTranscurrido(notif.fecha)}</small>
                                        </div>
                                    </a>
                                `;
                            });
                            notifList.innerHTML = html;
                        } else {
                            notifList.innerHTML = '<p class="no-notif" style="padding: 15px; text-align: center; color: #666;">No tienes notificaciones nuevas.</p>';
                        }
                    })
                    .catch(error => console.error('Error al cargar lista de notificaciones:', error));
            });
        }

        // Verificar periódicamente cada 30 segundos
        setInterval(verificarNotificaciones, 10000);
        <?php endif; ?>
    });
    function tiempoTranscurrido(fecha) {
        const ahora = new Date();
        const fechaNotif = new Date(fecha);
        const segundos = Math.floor((ahora - fechaNotif) / 1000);

        let intervalo = segundos / 31536000;
        if (intervalo > 1) return "Hace " + Math.floor(intervalo) + " años";
        
        intervalo = segundos / 2592000;
        if (intervalo > 1) return "Hace " + Math.floor(intervalo) + " meses";
        
        intervalo = segundos / 86400;
        if (intervalo > 1) return "Hace " + Math.floor(intervalo) + " días";
        
        intervalo = segundos / 3600;
        if (intervalo > 1) return "Hace " + Math.floor(intervalo) + " horas";
        
        intervalo = segundos / 60;
        if (intervalo > 1) return "Hace " + Math.floor(intervalo) + " minutos";
        
        return "Hace un momento";
    }
</script>

    <main>