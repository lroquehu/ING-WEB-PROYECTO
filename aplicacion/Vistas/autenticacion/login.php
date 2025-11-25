<?php
// Si ya está autenticado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'inicio');
    exit;
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $error ?? '';
$correo_ingresado = $correo_ingresado ?? '';
$intentos_restantes = 5 - ($_SESSION['intentos_login'] ?? 0);
$bloqueado = ($_SESSION['bloqueo_hasta'] ?? 0) > time();
$tiempo_espera = $bloqueado ? ($_SESSION['bloqueo_hasta'] - time()) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UniEmprende</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-color: #910202;
        --primary-dark: #700101;
        --primary-light: #b30303;
        --text-dark: #333;
        --text-light: #666;
        --border-color: #e1e1e1;
        --error-color: #dc3545;
        --success-color: #28a745;
        --warning-color: #ffc107;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .auth-container {
        display: flex;
        width: 100%;
        max-width: 1000px;
        height: auto;
        min-height: 600px;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .auth-card {
        flex: 1;
        padding: 3rem 2rem;
        display: flex;
        position: relative; /* Añadido para posicionar el botón de cierre */
        flex-direction: column;
        justify-content: center;
    }
    
    .auth-background {
        flex: 1;
        background: linear-gradient(135deg, var(--primary-dark) 0%, #500000 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .auth-background::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="rgba(255,255,255,0.05)"><circle cx="50" cy="50" r="2"/></svg>') repeat;
    }
    
    .background-content h2 {
        font-size: 2.2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    
    .background-content p {
        font-size: 1.1rem;
        line-height: 1.6;
        opacity: 0.9;
    }
    
    .features-list {
        margin-top: 2rem;
        text-align: left;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
    
    .feature-item i {
        margin-right: 0.75rem;
        color: #ffd700;
    }
    
    .auth-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .auth-header h1 {
        color: var(--primary-color);
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }
    
    .auth-header p {
        color: var(--text-light);
        font-size: 1rem;
    }
    
    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .input-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }
    
    .input-group input {
        width: 100%;
        padding: 0.6rem 0.6rem;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }
    
    .input-group input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
    }
    
    .input-group input:invalid:not(:focus):not(:placeholder-shown) {
        border-color: var(--error-color);
    }
    
    .password-toggle {
        position: absolute;
        right: 0.7rem;
        top: 54%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-light);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: color 0.3s;
    }
    
    .password-toggle:hover {
        color: var(--primary-color);
    }
    
    .btn {
        padding: 1rem 2rem;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
        width: 100%;
    }
    
    .btn-primary:hover:not(:disabled) {
        background: var(--primary-dark);
        box-shadow: 0 8px 20px rgba(145, 2, 2, 0.3);
    }
    
    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .btn-loading {
        position: relative;
        color: transparent;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        border-left: 4px solid;
    }
    
    .alert-error {
        background: #fee;
        color: var(--error-color);
        border-color: var(--error-color);
    }
    
    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-color: var(--warning-color);
    }
    
    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-color: #bee5eb;
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .auth-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }
    
    .auth-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    
    .forgot-password {
        text-align: right;
        margin-top: -0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .forgot-password a {
        color: var(--text-light);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
    }
    
    .forgot-password a:hover {
        color: var(--primary-color);
    }
    
    .security-info {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
        font-size: 0.85rem;
        color: var(--text-light);
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .auth-container {
            flex-direction: column;
            max-width: 100%;
            height: auto;
        }
        
        .auth-background {
            order: -1;
            padding: 1.5rem;
        }
        
        .auth-card {
            padding: 2rem 1.5rem;
        }
        
        .background-content h2 {
            font-size: 2rem;
        }
        
        .auth-header h1 {
            font-size: 1.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .auth-card {
            padding: 1.5rem 1rem;
        }
        
        .auth-background {
            padding: 1rem;
        }
        
        .background-content h2 {
            font-size: 1.75rem;
        }
    }

    /* Estilo para el botón de cierre (X) */
    .close-button {
        position: absolute;
        top: 1rem;
        right: 1.5rem;
        font-size: 2rem;
        color: #aaa;
        text-decoration: none;
        line-height: 1;
        transition: color 0.3s ease;
    }

    .close-button:hover {
        color: var(--primary-color);
    }

    @media (max-width: 768px) {
        .close-button { top: 0.5rem; right: 1rem; }
    }

</style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Iniciar Sesión</h1>
                <a href="<?php echo BASE_URL; ?>" class="close-button" aria-label="Cerrar y volver al inicio">
                    &times;
                </a>
                <p>Bienvenido de vuelta a UniEmprende</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert <?php echo $bloqueado ? 'alert-warning' : 'alert-error'; ?>">
                    <i class="fas fa-<?php echo $bloqueado ? 'clock' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="loginForm">
                <div class="input-group">
                    <label for="correo">
                        <i class="fas fa-envelope"></i> Correo institucional
                    </label>
                    <input type="email" id="correo" name="correo" 
                           value="<?php echo htmlspecialchars($correo_ingresado); ?>" 
                           placeholder="usuario@unjbg.edu.pe" 
                           required
                           <?php echo $bloqueado ? 'disabled' : ''; ?>>
                </div>

                <div class="input-group">
                    <label for="contrasenia">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <div style="position: relative;">
                        <input type="password" id="contrasenia" name="contrasenia" 
                               placeholder="Ingresa tu contraseña" 
                               required
                               minlength="6"
                               <?php echo $bloqueado ? 'disabled' : ''; ?>>
                        <button type="button" class="password-toggle" onclick="togglePassword('contrasenia')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="forgot-password">
                    <a href="<?php echo BASE_URL; ?>recuperar-contrasena" <?php echo $bloqueado ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn"
                        <?php echo $bloqueado ? 'disabled' : ''; ?>>
                    <i class="fas fa-sign-in-alt"></i> 
                    <?php echo $bloqueado ? 'Cuenta Bloqueada' : 'Iniciar Sesión'; ?>
                </button>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            </form>

            <div class="security-info">
                <i class="fas fa-info-circle"></i>
                <?php if ($bloqueado): ?>
                    Cuenta bloqueada temporalmente por seguridad
                <?php else: ?>
                    Tu información está protegida con encriptación SSL
                <?php endif; ?>
            </div>

            <div class="auth-footer">
                <p>¿No tienes cuenta? 
                   <a href="<?php echo BASE_URL; ?>registro" class="auth-link">
                        Regístrate aquí
                   </a>
                </p>
            </div>
        </div>

        <div class="auth-background">
            <div class="background-content">
                <h2>UniEmprende</h2>
                <p>La plataforma de compra y venta para la comunidad universitaria</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Conecta con estudiantes de tu universidad</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Compra y vende de forma segura</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-book"></i>
                        <span>Encuentra productos y servicios académicos</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-rocket"></i>
                        <span>Emprende con tu comunidad universitaria</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (input.disabled) return;
            
            const icon = input.nextElementSibling.querySelector('i');
            const type = input.type === 'password' ? 'text' : 'password';
            
            input.type = type;
            icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        }

        // Auto-habilitar cuando el bloqueo expire
        <?php if ($bloqueado): ?>
            setTimeout(function() {
                location.reload();
            }, <?php echo $tiempo_espera * 1000; ?>);
        <?php endif; ?>

        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = 'Iniciando sesión...';

            return true;
        });

        // Auto-focus en el campo de correo
        document.addEventListener('DOMContentLoaded', function() {
            const correoInput = document.getElementById('correo');
            if (correoInput && !correoInput.disabled) {
                setTimeout(() => correoInput.focus(), 100);
            }
        });
    </script>
</body>
</html>