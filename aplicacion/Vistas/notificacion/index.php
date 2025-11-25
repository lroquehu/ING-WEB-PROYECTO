<?php include 'aplicacion/Vistas/plantillas/header.php'; ?>

<style>
    .notificaciones-container {
        max-width: 800px;
        margin: 8rem auto 2rem auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .notificaciones-title {
        padding: 1.5rem;
        font-size: 1.8rem;
        color: var(--primary-color);
        border-bottom: 1px solid #e9e9e9;
        margin: 0;
    }

    .notificaciones-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .notificacion-item {
        display: block;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e9e9e9;
        text-decoration: none;
        color: var(--text-dark);
        transition: background-color 0.2s;
    }

    .notificacion-item:last-child {
        border-bottom: none;
    }

    .notificacion-item:hover {
        background-color: #f8f9fa;
    }

    .notificacion-item.no-leida {
        background-color: #fdf5f5;
        font-weight: 600;
    }
    .notificacion-item.no-leida:hover {
        background-color: #fbecec;
    }

    .notificacion-mensaje {
        margin: 0 0 0.5rem 0;
    }

    .notificacion-meta {
        font-size: 0.85rem;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .notificacion-meta i {
        color: var(--primary-light);
    }

    .no-notificaciones {
        text-align: center;
        padding: 3rem;
        color: var(--text-light);
    }
</style>

<div class="notificaciones-container">
    <h1 class="notificaciones-title">Mis Notificaciones</h1>

    <div class="notificaciones-list">
        <?php if (empty($datosVista['notificaciones'])): ?>
            <p class="no-notificaciones">No tienes ninguna notificación.</p>
        <?php else: ?>
            <?php foreach ($datosVista['notificaciones'] as $notif): ?>
                <a href="<?php echo BASE_URL . 'notificaciones/leer/' . $notif['id']; ?>" class="notificacion-item <?php echo ($notif['leido'] == 0) ? 'no-leida' : ''; ?>">
                    <p class="notificacion-mensaje"><?php echo htmlspecialchars($notif['mensaje']); ?></p>
                    <div class="notificacion-meta">
                        <?php if ($notif['tipo'] == 'favorito'): ?>
                            <i class="fas fa-heart"></i>
                        <?php else: ?>
                            <i class="fas fa-info-circle"></i>
                        <?php endif; ?>
                        <span><?php echo date('d/m/Y H:i', strtotime($notif['fecha'])); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'aplicacion/Vistas/plantillas/footer.php'; ?>
