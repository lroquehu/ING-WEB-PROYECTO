<?php
    // aplicacion/Vistas/perfil/favoritos.php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $favoritos = $favoritos ?? [];
?>

<?php include __DIR__ . '/../plantillas/encabezado.php'; ?>

<div class="profile-container">
    <?php include __DIR__ . '/../perfil/plantilla/sidebar.php'; ?>

    <div class="profile-content">
        <div class="page-header">
            <h1><i class="fas fa-heart"></i> Mis Favoritos</h1>
            <p>Aquí encontrarás todas las publicaciones que has guardado.</p>
        </div>

        <?php if (isset($_GET['eliminado']) && $_GET['eliminado'] === 'exito'): ?>
            <div class="alert alert-success">
                Publicación eliminada de tus favoritos correctamente.
            </div>
        <?php endif; ?>

        <?php if (empty($favoritos)): ?>
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h3>Aún no tienes favoritos</h3>
                <p>Explora las publicaciones y guarda las que más te gusten haciendo clic en el corazón.</p>
                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                    <i class="fas fa-search"></i> Explorar Publicaciones
                </a>
            </div>
        <?php else: ?>
            <div class="publications-grid">
                <?php foreach ($favoritos as $publicacion): ?>
                    <div class="publication-card">
                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="publication-image-link">
                            <?php if (!empty($publicacion['imagen_principal'])): ?>
                                <img src="/<?php echo htmlspecialchars($publicacion['imagen_principal']); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="publication-info">
                            <h4 class="publication-title">
                                <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>">
                                    <?php echo htmlspecialchars($publicacion['titulo']); ?>
                                </a>
                            </h4>
                            <div class="publication-price">
                                S/ <?php echo number_format($publicacion['precio'], 2); ?>
                            </div>
                            <div class="publication-meta">
                                <span class="meta-item">
                                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($publicacion['nombre_categoria']); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar-alt"></i> Agregado: <?php echo date('d/m/Y', strtotime($publicacion['fecha_agregado'])); ?>
                                </span>
                            </div>
                            <div class="publication-actions">
                                <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <!-- Formulario para eliminar de favoritos -->
                                <form action="<?php echo BASE_URL; ?>perfil/eliminar-favorito" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_publicacion" value="<?php echo $publicacion['id_publicacion']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres quitar esta publicación de tus favoritos?');">
                                        <i class="fas fa-trash-alt"></i> Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../plantillas/pie.php'; ?>