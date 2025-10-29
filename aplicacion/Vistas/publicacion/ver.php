<?php include __DIR__ . '\..\plantillas\encabezado.php'; ?>

<style>
    .product-view-container {
        padding: 2rem 0;
        min-height: calc(100vh - 200px);
    }

    .breadcrumb {
        margin-bottom: 2rem;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .product-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 968px) {
        .product-detail {
            grid-template-columns: 1fr;
        }
    }

    .product-gallery {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .main-image {
        width: 100%;
        height: 400px;
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        text-align: center;
        color: #94a3b8;
    }

    .no-image i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .image-thumbnails {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s;
    }

    .thumbnail.active,
    .thumbnail:hover {
        border-color: var(--primary);
    }

    .product-info {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .product-header {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .product-category {
        background: var(--primary);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .product-type {
        background: #e5e7eb;
        color: #374151;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .product-title {
        font-size: 2rem;
        color: #1f2937;
        margin: 0;
    }

    .product-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .product-meta {
        display: flex;
        gap: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
    }

    .product-description h3,
    .contact-info h3 {
        color: #374151;
        margin-bottom: 1rem;
        font-size: 1.2rem;
    }

    .contact-details {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .contact-item i {
        color: var(--primary);
        width: 20px;
    }

    .product-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .btn-large {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .owner-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .seller-info {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 3rem;
    }

    .seller-info h3 {
        color: #374151;
        margin-bottom: 1.5rem;
    }

    .seller-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.5rem;
        align-items: center;
    }

    .seller-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .seller-details h4 {
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .seller-email {
        color: #6b7280;
        margin-bottom: 0.3rem;
    }

    .seller-university,
    .seller-school {
        color: #374151;
        font-weight: 500;
    }

    .seller-stats {
        display: flex;
        gap: 2rem;
    }

    .stat {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .similar-products h3 {
        color: #374151;
        margin-bottom: 1.5rem;
    }

    .similar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .similar-product {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .similar-product:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .similar-image {
        height: 150px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .similar-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image-small {
        color: #94a3b8;
    }

    .no-image-small i {
        font-size: 2rem;
    }

    .similar-info {
        padding: 1rem;
    }

    .similar-info h4 {
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        line-height: 1.4;
    }

    .similar-price {
        color: var(--primary);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .error-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .error-state i {
        font-size: 4rem;
        color: #f59e0b;
        margin-bottom: 1.5rem;
    }

    .error-state h3 {
        color: #374151;
        margin-bottom: 1rem;
    }

    .error-state p {
        color: #6b7280;
        margin-bottom: 2rem;
    }
</style>

<div class="product-view-container">
    <div class="container">
        <?php if ($publicacion && empty($error)): ?>
            <!-- Ruta de navegación -->
            <nav class="breadcrumb">
                <a href="<?php echo BASE_URL; ?>">Inicio</a> >
                <a href="<?php echo BASE_URL; ?>producto">Productos</a> >
                <a href="<?php echo BASE_URL; ?>producto/categorias/<?php echo $publicacion['id_categoria']; ?>">
                    <?php echo htmlspecialchars($publicacion['nombre_categoria']); ?>
                </a> >
                <span><?php echo htmlspecialchars($publicacion['titulo']); ?></span>
            </nav>

            <div class="product-detail">
                <!-- Galería de imágenes -->
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if (!empty($imagenes)): ?>
                            <img src="<?php echo htmlspecialchars($imagenes[0]); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>" id="mainImage">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>Sin imagen</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($imagenes) > 1): ?>
                    <div class="image-thumbnails">
                        <?php foreach ($imagenes as $index => $imagen): ?>
                            <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                 alt="Vista <?php echo $index + 1; ?>" 
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage('<?php echo htmlspecialchars($imagen); ?>', this)">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información del producto -->
                <div class="product-info">
                    <div class="product-header">
                        <span class="product-category"><?php echo htmlspecialchars($publicacion['nombre_categoria']); ?></span>
                        <span class="product-type"><?php echo $publicacion['tipo']; ?></span>
                    </div>
                    
                    <h1 class="product-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h1>
                    
                    <div class="product-price">
                        S/ <?php echo number_format($publicacion['precio'], 2); ?>
                    </div>

                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-eye"></i>
                            <span><?php echo $publicacion['total_vistas'] ?? 0; ?> vistas</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Publicado: <?php echo date('d/m/Y', strtotime($publicacion['fecha_publicacion'])); ?></span>
                        </div>
                    </div>

                    <div class="product-description">
                        <h3>Descripción</h3>
                        <p><?php echo nl2br(htmlspecialchars($publicacion['descripcion'])); ?></p>
                    </div>

                    <!-- Información de contacto -->
                    <div class="contact-info">
                        <h3>Información de contacto</h3>
                        <div class="contact-details">
                            <?php if (!empty($publicacion['telefono_contacto'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($publicacion['telefono_contacto']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($publicacion['correo_contacto'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($publicacion['correo_contacto']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="product-actions">
                        <button class="btn btn-primary btn-large" onclick="contactSeller()">
                            <i class="fas fa-comments"></i> Contactar al Vendedor
                        </button>
                        <button class="btn btn-outline" onclick="addToFavorites()">
                            <i class="far fa-heart"></i> Guardar en Favoritos
                        </button>
                        
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $publicacion['id_usuario']): ?>
                        <div class="owner-actions">
                            <a href="<?php echo BASE_URL; ?>producto/editar/<?php echo $publicacion_id; ?>" class="btn btn-outline">
                                <i class="fas fa-edit"></i> Editar Publicación
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información del vendedor -->
            <div class="seller-info">
                <h3>Información del Vendedor</h3>
                <div class="seller-card">
                    <div class="seller-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="seller-details">
                        <h4><?php echo htmlspecialchars($publicacion['nombres'] . ' ' . $publicacion['apellidos']); ?></h4>
                        <p class="seller-email"><?php echo htmlspecialchars($publicacion['correo_institucional']); ?></p>
                        <?php if (!empty($publicacion['facultad'])): ?>
                        <p class="seller-university"><?php echo htmlspecialchars($publicacion['facultad']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($publicacion['escuela'])): ?>
                        <p class="seller-school"><?php echo htmlspecialchars($publicacion['escuela']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="seller-stats">
                        <div class="stat">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Ventas</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Calificación</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos similares -->
            <?php if (!empty($productos_similares)): ?>
            <div class="similar-products">
                <h3>Productos similares</h3>
                <div class="similar-grid">
                    <?php foreach ($productos_similares as $producto): ?>
                    <a href="<?php echo BASE_URL; ?>producto/ver/<?php echo $producto['id_publicacion']; ?>" class="similar-product">
                        <div class="similar-image">
                            <?php if (!empty($producto['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['titulo']); ?>">
                            <?php else: ?>
                                <div class="no-image-small">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="similar-info">
                            <h4><?php echo htmlspecialchars($producto['titulo']); ?></h4>
                            <div class="similar-price">S/ <?php echo number_format($producto['precio'], 2); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Publicación no disponible</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="<?php echo BASE_URL; ?>producto" class="btn btn-primary">Ver todas las publicaciones</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function changeMainImage(imageSrc, element) {
        // Cambiar imagen principal
        document.getElementById('mainImage').src = imageSrc;
        
        // Actualizar thumbnails activos
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        element.classList.add('active');
    }

    function contactSeller() {
        alert('Función de contacto próximamente disponible');
        // Aquí podrías implementar:
        // - Abrir modal de contacto
        // - Enviar mensaje directo
        // - Mostrar información de contacto completa
    }

    function addToFavorites() {
        alert('Producto agregado a favoritos');
        // Aquí podrías implementar:
        // - Llamada AJAX para guardar en favoritos
        // - Actualizar interfaz
        // - Mostrar notificación
    }
</script>

<?php include __DIR__ . '\..\plantillas\pie.php'; ?>