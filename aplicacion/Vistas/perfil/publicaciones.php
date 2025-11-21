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
    $estadisticas = $estadisticas ?? [
        'total' => 0,
        'activas' => 0,
        'pausadas' => 0,
        'eliminadas' => 0
    ];
    $estado_filtro = $estado_filtro ?? 'all';
    $error = $error ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Publicaciones - UniEmprende</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
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
            padding: 2rem 0;
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
</head>
<body>
    <!-- Header Simple -->
    <header style="background: white; padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="<?php echo BASE_URL; ?>" style="font-size: 1.5rem; font-weight: bold; color: #910202; text-decoration: none;">
                UniEmprende
            </a>
            <nav>
                <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline" style="margin-right: 1rem;">Mi Perfil</a>
                <a href="<?php echo BASE_URL; ?>logout" class="btn btn-secondary">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main style="padding: 2rem 0;">
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
                                        default: echo '⚫ Desconocido';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <!-- Body -->
                            <div class="publicacion-body">
                                <div class="publicacion-image">
                                    <?php if (!empty($publicacion['imagen'])): ?>
                                        <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            
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
                                        <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-publicacion" style="display: inline;">
                                            <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                                            <button type="submit" class="btn btn-sm btn-warning" name="action" value="pausar">
                                                ⏸️ Pausar
                                            </button>
                                        </form>
                                    <?php elseif ($publicacion['estado'] == 2): ?>
                                        <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-publicacion" style="display: inline;">
                                            <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success" name="action" value="reactivar">
                                                ▶️ Reactivar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-publicacion" style="display: inline;">
                                        <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" name="action" value="eliminar" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta publicación? Esta acción no se puede deshacer.')">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer Simple -->
    <footer style="background: #333; color: white; padding: 2rem 0; text-align: center; margin-top: 4rem;">
        <div class="container">
            <p>&copy; 2024 UniEmprende. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmación para acciones importantes
            const deleteButtons = document.querySelectorAll('button[name="action"][value="eliminar"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('¿Estás seguro de eliminar esta publicación? Esta acción no se puede deshacer.')) {
                        e.preventDefault();
                    }
                });
            });
            
            const pauseButtons = document.querySelectorAll('button[name="action"][value="pausar"]');
            pauseButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('¿Estás seguro de pausar esta publicación? No será visible para otros usuarios.')) {
                        e.preventDefault();
                    }
                });
            });
            
            const reactivateButtons = document.querySelectorAll('button[name="action"][value="reactivar"]');
            reactivateButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('¿Estás seguro de reactivar esta publicación? Será visible para otros usuarios.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>