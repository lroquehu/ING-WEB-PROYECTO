<?php 
    $vista_actual = 'usuarios'; 
    $titulo = 'Gestión de Usuarios';

    // Datos hardcodeados para demostración
    $usuarios = [
        [
            'id_usuario' => 1,
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Pérez García',
            'dni' => '76543210',
            'facultad' => 'Ingeniería de Sistemas',
            'codigo_univ' => '202100001',
            'correo_institucional' => 'juan.perez@university.edu',
            'telefono' => '987654321',
            'estado' => 1,
            'fecha_registro' => '2024-01-15'
        ],
        [
            'id_usuario' => 2,
            'nombres' => 'María Elena',
            'apellidos' => 'Rodríguez López',
            'dni' => '87654321',
            'facultad' => 'Administración',
            'codigo_univ' => '202100123',
            'correo_institucional' => 'maria.rodriguez@university.edu',
            'telefono' => '912345678',
            'estado' => 1,
            'fecha_registro' => '2024-01-20'
        ],
        [
            'id_usuario' => 3,
            'nombres' => 'Carlos Alberto',
            'apellidos' => 'Gutiérrez Torres',
            'dni' => '12345678',
            'facultad' => 'Medicina',
            'codigo_univ' => '202100456',
            'correo_institucional' => 'carlos.gutierrez@university.edu',
            'telefono' => '934567890',
            'estado' => 0,
            'fecha_registro' => '2024-02-05'
        ],
        [
            'id_usuario' => 4,
            'nombres' => 'Ana Patricia',
            'apellidos' => 'Silva Mendoza',
            'dni' => '23456789',
            'facultad' => 'Derecho',
            'codigo_univ' => '202100789',
            'correo_institucional' => 'ana.silva@university.edu',
            'telefono' => '945678901',
            'estado' => 1,
            'fecha_registro' => '2024-02-10'
        ],
        [
            'id_usuario' => 5,
            'nombres' => 'Luis Fernando',
            'apellidos' => 'Vargas Castro',
            'dni' => '34567890',
            'facultad' => 'Economía',
            'codigo_univ' => '202101234',
            'correo_institucional' => 'luis.vargas@university.edu',
            'telefono' => '956789012',
            'estado' => 1,
            'fecha_registro' => '2024-02-15'
        ],
        [
            'id_usuario' => 6,
            'nombres' => 'Sofia Alejandra',
            'apellidos' => 'Rojas Paredes',
            'dni' => '45678901',
            'facultad' => 'Psicología',
            'codigo_univ' => '202101567',
            'correo_institucional' => 'sofia.rojas@university.edu',
            'telefono' => '967890123',
            'estado' => 0,
            'fecha_registro' => '2024-02-20'
        ],
        [
            'id_usuario' => 7,
            'nombres' => 'Miguel Ángel',
            'apellidos' => 'Díaz Flores',
            'dni' => '56789012',
            'facultad' => 'Arquitectura',
            'codigo_univ' => '202101890',
            'correo_institucional' => 'miguel.diaz@university.edu',
            'telefono' => '978901234',
            'estado' => 1,
            'fecha_registro' => '2024-03-01'
        ],
        [
            'id_usuario' => 8,
            'nombres' => 'Elena Beatriz',
            'apellidos' => 'Morales Ruiz',
            'dni' => '67890123',
            'facultad' => 'Educación',
            'codigo_univ' => '202102123',
            'correo_institucional' => 'elena.morales@university.edu',
            'telefono' => '989012345',
            'estado' => 1,
            'fecha_registro' => '2024-03-05'
        ],
        [
            'id_usuario' => 9,
            'nombres' => 'Roberto Carlos',
            'apellidos' => 'Salazar Jiménez',
            'dni' => '78901234',
            'facultad' => 'Ingeniería Civil',
            'codigo_univ' => '202102456',
            'correo_institucional' => 'roberto.salazar@university.edu',
            'telefono' => '990123456',
            'estado' => 0,
            'fecha_registro' => '2024-03-10'
        ],
        [
            'id_usuario' => 10,
            'nombres' => 'Carmen Rosa',
            'apellidos' => 'Ortega Herrera',
            'dni' => '89012345',
            'facultad' => 'Enfermería',
            'codigo_univ' => '202102789',
            'correo_institucional' => 'carmen.ortega@university.edu',
            'telefono' => '901234567',
            'estado' => 1,
            'fecha_registro' => '2024-03-15'
        ]
    ];

    $total_usuarios = count($usuarios);
    $stats_usuarios = [
        'activos' => count(array_filter($usuarios, function($user) { return $user['estado'] == 1; })),
        'nuevos_mes' => 8,
        'vendedores' => 6
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

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.2rem;
        font-size: 0.85rem;
    }

    .user-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

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

    .btn-deactivate {
        background: var(--status-danger);
        color: white;
    }

    .btn-activate {
        background: var(--status-success);
        color: white;
    }

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
    }

    @media (max-width: 768px) {
        .management-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .metrics-grid {
            grid-template-columns: 1fr;
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
        .management-header {
            margin-bottom: 1.5rem;
        }
        
        .metrics-grid {
            gap: 1rem;
        }
        
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
    }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <!-- Estadísticas Rápidas -->
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($total_usuarios); ?></div>
                    <div class="metric-label">Total Usuarios</div>
                    <div class="metric-sub-label">Registrados en la plataforma</div>
                </div>
                <div class="metric-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_usuarios['activos']); ?></div>
                    <div class="metric-label">Usuarios Activos</div>
                    <div class="metric-sub-label">Con acceso al sistema</div>
                </div>
                <div class="metric-icon"><i class="fas fa-user-check"></i></div>
            </div>
            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_usuarios['nuevos_mes']); ?></div>
                    <div class="metric-label">Nuevos Este Mes</div>
                    <div class="metric-sub-label">Registros recientes</div>
                </div>
                <div class="metric-icon"><i class="fas fa-user-plus"></i></div>
            </div>
            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_usuarios['vendedores']); ?></div>
                    <div class="metric-label">Vendedores Activos</div>
                    <div class="metric-sub-label">Con publicaciones</div>
                </div>
                <div class="metric-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>

        <!-- Tabla de Usuarios con DataTables -->
        <table id="usuariosTable" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Información Universitaria</th>
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
                                <div class="user-details">
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?>
                                    </div>
                                    <div class="user-meta">
                                        <?php echo htmlspecialchars($usuario['id_usuario']); ?> | <?php echo htmlspecialchars($usuario['dni']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="user-details">
                                <div class="user-name"><?php echo htmlspecialchars($usuario['facultad']); ?></div>
                                <div class="user-meta"><?php echo htmlspecialchars($usuario['codigo_univ']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="user-details">
                                <div class="user-name"><?php echo htmlspecialchars($usuario['correo_institucional']); ?></div>
                                <div class="user-meta"><?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $usuario['estado'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $usuario['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                        </td>
                        <td>
                            <div class="actions-container">
                                <a href="<?php echo BASE_URL; ?>admin/usuarios/<?php echo $usuario['id_usuario']; ?>" 
                                   class="btn-action btn-view" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($usuario['id_usuario'] != ($_SESSION['usuario_id'] ?? 0)): ?>
                                    <?php if ($usuario['estado'] == 1): ?>
                                        <button class="btn-action btn-deactivate" 
                                                onclick="confirmDeactivation(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?>')"
                                                title="Desactivar usuario">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-action btn-activate" 
                                                onclick="activateUser(<?php echo $usuario['id_usuario']; ?>)"
                                                title="Activar usuario">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="user-meta" style="font-size: 0.65rem;">(Tu cuenta)</span>
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
            <h3 class="modal-title">Confirmar Desactivación</h3>
            <p class="modal-message" id="modalMessage">¿Estás seguro de que quieres desactivar a este usuario?</p>
        </div>
        <div class="modal-actions">
            <button class="btn-action btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-action btn-confirm" id="confirmButton">Sí, Desactivar</button>
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
    let currentUserId = null;
    let dataTable;

    // Inicializar DataTables
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
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i>',
                    className: 'btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    filename: 'usuarios_' + new Date().toISOString().split('T')[0]
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i>',
                    className: 'btn-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    filename: 'usuarios_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i>',
                    className: 'btn-info',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function (win) {
                        $(win.document.body).css('color', '#1A1E23');
                        $(win.document.body).find('table').addClass('compact').css('font-size', '10pt');
                    }
                }
            ],
            order: [[4, 'desc']], // Ordenar por fecha de registro descendente
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 100, -1], [5, 10, 25, 100, "Todos"]],
            columnDefs: [
                {
                    targets: [5], // Columna de acciones
                    orderable: false,
                    searchable: false
                },
                {
                    targets: [4], // Columna de fecha
                    type: 'date-eu' // Formato europeo para ordenamiento correcto
                }
            ]
        });
    });

    // Funciones de gestión de usuarios
    function confirmDeactivation(userId, userName) {
        currentUserId = userId;
        const modal = document.getElementById('confirmationModal');
        const message = document.getElementById('modalMessage');
        
        message.textContent = `¿Estás seguro de que quieres desactivar a ${userName}? El usuario no podrá acceder a la plataforma.`;
        modal.style.display = 'flex';
        
        document.getElementById('confirmButton').onclick = function() {
            deactivateUser(userId);
        };
    }

    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
        currentUserId = null;
    }

    function deactivateUser(userId) {
        showLoading();
        
        // Simulación de llamada a API
        setTimeout(() => {
            hideLoading();
            closeModal();
            showNotification('Usuario desactivado correctamente', 'success');
            
            // En una implementación real, aquí se actualizaría la tabla
            console.log('Usuario desactivado:', userId);
        }, 1000);
    }

    function activateUser(userId) {
        showLoading();
        
        // Simulación de llamada a API
        setTimeout(() => {
            hideLoading();
            showNotification('Usuario activado correctamente', 'success');
            
            // En una implementación real, aquí se actualizaría la tabla
            console.log('Usuario activado:', userId);
        }, 1000);
    }

    // Utilidades
    function showNotification(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            <div class="alert-content">
                <strong>${type === 'success' ? 'Éxito!' : 'Error!'}</strong>
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
</script>

<?php 
    $contenido = ob_get_clean(); 
    require_once 'layout.php'; 
?>