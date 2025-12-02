<?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticación (esto debería estar en el controlador)
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    // Datos que vienen del controlador
    $usuario = $usuario ?? [];
    $publicaciones = $publicaciones ?? [];
    $estadisticas = array_merge([
        'total_publicaciones' => 0,
        'publicaciones_activas' => 0,
        'publicaciones_pausadas' => 0,
        'total_ventas' => 0,
        'rating_promedio' => 0,
        'seguidores' => 0
    ], $estadisticas ?? []);

    $mensaje_exito = $mensaje_exito ?? '';
    $error = $error ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - UniEmprende</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #700101;
            --primary-light: rgba(145, 2, 2, 0.08);
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --text-color: #2c3e50;
            --text-light: #6c757d;
            --border-color: #e1e5e9;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            --box-shadow-hover: 0 8px 30px rgba(0,0,0,0.12);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #ffffff;
            color: var(--text-color);
            line-height: 1.6;
            font-weight: 400;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        /* Header Principal */
        .main-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.95);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
            background: var(--primary-light);
        }
        
        .nav-link.active {
            color: var(--primary-color);
            background: var(--primary-light);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 50%;
        }
        
        /* Header del Perfil */
        .profile-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 3rem 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 3rem;
        }
        
        .profile-content-header {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2.5rem;
            align-items: start;
        }
        
        .profile-avatar {
            position: relative;
        }
        
        .avatar-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            border: 4px solid white;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }
        
        .avatar-container:hover .avatar-overlay {
            opacity: 1;
        }
        
        .profile-info-main {
            padding-top: 0.5rem;
        }
        
        .profile-name {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .profile-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        .profile-bio {
            color: var(--text-light);
            line-height: 1.6;
            max-width: 500px;
            margin-bottom: 1.5rem;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-card:hover {
            box-shadow: var(--box-shadow-hover);
            border-color: var(--primary-color);
        }
        
        .stat-value {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-width: 200px;
        }
        
        /* Botones */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            justify-content: center;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(145, 2, 2, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-color);
        }
        
        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: var(--primary-light);
        }
        
        .btn-sm {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
        }
        
        .btn-icon {
            padding: 0.75rem;
            width: 42px;
            height: 42px;
        }
        
        /* Layout Principal */
        .main-content {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2.5rem;
            margin-bottom: 4rem;
        }
        
        /* Sidebar */
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .sidebar-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
        }
        
        .sidebar-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
        }
        
        .info-item:not(:last-child) {
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .info-value {
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        /* Contenido Principal */
        .profile-main {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        /* Pestañas */
        .tabs-container {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .tabs-header {
            display: flex;
            background: var(--light-gray);
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
        }
        
        .tab-button {
            flex: 1;
            padding: 1.25rem 1.5rem;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.95rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            border-bottom: 3px solid transparent;
        }
        
        .tab-button.active {
            color: var(--primary-color);
            background: white;
            border-bottom-color: var(--primary-color);
        }
        
        .tab-button:hover:not(.active) {
            color: var(--text-color);
            background: rgba(255,255,255,0.5);
        }
        
        .tab-content {
            padding: 2rem;
        }
        
        .tab-pane {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-pane.active {
            display: block;
        }
        
        /* Publicaciones - NUEVO DISEÑO DE 3 COLUMNAS */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .filter-bar {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            background: white;
            font-family: inherit;
            font-size: 0.9rem;
            min-width: 160px;
        }
        
        /* Grid de 3 columnas para productos */
        .publicaciones-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        
        /* Nuevo diseño de tarjeta de producto */
        .publicacion-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .publicacion-card:hover {
            box-shadow: var(--box-shadow-hover);
            border-color: var(--primary-color);
        }
        
        .publicacion-image {
            width: 100%;
            height: 200px;
            background: var(--light-gray);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .publicacion-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .publicacion-card:hover .publicacion-image img {
            transform: scale(1.05);
        }
        
        .no-image {
            color: var(--text-light);
            text-align: center;
            padding: 1rem;
        }
        
        .publicacion-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .publicacion-header {
            margin-bottom: 1rem;
        }
        
        .publicacion-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .publicacion-precio {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .publicacion-desc {
            color: var(--text-light);
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        
        .publicacion-meta {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .meta-tag {
            padding: 0.4rem 0.8rem;
            background: var(--light-gray);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .publicacion-status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active { background: #d4f4e6; color: var(--success-color); }
        .status-paused { background: #fff3cd; color: var(--warning-color); }
        .status-inactive { background: #fde8e8; color: var(--error-color); }
        
        .publicacion-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }
        
        .publicacion-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            transition: var(--transition);
        }
        
        .dashboard-card:hover {
            box-shadow: var(--box-shadow-hover);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
        }
        
        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--medium-gray);
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
            color: var(--text-color);
            font-size: 1.5rem;
        }
        
        .empty-state p {
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Alertas */
        .alert {
            padding: 1.25rem 1.5rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 2rem;
            border-left: 4px solid;
            background: white;
            border: 1px solid var(--border-color);
        }
        
        .alert-success {
            border-left-color: var(--success-color);
            background: #f8fff8;
        }
        
        .alert-error {
            border-left-color: var(--error-color);
            background: #fff8f8;
        }
        
        /* Footer */
        .main-footer {
            background: var(--light-gray);
            border-top: 1px solid var(--border-color);
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .footer-content {
            text-align: center;
            color: var(--text-light);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 240px 1fr; /* Sidebar un poco más pequeño */
            }
            
            .profile-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .publicaciones-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr; /* Stack sidebar y main content */
            }

            .profile-content-header {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 1.5rem;
            }
            .header-content {
                display: grid;
                justify-content: space-between;
                align-items: center;
            }
            .profile-actions {
                flex-direction: row;
                justify-content: center;
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .main-header{
                position:unset;
            }
            .container {
                padding: 0 1rem;
            }
            
            .profile-header {
                padding: 2rem 0;
            }

            .profile-stats {
                grid-template-columns: 1fr 1fr;
            }
            
            .publicaciones-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }
            
            .filter-bar {
                width: 100%;
                justify-content: space-between;
            }
            
            .logo {
                justify-content: center;
            }

            .nav-links {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }
            
            .publicacion-footer {
                /*flex-direction: column;*/
                gap: 1rem;
            }
            
            .publicacion-actions {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }
            
            .profile-name {
                font-size: 1.8rem;
            }
            
            .tabs-header {
                flex-direction: column;
            }
            
            .tab-button {
                justify-content: flex-start;
            }
            
            .filter-bar {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-select {
                width: 100%;
            }
            
        }
        /* --- AJUSTES RESPONSIVOS PERFIL (MÓVIL) --- */
        @media (max-width: 768px) {
            /* 1. Centrar Foto y Texto del Encabezado */
            .profile-content-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 1.5rem;
            }

            /* Asegurar que el avatar esté centrado */
            .profile-avatar {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            /* Centrar los metadatos (correo, universidad, rating) */
            .profile-meta {
                justify-content: center;
                gap: 1rem;
            }

            .profile-bio {
                margin-left: auto;
                margin-right: auto;
            }

            /* 2. Botones de Acción Apilados (Uno debajo de otro) */
            .profile-actions {
                width: 100%;
                max-width: 300px; /* Ancho máximo para que no se vean gigantes */
                margin: 0 auto;   /* Centrar el bloque de botones */
                flex-direction: column !important; /* Forzar columna (importante para sobrescribir) */
                gap: 0.8rem;
            }

            .profile-actions .btn {
                width: 100%;      /* Botones ocupan todo el ancho disponible */
                justify-content: center;
            }

            /* 3. Layout Sidebar en Stack (Apilado) */
            .main-content {
                display: flex;
                flex-direction: column;
                gap: 2rem;
            }

            /* Opcional: Hacer que la barra lateral (Info Personal) se vea más compacta */
            .profile-sidebar {
                width: 100%;
                order: 2; /* Si quieres que aparezca DEBAJO de las pestañas, usa 2. Si quieres arriba, pon 0 */
            }
            
            .profile-main {
                order: 1; /* El contenido principal (publicaciones) aparece primero */
            }

            .sidebar-card {
                background: #fcfcfc; /* Un fondo sutilmente distinto para diferenciar */
            }
            .publicacion-meta {
                justify-content: space-around;
                flex-direction: row;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    UniEmprende
                </a>

                <nav class="nav-links" id="navLinks">
                    <a href="<?php echo BASE_URL; ?>" class="nav-link">Inicio</a>
                    <a href="<?php echo BASE_URL; ?>publicaciones" class="nav-link">Productos</a>
                    <a href="<?php echo BASE_URL; ?>chat" class="nav-link">Mensajes</a>
                    <a href="<?php echo BASE_URL; ?>perfil" class="nav-link active">Mi Perfil</a>
                    <a href="<?php echo BASE_URL; ?>logout" class="btn btn-outline btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </nav>
            </div>
            <div class="menu-overlay" id="menuOverlay"></div>
        </div>
    </header>

    <!-- Header del Perfil -->
    <div class="profile-header">
        <div class="container">
            <div class="profile-content-header">
                <div class="profile-avatar">
                    <a href="<?php echo BASE_URL; ?>perfil/editar" class="avatar-container">
                        <img src="<?php echo !empty($usuario['foto_perfil']) ? obtenerImagenFinal($usuario['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Foto de perfil de <?php echo htmlspecialchars($usuario['nombres']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </a>
                </div>
                
                <div class="profile-info-main">
                    <h1 class="profile-name"><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></h1>
                    
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($usuario['correo_institucional']); ?>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-university"></i>
                            <?php echo htmlspecialchars($usuario['facultad'] ?? 'Sin facultad'); ?>
                        </div>
                        <!--<div class="meta-item">
                            <i class="fas fa-star"></i>
                            Rating: <?php echo isset($estadisticas['rating_promedio']) ? number_format($estadisticas['rating_promedio'], 1) : '0.0'; ?>/5.0
                        </div>-->
                    </div>
                    
                    <p class="profile-bio">
                        Miembro activo de la comunidad UniEmprende. 
                        <?php echo ($estadisticas['total_publicaciones'] ?? 0) > 0 ? 
                            'He publicado ' . ($estadisticas['total_publicaciones'] ?? 0) . ' productos.' : 
                            'Listo para comenzar a publicar productos.'; ?>
                    </p>
                    
                    <div class="profile-stats">
                        <div class="stat-card">
                            <span class="stat-value"><?php echo $estadisticas['total_vistas'] ?? 0; ?></span>
                            <span class="stat-label">Total Vistas</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-value"><?php echo $estadisticas['total_favoritos'] ?? 0; ?></span>
                            <span class="stat-label">Favoritos</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-value"><?php echo $estadisticas['total_contactos'] ?? 0; ?></span>
                            <span class="stat-label">Contactos</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-value"><?php echo $estadisticas['total_productos'] ?? 0; ?></span>
                            <span class="stat-label">Productos Activos</span>
                        </div>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nueva Publicación
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/editar" class="btn btn-outline">
                        <i class="fas fa-edit"></i>
                        Editar Perfil
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/configuracion" class="btn btn-outline">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/ventas" class="btn btn-outline">
                        <i class="fas fa-cash-register me-2"></i> Mis Ventas
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/mis-compras" class="btn btn-outline">Mis Compras</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Mensajes -->
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje_exito); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Contenido Principal -->
        <div class="main-content">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="sidebar-card">
                    <h3><i class="fas fa-info-circle"></i> Información Personal</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-id-card"></i> DNI
                            </span>
                            <span class="info-value"><?php echo htmlspecialchars($usuario['dni']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-phone"></i> Teléfono
                            </span>
                            <span class="info-value"><?php echo !empty($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : 'No registrado'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-university"></i> Facultad
                            </span>
                            <span class="info-value"><?php echo !empty($usuario['facultad']) ? htmlspecialchars($usuario['facultad']) : 'No especificada'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-school"></i> Escuela
                            </span>
                            <span class="info-value"><?php echo !empty($usuario['escuela']) ? htmlspecialchars($usuario['escuela']) : 'No especificada'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-calendar-alt"></i> Miembro desde
                            </span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                    <div class="quick-actions">
                        <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-outline">
                            <i class="fas fa-plus"></i> Nueva Publicación
                        </a>
                        <a href="<?php echo BASE_URL; ?>perfil/editar" class="btn btn-outline">
                            <i class="fas fa-user-edit"></i> Editar Perfil
                        </a>
                        <a href="<?php echo BASE_URL; ?>chat" class="btn btn-outline">
                            <i class="fas fa-envelope"></i> Mis Mensajes
                        </a>
                        <a href="<?php echo BASE_URL; ?>perfil/favoritos" class="btn btn-outline">
                            <i class="fas fa-heart"></i> Favoritos
                        </a>
                    </div>
                </div>

                <!--<div class="sidebar-card">
                    <h3><i class="fas fa-chart-line"></i> Estadísticas</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Visitas al perfil</span>
                            <span class="info-value">1,247</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Productos vistos</span>
                            <span class="info-value"><?php echo $estadisticas['total_vistas'] ?? 0; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tasa de respuesta</span>
                            <span class="info-value">89%</span>
                        </div>
                    </div>
                </div>-->
            </div>

            <!-- Contenido Principal -->
            <div class="profile-main">
                <!-- Dashboard Cards -->
                <!--<div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Rendimiento del Mes</h3>
                            <i class="fas fa-chart-bar" style="color: var(--primary-color);"></i>
                        </div>
                        <div style="display: flex; justify-content: space-around; text-align: center;">
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">12</div>
                                <div style="font-size: 0.8rem; color: var(--text-light);">Ventas</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success-color);">S/ 1,240</div>
                                <div style="font-size: 0.8rem; color: var(--text-light);">Ingresos</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--warning-color);"><?php echo $estadisticas['total_contactos'] ?? 0; ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-light);">Consultas</div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Actividad Reciente</h3>
                            <i class="fas fa-bell" style="color: var(--warning-color);"></i>
                        </div>
                        <div style="color: var(--text-light); font-size: 0.9rem;">
                            <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-eye" style="color: var(--primary-color);"></i> 
                                Tu producto "Laptop Gamer" tuvo 15 visitas
                            </div>
                            <div style="padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-comment" style="color: var(--success-color);"></i> 
                                Nuevo mensaje sobre "Silla Ergonómica"
                            </div>
                            <div style="padding: 0.5rem 0;">
                                <i class="fas fa-star" style="color: var(--warning-color);"></i> 
                                Recibiste 5 estrellas en tu valoración
                            </div>
                        </div>
                    </div>
                </div>-->

                <!-- Pestañas -->
                <div class="tabs-container">
                    <div class="tabs-header">
                        <button class="tab-button active" data-tab="publicaciones">
                            <i class="fas fa-box-open"></i> Mis Publicaciones
                        </button>
                        <!--<button class="tab-button" data-tab="analiticas">
                            <i class="fas fa-chart-pie"></i> Analíticas
                        </button>-->
                        <button class="tab-button" data-tab="favoritos">
                            <i class="fas fa-heart"></i> Favoritos
                        </button>
                        <button class="tab-button" data-tab="mensajes">
                            <i class="fas fa-envelope"></i> Mensajes
                        </button>
                        <!--<button class="tab-button" data-tab="configuracion">
                            <i class="fas fa-cog"></i> Configuración
                        </button>-->
                    </div>

                    <div class="tab-content">
                        <!-- Publicaciones -->
                        <div id="publicaciones" class="tab-pane active">
                            <div class="section-header">
                                <h2 class="section-title">
                                    <i class="fas fa-box-open"></i> Mis Publicaciones
                                </h2>
                                <div class="filter-bar">
                                    <select class="filter-select" id="estado-filter">
                                        <option value="all">Todas las publicaciones</option>
                                        <option value="1">Activas</option>
                                        <option value="2">Pausadas</option>
                                        <option value="3">Inactivas</option>
                                    </select>
                                    <select class="filter-select">
                                        <option value="newest">Más recientes</option>
                                        <option value="oldest">Más antiguas</option>
                                        <option value="popular">Más populares</option>
                                    </select>
                                </div>
                            </div>

                            <?php if (empty($publicaciones)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h3>No tienes publicaciones</h3>
                                    <p>Comienza a publicar tus productos o servicios para la comunidad universitaria</p>
                                    <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear primera publicación
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="publicaciones-grid">
                                    <?php foreach ($publicaciones as $publicacion): ?>
                                        <div class="publicacion-card" data-estado="<?php echo $publicacion['estado']; ?>">
                                            <div class="publicacion-image">
                                                <?php 
                                                // Obtener la URL final de la imagen
                                                $imgFinal = obtenerImagenFinal($publicacion['imagen'] ?? null);
                                                ?>
                                                
                                                <?php if (!empty($imgFinal)): ?>
                                                    <img src="<?php echo htmlspecialchars($imgFinal); ?>" 
                                                        alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-image"></i>
                                                        <div>Sin imagen</div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="publicacion-content">
                                                <div class="publicacion-header">
                                                    <h3 class="publicacion-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
                                                    <div class="publicacion-precio">S/ <?php echo number_format($publicacion['precio'], 2); ?></div>
                                                </div>
                                                
                                                <p class="publicacion-desc"><?php echo htmlspecialchars(substr($publicacion['descripcion'], 0, 150)); ?>...</p>
                                                
                                                <div class="publicacion-meta">
                                                    <span class="meta-tag"><?php echo htmlspecialchars($publicacion['nombre_categoria']); ?></span>
                                                    <span class="meta-tag"><?php echo $publicacion['tipo']; ?></span>
                                                    <span class="publicacion-status status-<?php echo $publicacion['estado'] == 1 ? 'active' : ($publicacion['estado'] == 2 ? 'paused' : 'inactive'); ?>">
                                                        <?php 
                                                        switch($publicacion['estado']) {
                                                            case 1: echo 'Activo'; break;
                                                            case 2: echo 'Pausado'; break;
                                                            default: echo 'Desconocido';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="publicacion-footer">
                                                    <div class="publicacion-actions">
                                                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo BASE_URL; ?>publicaciones/editar/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($publicacion['estado'] == 1): ?>
                                                            <button class="btn btn-outline btn-sm btn-pausar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        <?php elseif ($publicacion['estado'] == 2): ?>
                                                            <button class="btn btn-outline btn-sm btn-reactivar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button class="btn btn-outline btn-sm btn-eliminar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Otras pestañas (simuladas) -->
                        <div id="analiticas" class="tab-pane">
                            <div class="empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <h3>Análisis de Rendimiento</h3>
                                <p>Aquí podrás ver estadísticas detalladas sobre el rendimiento de tus publicaciones</p>
                                <button class="btn btn-primary">
                                    <i class="fas fa-chart-line"></i> Ver Reporte Completo
                                </button>
                            </div>
                        </div>

                        <div id="favoritos" class="tab-pane">
                            <?php if (empty($favoritos)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-heart"></i>
                                    <h3>No tienes favoritos aún</h3>
                                    <p>Los productos y servicios que guardes como favoritos aparecerán aquí. Es una forma fácil de mantener un registro de lo que te interesa.</p>
                                    <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Explorar Productos
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="publicaciones-grid">
                                    <?php foreach ($favoritos as $favorito): ?>
                                        <div class="publicacion-card">
                                            <div class="publicacion-image">
                                                <?php 
                                                // Obtener la URL final de la imagen principal
                                                $imgFinal = obtenerImagenFinal($favorito['imagen_principal'] ?? null);
                                                ?>
                                                
                                                <?php if (!empty($imgFinal)): ?>
                                                    <img src="<?php echo htmlspecialchars($imgFinal); ?>" 
                                                        alt="<?php echo htmlspecialchars($favorito['titulo']); ?>">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-image"></i>
                                                        <div>Sin imagen</div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="publicacion-content">
                                                <div class="publicacion-header">
                                                    <h3 class="publicacion-title">
                                                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" style="color: inherit; text-decoration: none;">
                                                            <?php echo htmlspecialchars($favorito['titulo']); ?>
                                                        </a>
                                                    </h3>
                                                    <div class="publicacion-precio">S/ <?php echo number_format($favorito['precio'], 2); ?></div>
                                                </div>
                                                <p class="publicacion-desc"><?php echo htmlspecialchars(substr($favorito['descripcion'], 0, 100)); ?>...</p>
                                                <div class="publicacion-meta">
                                                    <span class="meta-tag"><?php echo htmlspecialchars($favorito['nombre_categoria']); ?></span>
                                                    <span class="meta-tag"><?php echo $favorito['tipo']; ?></span>
                                                </div>
                                                <div class="publicacion-footer">
                                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                    </a>
                                                    <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-favorito" style="display: inline;" class="remove-favorite-form">
                                                        <input type="hidden" name="publicacion_id" value="<?php echo $favorito['id_publicacion']; ?>">
                                                        <button type="submit" class="btn btn-outline btn-sm" title="Quitar de favoritos">
                                                            <i class="fas fa-heart-broken"></i> Quitar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div id="mensajes" class="tab-pane">
                            <div class="empty-state">
                                <i class="fas fa-envelope"></i>
                                <h3>Bandeja de Mensajes</h3>
                                <p>Gestiona tus conversaciones con otros miembros de la comunidad</p>
                                <a href="<?php echo BASE_URL; ?>chat" class="btn btn-primary">
                                    <i class="fas fa-inbox"></i> Ver Mensajes
                                </a>
                            </div>
                        </div>

                        <div id="configuracion" class="tab-pane">
                            <div class="empty-state">
                                <i class="fas fa-cog"></i>
                                <h3>Configuración de Cuenta</h3>
                                <p>Personaliza tu experiencia en la plataforma y gestiona tus preferencias</p>
                                <a href="<?php echo BASE_URL; ?>perfil/configuracion" class="btn btn-primary">
                                    <i class="fas fa-sliders-h"></i> Configurar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 UniEmprende. Plataforma universitaria de emprendimiento.</p>
            </div>
        </div>
    </footer>

    <!-- Formularios ocultos para acciones -->
    <form id="form-cambiar-estado" action="<?php echo BASE_URL; ?>publicaciones/cambiarestado" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="estado-publicacion-id">
        <input type="hidden" name="nuevo_estado" id="estado-nuevo">
    </form>

    <form id="form-eliminar" action="<?php echo BASE_URL; ?>publicaciones/eliminar" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="eliminar-publicacion-id">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sistema de pestañas
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remover active de todos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Agregar active al seleccionado
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Filtro por estado
            const estadoFilter = document.getElementById('estado-filter');
            const publicacionCards = document.querySelectorAll('.publicacion-card');
            
            if (estadoFilter) {
                estadoFilter.addEventListener('change', function() {
                    const estado = this.value;
                    
                    publicacionCards.forEach(card => {
                        if (estado === 'all' || card.getAttribute('data-estado') === estado) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Efectos hover para tarjetas
            const statCards = document.querySelectorAll('.stat-card, .dashboard-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formularios de acciones
            const formCambiarEstado = document.getElementById('form-cambiar-estado');
            const formEliminar = document.getElementById('form-eliminar');

            // Eventos para pausar/reactivar
            document.querySelectorAll('.btn-pausar, .btn-reactivar').forEach(button => {
                button.addEventListener('click', function() {
                    const publicacionId = this.dataset.id;
                    const esPausar = this.classList.contains('btn-pausar');
                    const nuevoEstado = esPausar ? 2 : 1; // 2 para pausado, 1 para activo
                    
                    const confirmacion = confirm(
                        `¿Estás seguro de que quieres ${esPausar ? 'pausar' : 'reactivar'} esta publicación?`
                    );

                    if (confirmacion) {
                        document.getElementById('estado-publicacion-id').value = publicacionId;
                        document.getElementById('estado-nuevo').value = nuevoEstado;
                        formCambiarEstado.submit();
                    }
                });
            });

            // Evento para eliminar
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function() {
                    const publicacionId = this.dataset.id;
                    
                    const confirmacion = confirm(
                        '¿Estás seguro de que quieres eliminar esta publicación? Esta acción no se puede deshacer.'
                    );

                    if (confirmacion) {
                        document.getElementById('eliminar-publicacion-id').value = publicacionId;
                        formEliminar.submit();
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobileMenuBtn');
            const navLinks = document.getElementById('navLinks');
            const overlay = document.getElementById('menuOverlay');
            const icon = menuBtn ? menuBtn.querySelector('i') : null;

            if (menuBtn && navLinks && overlay) {
                function toggleMenu() {
                    navLinks.classList.toggle('active');
                    overlay.classList.toggle('active');
                    
                    // Cambiar ícono de hamburguesa a X
                    if (navLinks.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }

                menuBtn.addEventListener('click', toggleMenu);
                overlay.addEventListener('click', toggleMenu);
            }
        });
    </script>
</body>
</html>
