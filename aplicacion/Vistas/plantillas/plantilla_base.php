<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://38.250.161.160/ING-WEB-PROYECTO/');
        /**------------------------------------------- */
        /* SOLO SI QUIEREN VOLVER AL LOCAL HOST */
        /**------------------------------------------- */
        //define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/');
    }

    $usuario_autenticado = isset($_SESSION['usuario_id']);
    $titulo = $titulo ?? 'UniEmprende';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #910202;
            --primary-light: #c00404;
            --primary-dark: #700101;
            --accent: #ffd700;
            --text: #333333;
            --text-light: #777777;
            --background: #ffffff;
            --gray: #f5f5f5;
            --gray-dark: #e0e0e0;
            --success: #10b981;
            --blue: #2563eb;
            --blue-light: #3b82f6;
            --cyan: #06b6d4;
            --cyan-light: #22d3ee;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
        }

        /* Botón de like */
        .like-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .product-stock.out-of-stock {
            color: #ef4444 !important;
            background: #fef2f2 !important;
        }

        .like-btn:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .like-btn.liked {
            background: rgba(255, 255, 255, 0.95);
        }

        .like-btn.liked i {
            color: #e11d48;
        }

        .like-btn i {
            color: var(--text-light);
            transition: color 0.3s ease;
        }

        .like-count {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
        }

        /* Información de stock */
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .product-stock {
            font-size: 0.9rem;
            color: #4b5563; 
            font-weight: 500;
            background: #f3f4f6; 
            padding: 4px 10px;
            border-radius: 6px;
        }

        .seller-university {
            color: var(--blue);
            font-weight: 600;
        }

        .product-category {
            color: var(--blue-light);
        }

        .contact-item i {
            background: var(--blue);
        }

        .stat {
            border-top: 4px solid var(--blue);
        }

        .stat-number {
            color: var(--blue);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1500px;
            margin: 0 auto;
            gap: 1.5rem;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            flex-shrink: 0;
        }

        .logo i {
            margin-right: 0.5rem;
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            list-style: none;
            flex-wrap: wrap;
        }

        .nav-links li {
            margin: 0 1.2rem;
            position: relative;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 0;
            display: inline-block;
            position: relative;
            font-size: 1.05rem;
            cursor: pointer;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: white;
        }

        /* Buscador */
        .search-container {
            position: relative;
            flex: 1;
            max-width: 400px;
            margin: 0 1rem;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 0 2px var(--accent);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
            flex-shrink: 0;
        }

        .btn {
            padding: 0.7rem 1.4rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--primary);
        }

        .btn-filled {
            background: var(--accent);
            color: var(--primary);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }

        .btn-filled:hover {
            background: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Botones de producto */
        .product-actions {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.2rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .buy-btn {
            background: var(--primary);
            color: white;
        }

        .buy-btn:hover {
            background: var(--primary-dark);
        }

        .cart-btn {
            background: var(--gray);
            color: var(--text);
            border: 1px solid var(--gray-dark);
        }

        .cart-btn:hover {
            background: var(--gray-dark);
        }

        .cart-btn.added {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        /* Hero Section */
        .hero {
            padding: 5rem 2rem;
            text-align: center;
            background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            color: white;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(145, 2, 2, 0.8), rgba(112, 1, 1, 0.9));
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 1.2rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin: 0 auto 2rem;
            opacity: 0.95;
            max-width: 600px;
        }

        a {
            text-decoration: none;
        }

        /* Categorías */
        .categories {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.2rem;
            color: var(--primary);
            font-weight: 600;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
        }

        .category-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .category-filter {
            padding: 0.8rem 1.8rem;
            background: white;
            border: 2px solid var(--primary);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 1rem;
        }

        .category-filter:hover, .category-filter.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 6px 12px rgba(145, 2, 2, 0.2);
        }

        /* Product Grid */
        .products {
            padding: 2rem;
            max-width: 1450px;
            margin: 0 auto 4rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1450px;
            margin: 0 auto;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-dark);
        }

        .product-card:hover {
            box-shadow: 0 12px 25px rgba(145, 2, 2, 0.15);
        }

        .product-image {
            height: 200px;
            overflow: hidden;
            background: var(--gray);
            position: relative;
        }

        .product-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary);
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

        .product-info {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 600;
        }

        .product-description {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .product-seller {
            display: flex;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--gray);
            font-size: 0.9rem;
        }

        .seller-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 0.5rem;
            object-fit: cover;
            background: var(--gray);
            border: 1px solid var(--primary);
        }

        .seller-name {
            font-weight: 500;
        }

        .seller-university {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-left: auto;
        }

        /* Contacto Section */
        .contact {
            padding: 5rem 2rem;
            background: var(--gray);
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .contact-content h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .contact-content h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--primary);
        }

        .contact-content p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text);
        }

        .contact-info {
            margin-top: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .contact-item i {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--gray-dark);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
        }

        /* Sobre Nosotros Section */
        .about {
            padding: 5rem 2rem;
            background: var(--background);
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-content h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .about-content h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--primary);
        }

        .about-content p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text);
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-top: 4px solid var(--primary);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stat-text {
            font-size: 1rem;
            color: var(--text-light);
        }

        .about-image {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Footer */
        footer {
            background: var(--primary);
            color: white;
            padding: 2rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2.5rem;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: white;
            font-weight: 600;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.3s;
            font-size: 1rem;
            position: relative;
            padding-bottom: 3px;
            cursor: pointer;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: 0;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons {
            display: flex;
            gap: 1.2rem;
            margin-top: 1.5rem;
        }

        .social-icons a {
            color: white;
            font-size: 1.4rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--accent);
        }

        .copyright {
            text-align: center;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        /* estilos para Modal:Tamaño normal con labels flotantes */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 85%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(-50px);
            transition: transform 0.4s ease;
            overflow: hidden;
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            background: var(--primary);
            color: white;
            padding: 0.8rem;
            position: relative;
            text-align: center;
        }

        .modal-header h2 {
            font-size: 1.8rem;
            margin: 0;
        }

        .close-modal {
            position: absolute;
            top: 1.2rem;
            right: 1.2rem;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: var(--accent);
        }

        .modal-body {
            padding: 2rem;
        }

        /* Estilos para campos de formulario con labels flotantes */
        .input-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.5rem 0.8rem 0.8rem;
            border: 1px solid var(--gray-dark);
            border-radius: 6px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: transparent;
            z-index: 1;
            position: relative;
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 0.8rem;
            transform: translateY(-50%);
            font-size: 1.1rem;
            color: var(--text-light);
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 0.4rem;
            z-index: 2;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: 0;
            left: 0.8rem;
            transform: translateY(-50%) scale(0.9);
            color: var(--primary);
            font-weight: 600;
        }

        /* Placeholder transparente */
        .input-group input::placeholder {
            color: transparent;
        }

        .modal-footer {
            padding: 0 2rem 2rem;
            text-align: center;
        }

        .modal-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            margin-bottom: 1.2rem;
        }

        .modal-submit:hover {
            background: var(--primary-dark);
        }

        .modal-switch {
            color: var(--text-light);
            font-size: 1rem;
        }

        .modal-switch a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .modal-switch a:hover {
            text-decoration: underline;
        }

        .social-login {
            text-align: center;
        }

        .social-login p {
            color: var(--text-light);
            margin-bottom: 1.2rem;
            position: relative;
            font-size: 1rem;
        }

        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: var(--gray-dark);
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--gray-dark);
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .social-btn:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .social-btn.google {
            color: #DB4437;
        }

        .social-btn.facebook {
            color: #4267B2;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 2rem 0;
        }

        .checkbox-group input {
            width: auto;
            margin-right: 0.8rem;
        }

        .checkbox-group label {
            font-size: 1rem;
            color: var(--text-light);
        }

        .checkbox-group a {
            color: var(--primary);
            text-decoration: none;
        }

        .checkbox-group a:hover {
            text-decoration: underline;
        }

        /* Enlace de forgot password */
        .forgot-password {
            text-align: right;
            margin: -1rem 0 1.5rem;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .carousel-image {
            height: 300px;
            overflow: hidden;
        }

        .carousel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-content {
            padding: 1.5rem;
        }

        .carousel-category {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .carousel-title {
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
            color: var(--text);
        }

        .carousel-description {
            color: var(--text-light);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .carousel-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 0.5rem;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray-dark);
            cursor: pointer;
            transition: background 0.3s;
        }

        .carousel-dot.active {
            background: var(--primary);
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.8);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--primary);
            transition: all 0.3s;
            z-index: 10;
        }

        .carousel-nav:hover {
            background: var(--primary);
            color: white;
        }

        .carousel-prev {
            left: 15px;
        }

        .carousel-next {
            right: 15px;
        }

        .no-ads-message {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .no-ads-message i {
            font-size: 3rem;
            color: var(--gray-dark);
            margin-bottom: 1rem;
        }

        .no-ads-message h3 {
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .no-ads-message p {
            color: var(--text-light);
        }

        /* estilos Responsive */

        @media (max-width: 968px) {
            .about-container,
            .contact-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .about-image {
                order: -1;
                max-width: 600px;
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1.2rem;
            }

            .nav-links {
                margin: 1rem 0;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .about-stats {
                grid-template-columns: 1fr;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .modal {
                width: 95%;
                max-width: 450px;
            }
            
            .modal-body {
                padding: 2rem;
            }

            .product-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .like-btn {
                padding: 6px 10px;
            }
            
            .like-count {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .like-btn {
                top: 5px;
                right: 5px;
                padding: 5px 8px;
            }
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .nav-links li {
                margin: 0.5rem 0;
            }

            .auth-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 250px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .category-filters {
                gap: 0.8rem;
            }

            .category-filter {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .modal-body {
                padding: 1.5rem;
            }
            
            .modal-header {
                padding: 1.5rem;
            }
            
            .modal-header h2 {
                font-size: 1.5rem;
            }
            
            .modal-footer {
                padding: 0 1.5rem 1.5rem;
            }
            
            .input-group {
                margin-bottom: 1.5rem;
            }
            
            .input-group input {
                padding: 1.2rem 1rem 0.8rem;
                font-size: 1rem;
            }
            
            .input-group label {
                font-size: 1rem;
                left: 0.8rem;
            }
            
            .social-btn {
                width: 45px;
                height: 45px;
            }
        }
    </style> 
</head>
<body>
    <?php require_once 'aplicacion/Vistas/plantillas/header.php'; ?>
    
    <main>
        <?php echo $contenido ?? ''; ?>
    </main>
    
    <?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>
    
</body>
</html>