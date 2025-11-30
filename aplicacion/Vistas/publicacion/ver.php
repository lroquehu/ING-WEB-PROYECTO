<?php
$page_title = isset($publicacion) && $publicacion ? htmlspecialchars($publicacion['titulo']) . ' - UniEmprende' : 'Ver Publicación - UniEmprende';
include __DIR__ . '/../plantillas/header.php';
?>

<style>
    /* Contenedor principal de la vista del producto */
    .product-view-container {
        padding: 3rem 0 2rem 0; /* Padding superior ajustado */
        min-height: calc(100vh - 200px);
        background: #ffffff;
        position: relative; /* Necesario para posicionar el botón de volver */
    }

    /* Botón para volver atrás, ahora posicionado absolutamente */
    .back-link {
        position: absolute;
        top: 3rem; /* Alineado con el nuevo padding del contenedor */
        left: calc(50% - 700px - 2rem - 44px); /* Fórmula para acercarlo a la imagen */
        z-index: 10; /* Asegura que esté por encima de otros elementos */
        
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: var(--primary);
        margin-bottom: 1.5rem;
        font-size: 1.2rem; /* Tamaño del ícono */
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: #f0f2f5;
        transition: all 0.2s ease;
    }

    /* Ajuste para pantallas más pequeñas donde el cálculo anterior no funciona */
    @media (max-width: 1550px) {
        .back-link {
            left: 2rem; /* Vuelve a una posición fija en pantallas más pequeñas */
        }
    }
    .back-link:hover {
        background-color: #e4e6e9;
        transform: scale(1.05);
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

    .breadcrumb .breadcrumb-separator {
        font-size: 0.7rem;
        color: #999;
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
        /*position: sticky;*/
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

    /* Sidebar de acciones */
    .product-sidebar {
        /*position: sticky;*/
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
        font-weight: 700; /* Restaurando el grosor del precio */
        margin-bottom: 0.5rem;
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
        /*position: sticky;*/
        top: 100px;
        transition: all 0.3s ease;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .product-specs{
            width: 340px;
        }
        .seller-info{
            width: 340px;
        }
        .seller-stats{
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.5rem;
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }
        .main-footer {
            background: var(--secondary-color);
            color: var(--bg-white);
            padding: 3rem 0 1rem;
            margin-right: -8%;
        }
        .stat{
            width: 96px;
        }
        .main-image-container{
            width: 86%;
            margin-top: 10px;
        }
        .thumbnail{
            width: 77%;
        }
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

    /* Estilos para la descripción colapsable */
    .product-description-collapsible {
        margin-top: 1rem; /* Espacio respecto al precio */
        padding-top: 1rem;
        border-radius: 8px;
    }

    .product-description-collapsible h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 0 0 0.75rem 0;
    }

    #description-content {
        color: #555;
        font-size: 0.9rem;
        line-height: 1.6;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out;
    }

    #description-content.collapsed {
        max-height: 60px; /* Altura inicial, muestra aprox. 3 líneas */
    }

    #toggle-description-btn {
        background: none;
        border: none;
        color: var(--primary);
        font-weight: 600;
        cursor: pointer;
        padding: 0.5rem 0 0 0;
    }

    /* --- NUEVO: Estilos para el formulario de valoración --- */
    .rating-form-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .rating-form-container h3 {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #333;
    }

    #toggle-description-btn i {
        margin-left: 0.5rem;
        transition: transform 0.3s ease;
    }

    #toggle-description-btn.expanded i {
        transform: rotate(180deg);
    }

    /* --- NUEVO: Estilos para el input de estrellas moderno --- */
    .star-rating-input {
        display: flex;
        flex-direction: row-reverse; /* Invierte el orden para que el hover funcione correctamente */
        justify-content: center;
        gap: 0.25rem;
    }

    /* Ocultar los radio buttons reales */
    .star-rating-input input[type="radio"] {
        display: none;
    }

    /* Estilo de las etiquetas (las estrellas) */
    .star-rating-input label {
        font-size: 1.8rem;
        color: #d1d5db; /* Color de estrella vacía (gris claro) */
        cursor: pointer;
        transition: color 0.2s ease-in-out, transform 0.15s ease;
    }

    /* Efecto al pasar el cursor sobre una estrella */
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label {
        color: #f59e0b; /* Color de estrella al pasar el cursor (ámbar) */
        transform: scale(1.1);
    }

    /* Estilo de la estrella seleccionada y las anteriores */
    .star-rating-input input[type="radio"]:checked ~ label {
        color: #f59e0b; /* Color de estrella seleccionada (ámbar) */
    }

    /* Para la edición, mantener el color de la selección incluso sin hover */
    .star-rating-input.editing input[type="radio"]:checked ~ label,
    .star-rating-input.editing label.selected {
        color: #f59e0b;
    }

    /* Quitar el efecto hover de las estrellas ya seleccionadas para evitar parpadeo */
    .star-rating-input input[type="radio"]:checked ~ label:hover,
    .star-rating-input input[type="radio"]:checked ~ label:hover ~ label {
        color: #f59e0b;
    }

    /* --- NUEVO: Estilos para el campo de comentario --- */
    .rating-form-container .form-group {
        margin-top: 1rem;
    }
    .rating-form-container textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        line-height: 1.5;
        transition: border-color 0.2s ease;
        resize: vertical; /* Permite al usuario ajustar la altura */
    }
    .rating-form-container textarea:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* --- NUEVO: Estilos para la sección de valoraciones públicas --- */
    .ratings-list-section {
        grid-column: 1 / 3; /* Ocupa las dos primeras columnas */
        margin-top: 0;
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 12px;
    }
    /* NUEVO: Ajuste para el formulario dentro de la sección de valoraciones */
    .ratings-list-section .rating-form-container {
        margin-top: 0;
        margin-bottom: 2rem;
        box-shadow: none;
    }

    .ratings-list-section h2 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .rating-card {
        display: flex;
        gap: 1rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .rating-card:last-child {
        border-bottom: none;
    }

    .rating-author-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #f0f2f5;
    }

    .rating-author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rating-content {
        flex: 1;
    }

    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .rating-author-name {
        font-weight: 600;
        color: #333;
    }

    .rating-date {
        font-size: 0.8rem;
        color: #999;
    }

    .rating-comment p {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.6;
        margin: 0.5rem 0 0 0;
        white-space: pre-wrap; /* Conserva saltos de línea */
    }

    /* --- NUEVO: Estilos para el botón de eliminar comentario --- */
    .rating-actions {
        margin-top: 0.75rem;
    }

    .btn-delete-rating {
        background: none;
        border: none;
        color: #999;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .btn-delete-rating:hover {
        background-color: #fbe9e7;
        color: #d32f2f;
    }
</style>

<?php
$imagenes = $imagenes ?? [];
$productos_similares = $productos_similares ?? [];
?>

<div class="product-view-container">
    <div class="container">
        <?php if ($publicacion && empty($error)): ?>
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

                    <?php
                        // Lógica para mostrar la valoración promedio
                        $valoracion_promedio = $datosVista['valoracion_promedio'] ?? 0;
                        $total_valoraciones = $datosVista['total_valoraciones'] ?? 0;
                        $estrellas_html = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $valoracion_promedio) {
                                $estrellas_html .= '<i class="fas fa-star"></i>'; // Estrella llena
                            } else if ($i - 0.5 <= $valoracion_promedio) {
                                $estrellas_html .= '<i class="fas fa-star-half-alt"></i>'; // Media estrella
                            } else {
                                $estrellas_html .= '<i class="far fa-star"></i>'; // Estrella vacía
                            }
                        }
                    ?>
                    <div class="product-rating">
                        <div class="rating-stars"><?php echo $estrellas_html; ?></div>
                        <span class="rating-text"><?php echo number_format($valoracion_promedio, 1); ?> (<?php echo $total_valoraciones; ?> valoraciones)</span>
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
                                <span class="stat-number"><?php echo number_format($publicacion['vendedor_rating'] ?? 0, 1); ?></span>
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

                        <!-- Descripción del producto movida y colapsable -->
                        <div class="product-description-collapsible">
                            <h3>Descripción</h3>
                            <div id="description-content" class="collapsed">
                                <p><?php echo nl2br(htmlspecialchars($publicacion['descripcion'])); ?></p>
                            </div>
                            <button id="toggle-description-btn">Ver más <i id="description-arrow" class="fas fa-chevron-down"></i></button>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $publicacion['id_usuario']): ?>
                            <a href="<?php echo BASE_URL; ?>publicaciones/editar/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-primary">
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

            <!-- NUEVO: Sección para mostrar todas las valoraciones -->
            <?php if (!empty($datosVista['valoraciones'])): ?>
            <div class="ratings-list-section fade-in">
                <h2>Valoraciones de Clientes (<?php echo count($datosVista['valoraciones']); ?>)</h2>

                <!-- Formulario para dejar una valoración (MOVIDO AQUÍ) -->
                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $publicacion['id_usuario']): ?>
                    <?php if ($datosVista['usuario_ya_valoro'] && $datosVista['valoracion_usuario']): ?>
                        <!-- Formulario de EDICIÓN de valoración -->
                        <div class="rating-form-container">
                            <h3>Edita tu valoración</h3>
                            <form action="<?php echo BASE_URL; ?>publicaciones/editar-valoracion" method="POST">
                                <input type="hidden" name="id_publicacion" value="<?php echo $publicacion['id_publicacion']; ?>" />
                                <input type="hidden" name="id_valoracion" value="<?php echo $datosVista['valoracion_usuario']['id_valoracion']; ?>" />
                                <div class="star-rating-input">
                                    <input type="radio" id="star5-edit" name="puntuacion" value="5" <?php echo ($datosVista['valoracion_usuario']['puntuacion'] == 5) ? 'checked' : ''; ?> required /><label for="star5-edit" title="5 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4-edit" name="puntuacion" value="4" <?php echo ($datosVista['valoracion_usuario']['puntuacion'] == 4) ? 'checked' : ''; ?> /><label for="star4-edit" title="4 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3-edit" name="puntuacion" value="3" <?php echo ($datosVista['valoracion_usuario']['puntuacion'] == 3) ? 'checked' : ''; ?> /><label for="star3-edit" title="3 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2-edit" name="puntuacion" value="2" <?php echo ($datosVista['valoracion_usuario']['puntuacion'] == 2) ? 'checked' : ''; ?> /><label for="star2-edit" title="2 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1-edit" name="puntuacion" value="1" <?php echo ($datosVista['valoracion_usuario']['puntuacion'] == 1) ? 'checked' : ''; ?> /><label for="star1-edit" title="1 estrella"><i class="fas fa-star"></i></label>
                                </div>
                                <div class="form-group">
                                    <textarea name="comentario" 
                                                placeholder="Edita tu comentario (opcional)..." 
                                                rows="3"><?php echo htmlspecialchars($datosVista['valoracion_usuario']['comentario'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="width:100%;">Actualizar Valoración</button>
                                </div>
                            </form>
                        </div>
                    <?php elseif (!$datosVista['usuario_ya_valoro']): ?>
                        <!-- Formulario de CREACIÓN de valoración -->
                        <div class="rating-form-container">
                            <h3>Deja tu valoración</h3>
                            <form action="<?php echo BASE_URL; ?>publicaciones/valorar" method="POST">
                                <input type="hidden" name="id_publicacion" value="<?php echo $publicacion['id_publicacion']; ?>" />
                                <div class="star-rating-input">
                                    <input type="radio" id="star5" name="puntuacion" value="5" required /><label for="star5" title="5 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="puntuacion" value="4" /><label for="star4" title="4 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="puntuacion" value="3" /><label for="star3" title="3 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="puntuacion" value="2" /><label for="star2" title="2 estrellas"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="puntuacion" value="1" /><label for="star1" title="1 estrella"><i class="fas fa-star"></i></label>
                                </div>
                                <div class="form-group">
                                    <textarea name="comentario" placeholder="Escribe un comentario (opcional)..." rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="width:100%;">Enviar Valoración</button></div>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php foreach ($datosVista['valoraciones'] as $v): ?>
                <div class="rating-card">
                    <div class="rating-author-avatar">
                        <img src="<?php echo !empty($v['foto_perfil']) ? obtenerImagenFinal($v['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" 
                             alt="Foto de <?php echo htmlspecialchars($v['nombres']); ?>">
                    </div>
                    <div class="rating-content">
                        <div class="rating-header">
                            <span class="rating-author-name"><?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apellidos']); ?></span>
                            <span class="rating-date"><?php echo date('d/m/Y', strtotime($v['fecha_valoracion'])); ?></span>
                        </div>
                        <div class="rating-stars">
                            <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $v['puntuacion'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                            ?>
                        </div>
                        <?php if (!empty($v['comentario'])): ?>
                        <div class="rating-comment">
                            <p><?php echo htmlspecialchars($v['comentario']); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- NUEVO: Botón de eliminar valoración -->
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $v['id_usuario_valorador']): ?>
                        <div class="rating-actions"> 
                            <form action="<?php echo BASE_URL; ?>publicaciones/eliminar-valoracion" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu valoración?');">
                                <input type="hidden" name="id_publicacion" value="<?php echo $publicacion['id_publicacion']; ?>">
                                <input type="hidden" name="id_valoracion" value="<?php echo $v['id_valoracion']; ?>">
                                <button type="submit" class="btn-delete-rating">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
            <?php else: ?>
            <div class="ratings-list-section fade-in" style="grid-column: 1 / 3;">
                <h2>Valoraciones de Clientes</h2>
                <p style="color: #666;">Esta publicación aún no tiene valoraciones. ¡Sé el primero en dejar una!</p>
            </div>
            <?php endif; ?>

            <!-- Mover la barra lateral para que quede a la derecha de las valoraciones -->
            <div class="product-sidebar sticky-element" style="grid-row: 1 / span 2; grid-column: 3;">
                <!-- Contenido del sidebar movido aquí -->
                <!-- ... el contenido del sidebar se movió desde arriba ... -->
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

    <!-- Botón de volver movido fuera del container para mejor posicionamiento -->
    <a href="javascript:history.back()" class="back-link" title="Volver atrás">
        <i class="fas fa-arrow-left"></i>
    </a>
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

    // Lógica para "Ver más" / "Ver menos" en la descripción
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle-description-btn');
        const content = document.getElementById('description-content');
        const arrowIcon = document.getElementById('description-arrow');

        if (toggleBtn && content) {
            // Establecer la altura inicial desde JS para asegurar que la transición funcione en el primer clic
            content.style.maxHeight = '60px';

            toggleBtn.addEventListener('click', function() {
                const isCollapsed = content.classList.contains('collapsed');
                content.classList.toggle('collapsed');
                this.classList.toggle('expanded');
                
                this.firstChild.textContent = isCollapsed ? 'Ver menos ' : 'Ver más ';

                // Si está colapsado (y lo vamos a expandir), usamos scrollHeight. Si no, lo volvemos a 60px.
                content.style.maxHeight = isCollapsed ? content.scrollHeight + 'px' : '60px';
            });
        }
    });
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>