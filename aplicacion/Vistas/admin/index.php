<?php 
    $vista_actual = 'index';
    $titulo = 'Dashboard';
    ob_start();

    // Datos reales desde el Controlador
    $stats_usuarios = $stats_usuarios ?? [];
    $stats_publicaciones = $stats_publicaciones ?? [];
    $categorias_populares = $categorias_populares_chart ?? [];
    
    // Datos de gráficos
    $labels = $chart_labels ?? []; 
    $data_usuarios = $chart_data_usuarios ?? [];
    $data_publicaciones = $chart_data_publicaciones ?? [];
    $data_ingresos = $chart_data_ingresos ?? [];
    
    // Métricas
    $total_usuarios = $stats_usuarios['total_usuarios'] ?? 0;
    $publicaciones_activas = $stats_publicaciones['publicaciones_activas'] ?? 0;
    $vendedores_activos = $stats_publicaciones['total_vendedores'] ?? 0;
    $comision_mes = end($data_ingresos) ?: 0;
?>
<style>
    /* Estilos Generales Dashboard */
    .dashboard-main { width: 100%; transition: all 0.3s ease; }
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    
    /* Tarjetas de Estadísticas */
    .stat-card { background: var(--bg-card); padding: 1.25rem; border-radius: 10px; box-shadow: var(--shadow); transition: all 0.3s ease; border-left: 4px solid; display: flex; justify-content: space-between; align-items: center; }
    .stat-card:hover { box-shadow: var(--shadow-lg); }
    .stat-card.primary { border-left-color: var(--admin-primary); }
    .stat-card.success { border-left-color: var(--status-success); }
    .stat-card.warning { border-left-color: var(--status-warning); }
    .stat-card.info { border-left-color: #2196f3; }
    
    /* Textos y Colores en Tarjetas */
    .stat-content { flex: 1; }
    .stat-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-primary); }
    .stat-card.primary .stat-value { color: var(--admin-primary); }
    .stat-card.success .stat-value { color: var(--status-success); }
    .stat-card.warning .stat-value { color: var(--status-warning); }
    .stat-card.info .stat-value { color: #2196f3; }
    .stat-label { color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem; font-weight: 500; }
    .stat-sub-label { color: var(--text-muted); font-size: 0.75rem; }
    .stat-icon { font-size: 2rem; margin-left: 1rem; color: var(--text-secondary); opacity: 0.9; }

    /* Estilos Encabezados de Tarjetas (Custom) */
    .card-header-custom {
        background-color: var(--primary-color);
        color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.25rem;
    }
    .card-header-custom .card-title { color: white; margin: 0; font-weight: 600; font-size: 1rem; }
    .card-header-custom i { color: rgba(255,255,255,0.8); }

    /* Estilos Dark Mode Específicos para Headers */
    body.dark-mode .card-header-custom {
        background-color: var(--bg-card);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-light);
    }
    body.dark-mode .card-header-custom .card-title { color: var(--text-primary); }
    body.dark-mode .card-header-custom i { color: var(--admin-secondary); }

    /* Gráficos y Contenedores */
    .chart-card-body {
        display: flex;
        flex-direction: column;
        height: 100%; /* Llenar la tarjeta */
        padding: 1rem;
    }
    .chart-wrapper { 
        position: relative; 
        flex: 1; /* Ocupar todo el espacio vertical disponible */
        width: 100%; 
        min-height: 300px; /* Altura mínima para que no se aplaste */
    }
    .dona-chart-wrapper { position: relative; height: 200px; width: 100%; display: flex; justify-content: center; }

    /* Estilos Específicos para Estado del Sistema (Vertical) */
    .system-card {
        background: var(--bg-card);
        color: var(--text-primary);
        border: none;
        box-shadow: var(--shadow);
    }
    .system-status-box {
        background-color: rgba(40, 167, 69, 0.1);
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .system-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-light);
    }
    .system-detail-row:last-child { border-bottom: none; }
    .sys-label { font-size: 0.85rem; color: var(--text-secondary); }
    .sys-val { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

    /* Badge Customizado para modo oscuro */
    .badge-theme {
        background-color: var(--bg-body);
        color: var(--text-primary);
        border: 1px solid var(--border-light);
    }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <div class="dashboard-grid">
            <div class="stat-card primary">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($total_usuarios); ?></div>
                    <div class="stat-label">Usuarios Totales</div>
                    <div class="stat-sub-label">Registrados en la plataforma</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>

            <div class="stat-card success">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($publicaciones_activas); ?></div>
                    <div class="stat-label">Publicaciones Activas</div>
                    <div class="stat-sub-label">Disponibles para compra</div>
                </div>
                <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-content">
                    <div class="stat-value">S/ <?php echo number_format($comision_mes, 2); ?></div>
                    <div class="stat-label">Comisión del Mes</div>
                    <div class="stat-sub-label">Ingresos por transacciones</div>
                </div>
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>

            <div class="stat-card info">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($vendedores_activos); ?></div>
                    <div class="stat-label">Vendedores Activos</div>
                    <div class="stat-sub-label">Con publicaciones activas</div>
                </div>
                <div class="stat-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card h-100 system-card">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Crecimiento Mensual</h5>
                    </div>
                    <div class="card-body chart-card-body">
                        <div class="chart-wrapper">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100 system-card">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title"><i class="fas fa-server me-2"></i>Estado del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($info_sistema)): ?>
                            
                            <div class="system-status-box">
                                <div class="display-6 text-success me-3"><i class="fas fa-check-circle"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">Sistema Operativo</h6>
                                    <small style="color: var(--text-muted);">Servicios online</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold" style="color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-hdd me-2"></i>Almacenamiento</span>
                                    <small style="color: var(--text-muted);"><?php echo $info_sistema['disk_free']; ?> libres</small>
                                </div>
                                <div class="progress" style="height: 6px; background-color: var(--border-light);">
                                    <div class="progress-bar <?php echo $info_sistema['disk_used_percent'] > 90 ? 'bg-danger' : 'bg-info'; ?>" 
                                        role="progressbar" 
                                        style="width: <?php echo $info_sistema['disk_used_percent']; ?>%">
                                    </div>
                                </div>
                                <small class="d-block text-end mt-1" style="color: var(--text-muted); font-size: 0.7rem;">Total: <?php echo $info_sistema['disk_total']; ?></small>
                            </div>

                            <div>
                                <div class="system-detail-row">
                                    <span class="sys-label">Versión PHP</span>
                                    <span class="sys-val"><?php echo $info_sistema['php_version']; ?></span>
                                </div>
                                <div class="system-detail-row">
                                    <span class="sys-label">Memoria Límite</span>
                                    <span class="sys-val"><?php echo $info_sistema['memory_limit']; ?></span>
                                </div>
                                <div class="system-detail-row">
                                    <span class="sys-label">Base de Datos</span>
                                    <span class="sys-val text-truncate" style="max-width: 150px;" title="<?php echo $info_sistema['db_version']; ?>">
                                        <?php echo $info_sistema['db_version']; ?>
                                    </span>
                                </div>
                                <div class="system-detail-row">
                                    <span class="sys-label">IP Servidor</span>
                                    <span class="sys-val"><?php echo $info_sistema['server_ip']; ?></span>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-5" style="color: var(--text-muted);">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <p>No disponible.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 system-card">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title"><i class="fas fa-wallet me-2"></i>Salud Financiera</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 system-card">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title"><i class="fas fa-chart-pie me-2"></i>Categorías Populares</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="dona-chart-wrapper">
                                    <canvas id="categoriesChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="px-2">
                                    <?php 
                                    $total_pubs = $stats_publicaciones['total_publicaciones'] ?? 1;
                                    if($total_pubs == 0) $total_pubs = 1;
                                    $top_categorias = array_slice($categorias_populares, 0, 4);
                                    foreach ($top_categorias as $categoria): 
                                        $cantidad_percent = round(($categoria['total_productos'] / $total_pubs) * 100);
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <div class="color-indicator me-2 flex-shrink-0" style="width: 10px; height: 10px; background-color: <?php echo htmlspecialchars($categoria['color'] ?? '#00bcd4'); ?>; border-radius: 50%;"></div>
                                            <span class="text-truncate small fw-bold" style="color: var(--text-primary);"><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></span>
                                        </div>
                                        <span class="badge rounded-pill badge-theme ms-2"><?php echo $cantidad_percent; ?>%</span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = <?php echo json_encode($labels ?? []); ?>;
        const dataUsuarios = <?php echo json_encode($data_usuarios ?? []); ?>;
        const dataPublicaciones = <?php echo json_encode($data_publicaciones ?? []); ?>;
        const dataIngresos = <?php echo json_encode($data_ingresos ?? []); ?>;
        const categoriasData = <?php echo json_encode($categorias_populares ?? []); ?>;
        
        let categoryLabels = [], categoryCounts = [], categoryColors = [];
        if (categoriasData.length > 0) {
            categoryLabels = categoriasData.map(c => c.nombre_categoria);
            categoryCounts = categoriasData.map(c => c.total_productos);
            categoryColors = categoriasData.map(c => c.color || '#00bcd4');
        } else {
            categoryLabels = ['Sin datos']; categoryCounts = [1]; categoryColors = ['#e0e0e0'];
        }

        let growthChart, financeChart, categoriesChart;

        function getThemeColors() {
            const style = getComputedStyle(document.body);
            return {
                adminPrimary: style.getPropertyValue('--admin-primary').trim() || '#0A3D62',
                adminSecondary: style.getPropertyValue('--admin-secondary').trim() || '#b8860b',
                statusSuccess: style.getPropertyValue('--status-success').trim() || '#28a745',
                borderLight: style.getPropertyValue('--border-light').trim() || '#dee2e6',
                textPrimary: style.getPropertyValue('--text-primary').trim() || '#212529',
                textSecondary: style.getPropertyValue('--text-secondary').trim() || '#6c757d',
                bgCard: style.getPropertyValue('--bg-card').trim() || '#ffffff'
            };
        }

        function initializeCharts() {
            const colors = getThemeColors();

            if (growthChart) growthChart.destroy();
            if (financeChart) financeChart.destroy();
            if (categoriesChart) categoriesChart.destroy();

            // 1. Growth Chart
            const ctxGrowth = document.getElementById('growthChart');
            if (ctxGrowth) {
                growthChart = new Chart(ctxGrowth, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Usuarios', data: dataUsuarios, borderColor: colors.adminPrimary, backgroundColor: colors.adminPrimary + '20', fill: true, tension: 0.4 },
                            { label: 'Publicaciones', data: dataPublicaciones, borderColor: colors.adminSecondary, backgroundColor: colors.adminSecondary + '20', fill: true, tension: 0.4 }
                        ]
                    },
                    options: {
                        responsive: true, 
                        maintainAspectRatio: false, // Permitir estiramiento vertical
                        plugins: { legend: { labels: { color: colors.textPrimary } } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: colors.borderLight }, ticks: { color: colors.textSecondary } },
                            x: { grid: { color: colors.borderLight }, ticks: { color: colors.textSecondary } }
                        }
                    }
                });
            }

            // 2. Finance Chart (CORREGIDO: Cuadrícula ON, Borde 0)
            const ctxFinance = document.getElementById('financeChart');
            if (ctxFinance) {
                financeChart = new Chart(ctxFinance, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{ 
                            label: 'Ingresos (S/)', 
                            data: dataIngresos, 
                            backgroundColor: colors.statusSuccess, 
                            borderRadius: 0, // BARRAS CUADRADAS
                            barPercentage: 0.6 // Ancho de barras ajustado
                        }]
                    },
                    options: {
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: colors.borderLight }, ticks: { color: colors.textSecondary } },
                            x: { 
                                grid: { display: true, color: colors.borderLight }, // GRID ACTIVADO
                                ticks: { color: colors.textSecondary } 
                            }
                        }
                    }
                });
            }

            // 3. Categories Chart
            const ctxCategories = document.getElementById('categoriesChart');
            if (ctxCategories) {
                categoriesChart = new Chart(ctxCategories, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{ data: categoryCounts, backgroundColor: categoryColors, borderWidth: 0 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: { legend: { display: false } }
                    }
                });
            }
        }

        setTimeout(() => initializeCharts(), 100);
        window.addEventListener('themechange', () => setTimeout(() => initializeCharts(), 100));
        window.addEventListener('resize', function() {
            if (growthChart) growthChart.resize();
            if (financeChart) financeChart.resize();
            if (categoriesChart) categoriesChart.resize();
        });
    });
</script>

<?php 
    $contenido = ob_get_clean(); 
    include 'layout.php'; 
?>