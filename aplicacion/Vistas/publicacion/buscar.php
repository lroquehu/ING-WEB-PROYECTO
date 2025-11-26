<?php
// aplicacion/Vistas/publicacion/buscar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario_autenticado = isset($_SESSION['usuario_id']);

// Incluir el header
require_once __DIR__ . '/../plantillas/header.php';

// Datos que vienen del controlador
$resultados = $resultados ?? [];
$termino_busqueda = $termino_busqueda ?? '';
$total_resultados = $total_resultados ?? 0;

?>

<style>
    /* Estilos del archivo inicio/index.php para reutilizar */
    :root {
        --primary-color: #910202; --primary-dark: #510200; --primary-light: #b30303; --secondary-color: #2c3e50; --accent-color: #ffd700; --text-dark: #333; --text-light: #666; --text-lighter: #888; --bg-light: #f8f9fa; --bg-white: #ffffff; --border-color: #e1e1e1; --shadow: 0 4px 15px rgba(0,0,0,0.1); --shadow-hover: 0 8px 25px rgba(0,0,0,0.15); --transition: all 0.3s ease;
    }
    body { background: var(--bg-light); }
    .container { max-width: 1500px; margin: 0 auto; padding: 0 1rem; }
    .search-results-header { padding: 8rem 0 2rem; text-align: center; background: var(--bg-white); border-bottom: 1px solid var(--border-color); }
    .search-results-header h1 { font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 0.5rem; }
    .search-results-header p { font-size: 1.2rem; color: var(--text-light); }
    .search-results-header span { font-weight: 600; color: var(--primary-color); }
    section.results { padding: 3rem 0; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; }
    .product-card { background: var(--bg-white); overflow: hidden; box-shadow: var(--shadow); transition: var(--transition); position: relative; }
    .product-card:hover { box-shadow: var(--shadow-hover); transform: translateY(-5px); }
    .product-image { position: relative; height: 220px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover .product-image img { transform: scale(1.05); }
    .no-image { text-align: center; color: var(--text-lighter); }
    .no-image i { font-size: 3rem; margin-bottom: 1rem; }
    .product-badges { position: absolute; top: 1rem; left: 1rem; }
    .product-type { background: var(--primary-color); color: var(--bg-white); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .product-info { padding: 1.5rem; }
    .product-title { font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--secondary-color); }
    .product-description { color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .product-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .product-category { background: var(--bg-light); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; color: var(--text-light); }
    .product-price { font-weight: 700; color: var(--primary-color); font-size: 1.2rem; }
    .product-vendor { color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem; }
    .product-actions .btn { padding: 0.5rem 1rem; font-size: 0.85rem; }
    .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .btn-outline { background: transparent; border: 2px solid var(--border-color); color: var(--text-dark); }
    .btn-outline:hover { border-color: var(--primary-color); color: var(--primary-color); background: rgba(145, 2, 2, 0.05); }
    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); background: var(--bg-white); border-radius: 12px; margin-top: 2rem; }
    .empty-state i { font-size: 4rem; margin-bottom: 1.5rem; color: var(--border-color); }
    .empty-state h3 { font-size: 1.5rem; margin-bottom: 1rem; color: var(--text-dark); }
</style>

<main>
    <header class="search-results-header">
        <div class="container">
            <h1>Resultados de Búsqueda</h1>
            <?php if (!empty($termino_busqueda)): ?>
                <p>
                    <?php echo $total_resultados; ?> resultados para "<span><?php echo htmlspecialchars($termino_busqueda); ?></span>"
                </p>
            <?php endif; ?>
        </div>
    </header>

    <section class="results">
        <div class="container">
            <?php if (empty($resultados)): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron resultados</h3>
                    <p>Intenta con otras palabras clave o explora nuestras categorías.</p>
                    <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-outline">
                        <i class="fas fa-th-large"></i> Ver todas las publicaciones
                    </a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($resultados as $publicacion): ?>
                        <article class="product-card">
                            <div class="product-image">
                                <?php
                                $imgPrincipal = obtenerImagenFinal($publicacion['imagen_principal'] ?? null);
                                ?>
                                <?php if (!empty($imgPrincipal)): ?>
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>">
                                        <img src="<?php echo htmlspecialchars($imgPrincipal); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>" loading="lazy">
                                    </a>
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="product-badges">
                                    <span class="product-type"><?php echo htmlspecialchars($publicacion['tipo']); ?></span>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" style="text-decoration: none; color: inherit;">
                                        <?php echo htmlspecialchars($publicacion['titulo']); ?>
                                    </a>
                                </h3>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(mb_substr($publicacion['descripcion'], 0, 100)) . '...'; ?>
                                </p>
                                <div class="product-meta">
                                    <span class="product-category">
                                        <?php echo htmlspecialchars($publicacion['nombre_categoria']); ?>
                                    </span>
                                    <span class="product-price">
                                        S/ <?php echo number_format($publicacion['precio'], 2); ?>
                                    </span>
                                </div>
                                <div class="product-vendor">
                                    <i class="fas fa-user-graduate"></i>
                                    <?php echo htmlspecialchars($publicacion['nombres'] . ' ' . $publicacion['apellidos']); ?>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
// Incluir el footer
require_once __DIR__ . '/../plantillas/footer.php';
?>

```

### Pasos a seguir:

1.  **Crea el archivo**: Ve a la carpeta `c:\xampp\htdocs\ING-WEB-PROYECTO\aplicacion\Vistas\publicacion\` y crea un nuevo archivo llamado `buscar.php`.
2.  **Copia el código**: Pega el código que te proporcioné en este nuevo archivo.
3.  **Verifica las plantillas**: El código asume que tienes un `header.php` y un `footer.php` en una carpeta `aplicacion/Vistas/plantillas/`. Si tus archivos de cabecera y pie de página están en otro lugar, ajusta las rutas en las líneas `require_once`.
4.  **Prueba de nuevo**: Una vez guardado el archivo, intenta realizar una búsqueda otra vez. El error debería desaparecer y en su lugar verás la página de resultados.

Ahora tu aplicación ya tiene la capacidad de mostrar los resultados de las búsquedas. El siguiente paso sería asegurarte de que la lógica en `PublicacionController.php` esté recuperando correctamente los datos de la base de datos y pasándolos a esta nueva vista.

<!--
[PROMPT_SUGGESTION]Muéstrame el código del método `buscar` en `PublicacionController.php` para asegurarnos de que funciona correctamente.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]¿Cómo puedo agregar paginación a los resultados de búsqueda?[/PROMPT_SUGGESTION]
->