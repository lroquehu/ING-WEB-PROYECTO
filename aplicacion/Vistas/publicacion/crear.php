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
    $categorias = $categorias ?? [];
    $error = $error ?? '';
    $success = $success ?? '';
    $datos_formulario = $datos_formulario ?? [
        'titulo' => '', 'descripcion' => '', 'categoria_id' => '', 
        'tipo' => 'Producto', 'precio' => '', 'telefono_contacto' => '', 'correo_contacto' => ''
    ];
?>

<?php 
    $page_title = 'Crear Publicación - UniEmprende';
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
            font-size: 0.95rem;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Formulario */
        .create-product-form {
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
        
        .char-counter {
            font-weight: 600;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        /* Placeholder de imágenes */
        .image-upload-placeholder {
            text-align: center;
            padding: 3rem 2rem;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
        }
        
        .image-upload-placeholder i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }
        
        /* Preview de imágenes */
        .image-preview {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
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
        
        /* Consejos */
        .creation-tips {
            background: #f0f9ff;
            border-radius: 12px;
            padding: 2rem;
            border-left: 4px solid #910202;
        }
        
        .creation-tips h3 {
            color: #910202;
            margin-bottom: 1rem;
        }
        
        .creation-tips ul {
            list-style: none;
            padding: 0;
        }
        
        .creation-tips li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e1f5fe;
        }
        
        .creation-tips li:last-child {
            border-bottom: none;
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
        
        
        /* --- NUEVO: Estilos para el Modal de Confirmación Personalizado --- */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000; /* Muy alto para estar por encima de todo */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .custom-modal-overlay.visible {
            opacity: 1;
            visibility: visible;
        }

        .custom-modal-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 450px;
            text-align: center;
        }

        .custom-modal-buttons {
            margin-top: 1.5rem; display: flex; justify-content: center; gap: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column-reverse;
                gap: 1rem;
                text-align: center;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: row;
                align-items: stretch;
            }
            
            .create-product-form {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .create-product-form {
                padding: 1rem;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
            
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .btn-large {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
            
            .creation-tips {
                padding: 1.5rem;
            }
        }
    </style>

    <main>
        <div class="container">
            <!-- Header de Página -->
            <div class="page-header">
                <h1>Crear Nueva Publicación</h1>
                <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-outline">
                    ← Volver a Productos
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
            <div class="create-product-form">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Información Básica -->
                    <div class="form-section">
                        <h3>Información Básica</h3>
                        
                        <div class="form-group">
                            <label for="titulo">Título de la publicación *</label>
                            <input type="text" id="titulo" name="titulo" 
                                   value="<?php echo htmlspecialchars($datos_formulario['titulo']); ?>" 
                                   placeholder="Ej: Laptop HP i5 8GB RAM excelente estado" required maxlength="150">
                            <small>Máximo 150 caracteres. Sé claro y descriptivo.</small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción *</label>
                            <textarea id="descripcion" name="descripcion" rows="6" 
                                      placeholder="Describe detalladamente tu producto o servicio..." required><?php echo htmlspecialchars($datos_formulario['descripcion']); ?></textarea>
                            <small>Incluye características, condición, especificaciones técnicas, etc.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="categoria_id">Categoría *</label>
                                <select id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id_categoria']; ?>" 
                                                <?php echo ($datos_formulario['categoria_id'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tipo">Tipo *</label>
                                <select id="tipo" name="tipo" required>
                                    <option value="Producto" <?php echo ($datos_formulario['tipo'] == 'Producto') ? 'selected' : ''; ?>>Producto</option>
                                    <option value="Servicio" <?php echo ($datos_formulario['tipo'] == 'Servicio') ? 'selected' : ''; ?>>Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Precio y Contacto -->
                    <div class="form-section">
                        <h3>Precio y Contacto</h3>
                        
                        <div class="form-group">
                            <label for="precio">Precio (S/) *</label>
                            <input type="number" id="precio" name="precio" 
                                   value="<?php echo htmlspecialchars($datos_formulario['precio']); ?>" 
                                   step="0.01" min="0" placeholder="0.00" required>
                            <small>Ingresa 0 si es gratuito o el servicio no tiene precio fijo.</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono_contacto">Teléfono de contacto</label>
                                <input type="tel" id="telefono_contacto" name="telefono_contacto" 
                                       value="<?php echo htmlspecialchars($datos_formulario['telefono_contacto']); ?>"
                                       placeholder="Ej: 987654321">
                            </div>
                            
                            <div class="form-group">
                                <label for="correo_contacto">Correo de contacto</label>
                                <input type="email" id="correo_contacto" name="correo_contacto" 
                                       value="<?php echo htmlspecialchars($datos_formulario['correo_contacto']); ?>"
                                       placeholder="Ej: contacto@ejemplo.com">
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes (Próximamente) -->
                    <div class="form-section">
                        <h3>Imágenes</h3>
                        <div class="form-group">
                            <label for="imagenes">Subir imágenes (máx. 5, jpg/png/gif, 2MB c/u)</label>
                            <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg,image/png,image/gif" multiple>
                            <small>Selecciona hasta 5 imágenes. Recomendado 800x600px.</small>
                        </div>
                        <div id="preview" class="image-preview"></div>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            ➕ Crear Publicación
                        </button>
                        <a href="<?php echo BASE_URL; ?>publicaciones" class="btn btn-outline">Cancelar</a>
                    </div>
                </form>
            </div>

            <!-- Consejos -->
            <div class="creation-tips">
                <h3>Consejos para una buena publicación:</h3>
                <ul>
                    <li>📸 <strong>Usa fotos claras:</strong> Múltiples ángulos y buena iluminación</li>
                    <li>📝 <strong>Sé detallado:</strong> Incluye todas las características importantes</li>
                    <li>💰 <strong>Precio justo:</strong> Investiga precios similares en el mercado</li>
                    <li>📞 <strong>Contacto claro:</strong> Proporciona medios de contacto accesibles</li>
                    <li>⏰ <strong>Respuesta rápida:</strong> Responde pronto a los interesados</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Custom Confirmation Modal -->
    <div id="confirm-modal" class="custom-modal-overlay">
        <div class="custom-modal-box">
            <h3 id="confirm-modal-title" style="font-size: 1.4rem; color: #333; margin-bottom: 1rem;"></h3>
            <p id="confirm-modal-text" style="color: #666; line-height: 1.6;"></p>
            <div class="custom-modal-buttons">
                <button id="confirm-modal-cancel" class="btn btn-outline" style="border-color: #ccc; color: #333;">Cancelar</button>
                <button id="confirm-modal-ok" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Contador de caracteres para título y descripción
            const tituloInput = document.getElementById('titulo');
            const descripcionInput = document.getElementById('descripcion');
            
            function updateCharacterCount(input, maxLength) {
                const currentLength = input.value.length;
                const remaining = maxLength - currentLength;
                
                // Crear o actualizar contador
                let counter = input.parentNode.querySelector('.char-counter');
                if (!counter) {
                    counter = document.createElement('small');
                    counter.className = 'char-counter';
                    input.parentNode.appendChild(counter);
                }
                
                counter.textContent = `${currentLength}/${maxLength} caracteres`;
                counter.style.color = remaining < 20 ? '#ef4444' : '#6b7280';
            }
            
            if (tituloInput) {
                tituloInput.addEventListener('input', function() {
                    updateCharacterCount(this, 150);
                });
                updateCharacterCount(tituloInput, 150);
            }
            
            if (descripcionInput) {
                descripcionInput.addEventListener('input', function() {
                    updateCharacterCount(this, 2000);
                });
                updateCharacterCount(descripcionInput, 2000);
            }
            
            // Validación de precio
            const precioInput = document.getElementById('precio');
            if (precioInput) {
                precioInput.addEventListener('blur', function() {
                    if (this.value < 0) {
                        this.value = 0;
                    }
                });
            }
            
            const inputImgs = document.getElementById('imagenes');
            const preview = document.getElementById('preview');
            if (inputImgs) {
                inputImgs.addEventListener('change', function() {
                    preview.innerHTML = '';
                    const files = Array.from(this.files).slice(0,5);
                    files.forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '120px';
                            img.style.height = '80px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '6px';
                            img.style.border = '1px solid #e5e7eb';
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // --- Lógica para el Modal de Confirmación ---
            const modal = document.getElementById('confirm-modal');
            const modalTitle = document.getElementById('confirm-modal-title');
            const modalText = document.getElementById('confirm-modal-text');
            const modalOkBtn = document.getElementById('confirm-modal-ok');
            const modalCancelBtn = document.getElementById('confirm-modal-cancel');
            let confirmAction = null;

            function showModal(title, text, onConfirm) {
                modalTitle.textContent = title;
                modalText.textContent = text;
                confirmAction = onConfirm;
                modal.classList.add('visible');
            }

            function hideModal() {
                modal.classList.remove('visible');
                confirmAction = null;
            }

            modalCancelBtn.addEventListener('click', hideModal);
            modalOkBtn.addEventListener('click', () => {
                if (typeof confirmAction === 'function') {
                    confirmAction();
                }
                hideModal();
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal();
                }
            });

            // Validación del formulario antes de enviar
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevenir envío por defecto para validar y mostrar modal

                const titulo = document.getElementById('titulo').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const categoria = document.getElementById('categoria_id').value;
                const precio = document.getElementById('precio').value;
                
                // Validaciones básicas
                if (titulo.length < 5) { alert('El título debe tener al menos 5 caracteres'); return; }
                if (descripcion.length < 10) { alert('La descripción debe tener al menos 10 caracteres'); return; }
                if (!categoria) { alert('Por favor selecciona una categoría'); return; }
                if (precio < 0) { alert('El precio no puede ser negativo'); return; }
                
                // Confirmación con modal
                showModal(
                    'Confirmar Creación',
                    '¿Estás seguro de que quieres crear esta publicación?',
                    () => { form.submit(); } // Envía el formulario si se confirma
                );
            });
            
            // Prevenir envío con Enter en campos de texto
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.ctrlKey) {
                        // Permitir Ctrl+Enter para enviar
                        return;
                    }
                    if (e.key === 'Enter') {
                        e.stopPropagation();
                    }
                });
            });
        });
    </script>

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>