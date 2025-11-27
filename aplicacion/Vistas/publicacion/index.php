<?php include __DIR__ . '/../plantillas/header.php'; ?>

<style>
    .publications-container {
        padding: 8rem 1rem 4rem 1rem;
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
        position: absolute;
        top: 8rem; /* Alineado con el padding del contenedor */
        left: -2rem; /* Lo posiciona a la izquierda del contenido */
        z-index: 10;
        
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: var(--primary-color);
        font-size: 1.2rem;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: #f0f2f5;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        background-color: #e4e6e9;
        transform: scale(1.05);
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
                                <div class="product-vendor">
                                    <img src="<?php echo !empty($pub['foto_perfil']) ? obtenerImagenFinal($pub['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Vendedor" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 8px; border: 1px solid var(--border-color);">
                                    <span><?php echo htmlspecialchars($pub['nombres'] . ' ' . $pub['apellidos']); ?></span>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $pub['id_usuario']): ?>
                                        <a href="<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $pub['id_usuario']; ?>" class="btn-icon" title="Contactar vendedor">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    <?php elseif (!isset($_SESSION['usuario_id'])): ?>
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

            const categoryMatch = selectedCategory === 'all' || cardCategory === selectedCategory;
            const priceMatch = cardPrice <= maxPrice;
            const searchMatch = (searchTerm === '' || cardTitle.includes(searchTerm));

            card.style.display = (categoryMatch && priceMatch && searchMatch) ? 'block' : 'none';
        });
    }

    categoryFilters.forEach(filter => filter.addEventListener('click', e => { e.preventDefault(); categoryFilters.forEach(f => f.classList.remove('active')); filter.classList.add('active'); applyFilters(); }));
    priceRange.addEventListener('input', () => { priceValue.textContent = `S/ ${priceRange.value}`; applyFilters(); });
    searchFilter.addEventListener('input', applyFilters);
});
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>
