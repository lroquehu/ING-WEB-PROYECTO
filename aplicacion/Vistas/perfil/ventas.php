<?php require_once 'aplicacion/Vistas/plantillas/header.php'; ?>
<!-- Botón para volver atrás -->
<a href="javascript:history.back()" class="back-link" title="Volver atrás">
    <i class="fas fa-arrow-left"></i>
</a>
<style>
    /* Estilos para el botón de volver */
    .back-link {
        position: fixed;
        top: 9rem;
        left: calc(50% - 750px - 5rem); /* Posiciona el botón a la izquierda del contenido */
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
    @media (max-width: 1700px) {
        .back-link {
            left: 2rem; /* Fallback para pantallas más pequeñas */
        }
    }
    @media (max-width: 768px) {
        .back-link { display: none; }
    }
    /* Solución para que el footer se mantenga abajo (Sticky Footer) */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    main {
        flex-grow: 1;
    }
    main .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    /* Corrección para eliminar fondo transparente del header */
    body::before {
        display: none;
    }
    .product-info{
        padding:0.75rem;
    }
    .main-header{
        position: inherit;
    }
    .mb-4{
        margin-top:20px;
        margin-bottom:20px;
    }
    .sales-list {
        display: grid;
        gap: 1.5rem;
    }
    .sale-card {
        background: var(--bg-white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        transition: var(--transition);
        overflow: hidden;
    }
    .sale-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    .sale-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
        color: var(--text-light);
    }
    .sale-id { font-weight: 600; color: var(--text-dark); }
    .sale-card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .product-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--secondary-color);
        margin: 0 0 0.25rem 0;
    }
    .buyer-info {
        font-size: 0.95rem;
        color: var(--text-light);
        margin: 0;
    }
    .amount-info { text-align: right; margin-right: 20px;}
    .amount-label { font-size: 0.9rem; color: var(--text-light); display: block; }
    .amount-value { font-size: 1.5rem; font-weight: 700; color: var(--success-color, #28a745); }
    .sale-card-footer {
        padding: 0.5rem 1.5rem;
        text-align: right;
        background: var(--bg-light);
        border-top: 1px solid var(--border-color);
    }
</style>
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-chart-line"></i> Mis Ventas</h2>
    
    <?php if (empty($ventas)): ?>
        <div class="alert alert-info" style="background: #e1f5fe; color: #01579b; border-color: #0288d1;">
            <i class="fas fa-info-circle"></i> Aún no has realizado ninguna venta.
        </div>
    <?php else: ?>
        <div class="sales-list">
            <?php foreach ($ventas as $venta): ?>
            <div class="sale-card">
                <div class="sale-card-header">
                    <span class="sale-id">Operación #<?php echo $venta['mp_payment_id']; ?></span>
                    <span class="sale-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y H:i', strtotime($venta['fecha_pago'])); ?></span>
                </div>
                <div class="sale-card-body">
                    <div class="product-info">
                        <h5 class="product-title"><?php echo htmlspecialchars($venta['titulo']); ?></h5>
                        <p class="buyer-info">
                            Comprador: 
                            <a href="<?php echo BASE_URL; ?>perfil/ver/<?php echo $venta['id_comprador']; ?>" style="color: var(--primary-color); text-decoration: none;">
                                <?php echo htmlspecialchars($venta['comprador_nombre'] . ' ' . $venta['comprador_apellido']); ?>
                            </a>
                        </p>
                    </div>
                    <div class="amount-info">
                        <span class="amount-label">Monto</span>
                        <span class="amount-value">S/ <?php echo number_format($venta['monto'], 2); ?></span>
                    </div>
                </div>
                <div class="sale-card-footer">
                    <a href="<?php echo BASE_URL; ?>pago/recibo/<?php echo $venta['id_pago']; ?>" class="btn btn-sm btn-outline" target="_blank">
                        <i class="fas fa-receipt"></i> Ver Recibo
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>