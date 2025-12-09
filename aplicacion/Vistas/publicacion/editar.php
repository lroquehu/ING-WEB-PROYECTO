<?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticación
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    // Datos que vienen del controlador
    $publicacion = $publicacion ?? null;
    $categorias = $categorias ?? [];
    $error = $error ?? '';
    $success = $success ?? '';

    if (!$publicacion) {
        // Manejo si no hay publicación para editar
        // Esto podría redirigir o mostrar un error más amigable
        die('Error: No se encontró la publicación para editar.');
    }
?>

<?php 
    $page_title = 'Editar Publicación - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php'; 
?>
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
        body::before { display: none; } /* Anula el overlay del header global */
        
        main .container {
            max-width: 800px;
            margin: 1rem auto; /* Reducido el margen para un look más compacto */
            padding: 0 1rem;
        }
        
        /* Header de Página */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 0; /* Reducido el padding para que no se vea "gordito" */
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
        
        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        /* Formulario */
        .edit-product-form {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .form-section:last-of-type {
            border-bottom: none;
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-left: 4px solid #910202;
            padding-left: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #910202;
            box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
        }
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        /* Acciones del Formulario */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }

        /* Preview de imágenes */
        .image-preview-container {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        .image-preview-item {
            width: 120px;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .image-preview-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: row;
                justify-content:center;
                align-items: stretch;
            }
            
            .edit-product-form {
                padding: 1.5rem;
            }
        }
    </style>

    <main>
        <div class="container">
            <!-- Header de Página -->
            <div class="page-header">
                <h1>Editar Publicación</h1>
                <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline">
                    ← Volver a la Publicación
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="edit-product-form">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">

                    <!-- Información Básica -->
                    <div class="form-section">
                        <h3>Información Básica</h3>
                        
                        <div class="form-group">
                            <label for="titulo">Título de la publicación *</label>
                            <input type="text" id="titulo" name="titulo" 
                                   value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" 
                                   placeholder="Ej: Laptop HP i5 8GB RAM excelente estado" required maxlength="150">
                            <small>Máximo 150 caracteres.</small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción *</label>
                            <textarea id="descripcion" name="descripcion" rows="6" 
                                      placeholder="Describe detalladamente tu producto o servicio..." required><?php echo htmlspecialchars($publicacion['descripcion']); ?></textarea>
                            <small>Incluye características, condición, etc.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="categoria_id">Categoría *</label>
                                <select id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id_categoria']; ?>" 
                                                <?php echo ($publicacion['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tipo">Tipo *</label>
                                <select id="tipo" name="tipo" required>
                                    <option value="Producto" <?php echo ($publicacion['tipo'] == 'Producto') ? 'selected' : ''; ?>>Producto</option>
                                    <option value="Servicio" <?php echo ($publicacion['tipo'] == 'Servicio') ? 'selected' : ''; ?>>Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Precio y Contacto -->
                    <div class="form-section">
                        <h3>Precio y Estado</h3>
                        
                        <div class="form-group">
                            <label for="precio">Precio (S/) *</label>
                            <input type="number" id="precio" name="precio" 
                                   value="<?php echo htmlspecialchars($publicacion['precio']); ?>" 
                                   step="0.01" min="0" placeholder="0.00" required>
                            <small>Ingresa 0 si es gratuito.</small>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado de la publicación</label>
                            <select id="estado" name="estado" required>
                                <option value="1" <?php echo ($publicacion['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                <option value="2" <?php echo ($publicacion['estado'] == 2) ? 'selected' : ''; ?>>Pausado</option>
                            </select>
                            <small><strong>Activo:</strong> Visible para todos. <strong>Pausado:</strong> Oculto para los demás.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono_contacto">Teléfono de contacto</label>
                                <input type="tel" id="telefono_contacto" name="telefono_contacto" 
                                       value="<?php echo htmlspecialchars($publicacion['telefono_contacto']); ?>"
                                       placeholder="Ej: 987654321">
                            </div>
                            
                            <div class="form-group">
                                <label for="correo_contacto">Correo de contacto</label>
                                <input type="email" id="correo_contacto" name="correo_contacto" 
                                       value="<?php echo htmlspecialchars($publicacion['correo_contacto']); ?>"
                                       placeholder="Ej: contacto@ejemplo.com">
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes -->
                    <div class="form-section">
                        <h3>Imágenes</h3>
                        <div class="form-group">
                            <label>Imágenes Actuales</label>
                            <div class="image-preview-container" id="current-images">
                                <?php if (!empty($publicacion['imagenes'])): ?>
                                    <?php foreach ($publicacion['imagenes'] as $imagen): ?>
                                        <div class="image-preview-item" id="img-<?php echo $imagen['id_imagen']; ?>">
                                            <img src="<?php echo PROD_IMAGE_URL . $imagen['url_imagen']; ?>" alt="Imagen de la publicación">
                                        <button type="button" class="btn btn-danger delete-image-btn" data-img-id="<?php echo $imagen['id_imagen']; ?>">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No hay imágenes actuales.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="imagenes">Añadir nuevas imágenes (máx. 5 en total)</label>
                            <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg,image/png,image/gif" multiple>
                            <small>Puedes subir nuevas imágenes. Las existentes se pueden eliminar.</small>
                        </div>
                        <div id="preview" class="image-preview-container"></div>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline">Cancelar</a>
                    </div>
                </form>
                
                <!-- Botón de Eliminar -->
                <div style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 2rem;">
                    <form action="<?php echo BASE_URL; ?>publicaciones/eliminar" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta publicación? Esta acción no se puede deshacer.');">
                        <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Publicación
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // 1. Previsualización de nuevas imágenes
        document.getElementById('imagenes').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('preview');
            previewContainer.innerHTML = ''; // Limpiar preview anterior
            
            const files = event.target.files;
            
            if (files.length > 5) {
                alert('Máximo 5 imágenes permitidas');
                this.value = ''; // Limpiar selección
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.startsWith('image/')){ continue }

                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    
                    div.appendChild(img);
                    previewContainer.appendChild(div);
                }
                
                reader.readAsDataURL(file);
            }
        });

        // 2. Lógica para eliminar imágenes existentes
        document.querySelectorAll('.delete-image-btn').forEach(button => {
            button.addEventListener('click', function() {
                const imgId = this.getAttribute('data-img-id');
                const container = document.getElementById('img-' + imgId);
                
                if (confirm('¿Estás seguro de eliminar esta imagen? Se borrará al Guardar Cambios.')) {
                    // Ocultar visualmente
                    container.style.display = 'none';
                    
                    // Crear input oculto para enviar al servidor que se debe borrar esta imagen
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'eliminar_imagenes[]'; // Este nombre coincide con el Controlador
                    input.value = imgId;
                    
                    // Agregar al formulario
                    document.querySelector('form').appendChild(input);
                }
            });
        });
    </script>

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>
