<?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticación
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

    // --- CAMBIO 1: Definir título e incluir el HEADER global ---
    $page_title = 'Mi Perfil - UniEmprende';
    include __DIR__ . '/../plantillas/header.php';
?>

<style>
    /* Ajuste del container para esta vista */
    .container-profile {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    /* Banner del Perfil (Diferente al Main Header) */
    .profile-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 3rem 0;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 3rem;
        margin-top: -1rem; /* Ajuste para pegar con el header global */
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
        box-shadow: var(--shadow);
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
        color: var(--text-dark);
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
        border-radius: 12px;
        border: 1px solid var(--border-color);
        text-align: center;
        transition: var(--transition);
    }
    
    .stat-card:hover {
        box-shadow: var(--shadow-hover);
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
    
    /* Layout Principal */
    .main-content-profile {
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
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .sidebar-card h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
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
        color: var(--text-dark);
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
        border-radius: 12px;
        overflow: hidden;
    }
    
    .tabs-header {
        display: flex;
        background: var(--bg-light);
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
        color: var(--text-dark);
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
    
    /* Publicaciones Grid */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
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
        border-radius: 8px;
        background: white;
        font-family: inherit;
        font-size: 0.9rem;
        min-width: 160px;
    }
    
    .publicaciones-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    
    .publicacion-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .publicacion-card:hover {
        box-shadow: var(--shadow-hover);
        border-color: var(--primary-color);
    }
    
    .publicacion-image {
        width: 100%;
        height: 200px;
        background: var(--bg-light);
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
        color: var(--text-dark);
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
        background: var(--bg-light);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .publicacion-status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-active { background: #d4f4e6; color: #27ae60; }
    .status-paused { background: #fff3cd; color: #f39c12; }
    .status-inactive { background: #fde8e8; color: #e74c3c; }
    
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
    
    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-light);
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: var(--border-color);
        opacity: 0.5;
    }
    
    .empty-state h3 {
        margin-bottom: 1rem;
        color: var(--text-dark);
        font-size: 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .main-content-profile {
            grid-template-columns: 240px 1fr;
        }
        .profile-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        .publicaciones-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .main-content-profile {
            grid-template-columns: 1fr;
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
        .container-profile {
            padding: 0 1rem;
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
        .profile-content-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.5rem;
        }
        .profile-avatar {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .profile-meta {
            justify-content: center;
            gap: 1rem;
        }
        .profile-bio {
            margin-left: auto;
            margin-right: auto;
        }
        .profile-actions {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            flex-direction: column !important;
            gap: 0.8rem;
        }
        .profile-actions .btn {
            width: 100%;
            justify-content: center;
        }
        .profile-sidebar {
            width: 100%;
            order: 2;
        }
        .profile-main {
            order: 1;
        }
        .publicacion-actions {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="profile-banner">
    <div class="container-profile">
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

<div class="container-profile">
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

    <div class="main-content-profile">
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
                                                        case 0: echo 'Inactiva'; break;
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

                    <div id="favoritos" class="tab-pane">
                        <div class="empty-state">
                            <i class="fas fa-heart"></i>
                            <h3>Tus Favoritos</h3>
                            <p>Gestiona los productos que has guardado.</p>
                            <a href="<?php echo BASE_URL; ?>perfil/favoritos" class="btn btn-primary">
                                Ver Favoritos
                            </a>
                        </div>
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
                </div>
            </div>
        </div>
    </div>
</div>

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
                const pane = document.getElementById(tabId);
                if (pane) pane.classList.add('active');
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

        // Formularios de acciones
        const formCambiarEstado = document.getElementById('form-cambiar-estado');
        const formEliminar = document.getElementById('form-eliminar');

        // Eventos para pausar/reactivar
        document.querySelectorAll('.btn-pausar, .btn-reactivar').forEach(button => {
            button.addEventListener('click', function() {
                const publicacionId = this.dataset.id;
                const esPausar = this.classList.contains('btn-pausar');
                const nuevoEstado = esPausar ? 2 : 1; 
                
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
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>