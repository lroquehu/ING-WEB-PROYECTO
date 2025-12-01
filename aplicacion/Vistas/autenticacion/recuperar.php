<?php
$page_title = 'Recuperar Contraseña - UniEmprende';
include __DIR__ . '/../plantillas/header.php';

$error = $error ?? '';
$success = $success ?? '';
?>

<style>
    .recovery-container {
        display: flex;
        justify-content: center;
        align-items: center; /* REVERTIDO: Volver a centrar verticalmente */
        min-height: calc(100vh - 200px); /* Ajustar para header/footer */
        padding: 2rem 1rem; /* Padding original */
        background-color: #f8f9fa;
    }

    .recovery-box {
        background: white;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 650px; /* Aumentado aún más para correos largos */
        text-align: center;
    }

    .recovery-box h1 {
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .recovery-box p {
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
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
    }
    /* CORREGIDO: Alinear el texto del error a la izquierda para mejor legibilidad */
    .alert-danger { 
        background-color: #fef2f2; 
        color: #dc2626; 
        text-align: left;
    }

    /* --- NUEVO: Estilo rediseñado para la alerta de éxito --- */
    .alert-success {
        background-color: #f0fdf4; /* Un verde más suave */
        border: 1px solid #bbf7d0;
        color: #14532d; /* Texto más oscuro para mejor contraste */
        display: flex;
        flex-direction: column; /* Apila el icono, texto y correo */
        align-items: center;
        gap: 0.75rem; /* Espacio entre elementos */
        padding: 1.5rem;
    }

    .alert-success strong {
        background-color: #dcfce7; /* Fondo para el correo */
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        word-break: break-all; /* Asegura que el correo se rompa si es muy largo */
    }

    .back-to-login {
        margin-top: 1.5rem;
        font-size: 0.9rem;
    }

    /* --- NUEVO: Corrección para el botón principal en esta página --- */
    .recovery-box .btn-primary {
        background-color: var(--primary-color, #910202);
        color: white;
    }
    .recovery-box .btn-primary:hover {
        background-color: var(--primary-dark, #700101);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>

<div class="recovery-container">
    <div class="recovery-box">
        <h1>Recuperar Contraseña</h1>
        <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

        <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

        <?php if (!$success): ?>
        <form action="<?php echo BASE_URL; ?>recuperar-password" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="correo">Correo Electrónico Institucional</label>
                <input type="email" id="correo" name="correo" placeholder="ejemplo@uandina.edu.pe" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem;">Enviar Enlace de Recuperación</button>
        </form>
        <?php endif; ?>

        <p class="back-to-login">
            ¿Recordaste tu contraseña? <a href="<?php echo BASE_URL; ?>login">Vuelve a iniciar sesión</a>.
        </p>
    </div>
</div>

<?php include __DIR__ . '/../plantillas/footer.php'; ?>