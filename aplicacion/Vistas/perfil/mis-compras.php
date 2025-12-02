<?php require_once 'aplicacion/Vistas/plantillas/header.php'; ?>
<style>
    .container{
        margin-bottom:1rem;
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
    <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> Mis Compras</h2>
    
    <?php if (empty($compras)): ?>
        <div class="alert alert-info" style="background: #e1f5fe; color: #01579b; border-color: #0288d1;">
            <i class="fas fa-info-circle"></i> Aún no has realizado ninguna compra.
        </div>
    <?php else: ?>
        <div class="sales-list">
            <?php foreach ($compras as $compra): ?>
            <div class="sale-card">
                <div class="sale-card-header">
                    <span class="sale-id">Operación #<?php echo $compra['mp_payment_id']; ?></span>
                    <span class="sale-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y H:i', strtotime($compra['fecha_pago'])); ?></span>
                </div>
                <div class="sale-card-body">
                    <div class="product-info">
                        <h5 class="product-title"><?php echo htmlspecialchars($compra['titulo']); ?></h5>
                        <p class="buyer-info">Vendedor: <?php echo htmlspecialchars($compra['vendedor_nombre'] . ' ' . $compra['vendedor_apellido']); ?></p>
                    </div>
                    <div class="amount-info">
                        <span class="amount-label">Monto</span>
                        <span class="amount-value">S/ <?php echo number_format($compra['monto'], 2); ?></span>
                    </div>
                </div>
                <div class="sale-card-footer">
                    <a href="<?php echo BASE_URL; ?>pago/recibo/<?php echo $compra['id_pago']; ?>" class="btn btn-sm btn-outline" target="_blank">
                        <i class="fas fa-receipt"></i> Ver Recibo
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>