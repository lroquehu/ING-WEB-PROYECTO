<?php 
    $vista_actual = 'usuarios'; 
    $titulo = 'Gestión de Usuarios';

    $usuarios = $usuarios ?? [];
    $total_usuarios = $total_usuarios ?? 0;
    $stats_usuarios_general = $stats_usuarios_general ?? [
        'total_usuarios' => 0, 
        'usuarios_activos' => 0, 
        'usuarios_inactivos' => 0
    ];
    $vendedores_activos = $vendedores_activos ?? 0;
    $nuevos_este_mes = $nuevos_este_mes ?? 0; 

    ob_start();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    /* Estilos existentes */
    .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .metric-card { background: var(--bg-card); padding: 1.25rem; border-radius: 10px; box-shadow: var(--shadow); transition: all 0.3s ease; border-left: 4px solid; display: flex; justify-content: space-between; align-items: center; }
    .metric-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
    .metric-card.primary { border-left-color: var(--admin-primary); }
    .metric-card.success { border-left-color: var(--status-success); }
    .metric-card.warning { border-left-color: var(--status-warning); }
    .metric-card.info { border-left-color: #2196f3; }
    .metric-content { flex: 1; }
    .metric-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-primary); }
    .metric-card.primary .metric-value { color: var(--admin-primary); }
    .metric-card.success .metric-value { color: var(--status-success); }
    .metric-card.warning .metric-value { color: var(--status-warning); }
    .metric-card.info .metric-value { color: #2196f3; }
    .metric-label { color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem; font-weight: 500; }
    .metric-sub-label { color: var(--text-muted); font-size: 0.75rem; }
    .metric-icon { font-size: 2rem; margin-left: 1rem; color: var(--text-secondary); opacity: 0.9; }
    
    .user-info { display: flex; align-items: center; gap: 0.75rem; }
    
    /* IMAGEN CUADRADA */
    .user-avatar-small { 
        width: 48px; 
        height: 48px; 
        object-fit: cover; 
        border: 1px solid var(--border-light); 
    }
    
    .user-details { display: flex; flex-direction: column; justify-content: center; }
    .user-name { font-weight: 600; color: var(--text-primary); margin-bottom: 0.2rem; font-size: 0.9rem; }
    .user-meta { font-size: 0.75rem; color: var(--text-muted); }
    
    .status-badge { padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-active { background: rgba(46, 175, 125, 0.15); color: var(--status-success); }
    .status-inactive { background: rgba(196, 76, 76, 0.15); color: var(--status-danger); }
    
    /* BOTONES DE ACCIÓN (INTACTOS) */
    .actions-container { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .btn-action { padding: 0.4rem 0.6rem; border: none; border-radius: 3px; cursor: pointer; font-size: 0.75rem; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.2rem; text-decoration: none; color: white; }
    .btn-action:hover { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); opacity: 0.9; }
    .btn-view { background: #2196f3; }
    .btn-deactivate { background: var(--status-danger); }
    .btn-activate { background: var(--status-success); }
    .btn-delete { background: #6c757d; }
    
    /* MODALES */
    .confirmation-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
    
    /* MODAL CONTENT: Tamaño reducido y sin scroll */
    .modal-content { 
        background: var(--bg-card); 
        border-radius: 12px; 
        padding: 1.5rem; 
        max-width: 600px; /* Reducido de 700px a 600px */
        width: 90%; 
        box-shadow: var(--shadow-lg); 
        font-size: 0.85rem; 
        border: 1px solid var(--border-light);
    }
    
    .modal-header { margin-bottom: 1rem; padding-bottom: 0.8rem; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; }
    .modal-title { color: var(--text-primary); margin: 0; font-size: 1.1rem; font-weight: 700; }
    .modal-close { background: none; border: none; font-size: 1.1rem; color: var(--text-muted); cursor: pointer; }
    .modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.2rem; }
    .btn-cancel { background: var(--bg-body); color: var(--text-primary); border: 1px solid var(--border-light); padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.85rem;}
    .btn-confirm { background: var(--status-danger); color: white; padding: 0.4rem 1rem; border-radius: 6px; border: none; font-size: 0.85rem;}

    /* Estilos Detalle */
    .detail-grid { display: grid; grid-template-columns: 1fr; gap: 0.5rem; }
    .detail-item { margin-bottom: 0.3rem; display: flex; flex-direction: column; }
    .detail-label { font-weight: 600; color: var(--text-secondary); font-size: 0.75rem; }
    .detail-value { color: var(--text-primary); font-size: 0.9rem; word-break: break-word; }
    
    .detail-section-title { 
        font-size: 0.85rem; 
        font-weight: 700; 
        color: var(--admin-primary); 
        margin: 0 0 0.8rem 0; 
        padding-bottom: 0.3rem; 
        border-bottom: 1px solid var(--border-light); 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Caja Stats */
    .stats-box {
        padding: 0.8rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        background-color: var(--bg-body);
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .text-highlight { color: var(--admin-primary); }

    /* MODO OSCURO */
    body.dark-mode .detail-section-title { color: #64b5f6 !important; border-bottom-color: var(--border-light); }
    body.dark-mode .text-highlight { color: #64b5f6 !important; } 
    body.dark-mode .stats-box { background-color: rgba(255, 255, 255, 0.03); }

    /* IMAGEN MODAL */
    .profile-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.2rem; }
    .profile-img-lg { 
        width: 65px; 
        height: 65px; 
        border-radius: 10px; 
        object-fit: cover; 
        border: 1px solid var(--border-light); 
    }
    
    /* DATATABLES DARK MODE */
    body.dark-mode .dataTables_wrapper .dataTables_length,
    body.dark-mode .dataTables_wrapper .dataTables_filter,
    body.dark-mode .dataTables_wrapper .dataTables_info,
    body.dark-mode .dataTables_wrapper .dataTables_processing,
    body.dark-mode .dataTables_wrapper .dataTables_paginate {
        color: var(--text-secondary);
    }
    body.dark-mode .dataTables_wrapper .dataTables_length select,
    body.dark-mode .dataTables_wrapper .dataTables_filter input {
        color: var(--text-primary);
        border: 1px solid var(--border-light);
    }
    body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
        background-color: rgba(255,255,255,0.05);
        color: var(--text-primary);
    }
    body.dark-mode .table-striped > tbody > tr:nth-of-type(even) > * {
        background-color: var(--bg-card);
        color: var(--text-primary);
    }
    body.dark-mode .table > :not(caption) > * > * {
        background-color: transparent;
        color: var(--text-primary);
        border-bottom-color: var(--border-light);
    }
    
    #toast-container { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
    .toast-notification {
        background: var(--bg-card); color: var(--text-primary); padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-top: 10px; display: flex; align-items: center; gap: 10px; min-width: 300px; animation: slideInToast 0.3s ease forwards; border-left: 5px solid;
    }
    .toast-success { border-left-color: var(--status-success); }
    .toast-error { border-left-color: var(--status-danger); }
    @keyframes slideInToast { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes fadeOutToast { to { transform: translateX(100%); opacity: 0; } }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_usuarios_general['total_usuarios']); ?></div>
                    <div class="metric-label">Total Usuarios</div>
                    <div class="metric-sub-label">Registrados</div>
                </div>
                <div class="metric-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_usuarios_general['usuarios_activos']); ?></div>
                    <div class="metric-label">Activos</div>
                    <div class="metric-sub-label">Acceso permitido</div>
                </div>
                <div class="metric-icon"><i class="fas fa-user-check"></i></div>
            </div>
            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($nuevos_este_mes); ?></div>
                    <div class="metric-label">Nuevos (Mes)</div>
                    <div class="metric-sub-label">Registros recientes</div>
                </div>
                <div class="metric-icon"><i class="fas fa-user-plus"></i></div>
            </div>
            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($vendedores_activos); ?></div>
                    <div class="metric-label">Vendedores</div>
                    <div class="metric-sub-label">Con publicaciones</div>
                </div>
                <div class="metric-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="usuariosTable" class="table table-striped dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Facultad</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr data-user-id="<?php echo $usuario['id_usuario']; ?>">
                                <td>
                                    <div class="user-info">
                                        <img src="<?php 
                                            $rutaImagen = BASE_URL . 'assets/iconos/user.webp';
                                            if (!empty($usuario['foto_perfil'])) {
                                                $rutaImagen = BASE_URL . 'assets/uploads/usuarios/' . $usuario['id_usuario'] . '/' . $usuario['foto_perfil'];
                                            }
                                            echo $rutaImagen;
                                            ?>" 
                                            alt="<?php echo htmlspecialchars($usuario['nombres']); ?>" 
                                            class="user-avatar-small"
                                            onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>assets/iconos/user.webp';">
                                        
                                        <div class="user-details">
                                            <div class="user-name">
                                                <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?>
                                            </div>
                                            <div class="user-meta">
                                                ID: <?php echo htmlspecialchars($usuario['id_usuario']); ?> | <?php echo htmlspecialchars($usuario['dni']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['facultad'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($usuario['correo_institucional']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $usuario['estado'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $usuario['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $fecha = $usuario['fecha_registro'] ?? null;
                                    echo $fecha ? date('d/m/Y', strtotime($fecha)) : 'N/A'; 
                                    ?>
                                </td>
                                <td>
                                    <div class="actions-container">
                                        <button class="btn-action btn-view" 
                                                onclick="verUsuario(<?php echo $usuario['id_usuario']; ?>)" 
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if (($usuario['id_usuario'] ?? 0) != ($_SESSION['usuario_id'] ?? 0)): ?>
                                            <?php if ($usuario['estado'] == 1): ?>
                                                <button class="btn-action btn-deactivate" 
                                                        onclick="confirmAction(<?php echo $usuario['id_usuario']; ?>, 'suspender', '<?php echo htmlspecialchars($usuario['nombres']); ?>')"
                                                        title="Suspender cuenta">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-activate" 
                                                        onclick="confirmAction(<?php echo $usuario['id_usuario']; ?>, 'activar', '<?php echo htmlspecialchars($usuario['nombres']); ?>')"
                                                        title="Activar cuenta">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php endif; ?>

                                            <button class="btn-action btn-delete" 
                                                    onclick="confirmAction(<?php echo $usuario['id_usuario']; ?>, 'eliminar', '<?php echo htmlspecialchars($usuario['nombres']); ?>')"
                                                    title="Eliminar permanentemente">
                                                <i class="fas fa-trash"></i>
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

<div class="confirmation-modal" id="genericModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Confirmar Acción</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-3 text-center">
            <p id="modalMessage" style="margin: 1rem 0; font-size: 1.1rem;">¿Estás seguro?</p>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-confirm" id="confirmButton">Confirmar</button>
        </div>
    </div>
</div>

<div class="confirmation-modal" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Detalles del Usuario</h3>
            <button class="modal-close" onclick="closeDetailModal()"><i class="fas fa-times"></i></button>
        </div>
        
        <div id="detailContent">
            <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
        </div>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeDetailModal()">Cerrar</button>
        </div>
    </div>
</div>

<div class="confirmation-modal" id="suspendModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Suspender Usuario</h3>
            <button class="modal-close" onclick="closeSuspendModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-3">
            <p>Vas a suspender a: <b id="suspendUserName"></b></p>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Suspender hasta:</label>
                <input type="datetime-local" id="suspendDate" class="form-control">
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Motivo:</label>
                <textarea id="suspendReason" class="form-control" rows="3" placeholder="Ej: Comportamiento inapropiado..."></textarea>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeSuspendModal()">Cancelar</button>
            <button class="btn-confirm" onclick="submitSuspension()" style="background-color: var(--status-warning);">Suspender</button>
        </div>
    </div>
</div>

<div id="toast-container"></div>

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
    let currentUserId = null;
    let currentAction = null;
    let suspendUserId = null;
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#usuariosTable').DataTable({
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
                { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fas fa-file-excel"></i>', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'pdf', className: 'btn btn-danger btn-sm', text: '<i class="fas fa-file-pdf"></i>', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class="fas fa-print"></i>', exportOptions: { columns: [0, 1, 2, 3, 4] } }
            ],
            order: [[4, 'desc']],
            columnDefs: [ 
                { targets: [5], orderable: false },
                { targets: [0], responsivePriority: 1 },
                { targets: [5], responsivePriority: 2 }
            ]
        });
    });

    // --- LÓGICA DE VER DETALLES ---
    function verUsuario(userId) {
        const modal = document.getElementById('detailModal');
        const content = document.getElementById('detailContent');
        
        modal.style.display = 'flex';
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando información...</p></div>';

        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/obtener-usuario',
            method: 'POST',
            data: { id_usuario: userId },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    renderUserDetails(res.data);
                } else {
                    content.innerHTML = `<div class="alert alert-error">Error: ${res.error}</div>`;
                }
            },
            error: function() {
                content.innerHTML = `<div class="alert alert-error">Error de conexión al obtener datos.</div>`;
            }
        });
    }

    function renderUserDetails(data) {
        const content = document.getElementById('detailContent');
        const imgUrl = data.foto_perfil 
            ? `<?php echo BASE_URL; ?>assets/uploads/usuarios/${data.id_usuario}/${data.foto_perfil}`
            : `<?php echo BASE_URL; ?>assets/iconos/user.webp`;

        // REORGANIZACIÓN: Columna Izq (Personal), Columna Der (Academica + Stats)
        content.innerHTML = `
            <div class="profile-header">
                <img src="${imgUrl}" alt="Perfil" class="profile-img-lg" onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>assets/iconos/user.webp'">
                <div>
                    <h4 style="margin:0; color:var(--text-primary); font-size:1.1rem;">${data.nombres} ${data.apellidos}</h4>
                    <div style="margin-top: 5px;">
                        <span class="status-badge ${data.estado == 1 ? 'status-active' : 'status-inactive'}">
                            ${data.estado == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">
                        <i class="fas fa-id-badge"></i> ${data.rol} | ID: ${data.id_usuario}
                    </div>
                </div>
            </div>

            <div class="row g-4"> <div class="col-sm-6">
                    <div class="detail-section-title mt-0"><i class="fas fa-user-circle"></i> Info. Personal</div>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">DNI</span><span class="detail-value">${data.dni}</span></div>
                        <div class="detail-item"><span class="detail-label">Teléfono</span><span class="detail-value">${data.telefono || '<span class="text-muted">No reg.</span>'}</span></div>
                        <div class="detail-item"><span class="detail-label">Correo</span><span class="detail-value text-truncate" style="display:block" title="${data.correo_institucional}">${data.correo_institucional}</span></div>
                        <div class="detail-item"><span class="detail-label">Registro</span><span class="detail-value">${data.fecha_registro}</span></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="detail-section-title mt-0"><i class="fas fa-graduation-cap"></i> Info. Académica</div>
                    <div class="detail-grid mb-3">
                        <div class="detail-item"><span class="detail-label">Cód. Univ.</span><span class="detail-value">${data.codigo_univ}</span></div>
                        <div class="detail-item"><span class="detail-label">Facultad</span><span class="detail-value text-truncate" style="display:block" title="${data.facultad}">${data.facultad || 'N/A'}</span></div>
                        <div class="detail-item"><span class="detail-label">Escuela</span><span class="detail-value text-truncate" style="display:block" title="${data.escuela}">${data.escuela || 'N/A'}</span></div>
                    </div>

                    <div class="detail-section-title"><i class="fas fa-chart-pie"></i> Estadísticas</div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="h4 mb-0 fw-bold text-highlight">${data.total_productos || 0}</div>
                                <small class="text-muted">Publicaciones</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-box">
                                <div class="h4 mb-0 fw-bold text-success">${data.total_contactos || 0}</div>
                                <small class="text-muted">Interacciones</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // --- LÓGICA DE ACCIONES (ELIMINAR / SUSPENDER) ---
    function confirmAction(userId, action, userName) {
        currentUserId = userId;
        currentAction = action;

        if (action === 'suspender') {
            suspendUserId = userId;
            document.getElementById('suspendUserName').innerText = userName;
            
            const now = new Date();
            now.setDate(now.getDate() + 1);
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('suspendDate').value = now.toISOString().slice(0, 16);
            document.getElementById('suspendReason').value = '';
            
            document.getElementById('suspendModal').style.display = 'flex';
            return;
        }

        const modal = document.getElementById('genericModal');
        const titleEl = document.getElementById('modalTitle');
        const msgEl = document.getElementById('modalMessage');
        const btnEl = document.getElementById('confirmButton');

        btnEl.className = 'btn-confirm';
        btnEl.style.backgroundColor = '';

        if (action === 'eliminar') {
            titleEl.textContent = 'Eliminar Usuario';
            msgEl.innerHTML = `¿Estás seguro de eliminar a <b>${userName}</b>?<br>Esta acción es irreversible.`;
            btnEl.textContent = 'Eliminar';
            btnEl.style.backgroundColor = 'var(--status-danger)';
            btnEl.onclick = () => executeDelete(userId);
        } else if (action === 'activar') {
            titleEl.textContent = 'Activar Cuenta';
            msgEl.textContent = `¿Deseas reactivar el acceso a ${userName}?`;
            btnEl.textContent = 'Activar';
            btnEl.style.backgroundColor = 'var(--status-success)';
            btnEl.onclick = () => executeStatusChange(userId, 1);
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('genericModal').style.display = 'none';
        currentUserId = null;
        currentAction = null;
    }

    function closeSuspendModal() {
        document.getElementById('suspendModal').style.display = 'none';
        suspendUserId = null;
    }

    function submitSuspension() {
        const fecha = document.getElementById('suspendDate').value;
        const motivo = document.getElementById('suspendReason').value;

        if (!fecha || !motivo) {
            showNotification("Por favor ingresa la fecha y el motivo.", 'error');
            return;
        }

        const idParaSuspender = suspendUserId; 
        closeSuspendModal();
        if(typeof showLoading === 'function') showLoading();

        const formData = new FormData();
        formData.append('id_usuario', idParaSuspender); 
        formData.append('estado', 0); 
        formData.append('fecha_fin', fecha);
        formData.append('motivo', motivo);
        formData.append('accion', 'suspender');

        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/cambiar-estado-usuario', 
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(typeof hideLoading === 'function') hideLoading();
                if (response.success) {
                    updateTableRow(idParaSuspender, 0); 
                    showNotification('Usuario suspendido correctamente', 'success');
                } else {
                    showNotification(response.error || 'Error al suspender.', 'error');
                }
            },
            error: function(xhr) {
                if(typeof hideLoading === 'function') hideLoading();
                console.error('Error AJAX:', xhr.responseText);
                showNotification('Error de conexión al suspender usuario', 'error');
            }
        });
    }

    function executeStatusChange(userId, newState) {
        closeModal();
        if(typeof showLoading === 'function') showLoading();
        
        const formData = new FormData();
        formData.append('id_usuario', userId);
        formData.append('estado', newState);
        formData.append('accion', 'cambiar_estado');
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/cambiar-estado-usuario', 
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(typeof hideLoading === 'function') hideLoading();
                if (response.success) {
                    updateTableRow(userId, newState);
                    showNotification(newState == 1 ? 'Usuario activado' : 'Usuario suspendido', 'success');
                } else {
                    showNotification(response.error || 'Error al cambiar estado.', 'error');
                }
            },
            error: function(xhr) {
                if(typeof hideLoading === 'function') hideLoading();
                console.error('Error AJAX:', xhr.responseText);
                showNotification('Error de conexión', 'error');
            }
        });
    }  

    function executeDelete(userId) {
        closeModal();
        if(typeof showLoading === 'function') showLoading();
        
        const formData = new FormData();
        formData.append('id_usuario', userId);
        formData.append('accion', 'eliminar');
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/eliminar-usuario', 
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(typeof hideLoading === 'function') hideLoading();
                
                // Si el servidor responde 200 OK
                if (response.success) {
                    const row = $(`tr[data-user-id="${userId}"]`);
                    if (row.length) {
                        dataTable.row(row).remove().draw(false);
                    }
                    showNotification('Usuario eliminado correctamente', 'success');
                } else {
                    showNotification(response.error || 'No se puede eliminar el usuario.', 'error');
                }
            },
            error: function(xhr) {
                if(typeof hideLoading === 'function') hideLoading();
                console.error('Error AJAX:', xhr.responseText);
                
                // --- CORRECCIÓN: Leer el mensaje real del servidor ---
                let mensajeError = 'Error de conexión o servidor.';
                
                // 1. Intentar leer JSON parseado automáticamente
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    mensajeError = xhr.responseJSON.error;
                } 
                // 2. Intentar parsear manualmente si vino como texto
                else if (xhr.responseText) {
                    try {
                        const respuesta = JSON.parse(xhr.responseText);
                        if (respuesta.error) mensajeError = respuesta.error;
                    } catch (e) {
                        // Si no es JSON, podría ser un error fatal de PHP (HTML)
                        console.error("Respuesta no JSON:", xhr.responseText);
                    }
                }
                
                showNotification(mensajeError, 'error');
            }
        });
    }

    function updateTableRow(userId, newState) {
        var $row = $(`tr[data-user-id="${userId}"]`);
        var rowObj = dataTable.row($row);

        if (!rowObj.length) return;

        var nuevoEstadoHtml = newState == 1 
            ? '<span class="status-badge status-active">Activo</span>' 
            : '<span class="status-badge status-inactive">Inactivo</span>';

        var userName = $row.find('.user-name').text().trim();
        
        var btnView = `<button class="btn-action btn-view" onclick="verUsuario(${userId})" title="Ver detalles"><i class="fas fa-eye"></i></button>`;
        var btnDelete = `<button class="btn-action btn-delete" onclick="confirmAction(${userId}, 'eliminar', '${userName}')" title="Eliminar permanentemente"><i class="fas fa-trash"></i></button>`;
        
        var btnToggle = '';
        if (newState == 1) {
            btnToggle = `<button class="btn-action btn-deactivate" onclick="confirmAction(${userId}, 'suspender', '${userName}')" title="Suspender cuenta"><i class="fas fa-user-slash"></i></button>`;
        } else {
            btnToggle = `<button class="btn-action btn-activate" onclick="confirmAction(${userId}, 'activar', '${userName}')" title="Activar cuenta"><i class="fas fa-user-check"></i></button>`;
        }

        dataTable.cell(rowObj, 3).data(nuevoEstadoHtml); 
        dataTable.cell(rowObj, 5).data(`<div class="actions-container">${btnView} ${btnToggle} ${btnDelete}</div>`); 
        
        dataTable.draw(false);
    }

    // Función Toast
    function showNotification(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const icon = type === 'success' 
            ? '<i class="fas fa-check-circle" style="color:var(--status-success)"></i>' 
            : '<i class="fas fa-exclamation-circle" style="color:var(--status-danger)"></i>';
        
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `${icon} <span>${message}</span>`;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeOutToast 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    function showLoading() { if(typeof window.showLoading === 'function') window.showLoading(); }
    function hideLoading() { if(typeof window.hideLoading === 'function') window.hideLoading(); }

    window.onclick = function(event) {
        const modalConf = document.getElementById('genericModal');
        const modalDet = document.getElementById('detailModal');
        const modalSus = document.getElementById('suspendModal');
        
        if (event.target == modalConf) closeModal();
        if (event.target == modalDet) closeDetailModal();
        if (event.target == modalSus) closeSuspendModal();
    }
</script>

<?php 
    $contenido = ob_get_clean(); 
    require_once 'layout.php'; 
?>