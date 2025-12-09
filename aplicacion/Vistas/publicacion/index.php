<?php 
$page_title = 'Nuestras Publicaciones - UniEmprende';
include __DIR__ . '/../plantillas/header.php'; 
?>

<style>
    .publications-container {
        padding: 4rem 1rem;
        max-width: 1400px;
        position: relative; /* Necesario para posicionar el botón de volver */
        margin: 0 auto;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
        color: var(--secondary-color, #2c3e50);
    }

    /* Botón para volver atrás */
    .back-link {
        position: fixed;
        top: 9rem;
        left: calc(50% - 700px - 5rem); /* Posiciona el botón a la izquierda del contenido */
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
    @media (max-width: 1600px) {
        .back-link {
            left: 2rem; /* Fallback para pantallas más pequeñas */
        }
    }
    @media (max-width: 768px) {
        .back-link {
            display: none; /* Ocultamos en móvil para no estorbar */
        }
    }

    
    .pagination-container {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pagination-link {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color, #ddd);
        text-decoration: none;
        color: var(--text-dark, #333);
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .pagination-link.active,
    .pagination-link:hover {
        background: var(--primary-color, #910202);
        color: white;
        border-color: var(--primary-color, #910202);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Solución para que el texto del título no sea azul */
    .product-title a {
        color: inherit; /* Hereda el color del elemento padre */
        text-decoration: none; /* Elimina el subrayado */
    }

    /* Estilos para los botones de acción en las tarjetas */
    .product-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #f0f0f0);
        gap: 0.5rem;
    }

    .btn-action {
        text-decoration: none;
        padding: 0.5rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: 1px solid var(--border-color, #ddd);
        background-color: #fff;
        color: var(--text-dark, #333);
    }

    .btn-action:hover {
        background-color: var(--primary-color, #910202);
        color: white;
        border-color: var(--primary-color, #910202);
        transform: translateY(-1px);
    }
    /* --- REGLAS RESPONSIVAS PARA MÓVILES --- */
    @media (max-width: 768px) {
        /* Reducir el padding exagerado en móviles */
        .publications-container {
            padding-top: 6rem; /* Bajamos de 8rem a 6rem */
            padding-bottom: 2rem;
        }

        /* Título más pequeño para que no ocupe toda la pantalla */
        .page-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        /* Botones de acción más fáciles de tocar (Áreas táctiles grandes) */
        .product-actions {
            flex-direction: row; /* Mantener en fila o cambiar a column si prefieres vertical */
            justify-content: space-between; /* Separarlos bien */
            gap: 1rem;
        }

        .btn-action {
            flex: 1; /* Que ocupen el mismo ancho disponible */
            justify-content: center; /* Texto centrado */
            padding: 0.8rem 0.5rem; /* Más altos para el dedo */
            font-size: 0.9rem;
        }

        /* Ajuste de paginación para que no se desborde */
        .pagination-container {
            gap: 0.3rem;
        }
        
        .pagination-link {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
        }
    }

    /* --- NUEVO: Estilos para el layout de dos columnas --- */
    .page-layout {
        display: grid;
        grid-template-columns: 280px 1fr; /* Columna para sidebar y contenido */
        gap: 2.5rem;
        align-items: flex-start;
    }
    /* --- ESTILOS PARA FILTROS (COPIADOS Y CORREGIDOS DE INICIO) --- */
    .sidebar-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1rem;
    }

    .category-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .category-list-item a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-light);
        font-weight: 500;
        transition: var(--transition);
    }

    .category-list-item a:hover,
    .category-list-item a.active {
        background: var(--primary-color);
        color: var(--bg-white);
        transform: translateX(5px);
    }

    .price-filter-container {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .price-filter-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }

    input[type="range"] {
        width: 100%;
        -webkit-appearance: none;
        appearance: none;
        height: 8px;
        background: var(--bg-light);
        border-radius: 5px;
        outline: none;
        cursor: pointer;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--primary-color);
        border-radius: 50%;
        cursor: pointer;
    }

    .price-label {
        display: flex;
        justify-content: space-between;
        margin-top: 0.75rem;
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .search-filter-container {
        margin-bottom: 1.5rem;
    }

    .search-filter-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }

    .search-input-wrapper {
        position: relative;
    }

    #search-filter {
        width: 100%;
        padding: 0.75rem 1rem;
        padding-right: 2.5rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    #search-filter:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
    }

    .search-input-wrapper i {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-lighter);
    }

    .sidebar {
        position: static; /* Cambiado a estático para que no se mueva con el scroll */
        background: var(--bg-white);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
    }

    /* --- NUEVO: Botón de Favoritos en la tarjeta --- */
    .product-favorite-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 5;
        width: 36px;
        height: 36px;
        background-color: rgba(255, 255, 255, 0.8);
        border: 1px solid #eee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        color: #666;
        transition: all 0.3s ease;
        backdrop-filter: blur(2px);
    }

    .product-favorite-btn:hover {
        background-color: white;
        transform: scale(1.1);
        color: #e53935; /* Rojo en hover */
    }

    .product-favorite-btn.favorited {
        background-color: #e53935;
        color: white;
        border-color: #e53935;
    }

    .product-favorite-btn.favorited:hover {
        background-color: #c62828; /* Rojo más oscuro en hover */
    }

    /* --- NUEVO: Modal de "Inicio de Sesión Requerido" --- */
    .login-required-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .login-required-modal-overlay.visible {
        opacity: 1;
        visibility: visible;
    }

    .login-required-modal-box {
        background: white;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        width: 90%;
        max-width: 480px;
        text-align: center;
    }

    .login-required-modal-box > i {
        font-size: 3.5rem;
        color: var(--primary-color, #910202);
        margin-bottom: 1.5rem;
    }
    .custom-modal-buttons {
        margin-top: 1.5rem; display: flex; justify-content: center; gap: 1rem;
    }

    /* --- NUEVO: Estilos para descripción en la tarjeta --- */
    .product-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Asegura que el contenedor de info ocupe el espacio vertical */
    }

    .product-description {
        font-size: 0.85rem;
        color: var(--text-light, #6c757d);
        line-height: 1.5;
        margin: 0.75rem 0;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limitar a 2 líneas */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1; /* Hace que la descripción ocupe el espacio disponible, empujando el vendor/acciones hacia abajo */
        min-height: 2.55rem; /* 0.85rem * 1.5 * 2 */
    }
</style>

<div class="publications-container">

    <!-- Botón para volver atrás -->
    <a href="javascript:history.back()" class="back-link" title="Volver atrás">
        <i class="fas fa-arrow-left"></i>
    </a>

    <h1 class="page-title">Nuestras Publicaciones</h1>

    <div class="page-layout">
        <!-- Sidebar de Filtros -->
        <aside class="sidebar">
        <!-- Filtro de Búsqueda por Texto -->
        <div class="search-filter-container">
            <h4 class="search-filter-title">Buscar en la página</h4>
            <div class="search-input-wrapper">
                <input type="text" id="search-filter" placeholder="Escribe para filtrar...">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <h3 class="sidebar-title">
            <i class="fas fa-tags"></i>
            Categorías
        </h3>
        <ul class="category-list">
            <li class="category-list-item" role="presentation">
                <a href="#" class="category-filter active" data-categoria="all" role="menuitem">
                    <span>Todas</span>
                </a>
            </li>
            <?php foreach ($datosVista['categorias'] as $categoria): ?>
            <li class="category-list-item" role="presentation">
                <a href="#" class="category-filter" data-categoria="<?php echo $categoria['id_categoria']; ?>" role="menuitem">
                    <span><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Filtro de Precio -->
        <div class="price-filter-container">
            <h4 class="price-filter-title">Filtrar por Precio</h4>
            <div class="price-slider">
                <input type="range" id="price-range" min="0" max="1000" value="1000" step="10">
                <div class="price-label">
                    <span>S/ 0</span>
                    <span id="price-value">S/ 1000</span>
                </div>
            </div>
        </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="main-content">
            <?php if (!empty($datosVista['publicaciones'])): ?>
                <div class="product-grid" id="product-grid">
                    <?php foreach ($datosVista['publicaciones'] as $pub): ?>
                        <article class="product-card" data-price="<?php echo $pub['precio']; ?>" data-categoria="<?php echo $pub['id_categoria']; ?>">
                            <div class="product-image">
                                <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" class="product-image-link">
                                <?php 
                                    $imagenUrl = !empty($pub['imagen_principal']) ? obtenerImagenFinal($pub['imagen_principal']) : '';
                                ?>
                                <?php if ($imagenUrl): ?>
                                    <img src="<?php echo htmlspecialchars($imagenUrl); ?>" alt="<?php echo htmlspecialchars($pub['titulo']); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                        <span>No disponible</span>
                                    </div>
                                <?php endif; ?>
                                </a>
                                <!-- NUEVO: Botón de Favoritos -->
                                <?php 
                                    $is_fav = $pub['es_favorito'] ?? false;
                                    $fav_class = $is_fav ? 'favorited' : '';
                                    $fav_icon_class = $is_fav ? 'fas' : 'far';
                                ?>
                                <button class="product-favorite-btn <?php echo $fav_class; ?>" 
                                        data-id-publicacion="<?php echo $pub['id_publicacion']; ?>"
                                        title="<?php echo $is_fav ? 'Quitar de favoritos' : 'Agregar a favoritos'; ?>">
                                    <i class="<?php echo $fav_icon_class; ?> fa-heart"></i>
                                </button>

                                <div class="product-badges">
                                    <span class="product-type"><?php echo htmlspecialchars($pub['tipo']); ?></span>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>"><?php echo htmlspecialchars($pub['titulo']); ?></a>
                                </h3>
                                <div class="product-meta">
                                    <span class="product-category"><?php echo htmlspecialchars($pub['nombre_categoria']); ?></span>
                                    <span class="product-price">S/ <?php echo number_format($pub['precio'], 2); ?></span>
                                </div>

                                <!-- NUEVO: Descripción corta -->
                                <p class="product-description" title="<?php echo htmlspecialchars($pub['descripcion']); ?>">
                                    <?php 
                                        echo htmlspecialchars($pub['descripcion'] ?? 'Sin descripción.');
                                    ?>
                                </p>

                                <div class="product-vendor">
                                    <img src="<?php echo !empty($pub['foto_perfil']) ? obtenerImagenFinal($pub['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Vendedor" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 8px; border: 1px solid var(--border-color);">
                                    <a href="<?php echo BASE_URL; ?>perfil/ver/<?php echo $pub['id_usuario']; ?>" style="color: inherit; text-decoration: none;" title="Ver perfil de <?php echo htmlspecialchars($pub['nombres']); ?>">
                                        <?php echo htmlspecialchars($pub['nombres'] . ' ' . $pub['apellidos']); ?>
                                    </a>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" class="btn btn-action">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                    <?php if (isset($_SESSION['usuario_id'])): ?>
                                        <?php if ($_SESSION['usuario_id'] == $pub['id_usuario']): ?>
                                            <span class="btn-icon" title="No puedes contactarte a ti mismo" style="opacity: 0.5; cursor: not-allowed;">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $pub['id_usuario']; ?>" class="btn-icon" title="Contactar vendedor">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>login" class="btn-icon" title="Inicia sesión para contactar">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($datosVista['total_paginas'] > 1): ?>
                    <nav class="pagination-container">
                        <?php for ($i = 1; $i <= $datosVista['total_paginas']; $i++): ?>
                            <a href="?pagina=<?php echo $i; ?>" class="pagination-link <?php echo ($i == $datosVista['pagina_actual']) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No se encontraron publicaciones</h3>
                    <p>Intenta ajustar los filtros o vuelve a intentarlo más tarde.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- NUEVO: Modal de "Inicio de Sesión Requerido" -->
    <div id="login-required-modal" class="login-required-modal-overlay">
        <div class="login-required-modal-box">
            <i class="fas fa-sign-in-alt"></i>
            <h3 style="font-size: 1.5rem; color: #333; margin-bottom: 1rem;">Inicio de Sesión Requerido</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 2rem;">Necesitas iniciar sesión para poder agregar publicaciones a tus favoritos.</p>
            <div class="custom-modal-buttons">
                <button id="login-modal-cancel" class="btn btn-outline" style="border-color: #ccc; color: #333;">Cancelar</button>
                <a href="<?php echo BASE_URL; ?>login" id="login-modal-confirm" class="btn btn-primary">
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilters = document.querySelectorAll('.category-filter');
    const productCards = document.querySelectorAll('.product-card');
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    const searchFilter = document.getElementById('search-filter');
    
    function applyFilters() {
        const selectedCategory = document.querySelector('.category-filter.active').getAttribute('data-categoria');
        const maxPrice = parseFloat(priceRange.value);
        const searchTerm = searchFilter.value.toLowerCase().trim();

        productCards.forEach(card => {
            const cardCategory = card.getAttribute('data-categoria');
            const cardPrice = parseFloat(card.getAttribute('data-price'));
            const cardTitle = card.querySelector('.product-title').textContent.toLowerCase();
            const cardDescription = card.querySelector('.product-description').textContent.toLowerCase();

            const categoryMatch = selectedCategory === 'all' || cardCategory === selectedCategory;
            const priceMatch = cardPrice <= maxPrice;
            const searchMatch = (searchTerm === '' || cardTitle.includes(searchTerm) || cardDescription.includes(searchTerm));

            card.style.display = (categoryMatch && priceMatch && searchMatch) ? 'block' : 'none';
        });
    }

    categoryFilters.forEach(filter => filter.addEventListener('click', e => { e.preventDefault(); categoryFilters.forEach(f => f.classList.remove('active')); filter.classList.add('active'); applyFilters(); }));
    priceRange.addEventListener('input', () => { priceValue.textContent = `S/ ${priceRange.value}`; applyFilters(); });
    searchFilter.addEventListener('input', applyFilters);

    // --- NUEVO: Lógica para el botón de favoritos ---
    const isUserLoggedIn = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
    const loginModal = document.getElementById('login-required-modal');
    const loginModalCancelBtn = document.getElementById('login-modal-cancel');

    function showLoginModal() {
        if (loginModal) loginModal.classList.add('visible');
    }

    function hideLoginModal() {
        if (loginModal) loginModal.classList.remove('visible');
    }

    if (loginModalCancelBtn) {
        loginModalCancelBtn.addEventListener('click', hideLoginModal);
    }
    if (loginModal) {
        loginModal.addEventListener('click', function(e) {
            if (e.target === this) hideLoginModal();
        });
    }

    document.querySelectorAll('.product-favorite-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (!isUserLoggedIn) {
                showLoginModal();
                return;
            }

            const btn = this;
            const publicacionId = btn.getAttribute('data-id-publicacion');
            const icon = btn.querySelector('i');

            btn.disabled = true;

            fetch('<?php echo BASE_URL; ?>publicaciones/toggle-favorito', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ publicacion_id: publicacionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.esFavorito) {
                        btn.classList.add('favorited');
                        icon.className = 'fas fa-heart';
                        btn.setAttribute('title', 'Quitar de favoritos');
                    } else {
                        btn.classList.remove('favorited');
                        icon.className = 'far fa-heart';
                        btn.setAttribute('title', 'Agregar a favoritos');
                    }
                } else {
                    console.error('Error al cambiar favorito:', data.error);
                }
            })
            .catch(err => console.error('Error de red:', err))
            .finally(() => {
                btn.disabled = false;
            });
        });
    });
});
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>
