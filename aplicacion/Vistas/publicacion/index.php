<?php include __DIR__ . '/../plantillas/header.php'; ?>

<style>
    .publications-container {
        padding: 8rem 1rem 4rem 1rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
        color: var(--secondary-color, #2c3e50);
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
        justify-content: flex-end; /* Alinea los botones a la derecha */
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #f0f0f0);
        gap: 0.5rem; /* Espacio entre botones */
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
</style>

<div class="publications-container">

    <h1 class="page-title">Nuestras Publicaciones</h1>

    <!-- TODO: Add filters form here -->

    <?php if (!empty($datosVista['publicaciones'])): ?>
        <div class="product-grid">
            <?php foreach ($datosVista['publicaciones'] as $pub): ?>
                <div class="product-card">
                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" class="product-image-link">
                        <div class="product-image">
                            <?php 
                                $imagenUrl = !empty($pub['imagen_principal']) ? obtenerImagenFinal($pub['imagen_principal']) : '';
                            ?>
                            <?php if ($imagenUrl): ?>
                                <img src="<?php echo htmlspecialchars($imagenUrl); ?>" alt="<?php echo htmlspecialchars($pub['titulo']); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <span>No disponible</span>
                                </div>
                            <?php endif; ?>
                            <div class="product-badges">
                                <span class="product-type"><?php echo htmlspecialchars($pub['tipo']); ?></span>
                            </div>
                        </div>
                    </a>
                    <div class="product-info">
                         <div class="product-meta">
                             <span class="product-category"><?php echo htmlspecialchars($pub['nombre_categoria']); ?></span>
                             <span class="product-price">S/ <?php echo number_format($pub['precio'], 2); ?></span>
                        </div>
                        <h3 class="product-title">
                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>"><?php echo htmlspecialchars($pub['titulo']); ?></a>
                        </h3>
                        <div class="product-vendor">
                            <img src="<?php echo !empty($pub['foto_perfil']) ? obtenerImagenFinal($pub['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Vendedor" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 8px; border: 1px solid var(--border-color);">
                            <span><?php echo htmlspecialchars($pub['nombres'] . ' ' . $pub['apellidos']); ?></span>
                        </div>
                        <div class="product-actions">
                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" class="btn-action">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $pub['id_usuario']): ?>
                                <a href="<?php echo BASE_URL; ?>chat/iniciar?destinatario=<?php echo $pub['id_usuario']; ?>" class="btn-action">
                                    <i class="fas fa-comment-dots"></i> Mensaje
                                </a>
                            <?php elseif (!isset($_SESSION['usuario_id'])): ?>
                                <a href="<?php echo BASE_URL; ?>login" class="btn-action">
                                    <i class="fas fa-comment-dots"></i> Mensaje
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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

<?php include __DIR__ . '/../plantillas/footer.php'; ?>
