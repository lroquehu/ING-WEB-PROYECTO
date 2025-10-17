<?php
// Verificar si ya está autenticado
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'inicio');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (!empty($correo) && !empty($password)) {
        require_once '../../configuracion/conexion.php';
        $conexion = new Conexion();
        $db = $conexion->conectar();
        
        if ($db) {
            // Buscar usuario por correo
            $stmt = $db->prepare("SELECT id_usuario, nombres, apellidos, contraseña FROM Usuarios WHERE correo_institucional = ? AND estado = 1");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($password, $usuario['contraseña'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                $_SESSION['usuario_correo'] = $correo;
                
                header('Location: ' . BASE_URL . 'inicio');
                exit;
            } else {
                $error = 'Credenciales incorrectas o cuenta inactiva';
            }
        } else {
            $error = 'Error de conexión a la base de datos';
        }
    } else {
        $error = 'Por favor completa todos los campos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UniEmprende</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Iniciar Sesión</h1>
                <p>Bienvenido de vuelta a UniEmprende</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="input-group">
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" placeholder=" " required>
                    <label for="correo">Correo institucional</label>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Contraseña</label>
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Recordarme
                    </label>
                    <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Iniciar Sesión</button>
            </form>

            <div class="auth-divider">
                <span>o continúa con</span>
            </div>

            <div class="social-auth">
                <button class="social-btn google">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
            </div>

            <div class="auth-footer">
                <p>¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>autenticacion/registro" class="auth-link">Regístrate aquí</a></p>
            </div>
        </div>

        <div class="auth-background">
            <div class="background-content">
                <h2>UniEmprende</h2>
                <p>La plataforma de compra y venta para la comunidad universitaria</p>
            </div>
        </div>
    </div>

    <script>
        // Efectos para los inputs
        document.querySelectorAll('.input-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });

            // Verificar si el input tiene valor al cargar la página
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
    </script>
</body>
</html>