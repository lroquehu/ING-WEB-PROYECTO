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

    // Mapeo de abreviaturas a nombres completos para facultades y escuelas
    $facultades_map = [
        'FAIN' => 'FACULTAD DE INGENIERIA',
        'FCJE' => 'FACULTAD DE CIENCIAS JURIDICAS Y EMPRESARIALES',
        'FCAG' => 'FACULTAD DE CIENCIAS AGROPECUARIAS',
        'FACS' => 'FACULTAD DE CIENCIAS DE LA SALUD',
        'FECH' => 'FACULTAD DE EDUCACION, COMUNICACION Y HUMANIDADES',
        'FACI' => 'FACULTAD DE CIENCIAS',
        'FIAG' => 'FACULTAD DE INGENIERIA CIVIL, ARQUITECTURA Y GEOTECNIA'
    ];

    $escuelas_map = [
        'FAIN' => [
            'ESMI' => 'Ingeniería de Minas',
            'ESIS' => 'Ingeniería en Informática y Sistemas',
            'ESME' => 'Ingeniería Metalúrgica',
            'ESIQ' => 'Ingeniería Química',
            'ESMC' => 'Ingeniería Mecánica'
        ],
        'FCJE' => [
            'ESCF' => 'Ciencias Contables y Financieras',
            'ESAD' => 'Ciencias Administrativas',
            'ESDE' => 'Derecho y Ciencias Políticas',
            'ESCO' => 'Ingeniería Comercial'
        ],
        'FCAG' => [
            'ESAG' => 'Agronomía',
            'ESEA' => 'Economía Agraria',
            'EMVZ' => 'Medicina Veterinaria y Zootecnia',
            'ESIP' => 'Ingeniería Pesquera',
            'ESIA' => 'Ingeniería en Industrias Alimentarias',
            'ESAM' => 'Ingeniería Ambiental'
        ],
        'FACS' => [
            'ESMH' => 'Medicina Humana',
            'ESOB' => 'Obstetricia',
            'ESEN' => 'Enfermería',
            'ESOD' => 'Odontología',
            'ESFB' => 'Farmacia y Bioquímica'
        ],
        'FECH' => [
            'ESCC' => 'Ciencias de la Comunicación',
            'ESHI' => 'Historia',
            'IETI' => 'Educación: Idioma Extranjero',
            'LEGE' => 'Educación: Lengua y Literatura',
            'MACI' => 'Educación: Matemática, Computación e Informática',
            'NATA' => 'Educación: Ciencias de la Naturaleza y Promoción Educativa Ambiental',
            'SPRO' => 'Educación: Ciencias Sociales y Promoción Socio Cultural',
            'ESEI' => 'Educación: Educación Inicial',
            'ESEP' => 'Educación: Educación Primaria',
            'ESPS' => 'Psicología'
        ],
        'FACI' => ['ESBM' => 'Biología - Microbiología', 'ESFI' => 'Física Aplicada', 'ESMA' => 'Matemáticas'],
        'FIAG' => ['ESAQ' => 'Arquitectura', 'ESIC' => 'Ingeniería Civil', 'ESGE' => 'Ingeniería Geológica - Geotecnia', 'ESAR' => 'Artes']
    ];

    $nombre_completo_facultad = $facultades_map[$usuario['facultad'] ?? ''] ?? 'Sin facultad';
    $nombre_completo_escuela = $escuelas_map[$usuario['facultad'] ?? ''][$usuario['escuela'] ?? ''] ?? 'Sin escuela';

    $page_title = 'Mi Perfil - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php';
?>

    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #700101;
            --primary-light: rgba(145, 2, 2, 0.08);
            --secondary-color: #2c3e50;
            --accent-color: #ffc107;
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
        /* Corrección para eliminar fondo transparente del header */
        body::before {
            display: none;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
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
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
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
            .profile-actions {
                flex-direction: row;
                justify-content: center;
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .main-header{
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

        /* Modal de Confirmación */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 450px;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.visible .modal-content {
            transform: scale(1);
        }

        .modal-content h3 { margin-bottom: 1rem; font-size: 1.5rem; color: var(--text-color); }
        .modal-content p { margin-bottom: 2rem; color: var(--text-light); font-size: 1.1rem; line-height: 1.5; }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
    </style>

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
                            <span title="<?php echo htmlspecialchars($nombre_completo_facultad); ?>">
                                <?php echo htmlspecialchars($usuario['facultad'] ?? 'Sin facultad'); ?>
                            </span>
                        </div>
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
                    <a href="<?php echo BASE_URL; ?>perfil/publicaciones" class="btn btn-outline">
                        <i class="fas fa-tasks"></i>
                        Gestionar Publicaciones
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/editar" class="btn btn-outline">
                        <i class="fas fa-edit"></i>
                        Editar Perfil
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/ventas" class="btn btn-outline">
                        <i class="fas fa-cash-register me-2"></i> Mis Ventas
                    </a>
                    <a href="<?php echo BASE_URL; ?>perfil/mis-compras" class="btn btn-outline">
                        <i class="fas fa-shopping-bag"></i> Mis Compras
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        
        <?php 
        // Si viene un código 'success' en la URL, definimos el mensaje personalizado
        if (isset($_GET['success'])) {
            switch ($_GET['success']) {
                case '1':
                    $mensaje_exito = "Cambios en el perfil hechos correctamente.";
                    break;
                case '2':
                    $mensaje_exito = "Contraseña cambiada correctamente.";
                    break;
                case '3':
                    $mensaje_exito = "Publicación creada exitosamente.";
                    break;
                case '4':
                    $mensaje_exito = "Publicación actualizada exitosamente.";
                    break;
            }
        }
        ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje_exito); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
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
                            <span class="info-value" title="<?php echo htmlspecialchars($nombre_completo_facultad); ?>">
                                <?php echo !empty($usuario['facultad']) ? htmlspecialchars($usuario['facultad']) : 'No especificada'; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-school"></i> Escuela
                            </span>
                            <span class="info-value" title="<?php echo htmlspecialchars($nombre_completo_escuela); ?>">
                                <?php echo !empty($usuario['escuela']) ? htmlspecialchars($usuario['escuela']) : 'No especificada'; ?>
                            </span>
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
                        <a href="<?php echo BASE_URL; ?>perfil/publicaciones" class="btn btn-outline">
                            <i class="fas fa-tasks"></i> Gestionar Publicaciones
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

                </div>

            <div class="profile-main">
                <div class="tabs-container">
                    <div class="tabs-header">
                        <button class="tab-button active" data-tab="publicaciones">
                            <i class="fas fa-box-open"></i> Mis Publicaciones
                        </button>
                        <button class="tab-button" data-tab="favoritos">
                            <i class="fas fa-heart"></i> Favoritos
                        </button>
                        <button class="tab-button" data-tab="mensajes">
                            <i class="fas fa-envelope"></i> Mensajes
                        </button>
                        </div>

                    <div class="tab-content">
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
                                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="publicacion-image">
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
                                            </a>
                                            
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
                                                            case 0: echo 'Inactivo'; break;
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
                                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" class="publicacion-image">
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
                                            </a>
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

    <form id="form-cambiar-estado" action="<?php echo BASE_URL; ?>publicaciones/cambiarestado" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="estado-publicacion-id">
        <input type="hidden" name="nuevo_estado" id="estado-nuevo">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    </form>

    <form id="form-eliminar" action="<?php echo BASE_URL; ?>publicaciones/eliminar" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="eliminar-publicacion-id">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    </form>

    <div id="confirmation-modal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="modal-title">Confirmar Acción</h3>
            <p id="modal-text">¿Estás seguro?</p>
            <div class="modal-actions">
                <button id="modal-cancel-btn" class="btn btn-outline">Cancelar</button>
                <button id="modal-confirm-btn" class="btn">Confirmar</button>
            </div>
        </div>
    </div>

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

            // --- Lógica del Modal y Acciones ---
            const formCambiarEstado = document.getElementById('form-cambiar-estado');
            const formEliminar = document.getElementById('form-eliminar');
            const modal = document.getElementById('confirmation-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalText = document.getElementById('modal-text');
            const modalConfirmBtn = document.getElementById('modal-confirm-btn');
            const modalCancelBtn = document.getElementById('modal-cancel-btn');
            let confirmAction = null;

            function showModal(title, text, confirmBtnClass, confirmBtnText, action) {
                modalTitle.textContent = title;
                modalText.textContent = text;
                modalConfirmBtn.className = 'btn'; // Reset
                modalConfirmBtn.classList.add(confirmBtnClass);
                modalConfirmBtn.innerHTML = confirmBtnText;
                modal.classList.add('visible');
                confirmAction = action;
            }

            function hideModal() {
                modal.classList.remove('visible');
                confirmAction = null;
            }

            modalConfirmBtn.addEventListener('click', () => {
                if (typeof confirmAction === 'function') {
                    confirmAction();
                    hideModal(); // Ocultar modal después de confirmar
                }
            });

            modalCancelBtn.addEventListener('click', hideModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) hideModal();
            });

            // Eventos para pausar/reactivar
            document.querySelectorAll('.btn-pausar, .btn-reactivar').forEach(button => {
                button.addEventListener('click', function() {
                    const publicacionId = this.dataset.id;
                    const esPausar = this.classList.contains('btn-pausar');
                    const nuevoEstado = esPausar ? 2 : 1;
                    const accionTexto = esPausar ? 'pausar' : 'reactivar';
                    const btnClass = esPausar ? 'btn-warning' : 'btn-success';
                    const btnText = esPausar ? `Sí, ${accionTexto}` : `Sí, ${accionTexto}`;

                    showModal(`Confirmar ${accionTexto}`, `¿Estás seguro de que quieres ${accionTexto} esta publicación?`, btnClass, btnText, () => {
                        document.getElementById('estado-publicacion-id').value = publicacionId;
                        document.getElementById('estado-nuevo').value = nuevoEstado;
                        formCambiarEstado.submit();
                    });
                });
            });

            // Evento para eliminar
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function() {
                    const publicacionId = this.dataset.id;
                    showModal('Confirmar Eliminación', '¿Estás seguro de que quieres eliminar esta publicación? Esta acción cambiará su estado a "Eliminado" y no se podrá deshacer.', 'btn-danger', 'Sí, eliminar', () => {
                        document.getElementById('eliminar-publicacion-id').value = publicacionId;
                        formEliminar.submit();
                    });
                });
            });
        });
    </script>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>