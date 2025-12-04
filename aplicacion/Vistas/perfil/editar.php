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

    $facultades = [
        'FAIN' => 'FACULTAD DE INGENIERIA',
        'FCJE' => 'FACULTAD DE CIENCIAS JURIDICAS Y EMPRESARIALES',
        'FCAG' => 'FACULTAD DE CIENCIAS AGROPECUARIAS',
        'FACS' => 'FACULTAD DE CIENCIAS DE LA SALUD',
        'FECH' => 'FACULTAD DE EDUCACION, COMUNICACION Y HUMANIDADES',
        'FACI' => 'FACULTAD DE CIENCIAS',
        'FIAG' => 'FACULTAD DE INGENIERIA CIVIL, ARQUITECTURA Y GEOTECNIA'
    ];

    $escuelasPorFacultad = [
        'FAIN' => [
            'ESMI' => 'Ingeniería de Minas',
            'ESIS' => 'Ingeniería en Informática y Sistemas',
            'ESME' => 'Ingeniería Metalúrgica',
            'ESIQ' => 'Ingeniería Química',
            'ESMC' => 'Ingeniería Mecánica'
        ],
        'FCJE' => [
            'ESCF' => 'Ciencias Contables y Financieras',
            'ESAD' => 'Ciencias Administrativas',
            'ESDE' => 'Derecho y Ciencias Políticas',
            'ESCO' => 'Ingeniería Comercial'
        ],
        'FCAG' => [
            'ESAG' => 'Agronomía',
            'ESEA' => 'Economía Agraria',
            'EMVZ' => 'Medicina Veterinaria y Zootecnia',
            'ESIP' => 'Ingeniería Pesquera',
            'ESIA' => 'Ingeniería en Industrias Alimentarias',
            'ESAM' => 'Ingeniería Ambiental'
        ],
        'FACS' => [
            'ESMH' => 'Medicina Humana',
            'ESOB' => 'Obstetricia',
            'ESEN' => 'Enfermería',
            'ESOD' => 'Odontología',
            'ESFB' => 'Farmacia y Bioquímica'
        ],
        'FECH' => [
            'ESCC' => 'Ciencias de la Comunicación',
            'ESHI' => 'Historia',
            'IETI' => 'Educación: Idioma Extranjero',
            'LEGE' => 'Educación: Lengua y Literatura',
            'MACI' => 'Educación: Matemática, Computación e Informática',
            'NATA' => 'Educación: Ciencias de la Naturaleza y Promoción Educativa Ambiental',
            'SPRO' => 'Educación: Ciencias Sociales y Promoción Socio Cultural',
            'ESEI' => 'Educación: Educación Inicial',
            'ESEP' => 'Educación: Educación Primaria',
            'ESPS' => 'Psicología'
        ],
        'FACI' => [
            'ESBM' => 'Biología - Microbiología',
            'ESFI' => 'Física Aplicada',
            'ESMA' => 'Matemáticas'
        ],
        'FIAG' => [
            'ESAQ' => 'Arquitectura',
            'ESIC' => 'Ingeniería Civil',
            'ESGE' => 'Ingeniería Geológica - Geotecnia',
            'ESAR' => 'Artes'
        ]
    ];

    // Convertir a JSON para usarlo en JavaScript
    $escuelas_json = json_encode($escuelasPorFacultad);

    // Obtener la facultad y escuela actual del usuario
    $facultad_actual = $datos_formulario['facultad'] ?: ($usuario['facultad'] ?? '');
    $escuela_actual = $datos_formulario['escuela'] ?: ($usuario['escuela'] ?? '');

    // Obtener las escuelas correspondientes a la facultad actual
    $escuelas_disponibles = isset($escuelasPorFacultad[$facultad_actual]) ? $escuelasPorFacultad[$facultad_actual] : [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - UniEmprende</title>
    <style>
        /* --- NUEVO: Cargar Font Awesome para los íconos --- */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

        /* --- NUEVO: Estilos de input mejorados (como en registro.php) --- */
        .form-group {
            position: relative;
        }

        .form-group .form-control {
            width: 100%;
            padding: 0.85rem 1rem; /* --- CORRECCIÓN: Aumentar padding --- */
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem; /* --- CORRECCIÓN: Aumentar tamaño de fuente --- */
            transition: border-color 0.3s ease, background-color 0.3s ease;
            background: #fafafa;
            outline: none; /* Se mantiene para quitar el borde azul por defecto */
            -webkit-appearance: none;
            appearance: none;
        }

        .form-group .form-control:focus {
            border-color: #910202;
            background: white;
        }

        /* --- NUEVO: Estilo para el ícono de flecha del select --- */
        .form-group.has-select::after {
            content: '\f078'; /* Icono de flecha de Font Awesome */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 1rem;
            top: calc(50% + 1rem); /* --- CORRECCIÓN: Ajustar posición vertical --- */
            transform: translateY(-50%); /* Centrar la flecha en esa nueva posición */
            color: #666;
            pointer-events: none;
            transition: transform 0.3s ease;
        }

        /* --- CORRECCIÓN: Estilo de etiqueta clásica (arriba del campo) --- */
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
            font-size: 1rem; /* --- CORRECCIÓN: Aumentar tamaño de fuente --- */
        }


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
        
        /* Header Simple */
        .simple-header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #910202;
            text-decoration: none;
        }
        
        .header-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
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
        
        /* --- CORRECCIÓN: Aplicar estilo .form-control a todos los inputs y selects --- */
        .form-group input, .form-group select {
            /* Los estilos ya están en .form-control, no es necesario repetir */
            /* Solo se asegura que los selects también usen la clase */
            /* width, padding, etc. son heredados de .form-control */
            border: 2px solid #e1e1e1;
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

        /* --- NUEVO: Estilos modernos para la carga de foto de perfil --- */
        .profile-pic-upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .pic-uploader {
            position: relative;
            display: block;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 4px solid white;
            transition: all 0.3s ease;
        }

        .pic-uploader:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        #profile-pic-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pic-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            text-align: center;
        }

        .pic-uploader:hover .pic-overlay {
            opacity: 1;
        }

        .pic-overlay i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .pic-upload-info {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        /* Ocultar el input de archivo por defecto */
        #foto_perfil {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem 0;
        }
        
        /* Footer Simple */
        .simple-footer {
            background: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 4rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .simple-header{
                position:unset;
            }
            .page-header {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                text-align: center;
                padding: unset;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: unset;
                margin-bottom: unset;
            }
            
            .form-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                margin-top: unset;
                padding-top: unset;
            }
            .form-section {
                margin-bottom: 1rem;
                padding-bottom: unset;
                border-bottom: 2px solid #f8f9fa;
            }

            .profile-pic-container {
                flex-direction: column;
                text-align: center;
            }
            
            .header-inner {
                flex-direction: column;
                gap: 1rem;
            }
            
            .header-nav {
                justify-content: center;
            }
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .edit-profile-form {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Simple -->
    <header class="simple-header">
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    UniEmprende
                </a>
                <nav class="header-nav">
                    <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline">Mi Perfil</a>
                    <a href="<?php echo BASE_URL; ?>logout" class="btn btn-secondary">Cerrar Sesión</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
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
                        <div class="profile-pic-upload-area">
                            <label for="foto_perfil" class="pic-uploader">
                                <img id="profile-pic-preview" src="<?php echo !empty($usuario['foto_perfil']) ? obtenerImagenFinal($usuario['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Foto de perfil">
                                <div class="pic-overlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Cambiar foto</span>
                                </div>
                            </label>
                            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg, image/webp">
                            <div class="pic-upload-info">
                                Sube una imagen cuadrada (JPG, PNG, WebP). Máx 2MB.
                            </div>
                        </div>
                    </div>

                    <!-- Información Personal -->
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombres">Nombres *</label>
                                <input type="text" id="nombres" name="nombres" class="form-control"
                                       value="<?php echo htmlspecialchars($datos_formulario['nombres'] ?: $usuario['nombres']); ?>"
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="apellidos">Apellidos *</label>
                                <input type="text" id="apellidos" name="apellidos" class="form-control"
                                       value="<?php echo htmlspecialchars($datos_formulario['apellidos'] ?: $usuario['apellidos']); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" id="dni" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['dni']); ?>"
                                       disabled>
                                <small>El DNI no se puede modificar</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" class="form-control" 
                                       value="<?php echo htmlspecialchars($datos_formulario['telefono'] ?: $usuario['telefono']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Información Universitaria -->
                    <div class="form-section">
                        <h3>Información Universitaria</h3>
                        <div class="form-group">
                            <label for="correo_institucional">Correo Institucional</label>
                            <input type="email" id="correo_institucional" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['correo_institucional']); ?>"
                                   disabled>
                            <small>El correo institucional no se puede modificar</small>
                        </div>
                        <div class="form-group">
                            <label for="codigo_univ">Código Universitario</label>
                            <input type="text" id="codigo_univ" class="form-control"
                                   value="<?php echo htmlspecialchars($usuario['codigo_univ']); ?>" 
                                   disabled>
                            <small>El código universitario no se puede modificar</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group has-select">
                                <label for="facultad">Facultad</label>
                                <select id="facultad" name="facultad" class="form-control" required>
                                    <option value="">Selecciona una facultad</option>
                                    <?php foreach ($facultades as $valor => $texto): ?>
                                        <option value="<?php echo htmlspecialchars($valor); ?>" <?php echo ($facultad_actual == $valor) ? 'selected' : ''; ?> title="<?php echo htmlspecialchars($texto); ?>">
                                            <?php echo htmlspecialchars($valor); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group has-select">
                                <label for="escuela">Escuela Profesional</label>
                                <select id="escuela" name="escuela" class="form-control" required>
                                    <option value="">Selecciona una escuela</option>
                                    <?php foreach ($escuelas_disponibles as $valor => $texto): ?>
                                        <option value="<?php echo htmlspecialchars($valor); ?>" <?php echo ($escuela_actual == $valor) ? 'selected' : ''; ?> title="<?php echo htmlspecialchars($texto); ?>">
                                            <?php echo htmlspecialchars($valor); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div class="form-section">
                        <h3>Cambiar Contraseña</h3>
                        <p class="form-help">Deja estos campos en blanco si no deseas cambiar la contraseña</p>
                        
                        <div class="form-group">
                            <label for="password_actual">Contraseña Actual</label>
                            <input type="password" id="password_actual" name="password_actual" class="form-control" value="">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nuevo_password">Nueva Contraseña</label>
                                <input type="password" id="nuevo_password" name="nuevo_password" class="form-control" value="">
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                                <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" value="">
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
    <footer class="simple-footer">
        <div class="container">
            <p>&copy; 2025 UniEmprende. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Script para actualizar las escuelas según la facultad seleccionada
        const escuelasData = <?php echo $escuelas_json; ?>;
        const facultadSelect = document.getElementById('facultad');
        const escuelaSelect = document.getElementById('escuela');

        facultadSelect.addEventListener('change', function() {
            const facultadSeleccionada = this.value;
            const escuelas = escuelasData[facultadSeleccionada] || {};

            // Limpiar opciones actuales de escuela
            escuelaSelect.innerHTML = '<option value="">Selecciona una escuela</option>';

            // Añadir nuevas opciones
            for (const [valor, texto] of Object.entries(escuelas)) {
                const option = document.createElement('option');
                option.value = valor;
                option.textContent = valor; // Mostrar abreviatura
                option.title = texto;       // Mostrar nombre completo en hover
                escuelaSelect.appendChild(option);
            }
        });
    </script>

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
</body>
</html>