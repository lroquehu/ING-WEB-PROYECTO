<?php 
    // Variable para la plantilla de Admin
    $vista_actual = 'index';
    $titulo = 'Dashboard';
    ob_start();

    // Simulación de datos para los gráficos
    $data_ventas_mensuales = [1200, 1550, 1300, 1800, 2100, 2400, 2800, 2500, 3000, 3500, 3100, 3800];
    $data_gastos_mensuales = [300, 400, 350, 500, 600, 750, 800, 700, 850, 950, 900, 1000];
    $data_nuevos_usuarios = [50, 65, 45, 70, 80, 95, 110, 85, 120, 140, 130, 150];
    $data_nuevas_publicaciones = [15, 22, 18, 30, 28, 40, 45, 35, 50, 60, 55, 65];

    // Datos para gráficos de categorías
    $categorias_populares = [
        ['nombre' => 'Tecnología', 'cantidad' => 45, 'color' => '#00bcd4'],
        ['nombre' => 'Libros', 'cantidad' => 38, 'color' => '#1de9b6'],
        ['nombre' => 'Ropa', 'cantidad' => 32, 'color' => '#ffb300'],
        ['nombre' => 'Hogar', 'cantidad' => 28, 'color' => '#ff5252'],
        ['nombre' => 'Deportes', 'cantidad' => 25, 'color' => '#673ab7']
    ];

    // Datos de respaldo
    $stats_usuarios = $stats_usuarios ?? ['total_usuarios' => 0];
    $stats_publicaciones = $stats_publicaciones ?? [
        'publicaciones_activas' => 0,
        'total_vendedores' => 0,
        'total_publicaciones' => 0
    ];
?>

<style>
    .dashboard-main {
        width: 100%;
        transition: all 0.3s ease;
    }

    /* Cuando el sidebar está expandido */
    .sidebar:not(.collapsed) ~ .main-content .dashboard-main {
        margin-left: 0;
        width: 100%;
    }

    /* Cuando el sidebar está colapsado */
    .sidebar.collapsed ~ .main-content .dashboard-main {
        margin-left: 0;
        width: 100%;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        padding: 1.25rem;
        border-radius: 10px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border-left: 4px solid;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .stat-card.primary { border-left-color: var(--admin-primary); }
    .stat-card.success { border-left-color: var(--status-success); }
    .stat-card.warning { border-left-color: var(--status-warning); }
    .stat-card.info { border-left-color: #2196f3; }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card.primary .stat-value { color: var(--admin-primary); }
    .stat-card.success .stat-value { color: var(--status-success); }
    .stat-card.warning .stat-value { color: var(--status-warning); }
    .stat-card.info .stat-value { color: #2196f3; }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .stat-sub-label {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .stat-icon {
        font-size: 2rem;
        margin-left: 1rem;
        color: var(--text-secondary); 
        opacity: 0.9;
    }

    .chart-wrapper {
        position: relative;
        height: 200px;
    }

    /* Gráfico de dona específico */
    .dona-chart-wrapper {
        position: relative;
        height: 180px;
    }

    /* Ajustes para cuando el sidebar está colapsado */
    .sidebar.collapsed ~ .main-content .dashboard-main .container-fluid {
        width: 100%;
        max-width: 100%;
    }

    .sidebar.collapsed ~ .main-content .dashboard-main .dashboard-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .list-group-item h6 {
        color: var(--text-primary) !important; 
    }

    .list-group-item small {
        color: var(--text-muted) !important;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
    }

    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .stat-icon {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 768px) {
        .dashboard-main {
            padding: 0.5rem;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .stat-icon {
            font-size: 1.75rem;
        }
        
        .chart-wrapper {
            height: 180px;
        }
        
        .dona-chart-wrapper {
            height: 160px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-main {
            padding: 0.25rem;
        }
        
        .stat-card {
            padding: 0.75rem;
        }
        
        .stat-value {
            font-size: 1.25rem;
        }
        
        .stat-icon {
            font-size: 1.5rem;
            margin-left: 0.5rem;
        }
        
        .chart-wrapper {
            height: 160px;
        }
        
        .dona-chart-wrapper {
            height: 140px;
        }
    }


</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <div class="dashboard-grid">
            <div class="stat-card primary">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats_usuarios['total_usuarios']); ?></div>
                    <div class="stat-label">Usuarios Totales</div>
                    <div class="stat-sub-label">Registrados en la plataforma</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>

            <div class="stat-card success">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats_publicaciones['publicaciones_activas']); ?></div>
                    <div class="stat-label">Publicaciones Activas</div>
                    <div class="stat-sub-label">Disponibles para compra</div>
                </div>
                <div class="stat-icon"><i class="fas fa-box-open"></i></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-content">
                    <div class="stat-value">S/ 3,762.00</div>
                    <div class="stat-label">Comisión del Mes</div>
                    <div class="stat-sub-label">Ingresos por transacciones</div>
                </div>
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>

            <div class="stat-card info">
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($stats_publicaciones['total_vendedores']); ?></div>
                    <div class="stat-label">Vendedores Activos</div>
                    <div class="stat-sub-label">Con publicaciones activas</div>
                </div>
                <div class="stat-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Crecimiento Mensual</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Estado del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-2 me-3">
                                        <i class="fas fa-server text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Servidor Web</h6>
                                        <small class="text-muted">Estado operacional</small>
                                    </div>
                                </div>
                                <span class="badge bg-success">Activo</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-database text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Base de Datos</h6>
                                        <small class="text-muted">Conexión estable</small>
                                    </div>
                                </div>
                                <span class="badge bg-success">Activo</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle p-2 me-3">
                                        <i class="fas fa-save text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Último Backup</h6>
                                        <small class="text-muted">Hace 2 horas</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning">Pendiente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Salud Financiera
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Categorías Populares
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="dona-chart-wrapper">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <?php foreach ($categorias_populares as $categoria): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="color-indicator me-2" style="width: 12px; height: 12px; background-color: <?php echo $categoria['color']; ?>; border-radius: 2px;"></div>
                                    <small class="text-muted"><?php echo $categoria['nombre']; ?></small>
                                </div>
                                <small class="fw-bold"><?php echo $categoria['cantidad']; ?>%</small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // Datos simulados
        const ventas = <?php echo json_encode($data_ventas_mensuales); ?>;
        const gastos = <?php echo json_encode($data_gastos_mensuales); ?>;
        const nuevosUsuarios = <?php echo json_encode($data_nuevos_usuarios); ?>;
        const nuevasPublicaciones = <?php echo json_encode($data_nuevas_publicaciones); ?>;

        let growthChart, financeChart, categoriesChart;

        // Función para obtener colores basados en el tema actual
        function getThemeColors() {
            return {
                adminPrimary: getComputedStyle(document.body).getPropertyValue('--admin-primary').trim(),
                adminSecondary: getComputedStyle(document.body).getPropertyValue('--admin-secondary').trim(),
                statusSuccess: getComputedStyle(document.body).getPropertyValue('--status-success').trim(),
                statusDanger: getComputedStyle(document.body).getPropertyValue('--status-danger').trim(),
                borderLight: getComputedStyle(document.body).getPropertyValue('--border-light').trim(),
                textPrimary: getComputedStyle(document.body).getPropertyValue('--text-primary').trim(),
                textSecondary: getComputedStyle(document.body).getPropertyValue('--text-secondary').trim(),
                bgCard: getComputedStyle(document.body).getPropertyValue('--bg-card').trim()
            };
        }

        // Función para inicializar gráficos
        function initializeCharts() {
            const colors = getThemeColors();

            // Destruir gráficos existentes si los hay
            if (growthChart) growthChart.destroy();
            if (financeChart) financeChart.destroy();
            if (categoriesChart) categoriesChart.destroy();

            // Gráfico de Crecimiento (Usuarios vs Publicaciones)
            growthChart = new Chart(document.getElementById('growthChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Nuevos Usuarios',
                            data: nuevosUsuarios,
                            borderColor: colors.adminPrimary,
                            backgroundColor: colors.adminPrimary + '20',
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Nuevas Publicaciones',
                            data: nuevasPublicaciones,
                            borderColor: colors.adminSecondary,
                            backgroundColor: colors.adminSecondary + '20',
                            fill: true,
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.borderLight },
                            ticks: { color: colors.textSecondary }
                        },
                        x: {
                            grid: { color: colors.borderLight },
                            ticks: { color: colors.textSecondary }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: colors.textPrimary }
                        }
                    }
                }
            });

            // Gráfico de Salud Financiera (Ingresos vs Gastos)
            financeChart = new Chart(document.getElementById('financeChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Ingresos Netos (S/)',
                            data: ventas,
                            backgroundColor: colors.statusSuccess,
                            borderRadius: 4,
                        },
                        {
                            label: 'Egresos (S/)',
                            data: gastos,
                            backgroundColor: colors.statusDanger,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.borderLight },
                            ticks: { color: colors.textSecondary }
                        },
                        x: {
                            grid: { color: colors.borderLight },
                            ticks: { color: colors.textSecondary }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: colors.textPrimary }
                        }
                    }
                }
            });

            // Gráfico de Categorías Populares (Dona)
            categoriesChart = new Chart(document.getElementById('categoriesChart'), {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($categorias_populares, 'nombre')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($categorias_populares, 'cantidad')); ?>,
                        backgroundColor: <?php echo json_encode(array_column($categorias_populares, 'color')); ?>,
                        borderWidth: 2,
                        borderColor: colors.bgCard
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: colors.bgCard,
                            titleColor: colors.textPrimary,
                            bodyColor: colors.textSecondary,
                            borderColor: colors.borderLight
                        }
                    }
                }
            });
        }

        // Esperar a que el tema esté completamente aplicado antes de inicializar gráficos
        function waitForThemeAndInitialize() {
            // Pequeño delay para asegurar que el tema está aplicado
            setTimeout(() => {
                initializeCharts();
            }, 100);
        }

        // Inicializar gráficos después de que el tema esté listo
        waitForThemeAndInitialize();

        // Observar cambios en el sidebar (para redimensionar)
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        setTimeout(() => {
                            if (growthChart) growthChart.resize();
                            if (financeChart) financeChart.resize();
                            if (categoriesChart) categoriesChart.resize();
                        }, 300);
                    }
                });
            });

            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Redimensionar gráficos cuando cambie el tamaño de la ventana
        window.addEventListener('resize', function() {
            if (growthChart) growthChart.resize();
            if (financeChart) financeChart.resize();
            if (categoriesChart) categoriesChart.resize();
        });

        // Actualizar gráficos cuando cambie el tema
        window.addEventListener('themechange', function() {
            waitForThemeAndInitialize();
        });

        // También redimensionar cuando se haga clic en el toggle del sidebar
        const sidebarToggle = document.getElementById('mobileMenuToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                setTimeout(() => {
                    if (growthChart) growthChart.resize();
                    if (financeChart) financeChart.resize();
                    if (categoriesChart) categoriesChart.resize();
                }, 350);
            });
        }
    });
</script>

<?php 
    $contenido = ob_get_clean(); 
    include 'layout.php'; 
?>