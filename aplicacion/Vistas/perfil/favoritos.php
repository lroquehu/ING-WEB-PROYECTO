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
    $favoritos = $favoritos ?? [];
    $error = $error ?? '';

    $page_title = 'Mis Favoritos - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php';
?>

<style>
    /* --- CONFIGURACIÓN PRINCIPAL --- */
    .main-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        min-height: 80vh;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .page-header h1 {
        font-size: 2rem;
        color: #2c3e50;
        font-weight: 700;
        margin: 0;
    }

    .counter-badge {
        background: #f8f9fa;
        color: #910202;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: bold;
        border: 1px solid #ddd;
    }

    /* --- BOTÓN VOLVER (Fixed) --- */
    .back-link {
        position: fixed;
        top: 9rem;
        left: calc(50% - 700px - 5rem); /* Cálculo para posicionarlo a la izquierda del container */
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background-color: #f0f2f5;
        border-radius: 50%;
        color: var(--primary-color, #910202);
        font-size: 1.2rem;
        text-decoration: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background-color: #e4e6e9;
        transform: scale(1.05);
    }

    /* Ajuste responsive para el botón volver */
    @media (max-width: 1600px) {
        .back-link {
            left: 2rem; /* Posición fija en pantallas medianas */
        }
    }
    @media (max-width: 768px) {
        .back-link {
            display: none; /* Ocultar en móviles para no estorbar */
        }
    }

    /* --- GRID DE 3 COLUMNAS --- */
    .favoritos-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 columnas fijas */
        gap: 2rem;
        align-items: stretch; /* Estira las tarjetas para misma altura */
    }

    /* --- TARJETA (CARD) --- */
    .card-item {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        border-color: #ddd;
    }

    /* --- IMAGEN --- */
    .card-image-box {
        position: relative;
        width: 100%;
        height: 220px; /* Altura fija obligatoria */
        background-color: #f4f4f4;
        overflow: hidden;
    }

    .card-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card-item:hover .card-image-box img {
        transform: scale(1.05);
    }

    .badge-type {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #910202;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* --- CUERPO DE LA TARJETA --- */
    .card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        gap: 10px;
    }

    .card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-title a {
        text-decoration: none;
        color: inherit;
        transition: color 0.2s;
    }
    .card-title a:hover { color: #910202; }

    .card-price {
        font-size: 1.25rem;
        font-weight: 800;
        color: #910202;
        white-space: nowrap;
    }

    .card-category {
        display: inline-block;
        font-size: 0.8rem;
        color: #666;
        background-color: #f0f2f5;
        padding: 2px 8px;
        border-radius: 4px;
        margin-bottom: 0.8rem;
        align-self: flex-start;
    }

    /* Descripción corta */
    .card-desc {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.5;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Máximo 3 líneas */
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    /* --- VENDEDOR --- */
    .card-vendor {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
        margin-bottom: 1rem;
    }

    .vendor-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e1e1e1;
    }

    .vendor-link {
        font-size: 0.9rem;
        color: #666;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .vendor-link:hover {
        color: #910202;
        text-decoration: underline;
    }

    /* --- BOTONES --- */
    .card-actions {
        display: flex;
        gap: 0.8rem;
        margin-top: auto;
    }

    .btn-view {
        flex: 1;
        background-color: white;
        border: 1px solid #ccc;
        color: #333;
        padding: 0.7rem 1rem;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-view:hover {
        background-color: #f8f9fa;
        border-color: #999;
        color: #000;
    }

    .btn-delete {
        width: 44px;
        height: 44px;
        border: 1px solid #ffcccc;
        background-color: #fff5f5;
        color: #dc3545;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    /* Estado vacío */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .favoritos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .favoritos-grid {
            grid-template-columns: 1fr;
        }
        .main-container { padding: 1rem; }
    }
</style>

<a href="<?php echo BASE_URL; ?>perfil" class="back-link" title="Volver a mi perfil">
    <i class="fas fa-arrow-left"></i>
</a>

<div class="main-container">
    
    <div class="page-header">
        <h1>Mis Favoritos</h1>
        <div class="counter-badge">
            <?php echo count($favoritos); ?> Guardados
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background:#fff3f3; color:#d63031; padding:1rem; border-radius:8px; margin-bottom:1rem; border:1px solid #ffdede;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($favoritos)): ?>
        <div class="empty-state">
            <i class="fas fa-heart-broken" style="font-size:4rem; color:#ddd; margin-bottom:1rem;"></i>
            <h3 style="color:#555; margin-bottom: 0.5rem;">Aún no tienes favoritos</h3>
            <p style="color:#777; margin-bottom:2rem;">Guarda los productos que te interesen para verlos más tarde.</p>
            <a href="<?php echo BASE_URL; ?>publicaciones" style="display:inline-block; background:#910202; color:white; padding:0.8rem 2rem; text-decoration:none; border-radius:8px; font-weight:600; transition: background 0.3s;">
                Explorar publicaciones
            </a>
        </div>
    <?php else: ?>
        <div class="favoritos-grid">
            <?php foreach ($favoritos as $favorito): ?>
                <div class="card-item">
                    <div class="card-image-box">
                        <span class="badge-type"><?php echo htmlspecialchars($favorito['tipo'] ?? 'Producto'); ?></span>
                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>">
                            <?php 
                            $imgFinal = !empty($favorito['imagen_principal']) ? obtenerImagenFinal($favorito['imagen_principal']) : '';
                            ?>
                            <?php if ($imgFinal): ?>
                                <img src="<?php echo htmlspecialchars($imgFinal); ?>" alt="Imagen">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#aaa;">
                                    <i class="fas fa-image" style="font-size:3rem; margin-bottom:0.5rem; opacity:0.5;"></i>
                                    <span style="font-size:0.8rem;">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="card-header-row">
                            <h3 class="card-title" title="<?php echo htmlspecialchars($favorito['titulo'] ?? ''); ?>">
                                <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>">
                                    <?php echo htmlspecialchars($favorito['titulo'] ?? 'Sin título'); ?>
                                </a>
                            </h3>
                            <span class="card-price">
                                S/ <?php echo number_format($favorito['precio'] ?? 0, 2); ?>
                            </span>
                        </div>

                        <span class="card-category">
                            <?php echo htmlspecialchars($favorito['nombre_categoria'] ?? 'General'); ?>
                        </span>

                        <div class="card-desc">
                            <?php 
                                $desc = $favorito['descripcion'] ?? 'Sin descripción';
                                echo htmlspecialchars(strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc); 
                            ?>
                        </div>

                        <div class="card-vendor">
                            <img src="<?php echo !empty($favorito['foto_perfil']) ? obtenerImagenFinal($favorito['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" 
                                 class="vendor-avatar" alt="User">
                            
                            <?php 
                                // ID del vendedor (asegurado por el cambio en el modelo)
                                $idVendedor = $favorito['id_usuario'] ?? 0;
                                $nombreVendedor = ($favorito['nombres'] ?? 'Usuario') . ' ' . ($favorito['apellidos'] ?? '');
                                $nombreVendedor = trim($nombreVendedor) ?: 'Usuario Desconocido';
                            ?>
                            <a href="<?php echo BASE_URL; ?>perfil/ver/<?php echo $idVendedor; ?>" class="vendor-link" title="Ver perfil del vendedor">
                                <?php echo htmlspecialchars($nombreVendedor); ?>
                            </a>
                        </div>

                        <div class="card-actions">
                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                            
                            <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-favorito">
                                <input type="hidden" name="publicacion_id" value="<?php echo $favorito['id_publicacion']; ?>">
                                <button type="submit" class="btn-delete" title="Eliminar de favoritos" onclick="return confirm('¿Quitar de mis favoritos?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>