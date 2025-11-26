<?php include __DIR__ . '/../plantillas/header.php'; ?>

<style>
    .product-view-container {
        padding: 2rem 0;
        min-height: calc(100vh - 200px);
        background: #ffffff;
    }

    .breadcrumb {
        margin-bottom: 2rem;
        font-size: 0.85rem;
        color: #666;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .breadcrumb span:last-child {
        color: #999;
    }

    /* Layout principal tipo e-commerce */
    .product-main-layout {
        display: grid;
        grid-template-columns: 480px 1fr 380px;
        gap: 2rem;
        padding: 0 20px;
        margin-bottom: 4rem;
        align-items: start;
        max-width: 1400px; /* Limita el ancho máximo para pantallas grandes */
        margin-left: auto;   /* Centra el layout */
        margin-right: auto;  /* Centra el layout */
    }

    @media (max-width: 1200px) {
        .product-main-layout {
            grid-template-columns: 400px 1fr;
        }

        .product-sidebar {
            grid-column: 1 / -1;
            margin-top: 2rem;
        }
    }

    @media (max-width: 768px) {
        .product-main-layout {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    /* Galería de imágenes estilo Shein */
    .product-gallery {
        position: sticky;
        top: 100px;
    }

    .main-image-container {
        width: 100%;
        background: #f8f8f8;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border: 1px solid #f0f0f0;
        aspect-ratio: 1 / 1; /* Forzar a que sea un cuadrado */
    }

    .main-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Cambiado a cover para que llene el espacio */
    }

    .no-image {
        text-align: center;
        color: #ccc;
        padding: 2rem;
    }

    .no-image i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .image-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }

    .thumbnail {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 6px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background: #f8f8f8;
    }

    .thumbnail.active,
    .thumbnail:hover {
        border-color: var(--primary);
        transform: scale(1.05);
    }

    /* Información principal del producto */
    .product-info-main {
        padding: 2rem 1rem; /* Añadido padding superior para bajar el contenido */
    }

    .product-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0 0 1rem 0;
        line-height: 1.4;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .rating-stars {
        color: #ffc107;
        font-size: 1.1rem;
    }

    .rating-text {
        color: #666;
        font-size: 0.9rem;
    }

    .product-price-section {
        margin-bottom: 1.5rem;
    }

    .current-price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-right: 0.5rem;
    }

    .original-price {
        font-size: 1.2rem;
        color: #999;
        text-decoration: line-through;
        margin-right: 0.5rem;
    }

    .discount-badge {
        background: #ff4444;
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Especificaciones del producto */
    .product-specs {
        margin-bottom: 2rem;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f8f8;
    }

    .spec-label {
        color: #666;
        font-weight: 500;
    }

    .spec-value {
        color: #333;
        font-weight: 600;
    }

    .product-description {
        background: #f8f8f8;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .product-description h3 {
        color: #333;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .product-description p {
        color: #666;
        line-height: 1.6;
        margin: 0;
        font-size: 0.95rem;
    }

    /* Sidebar de acciones */
    .product-sidebar {
        position: sticky;
        top: 100px;
        background: #f8f8f8;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
    }

    .price-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .price-card .current-price {
        font-size: 2.2rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .shipping-info {
        color: #00a650;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .stock-info {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn {
        padding: 1rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        text-align: center;
    }

    .btn-primary {
        background: #910202; /* Un rojo fuerte y visible */
        color: white !important; /* Asegurar que el texto sea blanco */
        font-size: 1.05rem; /* Un poco más grande */
        padding: 1.1rem 1.5rem; /* Más padding vertical */
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2); /* Sombra para destacar */
        transform: translateY(0);
        transition: all 0.25s ease;
        text-align: center; /* Asegurar centrado de texto */
    }

    .btn-primary:hover, .btn-primary:focus {
        background: #610202; /* Rojo más oscuro al pasar el cursor o al enfocar */
        color: white !important;
        transform: translateY(-2px); /* Efecto de levantamiento */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25); /* Sombra más pronunciada */
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .btn-favorite {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-favorite:hover {
        background: #f8f8f8;
        border-color: #ccc;
    }

    .btn-favorite.active {
        background: #fff5f5;
        color: var(--primary);
        border-color: var(--primary);
    }

    /* Información del vendedor */
    .seller-info {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #f0f0f0;
    }

    .seller-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .seller-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .seller-details h4 {
        color: #333;
        margin: 0 0 0.25rem 0;
        font-size: 1rem;
        font-weight: 600;
    }

    .seller-email {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
    }

    .seller-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }

    .stat {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .stat-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary);
    }

    .stat-label {
        font-size: 0.75rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Productos similares */
    .similar-products {
        padding: 0 20px;
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary);
        display: inline-block;
    }

    .similar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .similar-product {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .similar-product:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .similar-image {
        height: 180px;
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .similar-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .similar-product:hover .similar-image img {
        transform: scale(1.05);
    }

    .no-image-small {
        color: #ccc;
    }

    .no-image-small i {
        font-size: 2.5rem;
        opacity: 0.5;
    }

    .similar-info {
        padding: 1rem;
    }

    .similar-info h4 {
        color: #333;
        margin: 0 0 0.5rem 0;
        font-size: 0.95rem;
        line-height: 1.3;
        font-weight: 500;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orvent: vertical;
        overflow: hidden;
        height: 2.6em;
    }

    .similar-price {
	    color: var(--primary);
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Estado de error */
    .error-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        max-width: 500px;
        margin: 0 auto;
        border: 1px solid #f0f0f0;
    }

    .error-state i {
        font-size: 4rem;
        color: #ffc107;
        margin-bottom: 1.5rem;
        opacity: 0.8;
    }

    .error-state h3 {
        color: #333;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .error-state p {
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    /* Animaciones y estados */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .sticky-element {
        position: sticky;
        top: 100px;
        transition: all 0.3s ease;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .product-main-layout {
            padding: 0 1rem;
        }

        .breadcrumb {
            padding: 0 1rem;
        }

        .similar-products {
            padding: 0 1rem;
        }

        .image-thumbnails {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Mejoras de accesibilidad */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    .btn:focus,
    .thumbnail:focus,
    .similar-product:focus {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }

    .similar-product h4 {
        color: #333; /* Hereda el color del texto normal */
        text-decoration: none; /* Quita el subrayado si lo tuviera */
    }
</style>

<?php
$imagenes = $imagenes ?? [];
$productos_similares = $productos_similares ?? [];
?>

<div class="product-view-container">
    <div class="container">
        <?php if ($publicacion && empty($error)): ?>
            <!-- Breadcrumb simplificado -->
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="<?php echo BASE_URL; ?>">Inicio</a>
                <span>></span>
                <a href="<?php echo BASE_URL; ?>publicaciones">Publicaciones</a>
                <span>></span>
                <span><?php echo htmlspecialchars($publicacion['titulo']); ?></span>
            </nav>

            <!-- Layout principal tipo e-commerce -->
            <div class="product-main-layout fade-in">
                <!-- Galería de imágenes -->
                <div class="product-gallery sticky-element">
                    <div class="main-image-container">
                        <?php if (!empty($imagenes)): ?>
                            <img src="<?php echo htmlspecialchars($imagenes[0]); ?>" 
                                 alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>" 
                                 class="main-image"
                                 id="mainImage"
                                 loading="eager">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <p>Imagen no disponible</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($imagenes) > 1): ?>
                    <div class="image-thumbnails">
                        <?php foreach ($imagenes as $index => $imagen): ?>
                            <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                 alt="Vista <?php echo $index + 1; ?>" 
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage('<?php echo htmlspecialchars($imagen); ?>', this)"
                                 tabindex="0">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información principal del producto -->
                <div class="product-info-main">
                    <h1 class="product-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h1>

                    <div class="product-rating">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-text">4.5 (128 valoraciones)</span>
                    </div>

                    <div class="product-price-section">
                        <span class="current-price">S/ <?php echo number_format($publicacion['precio'], 2); ?></span>
                        <span class="original-price">S/ <?php echo number_format($publicacion['precio'] * 1.2, 2); ?></span>
                        <span class="discount-badge">-20%</span>
                    </div>

                    <div class="product-specs">
                        <div class="spec-item">
                            <span class="spec-label">Categoría:</span>
                            <span class="spec-value"><?php echo htmlspecialchars($publicacion['nombre_categoria']); ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Tipo:</span>
                            <span class="spec-value"><?php echo $publicacion['tipo']; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Vistas:</span>
                            <span class="spec-value"><?php echo $publicacion['total_vistas'] ?? 0; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Publicado:</span>
                            <span class="spec-value"><?php echo date('d/m/Y', strtotime($publicacion['fecha_publicacion'])); ?></span>
                        </div>
                    </div>

                    <div class="product-description">
                        <h3>Descripción del Producto</h3>
                        <p><?php echo nl2br(htmlspecialchars($publicacion['descripcion'])); ?></p>
                    </div>

                    <!-- Información del vendedor en línea principal -->
                    <div class="seller-info">
                        <div class="seller-header">
                            <div class="seller-avatar">
                                <img src="<?php echo !empty($publicacion['foto_perfil']) ? obtenerImagenFinal($publicacion['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Foto de <?php echo htmlspecialchars($publicacion['nombres']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="seller-details">
                                <h4><?php echo htmlspecialchars($publicacion['nombres'] . ' ' . $publicacion['apellidos']); ?></h4>
                                <p class="seller-email"><?php echo htmlspecialchars($publicacion['correo_institucional']); ?></p>
                            </div>
                        </div>
                        <div class="seller-stats">
                            <div class="stat">
                                <span class="stat-number"><?php echo $publicacion['total_ventas'] ?? '0'; ?></span>
                                <span class="stat-label">Ventas</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">4.8</span>
                                <span class="stat-label">Rating</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">24h</span>
                                <span class="stat-label">Respuesta</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar de acciones -->
                <div class="product-sidebar sticky-element">
                    <div class="price-card">
                        <span class="current-price">S/ <?php echo number_format($publicacion['precio'], 2); ?></span>
                        <div class="shipping-info">
                            <i class="fas fa-shipping-fast"></i> Envío gratis
                        </div>
                        <div class="stock-info">
                            <i class="fas fa-check-circle" style="color: #00a650;"></i> En stock • 15 unidades disponibles
                        </div>
                    </div>

                    <div class="action-buttons">
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $publicacion['id_usuario']): ?>
                            <a href="<?php echo BASE_URL; ?>publicaciones/editar/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline">
                                <i class="fas fa-edit"></i> Editar Publicación
                            </a>
                        <?php elseif (isset($_SESSION['usuario_id'])): ?>
                            <a href="<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $publicacion['id_usuario']; ?>" 
                               class="btn btn-primary"
                               onclick="registrarContactoYRedirigir(event, <?php echo $publicacion['id_publicacion']; ?>, '<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $publicacion['id_usuario']; ?>')">
                                <i class="fas fa-comments"></i> Contactar al Vendedor
                            </a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Inicia sesión para contactar
                            </a>
                        <?php endif; ?>

                        <?php 
                            $isFav = ($datosVista['es_favorito'] ?? false); 
                            $btnClass = $isFav ? 'btn-favorite active' : 'btn-favorite';
                        ?>
                        <button id="favBtn" class="btn <?php echo $btnClass; ?>" 
                                onclick="handleAddToFavorites(<?php echo $publicacion['id_publicacion']; ?>)">
                            <i class="<?php echo $isFav ? 'fas' : 'far'; ?> fa-heart" id="favIcon"></i> 
                            <span id="favText"><?php echo $isFav ? 'En favoritos' : 'Agregar a favoritos'; ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Productos similares -->
            <?php if (!empty($productos_similares)): ?>
            <div class="similar-products fade-in">
                <h2 class="section-title">Productos Similares</h2>
                <div class="similar-grid">
                    <?php foreach ($productos_similares as $producto): ?>
                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $producto['id_publicacion']; ?>" class="similar-product">
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
            <div class="error-state fade-in">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Publicación no disponible</h3>
                <p><?php echo htmlspecialchars($error ?? 'La publicación que buscas no existe o fue eliminada.'); ?></p>
                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">Ver todas las publicaciones</a>
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

    async function registrarContactoYRedirigir(event, publicacionId, url) {
        event.preventDefault(); // Evita que el enlace redirija inmediatamente

        try {
            await fetch('<?php echo BASE_URL; ?>publicaciones/registrarContacto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id_publicacion: publicacionId })
            });
        } catch (error) {
            console.error('Error al registrar el contacto:', error);
        } finally {
            // Redirigir al chat sin importar si la petición falló o no
            window.location.href = url;
        }
    }

    function handleAddToFavorites(productId) {
        const btn = document.getElementById('favBtn');
        const icon = document.getElementById('favIcon');
        const text = document.getElementById('favText');

        // Deshabilitar temporalmente para evitar clics múltiples
        btn.disabled = true;

        fetch('<?php echo BASE_URL; ?>publicaciones/toggle-favorito', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ publicacion_id: productId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Actualizar el botón según el nuevo estado
                if (data.esFavorito) {
                    icon.className = 'fas fa-heart';
                    text.textContent = 'En favoritos';
                    btn.classList.add('active');
                } else {
                    icon.className = 'far fa-heart';
                    text.textContent = 'Agregar a favoritos';
                    btn.classList.remove('active');
                }
            } else {
                // Opcional: manejar el caso de error devuelto en el JSON
                console.error('Error del servidor:', data.error || 'Error desconocido');
            }
        })
        .catch(err => console.error('Error de red o de fetch:', err))
        .finally(() => {
            // Volver a habilitar el botón
            btn.disabled = false;
        });
    }

    // Hacer elementos sticky al scroll
    window.addEventListener('scroll', function() {
        const stickyElements = document.querySelectorAll('.sticky-element');
        const headerHeight = 80;

        stickyElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.top <= headerHeight) {
                element.style.top = headerHeight + 'px';
            }
        });
    });
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>