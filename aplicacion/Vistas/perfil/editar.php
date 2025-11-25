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
    $usuario = $usuario ?? [];
    $datos_formulario = $datos_formulario ?? [
        'nombres' => '', 'apellidos' => '', 'telefono' => '', 'facultad' => '', 'escuela' => ''
    ];
    $error = $error ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - UniEmprende</title>
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
            max-width: 1000px;
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
        
        /* Formulario */
        .edit-profile-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #910202;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
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
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #910202;
        }
        
        .form-group input:disabled {
            background: #f8f9fa;
            color: #666;
            cursor: not-allowed;
        }
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }
        
        .form-help {
            color: #666;
            margin-bottom: 1rem;
            font-style: italic;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f8f9fa;
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

        /* Estilos para la foto de perfil */
        .profile-pic-container {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .pic-preview img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e1e1e1;
        }

        .pic-upload label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .pic-upload input[type="file"] {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
        }

        .pic-upload small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
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
                <h1>Editar Perfil</h1>
                <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline">
                    ← Volver al Perfil
                </a>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="edit-profile-form">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Foto de Perfil -->
                    <div class="form-section">
                        <h3>Foto de Perfil</h3>
                        <div class="profile-pic-container">
                            <div class="pic-preview">
                                <img id="profile-pic-preview" src="<?php echo !empty($usuario['foto_perfil']) ? obtenerImagenFinal($usuario['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Foto de perfil">
                            </div>
                            <div class="pic-upload">
                                <label for="foto_perfil">Cambiar foto de perfil</label>
                                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg, image/webp">
                                <small>Sube una imagen cuadrada. Formatos permitidos: JPG, PNG, WebP. Máximo 2MB.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Información Personal -->
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombres">Nombres *</label>
                                <input type="text" id="nombres" name="nombres" 
                                       value="<?php echo htmlspecialchars($datos_formulario['nombres'] ?: $usuario['nombres']); ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="apellidos">Apellidos *</label>
                                <input type="text" id="apellidos" name="apellidos" 
                                       value="<?php echo htmlspecialchars($datos_formulario['apellidos'] ?: $usuario['apellidos']); ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" id="dni" 
                                       value="<?php echo htmlspecialchars($usuario['dni']); ?>" 
                                       disabled>
                                <small>El DNI no se puede modificar</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" 
                                       value="<?php echo htmlspecialchars($datos_formulario['telefono'] ?: $usuario['telefono']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Información Universitaria -->
                    <div class="form-section">
                        <h3>Información Universitaria</h3>
                        
                        <div class="form-group">
                            <label for="correo_institucional">Correo Institucional</label>
                            <input type="email" id="correo_institucional" 
                                   value="<?php echo htmlspecialchars($usuario['correo_institucional']); ?>" 
                                   disabled>
                            <small>El correo institucional no se puede modificar</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="codigo_univ">Código Universitario</label>
                            <input type="text" id="codigo_univ" 
                                   value="<?php echo htmlspecialchars($usuario['codigo_univ']); ?>" 
                                   disabled>
                            <small>El código universitario no se puede modificar</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="facultad">Facultad</label>
                                <input type="text" id="facultad" name="facultad" 
                                       value="<?php echo htmlspecialchars($datos_formulario['facultad'] ?: $usuario['facultad']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="escuela">Escuela</label>
                                <input type="text" id="escuela" name="escuela" 
                                       value="<?php echo htmlspecialchars($datos_formulario['escuela'] ?: $usuario['escuela']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div class="form-section">
                        <h3>Cambiar Contraseña</h3>
                        <p class="form-help">Deja estos campos en blanco si no deseas cambiar la contraseña</p>
                        
                        <div class="form-group">
                            <label for="password_actual">Contraseña Actual</label>
                            <input type="password" id="password_actual" name="password_actual">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nuevo_password">Nueva Contraseña</label>
                                <input type="password" id="nuevo_password" name="nuevo_password">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                                <input type="password" id="confirmar_password" name="confirmar_password">
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline">Cancelar</a>
                    </div>
                </form>
            </div>
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
            // Preview de imagen de perfil
            const inputFoto = document.getElementById('foto_perfil');
            const previewImg = document.getElementById('profile-pic-preview');
            
            inputFoto.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Validación de contraseñas en tiempo real
            const passwordNueva = document.getElementById('nuevo_password');
            const passwordConfirm = document.getElementById('confirmar_password');
            
            function validarPasswords() {
                if (passwordNueva.value && passwordConfirm.value) {
                    if (passwordNueva.value !== passwordConfirm.value) {
                        passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
                    } else {
                        passwordConfirm.setCustomValidity('');
                    }
                } else {
                    passwordConfirm.setCustomValidity('');
                }
            }
            
            passwordNueva.addEventListener('input', validarPasswords);
            passwordConfirm.addEventListener('input', validarPasswords);
            
            // Validación de longitud de contraseña
            passwordNueva.addEventListener('input', function() {
                if (this.value && this.value.length < 8) {
                    this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Validación del formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const passwordActual = document.getElementById('password_actual').value;
                const nuevaPassword = document.getElementById('nuevo_password').value;
                const confirmarPassword = document.getElementById('confirmar_password').value;
                
                // Si se intenta cambiar la contraseña, validar que todos los campos estén completos
                if (passwordActual || nuevaPassword || confirmarPassword) {
                    if (!passwordActual || !nuevaPassword || !confirmarPassword) {
                        e.preventDefault();
                        alert('Para cambiar la contraseña, debes completar todos los campos de contraseña');
                        return false;
                    }
                    
                    if (nuevaPassword.length < 8) {
                        e.preventDefault();
                        alert('La nueva contraseña debe tener al menos 8 caracteres');
                        return false;
                    }
                    
                    if (nuevaPassword !== confirmarPassword) {
                        e.preventDefault();
                        alert('Las nuevas contraseñas no coinciden');
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>