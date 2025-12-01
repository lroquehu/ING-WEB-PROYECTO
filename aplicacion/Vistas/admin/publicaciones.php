<?php 
$vista_actual = 'publicaciones'; 
$titulo = 'Gestión de Publicaciones';
$publicaciones = $publicaciones ?? [];
$total_publicaciones = $total_publicaciones ?? 0;
$pagina_actual = $pagina_actual ?? 1;
$total_paginas = $total_paginas ?? 1;
$estado_filtro = $estado_filtro ?? null;
$tipo_filtro = $tipo_filtro ?? null;
$categoria_filtro = $categoria_filtro ?? null;
$facultad_filtro = $facultad_filtro ?? null;
$precio_min = $precio_min ?? null;
$precio_max = $precio_max ?? null;
$fecha_inicio = $fecha_inicio ?? null;
$fecha_fin = $fecha_fin ?? null;
$error = $error ?? '';
$success = $success ?? '';

// Datos para filtros
$categorias = $categorias ?? [];
$facultades = $facultades ?? [];
$tipos_publicacion = ['producto' => 'Producto', 'servicio' => 'Servicio'];
$estados_publicacion = [
    '1' => 'Activas', 
    '0' => 'Inactivas',
    '2' => 'Pendientes',
    '3' => 'Rechazadas'
];

// Estadísticas mejoradas
$stats_publicaciones = $stats_publicaciones ?? [
    'total' => 0,
    'activas' => 0,
    'inactivas' => 0,
    'pendientes' => 0,
    'rechazadas' => 0,
    'productos' => 0,
    'servicios' => 0,
    'ingresos_totales' => 0,
    'promedio_precio' => 0
];

ob_start();
?>

<!-- Incluir DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.metric-card {
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

.metric-card:hover {
    box-shadow: var(--shadow-lg);
}

.metric-card.primary { border-left-color: var(--admin-primary); }
.metric-card.success { border-left-color: var(--status-success); }
.metric-card.warning { border-left-color: var(--status-warning); }
.metric-card.info { border-left-color: #2196f3; }
.metric-card.danger { border-left-color: var(--status-danger); }

.metric-content {
    flex: 1;
}

.metric-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.metric-card.primary .metric-value { color: var(--admin-primary); }
.metric-card.success .metric-value { color: var(--status-success); }
.metric-card.warning .metric-value { color: var(--status-warning); }
.metric-card.info .metric-value { color: #2196f3; }
.metric-card.danger .metric-value { color: var(--status-danger); }

.metric-label {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.metric-sub-label {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.metric-icon {
    font-size: 2rem;
    margin-left: 1rem;
    color: var(--text-secondary);
    opacity: 0.9;
}

/* Filtros simplificados */
.filters-container {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.filter-select,
.filter-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: all 0.3s ease;
    width: 100%;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(10, 61, 98, 0.1);
}

.filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

/* Estados y badges */
.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(46, 175, 125, 0.15);
    color: var(--status-success);
}

.status-inactive {
    background: rgba(196, 76, 76, 0.15);
    color: var(--status-danger);
}

.status-pending {
    background: rgba(255, 179, 0, 0.15);
    color: var(--status-warning);
}

.status-rejected {
    background: rgba(156, 39, 176, 0.15);
    color: #9c27b0;
}

.type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.type-producto {
    background: rgba(0, 188, 212, 0.15);
    color: var(--admin-primary);
}

.type-servicio {
    background: rgba(29, 233, 182, 0.15);
    color: var(--status-success);
}

/* Información de publicación */
.publication-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.publication-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.publication-details {
    display: flex;
    flex-direction: column;
}

.publication-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
    font-size: 0.85rem;
}

.publication-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* Información del vendedor */
.seller-info {
    display: flex;
    flex-direction: column;
}

.seller-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
    font-size: 0.85rem;
}

.seller-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* Precio */
.price-amount {
    font-weight: 700;
    color: var(--admin-primary);
    font-size: 1rem;
}

/* Acciones */
.actions-container {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.4rem 0.6rem;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    text-decoration: none;
}

.btn-action:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.btn-view {
    background: #2196f3;
    color: white;
}

.btn-edit {
    background: #ff9800;
    color: white;
}

.btn-activate {
    background: var(--status-success);
    color: white;
}

.btn-deactivate {
    background: var(--status-danger);
    color: white;
}

.btn-pending {
    background: var(--status-warning);
    color: white;
}

/* Modal de Confirmación */
.confirmation-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1.5rem;
    max-width: 400px;
    width: 90%;
    box-shadow: var(--shadow-lg);
    font-size: 0.9rem;
}

.modal-header {
    margin-bottom: 1rem;
}

.modal-title {
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}

.modal-message {
    color: var(--text-secondary);
    margin: 0;
    font-size: 0.85rem;
}

.modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.btn-cancel {
    background: var(--bg-body);
    color: var(--text-primary);
    border: 1px solid var(--border-light);
}

.btn-confirm {
    background: var(--status-danger);
    color: white;
}

/* Responsive */
@media (max-width: 1200px) {
    .metrics-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    
    .filters-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}

@media (max-width: 992px) {
    .metrics-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .metric-card {
        padding: 1rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
    
    .metric-icon {
        font-size: 1.75rem;
    }
    
    .filters-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .actions-container {
        flex-direction: column;
    }
    
    .btn-action {
        justify-content: center;
    }
    
    .metric-card {
        padding: 1rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
    
    .metric-icon {
        font-size: 1.75rem;
    }
}

@media (max-width: 576px) {
    .metric-card {
        padding: 0.75rem;
    }
    
    .metric-value {
        font-size: 1.25rem;
    }
    
    .metric-icon {
        font-size: 1.5rem;
        margin-left: 0.5rem;
    }
    
    .filters-container {
        padding: 1rem;
    }
    
    .modal-content {
        margin: 1rem;
        padding: 1.25rem;
    }
}
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <!-- Estadísticas Rápidas -->
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['total']); ?></div>
                    <div class="metric-label">Total Publicaciones</div>
                    <div class="metric-sub-label">En la plataforma</div>
                </div>
                <div class="metric-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['activas']); ?></div>
                    <div class="metric-label">Publicaciones Activas</div>
                    <div class="metric-sub-label">Disponibles para compra</div>
                </div>
                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['pendientes']); ?></div>
                    <div class="metric-label">Pendientes</div>
                    <div class="metric-sub-label">Por revisar</div>
                </div>
                <div class="metric-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value">S/ <?php echo number_format($stats_publicaciones['ingresos_totales'], 2); ?></div>
                    <div class="metric-label">Ingresos Totales</div>
                    <div class="metric-sub-label">Comisiones generadas</div>
                </div>
                <div class="metric-icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Estado</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados_publicacion as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $estado_filtro === $value ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Tipo</label>
                    <select class="filter-select" id="typeFilter">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos_publicacion as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $tipo_filtro === $value ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Categoría</label>
                    <select class="filter-select" id="categoryFilter">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo $categoria_filtro == $categoria['id_categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Facultad</label>
                    <select class="filter-select" id="facultyFilter">
                        <option value="">Todas las facultades</option>
                        <?php foreach ($facultades as $facultad): ?>
                            <option value="<?php echo $facultad['id_facultad'] ?? $facultad; ?>" <?php echo $facultad_filtro == ($facultad['id_facultad'] ?? $facultad) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($facultad['nombre_facultad'] ?? $facultad); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="filter-actions">
                <button class="btn-action btn-cancel" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> Limpiar
                </button>
                <button class="btn-action btn-view" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Aplicar
                </button>
            </div>
        </div>

        <!-- Tabla de Publicaciones con DataTables -->
        <table id="publicacionesTable" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Publicación</th>
                    <th>Vendedor</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicaciones as $pub): 
                    $estado_class = '';
                    $estado_text = '';
                    
                    switch($pub['estado']) {
                        case 1:
                            $estado_class = 'status-active';
                            $estado_text = 'Activa';
                            break;
                        case 0:
                            $estado_class = 'status-inactive';
                            $estado_text = 'Inactiva';
                            break;
                        case 2:
                            $estado_class = 'status-pending';
                            $estado_text = 'Pendiente';
                            break;
                        case 3:
                            $estado_class = 'status-rejected';
                            $estado_text = 'Rechazada';
                            break;
                        default:
                            $estado_class = 'status-inactive';
                            $estado_text = 'Desconocido';
                    }
                ?>
                    <tr data-publication-id="<?php echo $pub['id_publicacion']; ?>">
                        <td>
                            <div class="publication-info">
                                <img src="<?php echo BASE_URL . 'assets/uploads/publicaciones/' . htmlspecialchars($pub['id_publicacion']) . '/' . htmlspecialchars($pub['ruta_imagen'] ?? 'default.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($pub['titulo']); ?>" 
                                     class="publication-image"
                                     onerror="this.src='<?php echo BASE_URL; ?>assets/images/default-product.png'">
                                <div class="publication-details">
                                    <div class="publication-title">
                                        <?php echo htmlspecialchars($pub['titulo']); ?>
                                    </div>
                                    <div class="publication-meta">
                                        <span class="type-badge type-<?php echo $pub['tipo']; ?>">
                                            <?php echo $tipos_publicacion[$pub['tipo']] ?? $pub['tipo']; ?>
                                        </span>
                                        <?php if ($pub['destacado'] ?? false): ?>
                                            <span style="color: #ff9800; margin-left: 0.5rem;">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="seller-info">
                                <div class="seller-name">
                                    <?php echo htmlspecialchars(($pub['nombres'] ?? '') . ' ' . ($pub['apellidos'] ?? '')); ?>
                                </div>
                                <div class="seller-meta">
                                    <?php echo htmlspecialchars($pub['facultad'] ?? 'Sin facultad'); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($pub['nombre_categoria']); ?>
                        </td>
                        <td>
                            <div class="price-amount">
                                S/ <?php echo number_format($pub['precio'], 2); ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $estado_class; ?>">
                                <?php echo $estado_text; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($pub['fecha_publicacion'])); ?>
                        </td>
                        <td>
                            <div class="actions-container">
                                <a href="<?php echo BASE_URL; ?>publicacion/ver/<?php echo htmlspecialchars($pub['id_publicacion']); ?>" 
                                   target="_blank" 
                                   class="btn-action btn-view"
                                   title="Ver publicación">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <button class="btn-action btn-edit"
                                        onclick="editPublication(<?php echo $pub['id_publicacion']; ?>)"
                                        title="Editar publicación">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <?php if ($pub['estado'] == 1): ?>
                                    <button class="btn-action btn-deactivate" 
                                            onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'desactivar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')"
                                            title="Desactivar publicación">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php elseif ($pub['estado'] == 0): ?>
                                    <button class="btn-action btn-activate" 
                                            onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'activar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')"
                                            title="Activar publicación">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php elseif ($pub['estado'] == 2): ?>
                                    <button class="btn-action btn-activate" 
                                            onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'aprobar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')"
                                            title="Aprobar publicación">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-action btn-deactivate" 
                                            onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'rechazar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')"
                                            title="Rechazar publicación">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="confirmation-modal" id="confirmationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Acción</h3>
            <p class="modal-message" id="modalMessage">¿Estás seguro de que quieres realizar esta acción?</p>
        </div>
        <div class="modal-actions">
            <button class="btn-action btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-action btn-confirm" id="confirmButton">Confirmar</button>
        </div>
    </div>
</div>

<!-- Incluir DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
// Variables globales
let currentPublicationId = null;
let currentAction = null;
let dataTable;

// Inicializar DataTables
document.addEventListener('DOMContentLoaded', function() {
    dataTable = $('#publicacionesTable').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay publicaciones disponibles",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ publicaciones",
            "infoEmpty": "Mostrando 0 a 0 de 0 publicaciones",
            "infoFiltered": "(filtrado de _MAX_ publicaciones totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ publicaciones",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron publicaciones coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar columna ascendente",
                "sortDescending": ": activar para ordenar columna descendente"
            }
        },
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-7"B><"col-sm-12 col-md-2"l><"col-sm-12 col-md-3"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>', 
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i>',
                className: 'btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                filename: 'publicaciones_' + new Date().toISOString().split('T')[0]
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i>',
                className: 'btn-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                filename: 'publicaciones_' + new Date().toISOString().split('T')[0],
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i>',
                className: 'btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                customize: function (win) {
                    $(win.document.body).css('color', '#1A1E23');
                    $(win.document.body).find('table').addClass('compact').css('font-size', '10pt');
                }
            }
        ],
        order: [[5, 'desc']], // Ordenar por fecha descendente
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        columnDefs: [
            {
                targets: [6], // Columna de acciones
                orderable: false,
                searchable: false
            },
            {
                targets: [5], // Columna de fecha
                type: 'date-eu'
            }
        ]
    });
});

// Funciones de filtrado
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const faculty = document.getElementById('facultyFilter').value;
    
    let query = '';
    const params = [];
    
    if (status) params.push(`estado=${status}`);
    if (type) params.push(`tipo=${type}`);
    if (category) params.push(`categoria=${category}`);
    if (faculty) params.push(`facultad=${faculty}`);
    
    if (params.length > 0) {
        query = '?' + params.join('&');
    }
    
    window.location.href = `<?php echo BASE_URL; ?>admin/publicaciones${query}`;
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('facultyFilter').value = '';
    
    window.location.href = '<?php echo BASE_URL; ?>admin/publicaciones';
}

// Funciones de gestión
function confirmAction(publicationId, action, publicationTitle) {
    currentPublicationId = publicationId;
    currentAction = action;
    
    const modal = document.getElementById('confirmationModal');
    const message = document.getElementById('modalMessage');
    
    let actionText = '';
    switch(action) {
        case 'activar': actionText = 'activar'; break;
        case 'desactivar': actionText = 'desactivar'; break;
        case 'aprobar': actionText = 'aprobar'; break;
        case 'rechazar': actionText = 'rechazar'; break;
        default: actionText = 'realizar esta acción en';
    }
    
    message.textContent = `¿Estás seguro de que quieres ${actionText} la publicación "${publicationTitle}"?`;
    modal.style.display = 'flex';
    
    document.getElementById('confirmButton').onclick = function() {
        executeAction(publicationId, action);
    };
}

function closeModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    currentPublicationId = null;
    currentAction = null;
}

function executeAction(publicationId, action) {
    showLoading();
    
    // Simulación de llamada a API
    setTimeout(() => {
        hideLoading();
        closeModal();
        
        let message = '';
        switch(action) {
            case 'activar': message = 'Publicación activada correctamente'; break;
            case 'desactivar': message = 'Publicación desactivada correctamente'; break;
            case 'aprobar': message = 'Publicación aprobada correctamente'; break;
            case 'rechazar': message = 'Publicación rechazada correctamente'; break;
            default: message = 'Acción realizada correctamente';
        }
        
        showNotification(message, 'success');
        
        // En una implementación real, aquí se actualizaría la tabla
        console.log(`${action} publicación:`, publicationId);
    }, 1000);
}

function editPublication(publicationId) {
    showNotification('Funcionalidad de edición en desarrollo', 'info');
    console.log('Editar publicación:', publicationId);
}

// Utilidades
function showNotification(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        <div class="alert-content">
            <strong>${type === 'success' ? 'Éxito!' : 'Información!'}</strong>
            <p>${message}</p>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.querySelector('.content').insertBefore(alert, document.querySelector('.dashboard-main'));
    
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

// Cerrar modal al hacer clic fuera
document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Manejar imágenes rotas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.publication-image').forEach(img => {
        img.addEventListener('error', function() {
            this.src = '<?php echo BASE_URL; ?>assets/images/default-product.png';
        });
    });
});
</script>

<?php 
$contenido = ob_get_clean(); 
include 'layout.php'; 
?>