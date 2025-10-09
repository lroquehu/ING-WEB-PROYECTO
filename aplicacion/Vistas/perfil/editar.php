<?php include 'aplicacion/Vistas/plantillas/encabezado.php'; ?>

<style>
    .edit-profile-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .edit-profile-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .edit-profile-card h1 {
        color: var(--primary);
        margin-bottom: 2rem;
        font-size: 1.8rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text);
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid var(--gray-dark);
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-secondary {
        background: var(--gray);
        color: var(--text);
        border: 1px solid var(--gray-dark);
        padding: 0.8rem 2rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: var(--gray-dark);
    }

    .error-message {
        background: #fef2f2;
        color: #dc2626;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        border: 1px solid #fecaca;
    }
</style>

<div class="edit-profile-container">
    <div class="edit-profile-card">
        <h1>Editar Perfil</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="universidad">Universidad</label>
                <input type="text" id="universidad" name="universidad" value="<?php echo htmlspecialchars($usuario['universidad'] ?? ''); ?>" placeholder="Ej: Universidad de Tacna">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" placeholder="+51 XXX XXX XXX">
            </div>

            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio" placeholder="Cuéntanos sobre ti..."><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Guardar Cambios</button>
                <a href="<?php echo BASE_URL; ?>?c=Perfil&a=index" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include 'aplicacion/Vistas/plantillas/pie.php'; ?>