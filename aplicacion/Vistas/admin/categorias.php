<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $vista_actual = 'categorias'; 
    $titulo = 'Gestión de Categorías';

    require_once 'aplicacion/Modelos/Categoria.php';
    
    $categoria_modelo = new Categoria();

    $categorias = [];
    $stats_categorias = [
        'total_categorias' => 0,
        'categorias_activas' => 0,
        'total_publicaciones' => 0,
        'categoria_popular' => 'N/A'
    ];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? null) !== null) {
        
        $action = $_POST['action'];
        $id = $_POST['id_categoria'] ?? null;
        $success = false;
        $message = '';
        $data = []; 

        try {
            if ($action === 'guardar') {
                if (empty($_POST['nombre'] ?? '')) {
                    throw new Exception("El nombre de la categoría es obligatorio.");
                }

                $nombre = trim(htmlspecialchars($_POST['nombre']));
                $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
                $icono = trim(htmlspecialchars($_POST['icono'] ?? 'fas fa-tag')); 
                $color = trim(htmlspecialchars($_POST['color'] ?? '#00bcd4'));
                $estado = (int)($_POST['estado'] ?? 1);
                
                if ($id) {
                    $success = $categoria_modelo->actualizar((int)$id, $nombre, $descripcion, $icono, $color);
                    $message = $success ? 'Categoría actualizada correctamente.' : 'Error al actualizar la categoría.';
                } else {
                    $id_insertado = $categoria_modelo->crear($nombre, $descripcion, $icono, $color, $estado);
                    $success = (bool)$id_insertado;
                    $message = $success ? 'Categoría creada correctamente.' : 'Error al crear la categoría.';
                    if ($success) {
                       $data['id_nuevo'] = $id_insertado;
                    }
                }
            }
            elseif ($action === 'eliminar' && $id) {
                $success = $categoria_modelo->eliminar((int)$id);
                $message = $success ? 'Categoría eliminada correctamente.' : 'No se puede eliminar: ' . $message;
            }
            elseif ($action === 'toggle_estado' && $id) {
                $estado_actual = (int)($_POST['estado_actual'] ?? 0); 
                $nuevo_estado = $estado_actual === 1 ? 0 : 1;
                $success = $categoria_modelo->cambiarEstado((int)$id, $nuevo_estado);
                $accion_texto = $nuevo_estado === 1 ? 'activada' : 'desactivada';
                $message = $success ? "Categoría {$accion_texto} correctamente." : "Error al cambiar el estado de la categoría.";
                $data['nuevo_estado'] = $nuevo_estado; // Devolver nuevo estado
            }

        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => $success,
            'message' => $success ? null : $message, 
            'data' => $data
        ]);
        exit;
    }

    // 2. Carga de Datos (GET)
    try {
        // Cargar datos para la tabla y manejar posibles errores.
        $categorias = $categoria_modelo->obtenerParaAdmin();
        
        // Cargar estadísticas
        $stats = $categoria_modelo->obtenerEstadisticas();
        $stats_categorias = [
            'total_categorias' => $stats['total_categorias'] ?? 0,
            'categorias_activas' => $stats['categorias_activas'] ?? 0,
            'total_publicaciones' => $stats['total_publicaciones'] ?? 0,
            'categoria_popular' => $stats['categoria_popular'] ?? 'N/A'
        ];

    } catch (Exception $e) {
        error_log("Error al cargar datos de categorías en la vista admin: " . $e->getMessage());
    }

    ob_start();
?>

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

    /* Formulario mejorado - Cuadrícula 2x2 */
    .form-container {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        border: 1px solid var(--border-light);
        position: relative;
    }

    .form-header {
        margin-bottom: 1.5rem;
    }

    .form-title {
        color: var(--text-primary);
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-input, .form-textarea, .form-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        background: var(--bg-card);
        color: var(--text-primary);
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus {
        outline: none;
        border-color: var(--admin-primary);
        box-shadow: 0 0 0 3px rgba(10, 61, 98, 0.1);
    }

    /* Paleta de colores en esquina inferior izquierda */
    .color-picker-container {
        position: absolute;
        bottom: 1rem;
        left: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .color-picker-label {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .color-input {
        width: 60px;
        height: 40px;
        border: 2px solid var(--border-light);
        border-radius: 6px;
        cursor: pointer;
        background: var(--bg-card);
    }

    .color-input::-webkit-color-swatch {
        border: none;
        border-radius: 4px;
    }

    .color-input::-moz-color-swatch {
        border: none;
        border-radius: 4px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    body.dark-mode .btn-primary {
        background: var(--primary-dark);
        color: white;
        border: none;
    }

    body.dark-mode .btn-primary:hover {
        background: var(--primary-color);
    }

    body.dark-mode .cancelar-btn {
        background: var(--bg-sidebar);
        color: var(--text-primary);
        border: 1px solid var(--border-light);
    }

    body.dark-mode .cancelar-btn:hover {
        background: var(--border-light);
    }

    /* Información de categoría */
    .category-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .category-details {
        display: flex;
        flex-direction: column;
    }

    .category-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.2rem;
        font-size: 0.85rem;
    }

    .category-meta {
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

    .btn-edit {
        background: #ff9800;
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

    .btn-delete {
        background: var(--primary-light);
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
        display: flex;
        flex-direction: column;
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
        
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .color-picker-container {
            position: static;
            margin-top: 1rem;
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
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
        
        .form-container {
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
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
        
        .form-container {
            padding: 1rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        /* FIX: Centrar botones de DataTables en vista móvil */
        .dataTables_wrapper .row:first-child .col-sm-12:first-child {
            text-align: center;
        }
        .dataTables_wrapper .row:first-child .col-sm-12:nth-child(2) {
            text-align: center;
        }
    }
</style>

<div class="dashboard-main">
    <div class="container-fluid">
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
                <i class="fas fa-<?php echo $_SESSION['alert']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <div>
                    <strong><?php echo $_SESSION['alert']['type'] === 'success' ? 'Éxito!' : 'Error!'; ?></strong>
                    <p style="margin: 0;"><?php echo htmlspecialchars($_SESSION['alert']['message']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_categorias['total_categorias']); ?></div>
                    <div class="metric-label">Total de Categorías</div>
                    <div class="metric-sub-label">En la plataforma</div>
                </div>
                <div class="metric-icon"><i class="fas fa-tags"></i></div>
            </div>
            <div class="metric-card success">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_categorias['categorias_activas']); ?></div>
                    <div class="metric-label">Categorías Activas</div>
                    <div class="metric-sub-label">Disponibles para uso</div>
                </div>
                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="metric-card warning">
                <div class="metric-content">
                    <div class="metric-value"><?php echo number_format($stats_categorias['total_publicaciones']); ?></div>
                    <div class="metric-label">Publicaciones Totales</div>
                    <div class="metric-sub-label">En todas las categorías</div>
                </div>
                <div class="metric-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="metric-card info">
                <div class="metric-content">
                    <div class="metric-value"><?php echo htmlspecialchars($stats_categorias['categoria_popular']); ?></div>
                    <div class="metric-label">Categoría Más Popular</div>
                    <div class="metric-sub-label">Con más publicaciones</div>
                </div>
                <div class="metric-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>

        <div class="form-container" id="formContainer">
            <div class="form-header">
                <h3 class="form-title" id="formTitle">
                    <i class="fas fa-edit"></i> Crear Nueva Categoría
                </h3>
            </div>
            
            <div class="form-content">
                <form method="POST" action="" id="categoriaForm">
                    <input type="hidden" name="action" id="formAction" value="guardar">
                    <input type="hidden" name="id_categoria" id="id_categoria" value="">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-tag"></i> Nombre de la Categoría
                                <span style="color: var(--status-danger);">*</span>
                            </label>
                            <input type="text" 
                                name="nombre" 
                                id="nombre" 
                                class="form-input" 
                                required 
                                placeholder="Ej: Libros Universitarios"
                                maxlength="100">
                        </div>
                        
                        <div class="form-group">
                            <label for="icono" class="form-label">
                                <i class="fas fa-icons"></i> Icono (FontAwesome)
                            </label>
                            <input type="text" 
                                name="icono" 
                                id="icono" 
                                class="form-input" 
                                placeholder="fas fa-book"
                                maxlength="50"
                                value="fas fa-tag">
                            <small style="color: var(--text-muted); margin-top: 0.25rem; font-size: 0.8rem;">
                                Ej: fas fa-book, fas fa-laptop, fas fa-tshirt
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="estado" class="form-label">
                                <i class="fas fa-toggle-on"></i> Estado
                            </label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left"></i> Descripción
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      class="form-textarea" 
                                      rows="3" 
                                      placeholder="Describe brevemente los tipos de productos que pertenecen a esta categoría..."
                                      maxlength="255"></textarea>
                        </div>
                    </div>
                    
                    <div class="color-picker-container">
                        <span class="color-picker-label">
                            <i class="fas fa-palette"></i> Color:
                        </span>
                        <input type="color" 
                               name="color" 
                               id="color" 
                               class="color-input" 
                               value="#00bcd4"
                               title="Seleccionar color de la categoría">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="cancelar-btn btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <table id="categoriasTable" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Información</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr data-category-id="<?php echo $categoria['id_categoria']; ?>">
                        <td>
                            <div class="category-info">
                                <i class="<?php echo htmlspecialchars($categoria['icono'] ?? 'fas fa-tag'); ?>" 
                                   style="color: <?php echo htmlspecialchars($categoria['color'] ?? '#00bcd4'); ?>; 
                                          font-size: 1.5rem; 
                                          width: 40px; 
                                          text-align: center;"></i>
                                <div class="category-details">
                                    <div class="category-name">
                                        <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                                    </div>
                                    <div class="category-meta">
                                        <?php echo htmlspecialchars($categoria['descripcion'] ?? 'Sin descripción'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="category-details">
                                <div class="category-name">ID: <?php echo htmlspecialchars($categoria['id_categoria']); ?></div>
                                <div class="category-meta">
                                    Publicaciones: <?php echo htmlspecialchars($categoria['total_publicaciones'] ?? 0); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $categoria['estado'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $categoria['estado'] == 1 ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                // Formateo de fecha para SQL Server
                                $fecha = $categoria['fecha_creacion'] ?? null;
                                if ($fecha) {
                                    $timestamp = is_numeric($fecha) ? $fecha : strtotime($fecha);
                                    echo date('d/m/Y', $timestamp); 
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                        <td>
                            <div class="actions-container">
                                <button class="btn-action btn-edit" 
                                        onclick="editarCategoria(<?php echo htmlspecialchars(json_encode($categoria)); ?>)"
                                        title="Editar categoría">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <?php if ($categoria['estado'] == 1): ?>
                                    <button class="btn-action btn-deactivate" 
                                            onclick="confirmAction(<?php echo $categoria['id_categoria']; ?>, 'desactivar', '<?php echo htmlspecialchars(addslashes($categoria['nombre_categoria'])); ?>')"
                                            title="Desactivar categoría">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn-action btn-activate" 
                                            onclick="confirmAction(<?php echo $categoria['id_categoria']; ?>, 'activar', '<?php echo htmlspecialchars(addslashes($categoria['nombre_categoria'])); ?>')"
                                            title="Activar categoría">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn-action btn-delete"
                                        onclick="confirmAction(<?php echo $categoria['id_categoria']; ?>, 'eliminar', '<?php echo htmlspecialchars(addslashes($categoria['nombre_categoria'])); ?>')"
                                        title="Eliminar categoría">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="confirmation-modal" id="confirmationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Confirmar Acción</h3>
            <p class="modal-message" id="modalMessage">¿Estás seguro de que quieres realizar esta acción?</p>
        </div>
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
    // Variables globales
    let currentCategoryId = null;
    let currentAction = null;
    let dataTable;

    // Inicializar DataTables
    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#categoriasTable').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay categorías disponibles",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ categorías",
                "infoEmpty": "Mostrando 0 a 0 de 0 categorías",
                "infoFiltered": "(filtrado de _MAX_ categorías totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ categorías",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron categorías coincidentes",
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
                        columns: [0, 1, 2, 3]
                    },
                    filename: 'categorias_' + new Date().toISOString().split('T')[0]
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i>',
                    className: 'btn-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    filename: 'categorias_' + new Date().toISOString().split('T')[0],
                    orientation: 'portrait', // Corregido a vertical
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i>',
                    className: 'btn-info',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                }
            ],
            order: [[3, 'desc']],
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            columnDefs: [
                {
                    targets: [4],
                    orderable: false,
                    searchable: false
                },
                {
                    targets: [3],
                    type: 'date-eu'
                }
            ]
        });
    });

    /**
     * Actualiza visualmente el estado (badge y botones) de una fila.
     * @param {object} row - El objeto de fila de DataTables.
     * @param {number} nuevoEstado - El nuevo estado (0 o 1).
     */
    function updateRowState(row, nuevoEstado) {
        const isActivo = nuevoEstado === 1;
        const badgeHtml = `<span class="status-badge ${isActivo ? 'status-active' : 'status-inactive'}">
            ${isActivo ? 'Activa' : 'Inactiva'}
        </span>`;
        
        const rowData = row.data();
        const id = rowData.id_categoria;
        const nombre = rowData.nombre_categoria;

        // 1. Actualiza la columna de estado (índice 2)
        dataTable.cell(row, 2).data(badgeHtml).draw(false);
        
        // 2. Construir el contenido de los botones
        // 🏆 CORRECCIÓN: Se usa comilla simple (') para delimitar el atributo onclick
        // para que no haya conflicto con las comillas dobles (") del JSON.stringify().
   let actionsContent = `<button class="btn-action btn-edit" 
                            onclick='editarCategoria(${JSON.stringify(rowData)})'
                            title="Editar categoría">
                            <i class="fas fa-edit"></i>
                        </button>`;

        if (isActivo) {
        actionsContent += `<button class="btn-action btn-deactivate" 
                            onclick="confirmAction(${id}, 'desactivar', '${nombre}')"
                            title="Desactivar categoría">
                            <i class="fas fa-ban"></i>
                        </button>`;
    } else {
        actionsContent += `<button class="btn-action btn-activate" 
                            onclick="confirmAction(${id}, 'activar', '${nombre}')"
                            title="Activar categoría">
                            <i class="fas fa-check"></i>
                        </button>`;
    }
        
        actionsContent += `<button class="btn-action btn-delete"
                        onclick="confirmAction(${id}, 'eliminar', '${nombre}')"
                        title="Eliminar categoría">
                        <i class="fas fa-trash"></i>
                    </button>`;

        // Envolver el contenido de las acciones en el contenedor CSS.
        const actionsHtml = `<div class="actions-container">${actionsContent}</div>`;
        
        // 3. Actualiza la columna de acciones (índice 4)
        dataTable.cell(row, 4).data(actionsHtml).draw(false);
        
        // 4. Actualizar el objeto de datos de la fila en DataTables
        rowData.estado = nuevoEstado;
        dataTable.row(row).data(rowData).draw(false);
    }

    /**
     * Elimina la fila de la tabla.
     * @param {number} categoryId - ID de la categoría a eliminar.
     */
    function deleteRow(categoryId) {
        dataTable.$(`tr[data-category-id="${categoryId}"]`).remove().draw(false);
    }

    /**
     * Función central para enviar acciones de tabla (activar/desactivar/eliminar) vía AJAX.
     * @param {number} categoryId - ID de la categoría.
     * @param {string} action - Acción a realizar ('activar', 'desactivar', 'eliminar').
     */
    function executeAction(categoryId, action) {
        closeModal();
        const actionMap = {
            'activar': 'toggle_estado',
            'desactivar': 'toggle_estado',
            'eliminar': 'eliminar'
        };
        
        const rowElement = dataTable.$(`tr[data-category-id="${categoryId}"]`);
        const row = dataTable.row(rowElement);

        $.ajax({
            url: 'categorias', // Endpoint de AJAX
            type: 'POST',
            dataType: 'json',
            data: {
                action: actionMap[action],
                id_categoria: categoryId,
                // Solo se necesita el estado actual para toggle
                estado_actual: action === 'activar' ? 0 : 1 
            },
            success: function(response) {
                if (response.success) {
                    if (action === 'eliminar') {
                        deleteRow(categoryId);
                    } else {
                        // Usar la respuesta del servidor para el nuevo estado
                        updateRowState(row, response.data.nuevo_estado);
                    }
                } else {
                    // Muestra mensaje de error si falla
                    showNotification(response.message || 'Error al ejecutar la acción.', 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error de servidor o conexión.', 'error');
            }
        });
    }

    /**
     * Maneja la creación/edición del formulario vía AJAX.
     */
    document.getElementById('categoriaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        const nombre = document.getElementById('nombre').value.trim();
        if (!nombre) {
            showNotification('El nombre de la categoría es obligatorio', 'error');
            document.getElementById('nombre').focus();
            return;
        }
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        submitBtn.disabled = true;

        $.ajax({
            url: 'categorias', // Endpoint de AJAX
            type: 'POST',
            dataType: 'json',
            data: $('#categoriaForm').serialize(), // Serializa todos los datos del formulario
            success: function(response) {
                if (response.success) {
                    // Para creación/edición, mantenemos la recarga de la página (location.reload()) 
                    location.reload(); 
                } else {
                    showNotification(response.message || 'Error al guardar la categoría.', 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error de servidor o conexión: ' + error, 'error');
            },
            complete: function() {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });

    /**
     * Resetea los campos del formulario a valores por defecto.
     */
    function resetForm() {
        document.getElementById('id_categoria').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('icono').value = 'fas fa-tag'; 
        document.getElementById('color').value = '#00bcd4'; 
        document.getElementById('estado').value = '1';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Guardar Categoría';
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Crear Nueva Categoría';
        document.getElementById('formAction').value = 'guardar';
    }

    /**
     * Carga los datos de una categoría en el formulario para su edición.
     * @param {object} categoria - Objeto con los datos de la categoría.
     */
    function editarCategoria(categoria) {
        document.getElementById('id_categoria').value = categoria.id_categoria;
        document.getElementById('nombre').value = categoria.nombre_categoria;
        document.getElementById('descripcion').value = categoria.descripcion || '';
        document.getElementById('icono').value = categoria.icono || 'fas fa-tag'; 
        document.getElementById('color').value = categoria.color || '#00bcd4';
        document.getElementById('estado').value = categoria.estado;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Actualizar Categoría';
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Categoría';
        document.getElementById('formAction').value = 'guardar';
        
        document.getElementById('formContainer').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }

    /**
     * Muestra el modal de confirmación antes de una acción destructiva.
     */
    function confirmAction(categoryId, action, categoryName) {
        currentCategoryId = categoryId;
        currentAction = action;
        
        const modal = document.getElementById('confirmationModal');
        const message = document.getElementById('modalMessage');
        const title = document.getElementById('modalTitle');
        
        let actionText = '';
        let confirmBtnClass = 'btn-confirm';
        switch(action) {
            case 'activar': 
                actionText = 'activar'; 
                title.textContent = 'Activar Categoría';
                confirmBtnClass = 'btn-activate';
                break;
            case 'desactivar': 
                actionText = 'desactivar'; 
                title.textContent = 'Desactivar Categoría';
                confirmBtnClass = 'btn-deactivate';
                break;
            case 'eliminar': 
                actionText = 'eliminar'; 
                title.textContent = 'Eliminar Categoría';
                confirmBtnClass = 'btn-delete';
                break;
            default: 
                actionText = 'realizar esta acción en';
        }
        
        message.textContent = `¿Estás seguro de que quieres ${actionText} la categoría "${categoryName}"?`;
        modal.style.display = 'flex';
        
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.className = `btn-action ${confirmBtnClass}`; 
        
        confirmButton.onclick = function() {
            executeAction(categoryId, action);
        };
    }
    
    /**
     * Muestra una notificación temporal.
     * (Se utiliza para errores de formulario y respuestas de AJAX)
     */
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
        
        document.querySelector('.container-fluid').prepend(alert);

        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    }

    /**
     * Cierra el modal de confirmación.
     */
    function closeModal() {
        document.getElementById('confirmationModal').style.display = 'none';
        currentCategoryId = null;
        currentAction = null;
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Sistema de gestión de categorías con DB cargado');
    });
</script>

<?php 
    $contenido = ob_get_clean(); 
    require_once 'layout.php'; 
?>