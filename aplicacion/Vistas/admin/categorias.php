<?php 
    $vista_actual = 'categorias'; 
    $titulo = 'Gestión de Categorías';
    
    // Variables recibidas del controlador
    $categorias = $categorias ?? [];
    $stats_categorias = $stats_categorias ?? [
        'total_categorias' => 0,
        'categorias_activas' => 0,
        'total_publicaciones' => 0,
        'categoria_popular' => 'N/A'
    ];

    ob_start();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    /* --- METRIC CARDS (Estilo Unificado con Dashboard) --- */
    .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    
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
    
    /* Colores de Borde */
    .metric-card.primary { border-left-color: var(--admin-primary); }
    .metric-card.success { border-left-color: var(--status-success); }
    .metric-card.warning { border-left-color: var(--status-warning); }
    .metric-card.info { border-left-color: #2196f3; }
    
    /* Contenido de la Tarjeta */
    .metric-content { flex: 1; }
    
    .metric-value { 
        font-size: 1.75rem; 
        font-weight: 700; 
        margin-bottom: 0.25rem; 
        color: var(--text-primary); 
    }
    
    /* Colores del Valor según tipo */
    .metric-card.primary .metric-value { color: var(--admin-primary); }
    .metric-card.success .metric-value { color: var(--status-success); }
    .metric-card.warning .metric-value { color: var(--status-warning); }
    .metric-card.info .metric-value { color: #2196f3; }
    
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
        opacity: 0.8; 
    }

    /* --- FORMULARIO --- */
    .form-container { background: var(--bg-card); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow); margin-bottom: 2rem; }
    .icon-selector-group { display: flex; gap: 10px; }
    .selected-icon-preview { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); border: 1px solid var(--border-light); border-radius: 8px; font-size: 1.4rem; color: var(--text-primary); }
    
    /* --- TABLA --- */
    .category-info { display: flex; align-items: center; gap: 1rem; font-size: 0.85rem}
    .status-badge { padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .status-active { background: rgba(46, 175, 125, 0.15); color: var(--status-success); }
    .status-inactive { background: rgba(196, 76, 76, 0.15); color: var(--status-danger); }
    .actions-container { display: flex; gap: 5px; flex-wrap: wrap; }
    .btn-action { padding: 6px 10px; border-radius: 3px; border: none; cursor: pointer; color: white; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .btn-edit { background: #ff9800; }
    .btn-deactivate { background: var(--status-danger); }
    .btn-activate { background: var(--status-success); }
    .btn-delete { background: #6c757d; }
    .btn-action:hover { opacity: 0.9; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    /* --- MODALES --- */
    .icon-modal-overlay, .confirmation-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
    .icon-modal-content, .modal-content { background: var(--bg-card); width: 80%; max-width: 600px; border-radius: 6px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 10px 40px rgba(0,0,0,0.3); border: 1px solid var(--border-light); }
    .icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 10px; overflow-y: auto; padding: 15px; background: var(--bg-body); border-radius: 0px; border: 1px solid var(--border-light); margin-top: 1rem; max-height: 400px; }
    .icon-option { aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; border-radius: 4px; transition: all 0.2s; color: var(--text-secondary); font-size: 1.5rem; background: var(--bg-card); border: 1px solid transparent; }
    .icon-option:hover { background: var(--admin-primary); color: white; transform: scale(1.02); border-color: var(--admin-primary); }
    
    .modal-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem; }
    .btn-cancel { background: var(--bg-body); color: var(--text-primary); border: 1px solid var(--border-light); padding: 0.5rem 1.2rem; border-radius: 6px; }
    .btn-confirm { padding: 0.5rem 1.2rem; border-radius: 6px; color: white; border: none; }
    .btn-confirm-danger { background: var(--status-danger); }
    .btn-confirm-success { background: var(--status-success); }

    #loadingOverlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; color: white; font-size: 1.5rem; }
    
    @media (max-width: 768px) { .icon-modal-content { height: 90vh; } }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value" id="metric-total"><?php echo number_format($stats_categorias['total_categorias']); ?></div>
                    <div class="metric-label">Categorías</div>
                    <div class="metric-sub-label">Total registradas</div>
                </div>
                <div class="metric-icon"><i class="fas fa-tags"></i></div>
            </div>

            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value" id="metric-active"><?php echo number_format($stats_categorias['categorias_activas']); ?></div>
                    <div class="metric-label">Activas</div>
                    <div class="metric-sub-label">Disponibles</div>
                </div>
                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
            </div>

            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value" id="metric-pubs"><?php echo number_format($stats_categorias['total_publicaciones']); ?></div>
                    <div class="metric-label">Publicaciones</div>
                    <div class="metric-sub-label">Asociadas</div>
                </div>
                <div class="metric-icon"><i class="fas fa-box-open"></i></div>
            </div>

            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value" id="metric-popular" style="font-size: 1.4rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;" title="<?php echo htmlspecialchars($stats_categorias['categoria_popular']); ?>">
                        <?php echo htmlspecialchars($stats_categorias['categoria_popular']); ?>
                    </div>
                    <div class="metric-label">Popular</div>
                    <div class="metric-sub-label">Más usada</div>
                </div>
                <div class="metric-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>

        <div class="form-container" id="formContainer">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="form-title m-0" id="formTitle"><i class="fas fa-plus-circle"></i> Crear Nueva Categoría</h3>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetForm()"><i class="fas fa-eraser"></i> Limpiar</button>
            </div>
            
            <form id="categoriaForm">
                <input type="hidden" name="action" id="formAction" value="guardar">
                <input type="hidden" name="id_categoria" id="id_categoria" value="">
                <input type="hidden" name="total_publicaciones_hidden" id="total_publicaciones_hidden" value="0">
                <input type="hidden" name="fecha_creacion_hidden" id="fecha_creacion_hidden" value="">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Tecnología">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Icono</label>
                        <div class="icon-selector-group">
                            <div class="selected-icon-preview" id="iconPreview"><i class="fas fa-tag"></i></div>
                            <input type="hidden" name="icono" id="iconoInput" value="fas fa-tag">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="openIconModal()"><i class="fas fa-search me-1"></i> Seleccionar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="1">Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" id="color" class="form-control form-control-color" value="#00bcd4">
                            <small class="text-muted">Color distintivo</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="2" placeholder="Descripción..."></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Guardar Categoría</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="categoriasTable" class="table table-striped dt-responsive nowrap" style="width:100%">
                    <thead><tr><th>Categoría</th><th>ID</th><th>Publicaciones</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr id="row-<?php echo $cat['id_categoria']; ?>" data-json='<?php echo htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8'); ?>'>
                                <td>
                                    <div class="category-info">
                                        <i class="<?php echo htmlspecialchars($cat['icono']); ?>" style="color: <?php echo htmlspecialchars($cat['color']); ?>; font-size: 1.4rem; width: 30px; text-align: center;"></i>
                                        <div><div class="fw-bold"><?php echo htmlspecialchars($cat['nombre_categoria']); ?></div><small class="text-muted"><?php echo htmlspecialchars($cat['descripcion']); ?></small></div>
                                    </div>
                                </td>
                                <td><?php echo $cat['id_categoria']; ?></td>
                                <td><?php echo $cat['total_publicaciones']; ?></td>
                                <td><span class="status-badge <?php echo $cat['estado'] == 1 ? 'status-active' : 'status-inactive'; ?>"><?php echo $cat['estado'] == 1 ? 'Activa' : 'Inactiva'; ?></span></td>
                                <td><?php 
                                    $fecha = $cat['fecha_creacion'] ?? null;
                                    $fechaSafe = $fecha ? explode(' ', $fecha)[0] : null;
                                    echo $fechaSafe ? date('d/m/Y', strtotime($fechaSafe)) : 'N/A'; 
                                ?></td>
                                <td>
                                    <div class="actions-container">
                                        <button class="btn-action btn-edit" onclick="editarCategoria(<?php echo $cat['id_categoria']; ?>)" title="Editar"><i class="fas fa-edit"></i></button>
                                        <?php if ($cat['estado'] == 1): ?>
                                            <button class="btn-action btn-deactivate" onclick="confirmAction(<?php echo $cat['id_categoria']; ?>, 'desactivar', '<?php echo htmlspecialchars(addslashes($cat['nombre_categoria'])); ?>')" title="Desactivar"><i class="fas fa-ban"></i></button>
                                        <?php else: ?>
                                            <button class="btn-action btn-activate" onclick="confirmAction(<?php echo $cat['id_categoria']; ?>, 'activar', '<?php echo htmlspecialchars(addslashes($cat['nombre_categoria'])); ?>')" title="Activar"><i class="fas fa-check"></i></button>
                                        <?php endif; ?>
                                        <button class="btn-action btn-delete" onclick="confirmAction(<?php echo $cat['id_categoria']; ?>, 'eliminar', '<?php echo htmlspecialchars(addslashes($cat['nombre_categoria'])); ?>')" title="Eliminar"><i class="fas fa-trash"></i></button>
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

<div class="icon-modal-overlay" id="iconModal">
    <div class="icon-modal-content">
        <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="m-0">Seleccionar Icono</h4><button type="button" class="btn-close" onclick="closeIconModal()"></button></div>
        <input type="text" class="form-control mb-3" id="iconSearch" placeholder="Buscar...">
        <div class="icon-grid" id="iconGrid"></div>
    </div>
</div>

<div class="confirmation-modal" id="confirmationModal">
    <div class="modal-content">
        <div class="modal-header mb-3"><h4 class="modal-title m-0 text-center w-100" id="modalTitle">Confirmar</h4></div>
        <p class="modal-message mb-4 text-center" id="modalMessage">¿Estás seguro?</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-confirm" id="confirmButton">Confirmar</button>
        </div>
    </div>
</div>

<div id="loadingOverlay"><div class="text-center"><i class="fas fa-spinner fa-spin fa-3x mb-3"></i><div>Procesando...</div></div></div>

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
    let table;
    let currentCategoryId = null;
    let currentAction = null;
    const fontAwesomeIcons = ["fas fa-tag", "fas fa-tags", "fas fa-bookmark", "fas fa-book", "fas fa-laptop", "fas fa-desktop", "fas fa-mobile-alt", "fas fa-camera", "fas fa-tshirt", "fas fa-shopping-bag", "fas fa-gift", "fas fa-gamepad", "fas fa-music", "fas fa-headphones", "fas fa-video", "fas fa-film", "fas fa-bicycle", "fas fa-car", "fas fa-motorcycle", "fas fa-tools", "fas fa-wrench", "fas fa-home", "fas fa-couch", "fas fa-bed", "fas fa-utensils", "fas fa-coffee", "fas fa-pizza-slice", "fas fa-apple-alt", "fas fa-heart", "fas fa-star", "fas fa-trophy", "fas fa-medal", "fas fa-futbol", "fas fa-basketball-ball", "fas fa-running", "fas fa-dumbbell", "fas fa-paw", "fas fa-dog", "fas fa-cat", "fas fa-leaf", "fas fa-tree", "fas fa-seedling", "fas fa-briefcase", "fas fa-building", "fas fa-calculator", "fas fa-chart-line", "fas fa-graduation-cap", "fas fa-university", "fas fa-microscope", "fas fa-flask", "fas fa-palette", "fas fa-paint-brush", "fas fa-plane", "fas fa-globe", "fas fa-map-marker-alt", "fas fa-clock"];

    $(document).ready(function() {
        table = $('#categoriasTable').DataTable({ 
            responsive: true, 
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
            dom: '<"row"<"col-sm-12 col-md-7"B><"col-sm-12 col-md-2"l><"col-sm-12 col-md-3"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i>', className: 'btn btn-success' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i>', className: 'btn btn-danger'},
                { extend: 'print', text: '<i class="fas fa-print"></i>', className: 'btn btn-info' }
            ],
            order: [[4, 'desc']] 
        });
        renderIcons(fontAwesomeIcons);
        $('#iconSearch').on('input', function() { renderIcons(fontAwesomeIcons.filter(icon => icon.includes($(this).val().toLowerCase()))); });
    });

    function openIconModal() { $('#iconModal').css('display', 'flex'); $('#iconSearch').focus(); }
    function closeIconModal() { $('#iconModal').css('display', 'none'); }
    function renderIcons(icons) {
        const grid = $('#iconGrid').empty();
        if(icons.length === 0) { grid.html('<p class="text-center w-100">No encontrado</p>'); return; }
        icons.forEach(i => grid.append($('<div>').addClass('icon-option').html(`<i class="${i}"></i>`).on('click', () => { $('#iconoInput').val(i); $('#iconPreview').html(`<i class="${i}"></i>`); closeIconModal(); })));
    }

    function escapeHtml(text) { if (text == null) return ''; return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;"); }

    // Actualizar contadores en tarjetas
    function updateMetrics(stats) {
        if(!stats) return;
        $('#metric-total').text(stats.total_categorias);
        $('#metric-active').text(stats.categorias_activas);
        $('#metric-pubs').text(stats.total_publicaciones);
        $('#metric-popular').text(stats.categoria_popular);
    }

    // Manejo del Formulario (Guardar/Actualizar)
    $('#categoriaForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submitBtn');
        const original = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/guardar-categoria', 
            method: 'POST', 
            data: $(this).serialize(), 
            dataType: 'json',
            success: function(res) {
                if (res.success) { 
                    updateTable(res.data.categoria, res.data.id_nuevo); 
                    updateMetrics(res.data.stats); 
                    resetForm(); 
                } 
                else { 
                    alert('Error: ' + res.message);
                    btn.html(original);
                }
            },
            error: () => {
                alert('Error de conexión');
                btn.html(original);
            },
            complete: () => {
                btn.prop('disabled', false);
            }
        });
    });

    function updateTable(cat, isNewId) {
        const id = isNewId ? isNewId : cat.id_categoria;
        // Botones de Acción
        const btnEstado = cat.estado == 1 
            ? `<button class="btn-action btn-deactivate" onclick="confirmAction(${id}, 'desactivar', '${escapeHtml(cat.nombre_categoria)}')" title="Desactivar"><i class="fas fa-ban"></i></button>`
            : `<button class="btn-action btn-activate" onclick="confirmAction(${id}, 'activar', '${escapeHtml(cat.nombre_categoria)}')" title="Activar"><i class="fas fa-check"></i></button>`;
        const actions = `<div class="actions-container"><button class="btn-action btn-edit" onclick="editarCategoria(${id})" title="Editar"><i class="fas fa-edit"></i></button>${btnEstado}<button class="btn-action btn-delete" onclick="confirmAction(${id}, 'eliminar', '${escapeHtml(cat.nombre_categoria)}')" title="Eliminar"><i class="fas fa-trash"></i></button></div>`;
        
        // Badge estado
        const badge = `<span class="status-badge ${cat.estado == 1 ? 'status-active' : 'status-inactive'}">${cat.estado == 1 ? 'Activa' : 'Inactiva'}</span>`;
        
        // Info categoría
        const info = `<div class="category-info"><i class="${escapeHtml(cat.icono)}" style="color:${escapeHtml(cat.color)};font-size:1.4rem;width:30px;text-align:center;"></i><div><div class="fw-bold">${escapeHtml(cat.nombre_categoria)}</div><small class="text-muted">${escapeHtml(cat.descripcion)}</small></div></div>`;
        
        // Corrección de fecha (Limpiar hora y reemplazar T)
        let fecha = 'N/A';
        if (cat.fecha_creacion) { 
            let dateOnly = cat.fecha_creacion.replace('T', ' ').split(' ')[0]; 
            const p = dateOnly.split('-'); 
            fecha = p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : dateOnly; 
        }

        const json = JSON.stringify(cat);
        
        if (isNewId) {
            const row = table.row.add([info, id, 0, badge, fecha, actions]).draw(false).node();
            $(row).attr('id', `row-${id}`).attr('data-json', json);
        } else {
            const row = table.row(`#row-${id}`);
            $(row.node()).attr('data-json', json);
            // Mantener conteo original si es actualización
            row.data([info, id, cat.total_publicaciones, badge, fecha, actions]).draw(false);
        }
    }

    function editarCategoria(id) {
        const tr = $(`#row-${id}`);
        const json = tr.attr('data-json');
        if (!json) return;
        
        try {
            const d = JSON.parse(json);
            $('#id_categoria').val(d.id_categoria); 
            $('#nombre').val(d.nombre_categoria); 
            $('#descripcion').val(d.descripcion);
            $('#iconoInput').val(d.icono); 
            $('#iconPreview').html(`<i class="${d.icono}"></i>`); 
            $('#color').val(d.color);
            $('#estado').val(d.estado); 
            $('#total_publicaciones_hidden').val(d.total_publicaciones || 0); 
            $('#fecha_creacion_hidden').val(d.fecha_creacion || '');
            
            // Cambiar estado visual del formulario a "Editar"
            $('#formTitle').html('<i class="fas fa-edit"></i> Editar Categoría ' + id); 
            $('#formAction').val('guardar'); 
            $('#submitBtn').html('<i class="fas fa-sync"></i> Actualizar Categoría');
            
            $('html, body').animate({ scrollTop: $("#formContainer").offset().top - 100 }, 500);
        } catch(e) { console.error(e); }
    }

    function confirmAction(id, action, name) {
        currentCategoryId = id; currentAction = action;
        const btn = $('#confirmButton').removeClass('btn-confirm-danger btn-confirm-success');
        let title = 'Confirmar';
        
        if(action === 'activar') { title='Activar Categoría'; btn.addClass('btn-confirm-success').text('Activar'); }
        else if(action === 'desactivar') { title='Desactivar Categoría'; btn.addClass('btn-confirm-danger').text('Desactivar'); }
        else { title='Eliminar Categoría'; btn.addClass('btn-confirm-danger').text('Eliminar'); }
        
        $('#modalTitle').text(title);
        $('#modalMessage').text(`¿Seguro que deseas ${action} "${name}"?`);
        $('#confirmationModal').css('display', 'flex');
        
        btn.off('click').on('click', () => executeAction(id, action));
    }

    function closeModal() { $('#confirmationModal').hide(); }
    
    function executeAction(id, action) {
        closeModal(); 
        $('#loadingOverlay').show();

        // Definir URL y datos según la acción
        let url = '';
        let data = { id_categoria: id };

        if (action === 'eliminar') {
            url = '<?php echo BASE_URL; ?>admin/eliminar-categoria';
        } else {
            // Activar o Desactivar
            url = '<?php echo BASE_URL; ?>admin/cambiar-estado-categoria';
            data.estado_actual = (action === 'activar' ? 0 : 1);
        }

        const tr = $(`#row-${id}`);
        let d = JSON.parse(tr.attr('data-json') || '{}');

        $.ajax({
            url: url, 
            type: 'POST', 
            dataType: 'json',
            data: data,
            success: function(res) {
                $('#loadingOverlay').hide();
                if (res.success) {
                    if (action === 'eliminar') {
                        table.row(`#row-${id}`).remove().draw(false);
                    } else { 
                        // Actualizar dato local y tabla
                        d.estado = res.data.nuevo_estado; 
                        updateTable(d, null); 
                    }

                    let stats = res.data.stats || res.data; 
                    if (stats && stats.total_categorias) {
                        updateMetrics(stats);
                    }

                } else { 
                    alert(res.message); 
                }
            },
            error: () => { 
                $('#loadingOverlay').hide(); 
                alert('Error de conexión'); 
            }
        });
    }

    function resetForm() {
        $('#categoriaForm')[0].reset(); 
        $('#id_categoria').val(''); 
        $('#iconoInput').val('fas fa-tag');
        $('#iconPreview').html('<i class="fas fa-tag"></i>'); 
        $('#color').val('#00bcd4');
        $('#total_publicaciones_hidden').val('0');
        $('#fecha_creacion_hidden').val('');
        
        // Restaurar estado visual del formulario a "Crear"
        $('#formTitle').html('<i class="fas fa-plus-circle"></i> Crear Nueva Categoría'); 
        $('#submitBtn').html('<i class="fas fa-save"></i> Guardar Categoría');
        $('#formAction').val('guardar');
    }
    
    $('#confirmationModal').on('click', function(e) { if (e.target === this) closeModal(); });
</script>

<?php $contenido = ob_get_clean(); require_once 'layout.php'; ?>