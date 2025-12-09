<?php
    // Iniciar sesión al principio
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si ya está autenticado, redirigir
    if (isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'inicio');
        exit;
    }

    // Esta vista solo debe mostrar, la lógica está en el controlador
    $error = $error ?? '';
    $datos_formulario = $datos_formulario ?? [
        'nombres' => '', 'apellidos' => '', 'dni' => '', 'telefono' => '',
        'correo' => '', 'codigo_univ' => '', 'facultad' => '', 'escuela' => ''
    ];

    // Datos de ejemplo para facultades y escuelas (deberían venir del controlador)
    $facultades = [
        '' => '', // Opción vacía para el label flotante
        'fain' => 'FACULTAD DE INGENIERIA',
        'fcje' => 'FACULTAD DE CIENCIAS JURIDICAS Y EMPRESARIALES',
        'fcag' => 'FACULTAD DE CIENCIAS AGROPECUARIAS',
        'facs' => 'FACULTAD DE CIENCIAS DE LA SALUD',
        'fech' => 'FACULTAD DE EDUCACION, COMUNICACION Y HUMANIDADES',
        'faci' => 'FACULTAD DE CIENCIAS',
        'fiag' => 'FACULTAD DE INGENIERIA CIVIL, ARQUITECTURA Y GEOTECNIA'
    ];

    // CORRECCIÓN: Las claves deben coincidir con las de $facultades
    $escuelasPorFacultad = [
        'fain' => [
            '' => '', // Opción vacía
            'minas' => 'Ingeniería de Minas',
            'informatica_sistemas' => 'Ingeniería en Informática y Sistemas',
            'metalurgica' => 'Ingeniería Metalúrgica',
            'quimica' => 'Ingeniería Química',
            'mecanica' => 'Ingeniería Mecánica'
        ],
        'fcje' => [
            '' => '', // Opción vacía
            'contables_financieras' => 'Ciencias Contables y Financieras',
            'administrativas' => 'Ciencias Administrativas',
            'derecho_politicas' => 'Derecho y Ciencias Políticas',
            'comercial' => 'Ingeniería Comercial'
        ],
        'fcag' => [
            '' => '', // Opción vacía
            'agronomia' => 'Agronomía',
            'economia_agraria' => 'Economía Agraria',
            'veterinaria_zootecnia' => 'Medicina Veterinaria y Zootecnia',
            'pesquera' => 'Ingeniería Pesquera',
            'industrias_alimentarias' => 'Ingeniería en Industrias Alimentarias',
            'ambiental' => 'Ingeniería Ambiental'
        ],
        'facs' => [
            '' => '', // Opción vacía
            'medicina' => 'Medicina Humana',
            'obstetricia' => 'Obstetricia',
            'enfermeria' => 'Enfermería',
            'odontologia' => 'Odontología',
            'farmacia_bioquimica' => 'Farmacia y Bioquímica'
        ],
        'fech' => [
            '' => '', // Opción vacía
            'educacion' => 'Educación',
            'ciencias_comunicacion' => 'Ciencias de la Comunicación',
            'historia' => 'Historia'
        ],
        'faci' => [
            '' => '', // Opción vacía
            'biologia_microbiologia' => 'Biología - Microbiología',
            'fisica_aplicada' => 'Física Aplicada',
            'matematicas' => 'Matemáticas'
        ],
        'fiag' => [
            '' => '', // Opción vacía
            'arquitectura' => 'Arquitectura',
            'civil' => 'Ingeniería Civil',
            'geologica_geotecnia' => 'Ingeniería Geológica - Geotecnia',
            'artes' => 'Artes'
        ]
    ];
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro - UniEmprende</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #910202 0%, #700101 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            
            .auth-container {
                width: 100%;
                max-width: 1000px;
                background: white;
                border-radius: 12px;
                position: relative; /* Para posicionar el botón de cierre */
                overflow: hidden;
                box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            }
            
            .auth-header {
                background: #910202;
                color: white;
                padding: 1.5rem;
                text-align: center;
            }
            
            .auth-header h1 {
                font-size: 1.8rem;
                margin-bottom: 0.5rem;
            }
            
            .auth-body {
                padding: 2rem;
            }
            
            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .input-group {
                position: relative;
                margin-bottom: 1.5rem;
            }
            
            .input-group.full {
                grid-column: 1 / -1;
            }
            
            .input-group input, .input-group select {
                width: 100%;
                padding: 1rem 0.8rem 0.5rem;
                border: 2px solid #e0e0e0;
                border-radius: 6px;
                font-size: 0.9rem;
                transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
                background: #fafafa;
                outline: none;
            }
            
            .input-group input:focus, .input-group select:focus {
                border-color: #910202;
                background: white;
                box-shadow: 0 4px 10px rgba(0,0,0,0.08);
                transform: translateY(-2px);
            }
            
            .input-group label {
                position: absolute;
                left: 0.8rem;
                top: 50%;
                transform: translateY(-50%);
                color: #666;
                font-size: 0.9rem;
                pointer-events: none;
                transition: top 0.3s ease, transform 0.3s ease, font-size 0.3s ease, color 0.3s ease;
                background: transparent;
                padding: 0 0.2rem;
            }
            
            .input-group input:focus + label,
            .input-group input:not(:placeholder-shown) + label,
            .input-group select:valid + label,
            .input-group select:focus + label {
                top: -0.4rem;
                transform: translateY(0);
                font-size: 0.7rem;
                color: #910202;
                font-weight: 600;
                background: white;
            }
            
            .input-group select + label {
                background: #fafafa;
            }
            
            .input-group select:focus + label,
            .input-group select:valid + label {
                background: white;
            }
            
            /* Estilos mejorados para los select */
            .input-group select {
                -webkit-appearance: none;
                appearance: none;
                padding-right: 2.5rem; /* Espacio para la flecha */
            }

            .input-group:has(select)::after {
                content: '\f078'; /* Icono de flecha hacia abajo de Font Awesome */
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #666;
                pointer-events: none; /* Para que no interfiera con el clic en el select */
                transition: color 0.3s ease, transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
            }

            .input-group:has(select:focus)::after {
                color: #910202;
                transform: translateY(-50%) rotate(180deg);
            }

            .required::after {
                content: " *";
                color: #ff0000;
                font-weight: bold;
            }
            
            .btn {
                padding: 0.8rem 1.5rem;
                border: none;
                border-radius: 6px;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.3s;
            }
            
            .btn-primary {
                background: #910202;
                color: white;
                width: 250px;
                margin: 1rem auto;
                display: block;
            }
            
            .btn-primary:hover {
                background: #700101;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(145, 2, 2, 0.3);
            }
            
            .btn-primary:disabled {
                background: #cccccc;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }
            
            .alert {
                padding: 0.8rem;
                border-radius: 6px;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
            }
            
            .alert-error {
                background: #fee;
                color: #c33;
                border: 1px solid #fcc;
            }
            
            .auth-footer {
                text-align: center;
                margin-top: 2rem;
                padding-top: 1.5rem;
                border-top: 1px solid #e1e1e1;
                font-size: 0.9rem;
            }

            .auth-link {
                color: #910202;
                text-decoration: none;
                font-weight: 600;
                transition: color 0.3s;
            }

            .auth-link:hover {
                text-decoration: underline;
            }
            
            .password-requirements {
                font-size: 0.75rem;
                color: #666;
                margin-top: 0.5rem;
                line-height: 1.3;
            }
            
            .requirement {
                display: flex;
                align-items: center;
                margin-bottom: 0.2rem;
            }
            
            .requirement.valid {
                color: #10b981;
            }
            
            .requirement.invalid {
                color: #666;
            }
            
            .requirement i {
                margin-right: 0.3rem;
                font-size: 0.6rem;
            }
            
            .form-section {
                margin-bottom: 2rem;
            }
            
            .section-title {
                color: #910202;
                font-size: 1.1rem;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #910202;
            }
            
            .checkbox-group {
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
                margin: 1.5rem 0;
                padding: 1rem;
                background: #f9f9f9;
                border-radius: 6px;
            }
            
            .checkbox-group input[type="checkbox"] {
                width: auto;
                margin-top: 0.2rem;
            }
            
            .checkbox-group label {
                position: static;
                transform: none;
                font-weight: normal;
                font-size: 0.9rem;
                background: transparent;
                padding: 0;
                line-height: 1.4;
            }
            
            /* Correcciones específicas de posicionamiento */
            .form-grid > .input-group:nth-child(4) {
                grid-column: 1;
            }
            
            .form-grid > .input-group:nth-child(5) {
                grid-column: 2;
            }
            
            .form-grid > .input-group:nth-child(6) {
                grid-column: 3;
            }
            
            /* Asegurar que las contraseñas estén en la posición correcta */
            #contrasenia, #confirmar_contrasenia {
                position: relative;
            }
            
            /* Mejorar la posición de los requisitos de contraseña */
            .password-requirements {
                position: absolute;
                width: 100%;
                z-index: 1;
                background: white;
                padding: 0.5rem;
                border-radius: 0 0 6px 6px;
                border: 1px solid #e1e1e1;
                border-top: none;
                margin-top: 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                opacity: 0;
            }
            
            /* Ajustes para la sección de información universitaria */
            .form-section:last-child .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .form-section:last-child .input-group:nth-child(3),
            .form-section:last-child .input-group:nth-child(4) {
                grid-column: span 1;
            }
            
            @media (max-width: 1024px) {
                .form-grid {
                    grid-template-columns: 1fr 1fr;
                }
                
                .auth-container {
                    max-width: 900px;
                }
                
                .form-section:last-child .form-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            
            @media (max-width: 768px) {
                .form-grid {
                    display:flex;
                    flex-direction: column;
                    grid-template-columns: 1fr;
                    gap: 1rem;
                    margin-bottom: 1rem;
                }
                
                .auth-container {
                    margin: 0.5rem;
                    max-width: 100%;
                }
                
                .auth-body {
                    padding: 1.5rem;
                }
                
                .btn-primary {
                    width: 100%;
                    margin: 1rem 0;
                }
                
                .input-group {
                    margin-bottom: 1.2rem;
                }
                
                .form-section:last-child .form-grid {
                    grid-template-columns: 1fr;
                }
                
                .checkbox-group {
                    margin: 1rem 0;
                    padding: 0.8rem;
                }
            }
            
            @media (max-width: 480px) {
                .auth-body {
                    padding: 1rem;
                }
                
                .auth-header {
                    padding: 1rem;
                }
                
                .auth-header h1 {
                    font-size: 1.5rem;
                }
                
                .form-grid {
                    gap: 0.8rem;
                }
            }

            /* Estilo para el botón de cierre (X) */
            .close-button {
                position: absolute;
                top: 1rem;
                right: 1.5rem;
                font-size: 2.5rem;
                color: #fff;
                opacity: 0.7;
                text-decoration: none;
                line-height: 1;
                transition: opacity 0.3s ease;
                z-index: 10; /* Asegura que esté sobre el fondo rojo */
            }

            .close-button:hover {
                opacity: 1;
            }

            /* --- NUEVO: Estilo para el contenedor de la foto de perfil --- */
            .profile-pic-container {
                grid-column: 1 / -1;
                margin-top: 1rem;
                display: flex;
                justify-content: center;
            }

            /* --- NUEVO: Estilo para el título de la foto de perfil --- */
            .profile-pic-title {
                text-align: center;
                font-weight: 600;
                color: #555;
                margin-bottom: 0.75rem;
                font-size: 1rem;
            }

            .profile-pic-title .optional-text {
                font-weight: 400;
                color: #777;
            }

            /* --- NUEVO: Estilos para el campo de subir foto de perfil --- */
            .file-upload-label {
                display: block;
                border: 3px dashed #e0e0e0;
                border-radius: 50%; /* --- AJUSTE: Hacer el contenedor circular --- */
                padding: 1.5rem;
                text-align: center;
                cursor: pointer;
                transition: border-color 0.3s, background-color 0.3s, transform 0.3s;
                position: relative;
                overflow: hidden;
                background-color: #fdfdfd;
                max-width: 200px; /* --- AJUSTE: Reducir el tamaño máximo --- */
                /* --- CORRECCIÓN: Forzar una proporción cuadrada --- */
                height: auto;
                aspect-ratio: 1 / 1;
                display: flex;
                align-items: center;
                width: 100%;
            }

            .file-upload-label:hover, .file-upload-label.drag-over {
                border-color: #910202;
                transform: scale(1.05);
            }

            .file-upload-content {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: #666;
            }

            .file-upload-content i {
                font-size: 2.5rem;
                color: #910202;
                margin-bottom: 0.75rem;
            }

            .file-upload-content span {
                font-size: 0.9rem;
            }

            .file-upload-content small {
                font-size: 0.8rem;
                color: #888;
                margin-top: 0.25rem;
            }

            #image-preview {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover; /* --- AJUSTE: La imagen cubre el círculo (estándar para perfiles) --- */
                border-radius: 50%; /* Asegura que la imagen también sea circular */
            }

            /* --- NUEVO: Estilo para el botón de eliminar imagen --- */
            .remove-image-btn {
                position: absolute;
                top: 15%; /* --- CORRECCIÓN: Ajustar posición vertical --- */
                right: 15%; /* --- CORRECCIÓN: Ajustar posición horizontal --- */
                background-color: rgba(0, 0, 0, 0.6);
                color: white;
                border: none;
                border-radius: 50%;
                width: 2rem;
                height: 2rem;
                font-size: 1rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s, transform 0.2s;
                z-index: 10;
            }
            .remove-image-btn:hover {
                background-color: #c33; /* Un rojo más visible */
                transform: scale(1.1);
            }

            @media (max-width: 768px) {
                .close-button { top: 0.5rem; right: 1rem; font-size: 2rem; }
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-header">
                <h1>Crear Cuenta</h1>
                <a href="<?php echo BASE_URL; ?>" class="close-button" aria-label="Cerrar y volver al inicio">
                    &times;
                </a>
                <p>Únete a la comunidad universitaria de UniEmprende</p>
            </div>

            <div class="auth-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <?php 
                        if (is_array($error)) {
                            echo '<ul style="margin: 0; padding-left: 1.2rem; font-size: 0.9rem;">';
                            foreach ($error as $err) {
                                echo '<li>' . htmlspecialchars($err) . '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo htmlspecialchars($error);
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form" id="registroForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <!-- Información Personal -->
                    <div class="form-section">
                        <h3 class="section-title">Información Personal</h3>
                        <div class="form-grid">
                            <div class="input-group">
                                <input type="text" id="nombres" name="nombres" 
                                    value="<?php echo htmlspecialchars($datos_formulario['nombres']); ?>" 
                                    placeholder=" "
                                    required>
                                <label for="nombres">Nombres</label>
                            </div>

                            <div class="input-group">
                                <input type="text" id="apellidos" name="apellidos" 
                                    value="<?php echo htmlspecialchars($datos_formulario['apellidos']); ?>" 
                                    placeholder=" "
                                    required>
                                <label for="apellidos">Apellidos</label>
                            </div>

                            <div class="input-group">
                                <input type="tel" id="telefono" name="telefono" 
                                    value="<?php echo htmlspecialchars($datos_formulario['telefono']); ?>" 
                                    placeholder=" ">
                                <label for="telefono">Teléfono</label>
                            </div>

                            <div class="input-group">
                                <input type="text" id="dni" name="dni" 
                                    value="<?php echo htmlspecialchars($datos_formulario['dni']); ?>" 
                                    maxlength="8" pattern="[0-9]{8}" 
                                    placeholder=" "
                                    required>
                                <label for="dni">DNI</label>
                            </div>

                            <div class="input-group">
                                <input type="password" id="contrasenia" name="contrasenia" 
                                    minlength="8" required
                                    placeholder=" ">
                                <label for="contrasenia">Contraseña</label>
                                <div class="password-requirements" id="passwordRequirements">
                                    <div class="requirement invalid" id="reqLength">
                                        <i class="fas fa-circle"></i> Mínimo 8 caracteres
                                    </div>
                                    <div class="requirement invalid" id="reqUppercase">
                                        <i class="fas fa-circle"></i> Una letra mayúscula
                                    </div>
                                    <div class="requirement invalid" id="reqNumber">
                                        <i class="fas fa-circle"></i> Un número
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <input type="password" id="confirmar_contrasenia" name="confirmar_contrasenia" 
                                    minlength="8" required
                                    placeholder=" ">
                                <label for="confirmar_contrasenia">Confirmar Contraseña</label>
                                <div class="password-requirements">
                                    <div class="requirement invalid" id="reqMatch">
                                        <i class="fas fa-circle"></i> Las contraseñas coinciden
                                    </div>
                                </div>
                            </div>

                            <!-- --- NUEVO: Campo de carga de imagen rediseñado y reubicado --- -->
                            <div class="profile-pic-container">
                                <div>
                                    <h4 class="profile-pic-title">Foto de Perfil <span class="optional-text">(Opcional)</span></h4>
                                    <label for="foto_perfil" class="file-upload-label" id="file-upload-area">
                                        <div class="file-upload-content" id="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span id="file-upload-text"><strong>Haz clic para subir</strong> o arrastra y suelta</span>
                                            <small>PNG, JPG o WEBP</small>
                                        </div>
                                        <img id="image-preview" src="#" alt="Vista previa de la imagen" style="display: none;"/>
                                        <button type="button" id="remove-image-btn" class="remove-image-btn" style="display: none;" title="Eliminar imagen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </label>
                                </div>
                                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg, image/webp" style="display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Información Universitaria -->
                    <div class="form-section">
                        <h3 class="section-title">Información Universitaria</h3>
                        <div class="form-grid">
                            <div class="input-group">
                                <input type="email" id="correo" name="correo" 
                                    value="<?php echo htmlspecialchars($datos_formulario['correo']); ?>" 
                                    placeholder=" "
                                    required>
                                <label for="correo">Correo Institucional</label>
                            </div>

                            <div class="input-group">
                                <input type="text" id="codigo_univ" name="codigo_univ" 
                                    value="<?php echo htmlspecialchars($datos_formulario['codigo_univ']); ?>" 
                                    placeholder=" "
                                    required>
                                <label for="codigo_univ">Código Universitario</label>
                            </div>

                            <div class="input-group">
                                <select id="facultad" name="facultad" required>
                                    <?php foreach ($facultades as $valor => $texto): ?>
                                        <option value="<?php echo htmlspecialchars($valor); ?>"
                                            <?php echo ($datos_formulario['facultad'] === $valor) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($texto); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="facultad">Facultad</label>
                            </div>

                            <div class="input-group">
                                <select id="escuela" name="escuela" required disabled>
                                    <option value="" selected></option>
                                </select>
                                <label for="escuela">Escuela Profesional</label>
                            </div>
                        </div>
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="checkbox-group">
                        <input type="checkbox" id="terminos" name="terminos" required>
                        <label for="terminos">
                            Acepto los <a href="<?php echo BASE_URL; ?>terminos" target="_blank" style="color: #910202;">términos y condiciones</a> 
                            y la <a href="<?php echo BASE_URL; ?>privacidad" target="_blank" style="color: #910202;">política de privacidad</a> 
                            <span style="color: #ff0000;">*</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">Crear Cuenta</button>
                </form>

                <div class="auth-footer">
                    <p>¿Ya tienes cuenta? 
                    <a href="<?php echo BASE_URL; ?>login" class="auth-link">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>

        <script>
            // Datos de escuelas por facultad
            const escuelasPorFacultad = <?php echo json_encode($escuelasPorFacultad); ?>;

            // Función para actualizar las escuelas según la facultad seleccionada
            function actualizarEscuelas() {
                const facultadSelect = document.getElementById('facultad');
                const escuelaSelect = document.getElementById('escuela');
                const facultadSeleccionada = facultadSelect.value;
                
                console.log('Facultad seleccionada:', facultadSeleccionada); // Para debug
                console.log('Escuelas disponibles:', escuelasPorFacultad[facultadSeleccionada]); // Para debug
                
                // Limpiar el select de escuelas
                escuelaSelect.innerHTML = '';
                
                if (facultadSeleccionada && escuelasPorFacultad[facultadSeleccionada]) {
                    // Habilitar el select y agregar opciones
                    escuelaSelect.disabled = false;
                    const escuelas = escuelasPorFacultad[facultadSeleccionada];
                    
                    for (const [valor, texto] of Object.entries(escuelas)) {
                        const option = document.createElement('option');
                        option.value = valor;
                        option.textContent = texto;
                        escuelaSelect.appendChild(option);
                    }
                    
                    // Actualizar el label para que se posicione correctamente
                    escuelaSelect.setAttribute('data-filled', 'true');
                } else {
                    // Deshabilitar y mostrar mensaje
                    escuelaSelect.disabled = true;
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = ''; // Vacío para que el label funcione
                    escuelaSelect.appendChild(option);
                    escuelaSelect.removeAttribute('data-filled');
                }
            }

            // Inicializar escuelas al cargar la página
            document.addEventListener('DOMContentLoaded', function() {
                actualizarEscuelas();
                
                // Si hay una facultad seleccionada previamente, cargar sus escuelas
                const facultadSelect = document.getElementById('facultad');
                if (facultadSelect.value) {
                    actualizarEscuelas();
                    // Seleccionar la escuela guardada si existe
                    const escuelaGuardada = '<?php echo $datos_formulario["escuela"]; ?>';
                    if (escuelaGuardada) {
                        setTimeout(() => {
                            const escuelaSelect = document.getElementById('escuela');
                            escuelaSelect.value = escuelaGuardada;
                            // Forzar el evento change para actualizar el label
                            escuelaSelect.dispatchEvent(new Event('change'));
                        }, 100);
                    }
                }

                // Inicializar labels para inputs con valores pre-cargados
                document.querySelectorAll('input, select').forEach(input => {
                    if (input.value) {
                        input.setAttribute('data-filled', 'true');
                    }
                });
            });

            // Escuchar cambios en la facultad
            document.getElementById('facultad').addEventListener('change', actualizarEscuelas);

            // También escuchar cambios en la escuela para actualizar el label
            document.getElementById('escuela').addEventListener('change', function() {
                if (this.value) {
                    this.setAttribute('data-filled', 'true');
                } else {
                    this.removeAttribute('data-filled');
                }
            });

            // Validación básica del formulario
            document.getElementById('dni').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 8) {
                    this.value = this.value.slice(0, 8);
                }
            });

            // Validación de contraseña en tiempo real
            const passwordInput = document.getElementById('contrasenia');
            const confirmPasswordInput = document.getElementById('confirmar_contrasenia');
            
            function validatePassword() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                // Validar longitud
                const reqLength = document.getElementById('reqLength');
                if (password.length >= 8) {
                    reqLength.classList.remove('invalid');
                    reqLength.classList.add('valid');
                    reqLength.innerHTML = '<i class="fas fa-check-circle"></i> Mínimo 8 caracteres';
                } else {
                    reqLength.classList.remove('valid');
                    reqLength.classList.add('invalid');
                    reqLength.innerHTML = '<i class="fas fa-circle"></i> Mínimo 8 caracteres';
                }
                
                // Validar mayúscula
                const reqUppercase = document.getElementById('reqUppercase');
                if (/[A-Z]/.test(password)) {
                    reqUppercase.classList.remove('invalid');
                    reqUppercase.classList.add('valid');
                    reqUppercase.innerHTML = '<i class="fas fa-check-circle"></i> Una letra mayúscula';
                } else {
                    reqUppercase.classList.remove('valid');
                    reqUppercase.classList.add('invalid');
                    reqUppercase.innerHTML = '<i class="fas fa-circle"></i> Una letra mayúscula';
                }
                
                // Validar número
                const reqNumber = document.getElementById('reqNumber');
                if (/[0-9]/.test(password)) {
                    reqNumber.classList.remove('invalid');
                    reqNumber.classList.add('valid');
                    reqNumber.innerHTML = '<i class="fas fa-check-circle"></i> Un número';
                } else {
                    reqNumber.classList.remove('valid');
                    reqNumber.classList.add('invalid');
                    reqNumber.innerHTML = '<i class="fas fa-circle"></i> Un número';
                }
                
                // Validar coincidencia
                const reqMatch = document.getElementById('reqMatch');
                if (password === confirmPassword && password.length > 0) {
                    reqMatch.classList.remove('invalid');
                    reqMatch.classList.add('valid');
                    reqMatch.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
                } else {
                    reqMatch.classList.remove('valid');
                    reqMatch.classList.add('invalid');
                    reqMatch.innerHTML = '<i class="fas fa-circle"></i> Las contraseñas coinciden';
                }
            }

            function checkFormValidity() {
                const submitBtn = document.getElementById('submitBtn');
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const terminos = document.getElementById('terminos').checked;

                const isPasswordValid = password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password);
                const isMatchValid = password === confirmPassword && password.length > 0;
                
                submitBtn.disabled = !(isPasswordValid && isMatchValid && terminos);
            }
            
            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePassword);
            
            // --- NUEVO: Mostrar/ocultar requisitos de contraseña al enfocar/desenfocar ---
            const passwordRequirements = document.getElementById('passwordRequirements');

            // --- CORRECCIÓN: Validar también al cambiar el checkbox de términos ---
            document.getElementById('terminos').addEventListener('change', checkFormValidity);
            document.querySelectorAll('#contrasenia, #confirmar_contrasenia').forEach(input => input.addEventListener('input', checkFormValidity));
            
            passwordInput.addEventListener('focus', () => {
                passwordRequirements.style.display = 'block';
                setTimeout(() => passwordRequirements.style.opacity = '1', 10); // Pequeño retardo para la transición
            });
            
            passwordInput.addEventListener('blur', () => {
                passwordRequirements.style.opacity = '0';
                // Ocultar completamente después de la transición para que no interfiera
                setTimeout(() => passwordRequirements.style.display = 'none', 300); 
            });

            // --- CORRECCIÓN: Función para mostrar errores sin usar alert() ---
            function mostrarError(mensaje) {
                const errorDiv = document.querySelector('.alert.alert-error');
                if (errorDiv) {
                    errorDiv.innerHTML = `<li>${mensaje}</li>`;
                    errorDiv.style.display = 'block';
                    // Hacer scroll hacia el error para que sea visible
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    // Si no existe el div de error, recurrir a alert como fallback
                    alert(mensaje);
                }
            }

            // Validación final del formulario
            document.getElementById('registroForm').addEventListener('submit', function(e) {
                const password = document.getElementById('contrasenia').value;
                const confirmPassword = document.getElementById('confirmar_contrasenia').value;
                const dni = document.getElementById('dni').value;
                const terminos = document.getElementById('terminos').checked;
                const facultad = document.getElementById('facultad').value;
                const escuela = document.getElementById('escuela').value;
                const submitBtn = document.getElementById('submitBtn');

                // Ocultar errores previos
                const errorDiv = document.querySelector('.alert.alert-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
                
                // Validar DNI
                if (dni.length !== 8 || !/^\d+$/.test(dni)) {
                    e.preventDefault();
                    mostrarError('El DNI debe tener exactamente 8 dígitos numéricos.');
                    document.getElementById('dni').focus();
                    return false;
                }
                
                // Validar selects
                if (!facultad) {
                    e.preventDefault();
                    mostrarError('Por favor, selecciona una facultad.');
                    document.getElementById('facultad').focus();
                    return false;
                }
                
                if (!escuela) {
                    e.preventDefault();
                    mostrarError('Por favor, selecciona una escuela profesional.');
                    document.getElementById('escuela').focus();
                    return false;
                }
                
                // Validar contraseñas
                if (password.length < 8) {
                    e.preventDefault();
                    mostrarError('La contraseña debe tener al menos 8 caracteres.');
                    document.getElementById('contrasenia').focus();
                    return false;
                }
                
                if (!/(?=.*[A-Z])/.test(password)) {
                    e.preventDefault();
                    mostrarError('La contraseña debe contener al menos una letra mayúscula.');
                    return false;
                }
                
                if (!/(?=.*[0-9])/.test(password)) {
                    e.preventDefault();
                    mostrarError('La contraseña debe contener al menos un número.');
                    return false;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    mostrarError('Las contraseñas no coinciden.');
                    document.getElementById('confirmar_contrasenia').focus();
                    return false;
                }
                
                if (!terminos) {
                    e.preventDefault();
                    mostrarError('Debes aceptar los términos, condiciones y la política de privacidad.');
                    return false;
                }
                
                // Mostrar loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Creando cuenta...';
                
                return true;
            });

            // --- NUEVO: Placeholder dinámico para el correo institucional ---
            const correoInput = document.getElementById('correo');

            correoInput.addEventListener('focus', function() {
                this.placeholder = 'usuario@unjbg.edu.pe';
            });

            correoInput.addEventListener('blur', function() {
                // Se usa el placeholder de espacio para que el label flotante funcione bien
                this.placeholder = ' ';
            });
            
            // Inicializar validación
            validatePassword(); // Para los indicadores visuales
            checkFormValidity(); // Para el estado inicial del botón
        </script>

        <!-- --- NUEVO: Script para el campo de carga de imagen --- -->
        <script>
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('foto_perfil');
            const fileUploadContent = document.getElementById('file-upload-content');
            const imagePreview = document.getElementById('image-preview');
            const removeImageBtn = document.getElementById('remove-image-btn');

            // Prevenir comportamiento por defecto de arrastrar y soltar
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Resaltar el área al arrastrar un archivo sobre ella
            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, () => {
                    fileUploadArea.classList.add('drag-over');
                }, false);
            });

            // Quitar el resaltado cuando el archivo sale del área
            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, () => {
                    fileUploadArea.classList.remove('drag-over');
                }, false);
            });

            // Manejar el archivo soltado
            fileUploadArea.addEventListener('drop', (e) => {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect();
            }, false);

            // Manejar la selección de archivo (clic o soltar)
            fileInput.addEventListener('change', handleFileSelect);

            function handleFileSelect() {
                const file = fileInput.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        fileUploadContent.style.display = 'none'; // Ocultar el texto
                        removeImageBtn.style.display = 'flex'; // Mostrar el botón de eliminar
                    };
                    reader.readAsDataURL(file);
                }
                 else if (file) {
                    // Si se selecciona un archivo que no es imagen
                    mostrarError('Por favor, selecciona un archivo de imagen válido (PNG, JPG, WEBP).');
                    resetImageUploader();
                }
            }

            // --- NUEVO: Función para resetear el campo de imagen ---
            function resetImageUploader() {
                fileInput.value = ''; // Limpiar el input de archivo
                imagePreview.style.display = 'none'; // Ocultar la vista previa
                imagePreview.src = '#'; // Limpiar la fuente de la imagen
                fileUploadContent.style.display = 'flex'; // Mostrar el contenido original
                removeImageBtn.style.display = 'none'; // Ocultar el botón de eliminar
            }

            // --- NUEVO: Evento para el botón de eliminar ---
            removeImageBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Evitar que el clic active el input de archivo
                resetImageUploader();
            });
        </script>
    </body>
</html>