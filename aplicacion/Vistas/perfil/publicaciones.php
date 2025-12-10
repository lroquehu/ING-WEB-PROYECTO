<?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticación (esto debería estar en el controlador)
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    // Datos que vienen del controlador
    $publicaciones = $publicaciones ?? [];
    $estado_filtro = $estado_filtro ?? 'all';
    // Las estadísticas ahora siempre vienen del controlador, con valores por defecto si hay error.
    $estadisticas = $estadisticas ?? ['total' => 0, 'activas' => 0, 'pausadas' => 0, 'eliminadas' => 0];
    $error = $error ?? '';

    $page_title = 'Mis Publicaciones - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php';
?>
    <!-- Botón para volver atrás -->
    <a href="javascript:history.back()" class="back-link" title="Volver atrás">
        <i class="fas fa-arrow-left"></i>
    </a>

    <style>
        /* Estilos para el botón de volver */
        .back-link {
            position: fixed;
            top: 9rem;
            left: calc(50% - 600px - 5rem); /* Posiciona el botón a la izquierda del contenido */
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
        @media (max-width: 1400px) {
            .back-link {
                left: 2rem; /* Fallback para pantallas más pequeñas */
            }
        }
        @media (max-width: 768px) {
            .back-link {
                display: none; /* Ocultamos en móvil para no estorbar */
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Corrección para eliminar fondo transparente del header */
        body::before {
            display: none;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        main {
            padding-bottom: 4rem; /* Añade espacio inferior para separar del footer */
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header de Página */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 3.5rem 0 2rem;
        }
        
        .page-header h1 {
            color: #333;
            font-size: 2rem;
        }
        
        /* Botones */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #910202;
            color: white;
        }
        
        .btn-primary:hover {
            background: #700101;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #910202;
            color: #910202;
        }
        
        .btn-outline:hover {
            background: #910202;
            color: white;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        /* Estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: bold;
            color: #910202;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Filtros */
        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .filter-tab {
            padding: 0.75rem 1.5rem;
            background: #f8f9fa;
            border: 2px solid #e1e1e1;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
            color: #000000;
        }
        
        .filter-tab:hover,
        .filter-tab.active {
            background: #910202;
            color: white;
            border-color: #910202;
        }
        
        /* Grid de Publicaciones */
        .publicaciones-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .publicacion-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .publicacion-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .publicacion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .publicacion-title {
            font-size: 1.2rem;
            color: #333;
            margin: 0;
        }
        
        .publicacion-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-1 { background: #d4edda; color: #155724; }
        .status-2 { background: #fff3cd; color: #856404; }
        .status-3 { background: #f8d7da; color: #721c24; }
        .status-0 { background: #e2e3e5; color: #383d41; }
        
        .publicacion-body {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1.5rem;
        }
        
        .publicacion-image {
            width: 150px;
            height: 150px;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .publicacion-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .no-image {
            color: #666;
            text-align: center;
        }
        
        .no-image i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .publicacion-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .publicacion-desc {
            color: #666;
            line-height: 1.5;
        }
        
        .publicacion-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .meta-tag {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .categoria { background: #e9ecef; color: #495057; }
        .tipo { background: #d1ecf1; color: #0c5460; }
        .precio { background: #d4edda; color: #155724; }
        
        .publicacion-dates {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .publicacion-footer {
            padding: 1.5rem;
            border-top: 1px solid #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .publicacion-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }
        
        .empty-state h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        /* Alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Modal de Confirmación */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 450px;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.visible .modal-content {
            transform: scale(1);
        }

        .modal-content h3 { margin-bottom: 1rem; font-size: 1.5rem; color: #333; }
        .modal-content p { margin-bottom: 2rem; color: #666; font-size: 1.1rem; line-height: 1.5; }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .publicacion-body {
                grid-template-columns: 1fr;
            }
            
            .publicacion-image {
                width: 100%;
                height: 200px;
            }
            
            .publicacion-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .publicacion-actions {
                justify-content: center;
            }
        }
    </style>
    <div class="container">
            <!-- Header de Página -->
            <div class="page-header">
                <h1>Gestión de Publicaciones</h1>
                <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">
                    ➕ Nueva Publicación
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $estadisticas['total']; ?></span>
                    <span class="stat-label">Total Publicaciones</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $estadisticas['activas']; ?></span>
                    <span class="stat-label">Activas</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $estadisticas['pausadas']; ?></span>
                    <span class="stat-label">Pausadas</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $estadisticas['eliminadas']; ?></span>
                    <span class="stat-label">Eliminadas</span>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filters-section">
                <h3 style="margin-bottom: 1rem; color: #333;">Filtrar por estado:</h3>
                <div class="filter-tabs">
                    <a href="?estado=all" class="filter-tab <?php echo $estado_filtro === 'all' ? 'active' : ''; ?>">
                        Todas (<?php echo $estadisticas['total']; ?>)
                    </a>
                    <a href="?estado=1" class="filter-tab <?php echo $estado_filtro === '1' ? 'active' : ''; ?>">
                        Activas (<?php echo $estadisticas['activas']; ?>)
                    </a>
                    <a href="?estado=2" class="filter-tab <?php echo $estado_filtro === '2' ? 'active' : ''; ?>">
                        Pausadas (<?php echo $estadisticas['pausadas']; ?>)
                    </a>
                    <a href="?estado=3" class="filter-tab <?php echo $estado_filtro === '3' ? 'active' : ''; ?>">
                        Eliminadas (<?php echo $estadisticas['eliminadas']; ?>)
                    </a>
                </div>
            </div>

            <!-- Lista de Publicaciones -->
            <?php if (empty($publicaciones)): ?>
                <div class="empty-state">
                    <div>📦</div>
                    <h3>No tienes publicaciones</h3>
                    <p>Comienza a publicar tus productos o servicios para conectarte con la comunidad universitaria</p>
                    <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="btn btn-primary">Crear primera publicación</a>
                </div>
            <?php else: ?>
                <div class="publicaciones-grid">
                    <?php foreach ($publicaciones as $publicacion): ?>
                        <div class="publicacion-card">
                            <!-- Header -->
                            <div class="publicacion-header">
                                <h3 class="publicacion-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
                                <span class="publicacion-status status-<?php echo $publicacion['estado']; ?>">
                                    <?php 
                                    switch($publicacion['estado']) {
                                        case 1: echo '🟢 Activo'; break;
                                        case 2: echo '🟡 Pausado'; break;
                                        case 3: echo '🔴 Eliminado'; break;
                                        case 0: echo '⚪ Inactivo'; break;  
                                        default: echo '⚫ Desconocido';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <!-- Body -->
                            <div class="publicacion-body">
                                <div class="publicacion-image">
                                    <?php $imgFinal = obtenerImagenFinal($publicacion['imagen'] ?? null); ?>
                                    <?php if (!empty($imgFinal)): ?>
                                        <img src="<?php echo htmlspecialchars($imgFinal); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="publicacion-info">
                                    <p class="publicacion-desc">
                                        <?php echo htmlspecialchars($publicacion['descripcion']); ?>
                                    </p>
                                    
                                    <div class="publicacion-meta">
                                        <span class="meta-tag categoria"><?php echo htmlspecialchars($publicacion['nombre_categoria']); ?></span>
                                        <span class="meta-tag tipo"><?php echo $publicacion['tipo']; ?></span>
                                        <span class="meta-tag precio">S/ <?php echo number_format($publicacion['precio'], 2); ?></span>
                                    </div>
                                    
                                    <div class="publicacion-dates">
                                        <span><strong>Publicado:</strong> <?php echo date('d/m/Y', strtotime($publicacion['fecha_publicacion'])); ?></span>
                                        <?php if ($publicacion['fecha_actualizacion'] && $publicacion['fecha_actualizacion'] !== $publicacion['fecha_publicacion']): ?>
                                            <span><strong>Actualizado:</strong> <?php echo date('d/m/Y', strtotime($publicacion['fecha_actualizacion'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="publicacion-footer">
                                <div class="publicacion-views">
                                    <small>Vistas: <?php echo $publicacion['total_vistas'] ?? 0; ?></small>
                                </div>
                                
                                <div class="publicacion-actions">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-sm btn-outline">
                                        👁️ Ver
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>publicaciones/editar/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-sm btn-outline">
                                        ✏️ Editar
                                    </a>

                                    <?php if ($publicacion['estado'] == 1): ?>
                                        <button class="btn btn-sm btn-warning btn-pausar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                            ⏸️ Pausar
                                        </button>
                                    <?php elseif ($publicacion['estado'] == 2): ?>
                                        <button class="btn btn-sm btn-success btn-reactivar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                            ▶️ Reactivar
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($publicacion['estado'] != 3): // No mostrar botón de eliminar si ya está eliminado ?>
                                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?php echo $publicacion['id_publicacion']; ?>">
                                            🗑️ Eliminar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <!-- Formularios ocultos para acciones -->
    <form id="form-cambiar-estado" action="<?php echo BASE_URL; ?>publicaciones/cambiarestado" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="estado-publicacion-id">
        <input type="hidden" name="nuevo_estado" id="estado-nuevo">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    </form>

    <form id="form-eliminar" action="<?php echo BASE_URL; ?>publicaciones/eliminar" method="POST" style="display: none;">
        <input type="hidden" name="publicacion_id" id="eliminar-publicacion-id">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    </form>

    <!-- Modal de Confirmación -->
    <div id="confirmation-modal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="modal-title">Confirmar Acción</h3>
            <p id="modal-text">¿Estás seguro?</p>
            <div class="modal-actions">
                <button id="modal-cancel-btn" class="btn btn-outline">Cancelar</button>
                <button id="modal-confirm-btn" class="btn">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formularios de acciones
            const formCambiarEstado = document.getElementById('form-cambiar-estado');
            const formEliminar = document.getElementById('form-eliminar');

            // Elementos del Modal
            const modal = document.getElementById('confirmation-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalText = document.getElementById('modal-text');
            const modalConfirmBtn = document.getElementById('modal-confirm-btn');
            const modalCancelBtn = document.getElementById('modal-cancel-btn');

            let confirmAction = null;

            function showModal(title, text, confirmBtnClass, confirmBtnText, action) {
                modalTitle.textContent = title;
                modalText.textContent = text;
                
                modalConfirmBtn.className = 'btn'; // Reset
                modalConfirmBtn.classList.add(confirmBtnClass);
                modalConfirmBtn.innerHTML = confirmBtnText;

                modal.classList.add('visible');

                confirmAction = action;
            }

            function hideModal() {
                modal.classList.remove('visible');
                confirmAction = null;
            }

            modalConfirmBtn.addEventListener('click', () => {
                if (typeof confirmAction === 'function') {
                    confirmAction();
                }
            });

            modalCancelBtn.addEventListener('click', hideModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal();
                }
            });

            // Eventos para pausar/reactivar
            document.querySelectorAll('.btn-pausar, .btn-reactivar').forEach(button => {
                button.addEventListener('click', function(e) {
                    const publicacionId = this.dataset.id;
                    const esPausar = this.classList.contains('btn-pausar');
                    const nuevoEstado = esPausar ? 2 : 1;
                    const accionTexto = esPausar ? 'pausar' : 'reactivar';
                    const btnClass = esPausar ? 'btn-warning' : 'btn-success';
                    const btnText = esPausar ? 'Sí, pausar' : 'Sí, reactivar';

                    showModal(`Confirmar ${accionTexto}`, `¿Estás seguro de que quieres ${accionTexto} esta publicación?`, btnClass, btnText, () => {
                        document.getElementById('estado-publicacion-id').value = publicacionId;
                        document.getElementById('estado-nuevo').value = nuevoEstado;
                        formCambiarEstado.submit();
                    });
                });
            });

            // Evento para eliminar
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function(e) {
                    const publicacionId = this.dataset.id;
                    showModal('Confirmar Eliminación', '¿Estás seguro de que quieres eliminar esta publicación? Esta acción cambiará su estado a "Eliminado" y no se podrá deshacer.', 'btn-danger', 'Sí, eliminar', () => {
                        document.getElementById('eliminar-publicacion-id').value = publicacionId;
                        formEliminar.submit();
                    });
                });
            });
        });
    </script>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>