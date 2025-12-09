<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // --- BLOQUE DE SEGURIDAD: VERIFICAR ESTADO DEL USUARIO ---
    if (isset($_SESSION['usuario_id'])) {
        // Conectar a BD para verificar estado actual (en caso de que lo acaben de suspender)
        require_once __DIR__ . '/../../Configuracion/conexion.php';
        try {
            $conexionHeader = new Conexion();
            $dbHeader = $conexionHeader->conectar();
            
            $stmtH = $dbHeader->prepare("SELECT estado, suspension_fin, motivo_suspension FROM Usuarios WHERE id_usuario = ?");
            $stmtH->execute([$_SESSION['usuario_id']]);
            $userStatus = $stmtH->fetch(PDO::FETCH_ASSOC);

            // Si el usuario fue suspendido (estado 0)
            if ($userStatus && $userStatus['estado'] == 0) {
                $ahora = new DateTime();
                $fin = $userStatus['suspension_fin'] ? new DateTime($userStatus['suspension_fin']) : null;
                
                // Si la suspensión sigue vigente (fecha futura o indefinida)
                if (!$fin || $ahora < $fin) {
                    // 1. Destruir sesión actual
                    session_unset();
                    session_destroy();
                    
                    // 2. Iniciar nueva sesión para guardar el mensaje de error
                    session_start();
                    
                    $fecha_fmt = $fin ? $fin->format('d/m/Y H:i') : 'Indefinido';
                    $motivo = htmlspecialchars($userStatus['motivo_suspension']);
                    
                    $_SESSION['error_login'] = "<strong>Sesión Cerrada</strong><br>
                                                Tu cuenta ha sido suspendida.<br>
                                                Hasta: <b>$fecha_fmt</b><br>
                                                Motivo: $motivo";
                    
                    // 3. Redirigir al login
                    $redirect = defined('BASE_URL') ? BASE_URL . 'login' : '../../login';
                    header('Location: ' . $redirect);
                    exit;
                } 
            }
        } catch (Exception $e) {
            // Silencioso en header para no romper la página por errores de conexión momentáneos
            error_log("Error verificación header: " . $e->getMessage());
        }
    }

    $usuario_autenticado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'UniEmprende'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #910202;
            --primary-dark: #510200;
            --primary-light: #b30303;
            --secondary-color: #2c3e50;
            --accent-color: #ffc107;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-color: #e1e1e1;
            --shadow: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.2);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg-light); color: var(--text-dark); }

        /* Estilos del Header */
        .main-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            padding: 1rem 0;
            box-shadow: var(--shadow-lg);
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }
        
        .header-scrolled {
            background: rgba(81, 2, 0, 0.95);
        }

        .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--bg-white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--accent-color), #ffed4a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-buttons {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        /* --- BOTONES GENERALES (Con Texto: Publicar, Perfil, etc.) --- */
        .nav-btn {
            /* Estado inicial: Círculo pequeño */
            width: 44px; 
            height: 44px;
            padding: 0;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden; /* Mantiene el efecto de brillo contenido */
            transition: all 0.3s ease;
            color: var(--bg-white);
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .nav-btn:hover::before { left: 100%; }
        
        .nav-btn i { 
            position: relative; 
            z-index: 2; 
            font-size: 1.1rem; 
            transition: var(--transition); 
        }
        
        .nav-btn .btn-text { 
            max-width: 0; 
            opacity: 0; 
            white-space: nowrap; 
            overflow: hidden; 
            transition: all 0.3s ease; 
            margin-left: 0; 
            position: relative; 
            z-index: 2; 
        }
        
        /* Efecto de expansión al pasar el mouse (SOLO para botones con texto) */
        .nav-btn:hover {
            width: auto; 
            padding: 0 1.5rem; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        }
        
        .nav-btn:hover .btn-text { 
            max-width: 200px; 
            opacity: 1; 
            margin-left: 0.5rem; 
        }

        .nav-btn-primary { background: rgba(255, 255, 255, 0.15); border: 2px solid rgba(255, 255, 255, 0.3); }
        .nav-btn-primary:hover { background: rgba(255, 255, 255, 0.25); border-color: rgba(255, 255, 255, 0.5); }

        .nav-btn-outline { background: transparent; border: 2px solid rgba(255, 255, 255, 0.4); }
        .nav-btn-outline:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.8); }

        .nav-btn-secondary { background: rgba(255, 215, 0, 0.2); border: 2px solid var(--accent-color); color: var(--accent-color); }
        .nav-btn-secondary:hover { background: var(--accent-color); color: var(--primary-dark); }

        /* Contenedor de Iconos de Usuario */
        .user-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* --- CORRECCIÓN ESPECÍFICA PARA NOTIFICACIONES Y MENSAJES --- */
        /* Sobrescribimos estilos de .nav-btn solo para estos elementos */
        .nav-btn.nav-btn-icon {
            overflow: visible !important;
        }
        
        /* Anular el efecto de brillo en estos botones para evitar conflictos */
        .nav-btn.nav-btn-icon::before {
            display: none;
        }

        /* Anular la expansión al pasar el mouse */
        .nav-btn.nav-btn-icon:hover {
            width: 44px !important;
            padding: 0 !important;
            background: rgba(255, 255, 255, 0.2); /* Fondo sutil */
        }

        .nav-btn-icon i { margin-right: 0; }

        /* Estilo del Numerito (Badge) */
        .badge {
            position: absolute;
            top: -5px;  
            right: -5px; 
            background: var(--accent-color);
            color: var(--primary-dark);
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            display: none; /* Se muestra con JS */
            z-index: 10;
            padding: 0 4px; /* Un poco de padding si el número es grande */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-btn:hover .btn-text { display: none; }
            .nav-btn i { margin-right: 0; }
            .nav-buttons { gap: 0.5rem; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <i class="fas fa-graduation-cap"></i> UniEmprende
                </a>

                <div class="nav-buttons">
                    <?php if ($usuario_autenticado): ?>
                        <a href="<?php echo BASE_URL; ?>publicaciones/crear" class="nav-btn nav-btn-primary">
                            <i class="fas fa-plus"></i><span class="btn-text">Publicar</span>
                        </a>

                        <?php if (isset($_SESSION['usuario_rol']) && strtolower($_SESSION['usuario_rol']) === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>admin" class="nav-btn nav-btn-outline" title="Panel Admin">
                                <i class="fas fa-user-shield"></i><span class="btn-text">Admin</span>
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo BASE_URL; ?>perfil" class="nav-btn nav-btn-outline">
                            <i class="fas fa-user"></i><span class="btn-text">Perfil</span>
                        </a>

                        <div class="user-actions">
                            <a href="<?php echo BASE_URL; ?>chat" class="nav-btn nav-btn-outline nav-btn-icon" id="chat-link">
                                <i class="fas fa-comments"></i>
                                <span class="badge" id="chat-badge">0</span>
                            </a>
                            
                            <a href="<?php echo BASE_URL; ?>notificaciones" class="nav-btn nav-btn-outline nav-btn-icon" id="notif-link">
                                <i class="fas fa-bell"></i>
                                <span class="badge" id="notif-badge">0</span>
                            </a>
                        </div>

                        <a href="<?php echo BASE_URL; ?>logout" class="nav-btn nav-btn-secondary">
                            <i class="fas fa-sign-out-alt"></i><span class="btn-text">Salir</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login" class="nav-btn nav-btn-outline">
                            <i class="fas fa-sign-in-alt"></i><span class="btn-text">Ingresar</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>registro" class="nav-btn nav-btn-primary">
                            <i class="fas fa-user-plus"></i><span class="btn-text">Registro</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main> <script>
        const base_url = "<?php echo BASE_URL; ?>";
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($usuario_autenticado): ?>
            function verificarNotificaciones() {
                fetch(base_url + 'notificaciones/verificarestado')
                    .then(response => response.json())
                    .then(data => {
                        const notifBadge = document.getElementById('notif-badge');
                        const chatBadge = document.getElementById('chat-badge');
                        
                        if (data.alertas > 0) {
                            notifBadge.textContent = data.alertas > 9 ? '9+' : data.alertas;
                            notifBadge.style.display = 'flex';
                        } else { notifBadge.style.display = 'none'; }

                        if (data.mensajes > 0) {
                            chatBadge.textContent = data.mensajes > 9 ? '9+' : data.mensajes;
                            chatBadge.style.display = 'flex';
                        } else { chatBadge.style.display = 'none'; }
                    });
            }
            verificarNotificaciones();
            setInterval(verificarNotificaciones, 15000);
            <?php endif; ?>
        });
    </script>