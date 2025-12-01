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
    $error = $error ?? '';
    $success = $success ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - UniEmprende</title>
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
            max-width: 600px;
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
        
        /* Tarjeta del Formulario */
        .password-card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #910202 0%, #700101 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }
        
        .card-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .card-header p {
            color: #666;
            line-height: 1.5;
        }
        
        /* Formulario */
        .password-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 0.75rem 3rem 0.75rem 1rem;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #910202;
            box-shadow: 0 0 0 3px rgba(145, 2, 2, 0.1);
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .input-icon:hover {
            color: #910202;
        }
        
        .password-strength {
            margin-top: 0.5rem;
        }
        
        .strength-bar {
            height: 4px;
            background: #e1e1e1;
            border-radius: 2px;
            margin-bottom: 0.25rem;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: all 0.3s;
        }
        
        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #28a745; width: 100%; }
        
        .strength-text {
            font-size: 0.8rem;
            color: #666;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }
        
        .requirements-title {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .requirements-list {
            list-style: none;
            font-size: 0.9rem;
            color: #666;
        }
        
        .requirements-list li {
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .requirement-met {
            color: #28a745;
        }
        
        .requirement-unmet {
            color: #666;
        }
        
        /* Botones */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
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
            flex: 1;
            justify-content: center;
        }
        
        .btn-primary {
            background: #910202;
            color: white;
        }
        
        .btn-primary:hover {
            background: #700101;
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
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
        
        /* Alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
            
            .password-card {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
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
                <h1>Cambiar Contraseña</h1>
                <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline">
                    ← Volver al Perfil
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Tarjeta del Formulario -->
            <div class="password-card">
                <div class="card-header">
                    <div class="card-icon">
                        🔒
                    </div>
                    <h2>Seguridad de la Cuenta</h2>
                    <p>Protege tu cuenta con una contraseña segura. Te recomendamos usar una combinación de letras, números y símbolos.</p>
                </div>

                <form method="POST" class="password-form" id="password-form">
                    <!-- Contraseña Actual -->
                    <div class="form-group">
                        <label for="password_actual">Contraseña Actual *</label>
                        <div class="input-with-icon">
                            <input type="password" 
                                   id="password_actual" 
                                   name="password_actual" 
                                   required
                                   placeholder="Ingresa tu contraseña actual">
                            <span class="input-icon toggle-password" data-target="password_actual">
                                👁️
                            </span>
                        </div>
                    </div>

                    <!-- Nueva Contraseña -->
                    <div class="form-group">
                        <label for="nuevo_password">Nueva Contraseña *</label>
                        <div class="input-with-icon">
                            <input type="password" 
                                   id="nuevo_password" 
                                   name="nuevo_password" 
                                   required
                                   minlength="6"
                                   placeholder="Crea una nueva contraseña">
                            <span class="input-icon toggle-password" data-target="nuevo_password">
                                👁️
                            </span>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                            <div class="strength-text" id="strength-text">Seguridad de la contraseña</div>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="form-group">
                        <label for="confirmar_password">Confirmar Nueva Contraseña *</label>
                        <div class="input-with-icon">
                            <input type="password" 
                                   id="confirmar_password" 
                                   name="confirmar_password" 
                                   required
                                   minlength="6"
                                   placeholder="Repite la nueva contraseña">
                            <span class="input-icon toggle-password" data-target="confirmar_password">
                                👁️
                            </span>
                        </div>
                        <div id="password-match" style="font-size: 0.8rem; margin-top: 0.25rem;"></div>
                    </div>

                    <!-- Requisitos de Contraseña -->
                    <div class="password-requirements">
                        <div class="requirements-title">La contraseña debe cumplir con:</div>
                        <ul class="requirements-list">
                            <li id="req-length" class="requirement-unmet">✅ Mínimo 6 caracteres</li>
                            <li id="req-uppercase" class="requirement-unmet">✅ Al menos una letra mayúscula</li>
                            <li id="req-lowercase" class="requirement-unmet">✅ Al menos una letra minúscula</li>
                            <li id="req-number" class="requirement-unmet">✅ Al menos un número</li>
                        </ul>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            🔐 Cambiar Contraseña
                        </button>
                        <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer Simple -->
    <footer style="background: #333; color: white; padding: 2rem 0; text-align: center; margin-top: 4rem;">
        <div class="container">
            <p>&copy; 2025 UniEmprende. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('password-form');
            const nuevoPassword = document.getElementById('nuevo_password');
            const confirmarPassword = document.getElementById('confirmar_password');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            const submitBtn = document.getElementById('submit-btn');
            const passwordMatch = document.getElementById('password-match');
            
            // Elementos de requisitos
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            
            // Toggle visibilidad de contraseña
            document.querySelectorAll('.toggle-password').forEach(icon => {
                icon.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const isPassword = input.type === 'password';
                    
                    input.type = isPassword ? 'text' : 'password';
                    this.textContent = isPassword ? '🔒' : '👁️';
                });
            });
            
            // Validar fortaleza de contraseña
            function validarFortaleza(password) {
                let score = 0;
                let requirements = {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false
                };
                
                // Longitud
                if (password.length >= 6) {
                    score++;
                    requirements.length = true;
                }
                
                // Mayúsculas
                if (/[A-Z]/.test(password)) {
                    score++;
                    requirements.uppercase = true;
                }
                
                // Minúsculas
                if (/[a-z]/.test(password)) {
                    score++;
                    requirements.lowercase = true;
                }
                
                // Números
                if (/[0-9]/.test(password)) {
                    score++;
                    requirements.number = true;
                }
                
                return { score, requirements };
            }
            
            // Actualizar indicador de fortaleza
            function actualizarFortaleza() {
                const password = nuevoPassword.value;
                const { score, requirements } = validarFortaleza(password);
                
                // Actualizar barra de fortaleza
                strengthFill.className = 'strength-fill';
                if (password.length === 0) {
                    strengthFill.style.width = '0%';
                    strengthText.textContent = 'Seguridad de la contraseña';
                } else if (score <= 1) {
                    strengthFill.classList.add('strength-weak');
                    strengthText.textContent = 'Débil';
                    strengthText.style.color = '#dc3545';
                } else if (score <= 2) {
                    strengthFill.classList.add('strength-medium');
                    strengthText.textContent = 'Media';
                    strengthText.style.color = '#ffc107';
                } else {
                    strengthFill.classList.add('strength-strong');
                    strengthText.textContent = 'Fuerte';
                    strengthText.style.color = '#28a745';
                }
                
                // Actualizar requisitos
                reqLength.className = requirements.length ? 'requirement-met' : 'requirement-unmet';
                reqUppercase.className = requirements.uppercase ? 'requirement-met' : 'requirement-unmet';
                reqLowercase.className = requirements.lowercase ? 'requirement-met' : 'requirement-unmet';
                reqNumber.className = requirements.number ? 'requirement-met' : 'requirement-unmet';
                
                validarFormulario();
            }
            
            // Validar coincidencia de contraseñas
            function validarCoincidencia() {
                const password = nuevoPassword.value;
                const confirm = confirmarPassword.value;
                
                if (confirm.length === 0) {
                    passwordMatch.textContent = '';
                    passwordMatch.style.color = '';
                } else if (password === confirm) {
                    passwordMatch.textContent = '✅ Las contraseñas coinciden';
                    passwordMatch.style.color = '#28a745';
                } else {
                    passwordMatch.textContent = '❌ Las contraseñas no coinciden';
                    passwordMatch.style.color = '#dc3545';
                }
                
                validarFormulario();
            }
            
            // Validar formulario completo
            function validarFormulario() {
                const passwordActual = document.getElementById('password_actual').value;
                const nueva = nuevoPassword.value;
                const confirmar = confirmarPassword.value;
                const { score } = validarFortaleza(nueva);  
                
                const formularioValido = passwordActual.length > 0 &&
                    nueva.length >= 6 &&
                    nueva === confirmar &&
                    score >= 2; // Al menos fortaleza media
                
                submitBtn.disabled = !formularioValido;
            }
            
            // Event listeners
            nuevoPassword.addEventListener('input', actualizarFortaleza);
            confirmarPassword.addEventListener('input', validarCoincidencia);
            
            document.getElementById('password_actual').addEventListener('input', validarFormulario);
            nuevoPassword.addEventListener('input', validarFormulario);
            confirmarPassword.addEventListener('input', validarFormulario);
            
            // Validación inicial
            validarFormulario();
            
            // Confirmación antes de enviar
            form.addEventListener('submit', function(e) {
                if (!submitBtn.disabled) {
                    if (!confirm('¿Estás seguro de que deseas cambiar tu contraseña?')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>