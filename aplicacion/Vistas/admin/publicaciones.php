<?php 
    $vista_actual = 'publicaciones'; 
    $titulo = 'Gestión de Publicaciones';
    $publicaciones = $publicaciones ?? [];
    
    // Variables de filtro
    $estado_filtro = $estado_filtro ?? null;
    $tipo_filtro = $tipo_filtro ?? null;
    $categoria_filtro = $categoria_filtro ?? null;
    $facultad_filtro = $facultad_filtro ?? null;

    // Datos para filtros
    $categorias = $categorias ?? []; 
    $facultades = $facultades ?? []; 
    $tipos_publicacion = ['Producto' => 'Producto', 'Servicio' => 'Servicio'];

    // Estadísticas
    $stats_publicaciones = $stats_publicaciones ?? [
        'total_publicaciones' => 0,
        'publicaciones_activas' => 0,
        'publicaciones_pendientes' => 0,
        'publicaciones_rechazadas' => 0,
        'ingresos_totales' => 0,
        'ingresos_mes' => 0
    ];

    ob_start();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    /* --- ESTILOS DE MÉTRICAS --- */
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
    .metric-card:hover { box-shadow: var(--shadow-lg); }
    .metric-card.primary { border-left-color: var(--admin-primary); }
    .metric-card.success { border-left-color: var(--status-success); }
    .metric-card.warning { border-left-color: var(--status-warning); }
    .metric-card.info { border-left-color: #2196f3; }
    
    .metric-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
    .metric-card.primary .metric-value { color: var(--admin-primary); }
    .metric-card.success .metric-value { color: var(--status-success); }
    .metric-card.warning .metric-value { color: var(--status-warning); }
    .metric-card.info .metric-value { color: #2196f3; }
    
    .metric-label { color: var(--text-secondary); font-size: 0.85rem; font-weight: 500; }
    .metric-sub-label { color: var(--text-muted); font-size: 0.75rem; }
    .metric-icon { font-size: 2rem; margin-left: 1rem; color: var(--text-secondary); opacity: 0.9; }

    /* --- FILTROS --- */
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
    .filter-label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--text-primary); }
    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        width: 100%;
        background: var(--bg-card);
        color: var(--text-primary);
    }
    .filter-actions {
        display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--border-light);
    }

    /* --- BADGES --- */
    .status-badge { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .status-active { background: rgba(46, 175, 125, 0.15); color: var(--status-success); }
    .status-inactive { background: rgba(196, 76, 76, 0.15); color: var(--status-danger); }
    .status-pending { background: rgba(255, 179, 0, 0.15); color: var(--status-warning); }
    .status-rejected { background: rgba(156, 39, 176, 0.15); color: #9c27b0; }

    .type-badge { padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    
    .type-producto {
        background: rgba(111, 66, 193, 0.15); 
        color: #6f42c1;
    }

    .type-servicio {
        background: rgba(253, 126, 20, 0.15);
        color: #fd7e14;
    }

    /* --- TABLA --- */
    tbody { color: var(--text-primary); margin-bottom: 0.2rem; font-size: 0.85rem;}
    .publication-info { display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem;}
    .publication-image { width: 50px; height: 50px; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-light); }
    .publication-title { font-weight: 600; color: var(--text-primary); font-size: 0.85rem; }
    .publication-meta { font-size: 0.75rem; color: var(--text-muted); }
    .seller-name { font-weight: 600;}
    .seller-meta { font-size: 0.75rem; color: var(--text-muted);}
    
    .price-amount { font-weight: 500; font-size: 1rem; }
    
    /* --- ACCIONES --- */
    .actions-container { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .btn-action {
        padding: 0.4rem 0.6rem; border: none; border-radius: 3px; cursor: pointer; font-size: 0.75rem;
        color: white; transition: 0.2s; display: inline-flex; align-items: center; text-decoration: none;
    }
    .btn-view { background: #2196f3; }
    .btn-edit { background: #ff9800; }
    .btn-activate { background: var(--status-success); }
    .btn-deactivate { background: var(--status-danger); }

    /* --- MODAL ESTILO CATEGORIAS (Fondo Opaco) --- */
    .confirmation-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6); /* Fondo más oscuro */
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px); /* Efecto blur */
    }

    .modal-content {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        border: 1px solid var(--border-light);
        text-align: center;
    }

    .modal-header { margin-bottom: 1rem; border: none; padding: 0; }
    .modal-title { color: var(--text-primary); font-size: 1.2rem; margin-bottom: 0.5rem; width: 100%; }
    .modal-message { color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1.5rem; }

    .modal-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem; }
    
    .btn-cancel {
        background: var(--bg-body);
        color: var(--text-primary);
        border: 1px solid var(--border-light);
        padding: 0.5rem 1.2rem;
        border-radius: 6px;
    }
    
    .btn-confirm {
        padding: 0.5rem 1.2rem;
        border-radius: 6px;
        color: white;
        border: none;
    }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['total_publicaciones']); ?></div>
                    <div class="metric-label">Total Publicaciones</div>
                </div>
                <div class="metric-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['publicaciones_activas']); ?></div>
                    <div class="metric-label">Activas</div>
                </div>
                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_publicaciones['publicaciones_pendientes']); ?></div>
                    <div class="metric-label">Pendientes</div>
                </div>
                <div class="metric-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value">S/ <?php echo number_format($stats_publicaciones['ingresos_totales'], 2); ?></div>
                    <div class="metric-label">Ingresos Totales</div>
                </div>
                <div class="metric-icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>

        <div class="filters-container">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Estado</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">Todos</option>
                        <?php $estados_map = [1 => 'Activas', 0 => 'Inactivas', 2 => 'Pendientes', 3 => 'Rechazadas'];
                        foreach ($estados_map as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo (string)$estado_filtro === (string)$value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Tipo</label>
                    <select class="filter-select" id="typeFilter">
                        <option value="">Todos</option>
                        <?php foreach ($tipos_publicacion as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo strtolower($tipo_filtro ?? '') === strtolower($value) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Categoría</label>
                    <select class="filter-select" id="categoryFilter">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" <?php echo (string)$categoria_filtro === (string)$cat['id_categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Facultad</label>
                    <select class="filter-select" id="facultyFilter">
                        <option value="">Todas</option>
                        <?php foreach ($facultades as $fac): ?>
                            <option value="<?php echo htmlspecialchars($fac); ?>" <?php echo strtolower($facultad_filtro ?? '') === strtolower($fac) ? 'selected' : ''; ?>><?php echo htmlspecialchars($fac); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn-action btn-cancel" onclick="resetFilters()"><i class="fas fa-undo"></i> Limpiar</button>
                <button class="btn-action btn-view" onclick="applyFilters()"><i class="fas fa-search"></i> Aplicar</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
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
                            $estado_class = ''; $estado_text = '';
                            switch((int)$pub['estado']) {
                                case 1: $estado_class = 'status-active'; $estado_text = 'Activa'; break;
                                case 0: $estado_class = 'status-inactive'; $estado_text = 'Inactiva'; break;
                                case 2: $estado_class = 'status-pending'; $estado_text = 'Pendiente'; break;
                                case 3: $estado_class = 'status-rejected'; $estado_text = 'Rechazada'; break;
                                default: $estado_class = 'status-inactive'; $estado_text = 'Desconocido';
                            }
                            
                            $imgUrl = !empty($pub['ruta_imagen']) ? obtenerImagenFinal($pub['ruta_imagen']) : BASE_URL . 'assets/images/default-product.png';
                        ?>
                            <tr data-publication-id="<?php echo $pub['id_publicacion']; ?>">
                                <td>
                                    <div class="publication-info">
                                        <img src="<?php echo $imgUrl; ?>" 
                                             alt="Img" 
                                             class="publication-image"
                                             onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>assets/images/default-product.png';">
                                        <div class="publication-details">
                                            <div class="publication-title"><?php echo htmlspecialchars($pub['titulo']); ?></div>
                                            <div class="publication-meta">
                                                <span class="type-badge type-<?php echo strtolower($pub['tipo']); ?>"><?php echo $pub['tipo']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="seller-info">
                                        <div class="seller-name"><?php echo htmlspecialchars(($pub['nombres'] ?? '') . ' ' . ($pub['apellidos'] ?? '')); ?></div>
                                        <div class="seller-meta"><?php echo htmlspecialchars($pub['facultad'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($pub['nombre_categoria']); ?></td>
                                <td><div class="price-amount">S/ <?php echo number_format($pub['precio'] ?? 0, 2); ?></div></td>
                                <td><span class="status-badge <?php echo $estado_class; ?>"><?php echo $estado_text; ?></span></td>
                                <td><?php echo $pub['fecha_publicacion'] ? date('d/m/Y', strtotime($pub['fecha_publicacion'])) : 'N/A'; ?></td>
                                <td>
                                    <div class="actions-container">
                                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $pub['id_publicacion']; ?>" target="_blank" class="btn-action btn-view" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button class="btn-action btn-edit" onclick="editPublication(<?php echo $pub['id_publicacion']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <?php if ($pub['estado'] == 1): ?>
                                            <button class="btn-action btn-deactivate" 
                                                    onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'desactivar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')" 
                                                    title="Desactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>

                                        <?php elseif ($pub['estado'] == 0): ?>
                                            <button class="btn-action btn-activate" 
                                                    onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'activar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')" 
                                                    title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>

                                        <?php elseif ($pub['estado'] == 2): ?>
                                            <button class="btn-action btn-activate" 
                                                    onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'aprobar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')" 
                                                    title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn-action btn-deactivate" 
                                                    onclick="confirmAction(<?php echo $pub['id_publicacion']; ?>, 'rechazar', '<?php echo htmlspecialchars(addslashes($pub['titulo'])); ?>')" 
                                                    title="Rechazar">
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
    </div>
</div>

<div class="confirmation-modal" id="confirmationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Confirmar</h3>
        </div>
        <p class="modal-message" id="modalMessage">¿Está seguro?</p>
        <div class="modal-actions">
            <button class="btn-action btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-action btn-confirm" id="confirmButton">Confirmar</button>
        </div>
    </div>
</div>

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
    let currentPublicationId = null;
    let currentAction = null;
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#publicacionesTable').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
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
                { extend: 'excel', className: 'btn-success', text: '<i class="fas fa-file-excel"></i>' },
                { extend: 'pdf', className: 'btn-danger', text: '<i class="fas fa-file-pdf"></i>'},
                { extend: 'print', className: 'btn-info', text: '<i class="fas fa-print"></i>' }
            ],
            order: [[5, 'desc']],
            columnDefs: [ { targets: [6], orderable: false }, { targets: [5], type: 'date-eu' } ]
        });
    });

    function applyFilters() {
        const params = [];
        ['statusFilter', 'typeFilter', 'categoryFilter', 'facultyFilter'].forEach((id, idx) => {
            const val = document.getElementById(id).value;
            const keys = ['estado', 'tipo', 'categoria', 'facultad'];
            if(val) params.push(`${keys[idx]}=${val}`);
        });
        window.location.href = `<?php echo BASE_URL; ?>admin/publicaciones` + (params.length ? '?' + params.join('&') : '');
    }

    function resetFilters() {
        window.location.href = '<?php echo BASE_URL; ?>admin/publicaciones';
    }

    function confirmAction(pubId, action, title) {
        currentPublicationId = pubId;
        currentAction = action;
        
        const modal = document.getElementById('confirmationModal');
        const msgEl = document.getElementById('modalMessage');
        const titleEl = document.getElementById('modalTitle');
        const btn = document.getElementById('confirmButton');

        // Resetear estilos botón
        btn.className = 'btn-action btn-confirm';
        
        let verb = '';
        if(action === 'activar' || action === 'aprobar') { 
            verb = 'activar/aprobar'; 
            btn.classList.add('btn-activate');
            btn.style.backgroundColor = 'var(--status-success)';
        } else { 
            verb = 'desactivar/rechazar'; 
            btn.classList.add('btn-deactivate');
            btn.style.backgroundColor = 'var(--status-danger)';
        }

        titleEl.textContent = 'Confirmar Acción';
        msgEl.innerHTML = `¿Deseas ${verb} la publicación "${title}"?`;
        
        // Mostrar modal
        modal.style.display = 'flex';
        
        btn.onclick = () => executeAction(pubId, action);
    }

    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
        currentPublicationId = null;
    }

    function executeAction(pubId, action) {
        if(typeof showLoading === 'function') showLoading();
        
        let newState = (action === 'activar' || action === 'aprobar') ? 1 : (action === 'rechazar' ? 3 : 0);

        closeModal();
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/cambiar-estado-publicacion', 
            method: 'POST',
            // [CORRECCIÓN] Enviar como JSON explícito
            contentType: 'application/json',
            data: JSON.stringify({ 
                id_publicacion: pubId, 
                estado: newState 
            }),
            dataType: 'json',
            success: function(res) {
                if(typeof hideLoading === 'function') hideLoading();
                
                if (res.success) {
                    // Recargar para ver los cambios
                    location.reload();
                } else {
                    if(typeof showNotification === 'function') {
                        showNotification(res.error || 'Error al cambiar estado.', 'error');
                    } else {
                        alert(res.error || 'Error al cambiar estado.');
                    }
                }
            },
            error: function(xhr, status, error) {
                if(typeof hideLoading === 'function') hideLoading();
                
                // [MEJORA] Intentar leer el mensaje de error real del servidor
                let errorMsg = 'Error de conexión.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    // A veces el error viene en texto plano
                    console.error("Error respuesta:", xhr.responseText); 
                }
                
                if(typeof showNotification === 'function') {
                    showNotification(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        });
    }
    
    function updatePublicationTableRow(pubId, newState) {
        location.reload(); 
    }    

    function editPublication(id) {
        window.location.href = '<?php echo BASE_URL; ?>publicaciones/editar/' + id;
    }

    // Cerrar modal con click fuera
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

<?php 
    $contenido = ob_get_clean(); 
    include 'layout.php'; 
?>