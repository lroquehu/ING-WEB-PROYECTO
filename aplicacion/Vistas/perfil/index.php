 
<?php include 'aplicacion/Vistas/plantillas/encabezado.php'; ?>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
    }

    .profile-info {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--primary);
        border: 4px solid white;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .profile-details h1 {
        margin: 0 0 0.5rem 0;
        font-size: 2.2rem;
        font-weight: 700;
    }

    .profile-details .university {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .profile-details .email {
        font-size: 1rem;
        opacity: 0.8;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border-top: 4px solid var(--primary);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--text-light);
        font-size: 1rem;
    }

    .profile-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .action-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--primary);
    }

    .action-card h3 {
        margin: 0 0 1rem 0;
        color: var(--primary);
        font-size: 1.3rem;
    }

    .action-card p {
        color: var(--text-light);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .btn-profile {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-profile:hover {
        background: var(--primary-dark);
    }

    .btn-outline-profile {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 0.8rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-outline-profile:hover {
        background: var(--primary);
        color: white;
    }

    .recent-activity {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .recent-activity h2 {
        margin: 0 0 1.5rem 0;
        color: var(--primary);
        font-size: 1.5rem;
    }

    .activity-list {
        list-style: none;
    }

    .activity-item {
        padding: 1rem 0;
        border-bottom: 1px solid var(--gray);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        background: var(--gray);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .activity-time {
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .success-message {
        background: #10b981;
        color: white;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 2rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .profile-info {
            flex-direction: column;
            text-align: center;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }
        
        .profile-details h1 {
            font-size: 1.8rem;
        }
        
        .profile-stats {
            grid-template-columns: 1fr;
        }
        
        .profile-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container">
    <!-- Mensajes de éxito -->
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <?php 
            if ($_GET['success'] == 1) {
                echo "Perfil actualizado correctamente";
            } elseif ($_GET['success'] == 2) {
                echo "Contraseña cambiada correctamente";
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- Header del perfil -->
    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-details">
                <h1><?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?></h1>
                <div class="university">
                    <i class="fas fa-university"></i>
                    <?php echo htmlspecialchars($usuario['universidad'] ?? 'Universidad no especificada'); ?>
                </div>
                <div class="email">
                    <i class="fas fa-envelope"></i>
                    <?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($productosUsuario); ?></div>
            <div class="stat-label">Productos Publicados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Ventas Realizadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Likes Recibidos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Seguidores</div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="profile-actions">
        <div class="action-card">
            <h3>Gestionar Productos</h3>
            <p>Administra tus productos publicados, crea nuevos productos o edita los existentes.</p>
            <a href="<?php echo BASE_URL; ?>?c=Perfil&a=misProductos" class="btn-profile">
                <i class="fas fa-boxes"></i> Mis Productos
            </a>
        </div>
        
        <div class="action-card">
            <h3>Editar Perfil</h3>
            <p>Actualiza tu información personal, universidad y datos de contacto.</p>
            <a href="<?php echo BASE_URL; ?>?c=Perfil&a=editar" class="btn-outline-profile">
                <i class="fas fa-edit"></i> Editar Perfil
            </a>
        </div>
        
        <div class="action-card">
            <h3>Seguridad</h3>
            <p>Cambia tu contraseña y gestiona la seguridad de tu cuenta.</p>
            <a href="<?php echo BASE_URL; ?>?c=Perfil&a=cambiarPassword" class="btn-outline-profile">
                <i class="fas fa-lock"></i> Cambiar Contraseña
            </a>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="recent-activity">
        <h2>Actividad Reciente</h2>
        <ul class="activity-list">
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">Te uniste a UniEmprende</div>
                    <div class="activity-time">Hace 1 día</div>
                </div>
            </li>
             
        </ul>
    </div>
</div>

<?php include 'aplicacion/Vistas/plantillas/pie.php'; ?>