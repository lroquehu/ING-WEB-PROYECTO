<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'autenticacion/login');
    exit;
}

// Obtener ID de la publicación desde la URL
$publicacion_id = $_GET['id'] ?? 0;

if (!$publicacion_id) {
    header('Location: ' . BASE_URL . 'perfil');
    exit;
}

require_once '../../configuracion/conexion.php';

$publicacion = [];
$categorias = [];
$error = '';
$success = '';

try {
    $conexion = new Conexion();
    $db = $conexion->conectar();
    
    if ($db) {
        // Verificar que la publicación pertenece al usuario
        $stmt = $db->prepare("
            SELECT p.*, c.nombre_categoria 
            FROM Publicaciones p 
            JOIN Categorias c ON p.id_categoria = c.id_categoria 
            WHERE p.id_publicacion = ? AND p.id_usuario = ?
        ");
        $stmt->execute([$publicacion_id, $_SESSION['usuario_id']]);
        $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$publicacion) {
            $error = "Publicación no encontrada o no tienes permisos para editarla";
        } else {
            // Obtener categorías
            $stmt = $db->query("SELECT id_categoria, nombre_categoria FROM Categorias WHERE estado = 1 ORDER BY nombre_categoria");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $titulo = trim($_POST['titulo'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $categoria_id = intval($_POST['categoria_id'] ?? 0);
                $tipo = $_POST['tipo'] ?? 'Producto';
                $precio = floatval($_POST['precio'] ?? 0);
                $telefono_contacto = trim($_POST['telefono_contacto'] ?? '');
                $correo_contacto = trim($_POST['correo_contacto'] ?? '');
                $estado = intval($_POST['estado'] ?? 1);
                
                // Validaciones
                if (empty($titulo) || empty($descripcion) || $categoria_id === 0) {
                    $error = "Por favor completa todos los campos obligatorios";
                } elseif (strlen($titulo) < 5) {
                    $error = "El título debe tener al menos 5 caracteres";
                } elseif (strlen($descripcion) < 10) {
                    $error = "La descripción debe tener al menos 10 caracteres";
                } elseif ($precio < 0) {
                    $error = "El precio no puede ser negativo";
                } else {
                    // Actualizar publicación
                    $stmt = $db->prepare("
                        UPDATE Publicaciones 
                        SET titulo = ?, descripcion = ?, id_categoria = ?, tipo = ?, 
                            precio = ?, telefono_contacto = ?, correo_contacto = ?, estado = ?
                        WHERE id_publicacion = ? AND id_usuario = ?
                    ");
                    
                    if ($stmt->execute([
                        $titulo,
                        $descripcion,
                        $categoria_id,
                        $tipo,
                        $precio,
                        $telefono_contacto,
                        $correo_contacto,
                        $estado,
                        $publicacion_id,
                        $_SESSION['usuario_id']
                    ])) {
                        $success = "Publicación actualizada exitosamente";
                        // Actualizar datos locales
                        $publicacion = array_merge($publicacion, [
                            'titulo' => $titulo,
                            'descripcion' => $descripcion,
                            'id_categoria' => $categoria_id,
                            'tipo' => $tipo,
                            'precio' => $precio,
                            'telefono_contacto' => $telefono_contacto,
                            'correo_contacto' => $correo_contacto,
                            'estado' => $estado
                        ]);
                    } else {
                        $error = "Error al actualizar la publicación";
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    error_log("Error al editar publicación: " . $e->getMessage());
    $error = "Error al procesar la solicitud";
}
?>

<?php include '../plantillas/encabezado.php'; ?>

<div class="edit-product-container">
    <div class="container">
        <div class="page-header">
            <h1>Editar Publicación</h1>
            <a href="<?php echo BASE_URL; ?>producto/ver/<?php echo $publicacion_id; ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a la Publicación
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($publicacion): ?>
        <div class="edit-product-form">
            <form method="POST">
                <div class="form-section">
                    <h3>Información Básica</h3>
                    
                    <div class="form-group">
                        <label for="titulo">Título de la publicación *</label>
                        <input type="text" id="titulo" name="titulo" 
                               value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" 
                               placeholder="Ej: Laptop HP i5 8GB RAM excelente estado" required maxlength="150">
                        <small>Máximo 150 caracteres. Sé claro y descriptivo.</small>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" rows="6" 
                                  placeholder="Describe detalladamente tu producto o servicio..." required><?php echo htmlspecialchars($publicacion['descripcion']); ?></textarea>
                        <small>Incluye características, condición, especificaciones técnicas, etc.</small>
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

                <div class="form-section">
                    <h3>Precio y Contacto</h3>
                    
                    <div class="form-group">
                        <label for="precio">Precio (S/) *</label>
                        <input type="number" id="precio" name="precio" 
                               value="<?php echo htmlspecialchars($publicacion['precio']); ?>" 
                               step="0.01" min="0" placeholder="0.00" required>
                        <small>Ingresa 0 si es gratuito o el servicio no tiene precio fijo.</small>
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

                <div class="form-section">
                    <h3>Estado de la Publicación</h3>
                    
                    <div class="form-group">
                        <label for="estado">Estado actual</label>
                        <select id="estado" name="estado">
                            <option value="1" <?php echo ($publicacion['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="2" <?php echo ($publicacion['estado'] == 2) ? 'selected' : ''; ?>>Pausado</option>
                            <option value="3" <?php echo ($publicacion['estado'] == 3) ? 'selected' : ''; ?>>Eliminado</option>
                        </select>
                        <small>
                            <strong>Activo:</strong> Visible para todos<br>
                            <strong>Pausado:</strong> No visible, puedes reactivarlo después<br>
                            <strong>Eliminado:</strong> Se eliminará permanentemente
                        </small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>producto/ver/<?php echo $publicacion_id; ?>" class="btn btn-outline">Cancelar</a>
                    
                    <?php if ($publicacion['estado'] != 3): ?>
                    <a href="<?php echo BASE_URL; ?>producto/eliminar/<?php echo $publicacion_id; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('¿Estás seguro de eliminar esta publicación? Esta acción no se puede deshacer.')">
                        <i class="fas fa-trash"></i> Eliminar Publicación
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php else: ?>
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>No se pudo cargar la publicación</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-primary">Volver a Mis Publicaciones</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.edit-product-container {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.edit-product-form {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.error-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.error-state i {
    font-size: 4rem;
    color: #f59e0b;
    margin-bottom: 1.5rem;
}

.error-state h3 {
    color: #374151;
    margin-bottom: 1rem;
}

.error-state p {
    color: #6b7280;
    margin-bottom: 2rem;
}

.btn-danger {
    background: #dc2626;
    color: white;
    border: none;
}

.btn-danger:hover {
    background: #b91c1c;
}
</style>

<script>
// Mismo JavaScript que crear.php para contadores de caracteres
document.addEventListener('DOMContentLoaded', function() {
    const tituloInput = document.getElementById('titulo');
    const descripcionInput = document.getElementById('descripcion');
    
    function updateCharacterCount(input, maxLength) {
        const currentLength = input.value.length;
        const remaining = maxLength - currentLength;
        
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
});
</script>

<?php include '../plantillas/pie.php'; ?>