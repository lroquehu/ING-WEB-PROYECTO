<?php
$page_title = 'Restablecer Contraseña - UniEmprende';
include __DIR__ . '/../plantillas/header.php';

$error = $error ?? '';
$success = $success ?? '';
$token = $token ?? '';
?>

<style>
    .reset-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 2rem 1rem;
        background-color: #f8f9fa;
    }

    .reset-box {
        background: white;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 480px;
        text-align: center;
    }

    .reset-box h1 {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .reset-box p {
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
        position: relative; /* Necesario para el botón de ver contraseña */
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }

    .form-group input {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary-color, #910202);
        box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: left;
    }
    .alert-danger { background-color: #f8d7da; color: #721c24; }
    .alert-success { background-color: #d4edda; color: #155724; }

    .back-to-login {
        margin-top: 1.5rem;
        font-size: 0.9rem;
    }

    .reset-box .btn-primary {
        background-color: var(--primary-color, #910202);
        color: white;
    }
    .reset-box .btn-primary:hover {
        background-color: var(--primary-dark, #700101);
    }

    /* --- NUEVO: Estilos para el botón de ver contraseña --- */
    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 60%; /* Ajustado para alinearse con el input */
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: color 0.3s;
    }

    .password-toggle:hover {
        color: var(--primary-color, #910202);
    }
</style>

<div class="reset-container">
    <div class="reset-box">
        <h1>Restablecer Contraseña</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <p class="back-to-login">
                <a href="<?php echo BASE_URL; ?>login" class="btn btn-primary" style="width: 100%; padding: 0.9rem;">Ir a Iniciar Sesión</a>
            </p>
        <?php else: ?>
            <p>Crea una nueva contraseña para tu cuenta. Asegúrate de que sea segura.</p>
            <form action="<?php echo BASE_URL; ?>resetear-password/<?php echo htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="nueva_contrasenia">Nueva Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="nueva_contrasenia" name="nueva_contrasenia" placeholder="Mínimo 8 caracteres" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('nueva_contrasenia')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_contrasenia">Confirmar Nueva Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="confirmar_contrasenia" name="confirmar_contrasenia" placeholder="Repite la contraseña" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmar_contrasenia')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem;">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        // El botón es el siguiente hermano del input
        const icon = input.nextElementSibling.querySelector('i');
        const type = input.type === 'password' ? 'text' : 'password';
        
        input.type = type;
        icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
</script>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>