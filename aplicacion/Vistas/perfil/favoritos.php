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
    $favoritos = $favoritos ?? [];
    $error = $error ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos - UniEmprende</title>
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
        
        .page-subtitle {
            color: #666;
            margin-bottom: 0.5rem;
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
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        /* Estadísticas */
        .stats-banner {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .stats-banner .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #910202;
            margin-bottom: 0.5rem;
        }
        
        .stats-banner .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* Grid de Favoritos */
        .favoritos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .favorito-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }
        
        .favorito-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .favorito-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #910202;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 2;
        }
        
        .favorito-image {
            position: relative;
            height: 200px;
            background: #f8f9fa;
            overflow: hidden;
        }
        
        .favorito-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .favorito-card:hover .favorito-image img {
            transform: scale(1.05);
        }
        
        .no-image {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
        }
        
        .no-image i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .favorito-info {
            padding: 1.5rem;
        }
        
        .favorito-title {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        
        .favorito-desc {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .favorito-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .favorito-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #910202;
        }
        
        .favorito-category {
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .favorito-vendor {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .vendor-avatar {
            width: 24px;
            height: 24px;
            background: #910202;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
        }
        
        .favorito-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-favorite {
            background: #910202;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-favorite:hover {
            background: #700101;
            transform: scale(1.1);
        }
        
        .favorito-date {
            color: #999;
            font-size: 0.8rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        
        .empty-state p {
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
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
        
        /* Filtros */
        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .filter-options {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 0.5rem;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            background: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .favoritos-grid {
                grid-template-columns: 1fr;
            }
            
            .favorito-meta {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
            
            .filter-options {
                flex-direction: column;
                align-items: stretch;
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
                <div>
                    <h1>Mis Favoritos</h1>
                    <p class="page-subtitle">Productos y servicios que has guardado para ver después</p>
                </div>
                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                    🔍 Explorar Más
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-banner">
                <div class="stat-number"><?php echo count($favoritos); ?></div>
                <div class="stat-label">Productos en tus favoritos</div>
            </div>

            <!-- Filtros (opcional para futuras implementaciones) -->
            <!--
            <div class="filters-section">
                <h3 style="margin-bottom: 1rem; color: #333;">Filtrar favoritos:</h3>
                <div class="filter-options">
                    <select class="filter-select">
                        <option value="all">Todos los tipos</option>
                        <option value="Producto">Solo productos</option>
                        <option value="Servicio">Solo servicios</option>
                    </select>
                    <select class="filter-select">
                        <option value="recent">Más recientes primero</option>
                        <option value="oldest">Más antiguos primero</option>
                        <option value="price-low">Precio: menor a mayor</option>
                        <option value="price-high">Precio: mayor a menor</option>
                    </select>
                </div>
            </div>
            -->

            <!-- Lista de Favoritos -->
            <?php if (empty($favoritos)): ?>
                <div class="empty-state">
                    <div>❤️</div>
                    <h3>No tienes favoritos aún</h3>
                    <p>Los productos y servicios que guardes como favoritos aparecerán aquí. Es una forma fácil de mantener un registro de lo que te interesa.</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-primary">
                            🔍 Explorar Productos
                        </a>
                        <a href="<?php echo BASE_URL; ?>publicaciones/categorias" class="btn btn-outline">
                            📂 Ver por Categorías
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="favoritos-grid">
                    <?php foreach ($favoritos as $favorito): ?>
                        <div class="favorito-card">
                            <!-- Badge de Tipo -->
                            <div class="favorito-badge">
                                <?php echo $favorito['tipo']; ?>
                            </div>
                            
                            <!-- Imagen -->
                            <div class="favorito-image">
                                <?php 
                                // Obtener la URL final de la imagen principal
                                $imgFinal = obtenerImagenFinal($favorito['imagen_principal'] ?? null);
                                ?>

                                <?php if (!empty($imgFinal)): ?>
                                    <img src="<?php echo htmlspecialchars($imgFinal); ?>" 
                                        alt="<?php echo htmlspecialchars($favorito['titulo']); ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        🖼️
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Información -->
                            <div class="favorito-info">
                                <h3 class="favorito-title">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" 
                                       style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($favorito['titulo']); ?>
                                    </a>
                                </h3>
                                
                                <p class="favorito-desc">
                                    <?php echo htmlspecialchars(substr($favorito['descripcion'] ?? '', 0, 100)); ?>
                                    <?php if (strlen($favorito['descripcion'] ?? '') > 100): ?>...<?php endif; ?>
                                </p>
                                
                                <div class="favorito-meta">
                                    <span class="favorito-price">S/ <?php echo number_format($favorito['precio'], 2); ?></span>
                                    <span class="favorito-category"><?php echo htmlspecialchars($favorito['nombre_categoria']); ?></span>
                                </div>
                                
                                <div class="favorito-vendor">
                                    <div class="vendor-avatar">
                                        <?php echo strtoupper(substr($favorito['nombres'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($favorito['nombres'] . ' ' . $favorito['apellidos']); ?></span>
                                </div>
                                
                                <div class="favorito-actions">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $favorito['id_publicacion']; ?>" 
                                       class="btn btn-outline btn-sm" style="flex: 1;">
                                        👁️ Ver Detalles
                                    </a>
                                    <form method="POST" action="<?php echo BASE_URL; ?>perfil/eliminar-favorito" 
                                          style="display: inline;" class="remove-favorite-form">
                                        <input type="hidden" name="publicacion_id" value="<?php echo $favorito['id_publicacion']; ?>">
                                        <button type="submit" class="btn-favorite" title="Quitar de favoritos">
                                            ❤️
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="favorito-date">
                                    Guardado el <?php echo date('d/m/Y', strtotime($favorito['fecha_agregado'])); ?>
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
            // Confirmación para quitar de favoritos
            const removeForms = document.querySelectorAll('.remove-favorite-form');
            removeForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('¿Estás seguro de quitar este producto de tus favoritos?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Efecto de hover en tarjetas (ya está en CSS, pero podemos agregar interactividad adicional)
            const favoritoCards = document.querySelectorAll('.favorito-card');
            favoritoCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Futura implementación: filtros dinámicos
            const filterSelects = document.querySelectorAll('.filter-select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    // Aquí iría la lógica para filtrar los favoritos
                    console.log('Filtrar por:', this.value);
                    // Podría ser una llamada AJAX o recarga de página con parámetros
                });
            });
        });
    </script>
</body>
</html>
