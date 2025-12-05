<?php
    // Iniciar sesión para poder ver el header con los datos del usuario logueado
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si hay un error o el usuario del perfil no existe, mostrar mensaje y salir.
    // Este bloque se activa si el controlador pasa $error o no encuentra $usuario.
    if (!empty($error) || empty($usuario)) {
        // Para la página de error, usamos un header y footer genéricos para no romper la página.
        require_once 'aplicacion/Vistas/plantillas/header.php'; 
        
        // Estilos en línea para el mensaje de error para que sea autocontenido.
        echo "
        <style>
            .btn {
                padding: 0.75rem 1.5rem; border: none; border-radius: 8px;
                text-decoration: none; font-weight: 600;
                display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;
            }
            .btn-primary { background: #910202; color: white; }
            .btn-primary:hover { background: #700101; }
        </style>
        <div class='container' style='padding: 4rem 1rem;'>
            <div style='text-align: center; background: white; padding: 3rem; border-radius: 12px; border: 1px solid #e1e5e9;'>
                <i class='fas fa-exclamation-triangle fa-4x' style='color: #f39c12;'></i>
                <h3 style='margin-top: 1.5rem; font-size: 1.8rem; color: #2c3e50;'>" . ($error ?? 'Perfil no encontrado') . "</h3>
                <p style='color: #6c757d; max-width: 450px; margin: 1rem auto;'>No pudimos encontrar al usuario. Es posible que el enlace sea incorrecto o que el usuario ya no exista.</p>
                <a href='" . BASE_URL . "' class='btn btn-primary' style='margin-top: 1.5rem;'>Volver al inicio</a>
            </div>
        </div>";
        
        require_once 'aplicacion/Vistas/plantillas/footer.php';
        return; // Termina la ejecución del script aquí
    }

    // Datos que vienen del controlador (con valores por defecto para evitar errores)
    $publicaciones = $publicaciones ?? [];
    $estadisticas = array_merge([
        'total_vistas' => 0,
        'total_favoritos' => 0,
        'total_contactos' => 0,
        'total_productos' => 0,
    ], $estadisticas ?? []);

    // Mapeo de abreviaturas a nombres completos para facultades y escuelas (copiado de index.php)
    $facultades_map = [
        'FAIN' => 'FACULTAD DE INGENIERIA',
        'FCJE' => 'FACULTAD DE CIENCIAS JURIDICAS Y EMPRESARIALES',
        'FCAG' => 'FACULTAD DE CIENCIAS AGROPECUARIAS',
        'FACS' => 'FACULTAD DE CIENCIAS DE LA SALUD',
        'FECH' => 'FACULTAD DE EDUCACION, COMUNICACION Y HUMANIDADES',
        'FACI' => 'FACULTAD DE CIENCIAS',
        'FIAG' => 'FACULTAD DE INGENIERIA CIVIL, ARQUITECTURA Y GEOTECNIA'
    ];

    $escuelas_map = [
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
        'FACI' => ['ESBM' => 'Biología - Microbiología', 'ESFI' => 'Física Aplicada', 'ESMA' => 'Matemáticas'],
        'FIAG' => ['ESAQ' => 'Arquitectura', 'ESIC' => 'Ingeniería Civil', 'ESGE' => 'Ingeniería Geológica - Geotecnia', 'ESAR' => 'Artes']
    ];

    $nombre_completo_facultad = $facultades_map[$usuario['facultad'] ?? ''] ?? 'Sin facultad';
    $nombre_completo_escuela = $escuelas_map[$usuario['facultad'] ?? ''][$usuario['escuela'] ?? ''] ?? 'Sin escuela';

    $page_title = 'Perfil de ' . htmlspecialchars($usuario['nombres']) . ' - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php';
?>
    <!-- Botón para volver atrás -->
    <a href="javascript:history.back()" class="back-link" title="Volver atrás">
        <i class="fas fa-arrow-left"></i>
    </a>
    <style>
        /* Estilos para el botón de volver */
        .back-link {
            position: fixed;
            top: 9rem;
            left: calc(50% - 700px - 5rem); /* Posiciona el botón a la izquierda del contenido */
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background-color: #f0f2f5;
            border-radius: 50%;
            color: var(--primary-color, #910202);
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .back-link:hover {
            background-color: #e4e6e9;
            transform: scale(1.05);
        }
        @media (max-width: 1600px) {
            .back-link {
                left: 2rem; /* Fallback para pantallas más pequeñas */
            }
        }
        @media (max-width: 768px) {
            .back-link {
                display: none; /* Ocultamos en móvil para no estorbar */
            }
        }
        :root {
            --primary-color: #910202;
            --primary-dark: #700101;
            --primary-light: rgba(145, 2, 2, 0.08);
            --secondary-color: #2c3e50;
            --accent-color: #ffc107;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --text-color: #2c3e50;
            --text-light: #6c757d;
            --border-color: #e1e5e9;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            --box-shadow-hover: 0 8px 30px rgba(0,0,0,0.12);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        /* Corrección para eliminar fondo transparente del header */
        body::before {
            display: none;
        }
        body { font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif; background: #ffffff; color: var(--text-color); line-height: 1.6; font-weight: 400; min-height: 100vh; }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }
        .profile-header { background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 3rem 0; border-bottom: 1px solid var(--border-color); margin-bottom: 3rem; }
        .profile-content-header { display: grid; grid-template-columns: auto 1fr auto; gap: 2.5rem; align-items: start; }
        .profile-avatar { position: relative; }
        .avatar-container { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; border: 4px solid white; box-shadow: var(--box-shadow); position: relative; overflow: hidden; }
        .profile-info-main { padding-top: 0.5rem; }
        .profile-name { font-size: 2.2rem; font-weight: 700; color: var(--text-color); margin-bottom: 0.5rem; line-height: 1.2; }
        .profile-meta { display: flex; gap: 2rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .meta-item { display: flex; align-items: center; gap: 0.5rem; color: var(--text-light); font-size: 0.95rem; }
        .profile-bio { color: var(--text-light); line-height: 1.6; max-width: 500px; margin-bottom: 1.5rem; }
        .profile-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-top: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: var(--border-radius); border: 1px solid var(--border-color); text-align: center; transition: var(--transition); }
        .stat-card:hover { box-shadow: var(--box-shadow-hover); border-color: var(--primary-color); }
        .stat-value { display: block; font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem; }
        .stat-label { font-size: 0.85rem; color: var(--text-light); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .profile-actions { display: flex; flex-direction: column; gap: 1rem; min-width: 200px; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: var(--border-radius-sm); text-decoration: none; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-family: inherit; font-size: 0.9rem; justify-content: center; }
        .btn-primary { background: var(--primary-color); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(145, 2, 2, 0.3); }
        .btn-outline { background: transparent; border: 2px solid var(--border-color); color: var(--text-color); }
        .btn-outline:hover { border-color: var(--primary-color); color: var(--primary-color); background: var(--primary-light); }
        .btn-sm { padding: 0.6rem 1.2rem; font-size: 0.85rem; }
        .main-content { display: grid; grid-template-columns: 280px 1fr; gap: 2.5rem; margin-bottom: 4rem; }
        .profile-sidebar { display: flex; flex-direction: column; gap: 1.5rem; }
        .sidebar-card { background: white; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1.5rem; }
        .sidebar-card h3 { font-size: 1rem; font-weight: 600; color: var(--text-color); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .info-list { display: flex; flex-direction: column; gap: 1rem; }
        .info-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; }
        .info-item:not(:last-child) { border-bottom: 1px solid var(--border-color); }
        .info-label { display: flex; align-items: center; gap: 0.5rem; color: var(--text-light); font-size: 0.9rem; }
        .info-value { color: var(--text-color); font-weight: 500; font-size: 0.9rem; text-align: right; }
        .profile-main { display: flex; flex-direction: column; gap: 2rem; }
        .tabs-container { background: white; border: 1px solid var(--border-color); border-radius: var(--border-radius); overflow: hidden; }
        .tab-content { padding: 2rem; }
        .tab-pane { display: none; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .tab-pane.active { display: block; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: var(--text-color); display: flex; align-items: center; gap: 0.75rem; }
        .publicaciones-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .publicacion-card { background: white; border: 1px solid var(--border-color); border-radius: var(--border-radius); overflow: hidden; transition: var(--transition); display: flex; flex-direction: column; height: 100%; }
        .publicacion-card:hover { box-shadow: var(--box-shadow-hover); border-color: var(--primary-color); }
        .publicacion-image { width: 100%; height: 200px; background: var(--light-gray); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative; }
        .publicacion-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .publicacion-card:hover .publicacion-image img { transform: scale(1.05); }
        .no-image { color: var(--text-light); text-align: center; padding: 1rem; }
        .publicacion-content { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
        .publicacion-header { margin-bottom: 1rem; }
        .publicacion-title { font-size: 1.2rem; font-weight: 600; color: var(--text-color); margin-bottom: 0.5rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .publicacion-precio { font-size: 1.3rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.5rem; }
        .publicacion-desc { color: var(--text-light); line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; /* Se quita flex: 1 para que el footer se alinee correctamente */ }
        .publicacion-meta { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; align-items: center; }
        .meta-tag { padding: 0.4rem 0.8rem; background: var(--light-gray); border-radius: 20px; font-size: 0.8rem; font-weight: 500; color: var(--text-color); }
        .publicacion-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color); margin-top: auto; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); }
        .empty-state i { font-size: 4rem; margin-bottom: 1.5rem; color: var(--medium-gray); opacity: 0.5; }
        .empty-state h3 { margin-bottom: 1rem; color: var(--text-color); font-size: 1.5rem; }
        .empty-state p { margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; }
        @media (max-width: 1200px) { .main-content { grid-template-columns: 240px 1fr; } .profile-stats { grid-template-columns: repeat(2, 1fr); } /* .publicaciones-grid ya es responsive */ }
        @media (max-width: 992px) { .main-content { grid-template-columns: 1fr; } .profile-content-header { grid-template-columns: 1fr; text-align: center; gap: 1.5rem; } .profile-actions { flex-direction: row; justify-content: center; width: 100%; } }
        @media (max-width: 768px) { .container { padding: 0 1rem; } .profile-header { padding: 2rem 0; } .profile-stats { grid-template-columns: 1fr 1fr; } /* .publicaciones-grid ya es responsive */ .section-header { flex-direction: column; align-items: stretch; gap: 1.5rem; } }
        @media (max-width: 480px) { .container { padding: 0 1rem; } .profile-name { font-size: 1.8rem; } }
        @media (max-width: 768px) { .profile-content-header { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1.5rem; } .profile-avatar { display: flex; justify-content: center; width: 100%; } .profile-meta { justify-content: center; gap: 1rem; } .profile-bio { margin-left: auto; margin-right: auto; } .profile-actions { width: 100%; max-width: 300px; margin: 0 auto; flex-direction: column !important; gap: 0.8rem; } .profile-actions .btn { width: 100%; justify-content: center; } .main-content { display: flex; flex-direction: column; gap: 2rem; } .profile-sidebar { width: 100%; order: 2; } .profile-main { order: 1; } .sidebar-card { background: #fcfcfc; } .publicacion-meta { justify-content: space-around; flex-direction: row; align-items: center; } }
    </style>

    <!-- Header del Perfil Público -->
    <div class="profile-header">
        <div class="container">
            <div class="profile-content-header">
                <div class="profile-avatar">
                    <div class="avatar-container">
                        <img src="<?php echo !empty($usuario['foto_perfil']) ? obtenerImagenFinal($usuario['foto_perfil']) : PROD_IMAGE_URL . 'assets/iconos/user.webp'; ?>" alt="Foto de perfil de <?php echo htmlspecialchars($usuario['nombres']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                
                <div class="profile-info-main">
                    <h1 class="profile-name"><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></h1>
                    
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars($usuario['correo_institucional']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-university"></i>
                            <span title="<?php echo htmlspecialchars($nombre_completo_facultad); ?>">
                                <?php echo htmlspecialchars($usuario['facultad'] ?? 'Sin facultad'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <p class="profile-bio">
                        Miembro de la comunidad UniEmprende.
                        <?php 
                            $total_productos = count($publicaciones);
                            if ($total_productos == 0) {
                                echo 'Este usuario aún no tiene productos activos.';
                            } elseif ($total_productos == 1) {
                                echo 'Este usuario tiene 1 producto activo.';
                            } else {
                                echo "Este usuario tiene {$total_productos} productos activos.";
                            }
                        ?>
                    </p>
                </div>
                
                <div class="profile-actions">
                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $usuario['id_usuario']): ?>
                        <a href="<?php echo BASE_URL; ?>perfil/editar" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Mi Perfil
                        </a>
                    <?php else: ?>
                        <a href="<?php echo isset($_SESSION['usuario_id']) ? BASE_URL . 'chat/iniciar?destinatario=' . $usuario['id_usuario'] : BASE_URL . 'login'; ?>" class="btn btn-primary">
                            <i class="fas fa-comment-dots"></i> Contactar Vendedor
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Contenido Principal de 2 columnas -->
        <div class="main-content">
            <!-- Sidebar con información pública -->
            <aside class="profile-sidebar">
                <div class="sidebar-card">
                    <h3><i class="fas fa-info-circle"></i> Información del Vendedor</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-phone"></i> Teléfono
                            </span>
                            <span class="info-value">
                                <?php echo !empty($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : 'No proporcionado'; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-university"></i> Facultad
                            </span>
                            <span class="info-value" title="<?php echo htmlspecialchars($nombre_completo_facultad); ?>">
                                <?php echo !empty($usuario['facultad']) ? htmlspecialchars($usuario['facultad']) : 'No especificada'; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-school"></i> Escuela
                            </span>
                            <span class="info-value" title="<?php echo htmlspecialchars($nombre_completo_escuela); ?>">
                                <?php echo !empty($usuario['escuela']) ? htmlspecialchars($usuario['escuela']) : 'No especificada'; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-calendar-alt"></i> Miembro desde
                            </span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Contenido Principal (Publicaciones) -->
            <main class="profile-main">
                <div class="tabs-container">
                    <div class="tab-content">
                        <div id="publicaciones" class="tab-pane active">
                            <div class="section-header">
                                <h2 class="section-title">
                                    <i class="fas fa-box-open"></i> Publicaciones de <?php echo htmlspecialchars(explode(' ', $usuario['nombres'])[0]); ?>
                                </h2>
                            </div>

                            <?php if (empty($publicaciones)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h3>Este emprendedor aún no tiene publicaciones</h3>
                                    <p>Vuelve a visitar este perfil más tarde para ver sus productos o servicios.</p>
                                </div>
                            <?php else: ?>
                                <div class="publicaciones-grid">
                                    <?php foreach ($publicaciones as $publicacion): ?>
                                        <div class="publicacion-card">
                                            <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="publicacion-image">
                                                <?php $imgFinal = obtenerImagenFinal($publicacion['imagen'] ?? null); ?>
                                                <?php if (!empty($imgFinal)): ?>
                                                    <img src="<?php echo htmlspecialchars($imgFinal); ?>" alt="<?php echo htmlspecialchars($publicacion['titulo']); ?>">
                                                <?php else: ?>
                                                    <div class="no-image"><i class="fas fa-image"></i><div>Sin imagen</div></div>
                                                <?php endif; ?>
                                            </a>
                                            
                                            <div class="publicacion-content">
                                                <div class="publicacion-header">
                                                    <h3 class="publicacion-title">
                                                        <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" style="color: inherit; text-decoration: none;">
                                                            <?php echo htmlspecialchars($publicacion['titulo']); ?>
                                                        </a>
                                                    </h3>
                                                    <div class="publicacion-precio">S/ <?php echo number_format($publicacion['precio'], 2); ?></div>
                                                </div>
                                                
                                                <p class="publicacion-desc"><?php echo htmlspecialchars(substr($publicacion['descripcion'], 0, 100)); ?>...</p>
                                                
                                                <div class="publicacion-meta">
                                                    <span class="meta-tag"><?php echo htmlspecialchars($publicacion['nombre_categoria']); ?></span>
                                                    <span class="meta-tag"><?php echo $publicacion['tipo']; ?></span>
                                                </div>
                                                
                                                <div class="publicacion-footer">
                                                    <a href="<?php echo BASE_URL; ?>publicaciones/ver/<?php echo $publicacion['id_publicacion']; ?>" class="btn btn-outline btn-sm" style="width: 100%;">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>
