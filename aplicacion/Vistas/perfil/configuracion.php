<?php 
// aplicacion/Vistas/perfil/configuracion.php
$page_title = 'Configuración - UniEmprende';
require_once 'aplicacion/Vistas/plantillas/header.php';
?>

<style>
    /* Estilos para el Switch Slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px; width: 26px;
        left: 4px; bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider { background-color: #740074; /* Color Yape Morado */ }
    input:focus + .slider { box-shadow: 0 0 1px #740074; }
    input:checked + .slider:before { transform: translateX(26px); }

    /* Contenedor del formulario */
    .config-container {
        max-width: 800px;
        margin: 2rem auto;
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .yape-section {
        border: 1px solid #eee;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1rem;
    }
    .yape-details {
        display: none; /* Oculto por defecto si el switch está off */
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        animation: fadeIn 0.3s ease-in-out;
    }
    .yape-details.visible { display: block; }
    
    .form-group { margin-bottom: 1.2rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
    .form-control {
        width: 100%; padding: 0.75rem;
        border: 1px solid #ddd; border-radius: 6px;
    }
    .current-qr {
        width: 150px; height: 150px; object-fit: cover;
        border: 2px dashed #ddd; border-radius: 8px; margin-top: 0.5rem;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container">
    <div class="config-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Configuración de Cuenta</h1>
            <a href="<?php echo BASE_URL; ?>perfil" class="btn btn-outline-secondary">Volver al Perfil</a>
        </div>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>perfil/guardar-yape" method="POST" enctype="multipart/form-data">
            <div class="yape-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo LOCAL_IMAGE_URL; ?>assets/iconos/yape-logo.png" alt="Yape" style="height: 40px; border-radius:8px;"> <div>
                            <h3 class="m-0" style="color: #740074;">Pago Manual - Yape</h3>
                            <p class="text-muted m-0 small">Permite a tus compradores pagarte escaneando tu QR.</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="yape_activo" id="yapeToggle" <?php echo ($usuario['yape_activo'] == 1) ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="yape-details <?php echo ($usuario['yape_activo'] == 1) ? 'visible' : ''; ?>" id="yapeForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Número de Celular Yape</label>
                                <input type="text" name="yape_numero" class="form-control" placeholder="Ej: 987654321" value="<?php echo htmlspecialchars($usuario['yape_numero'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre del Titular</label>
                                <input type="text" name="yape_nombre" class="form-control" placeholder="Nombre completo como aparece en Yape" value="<?php echo htmlspecialchars($usuario['yape_nombre'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="form-group">
                                <label class="form-label">Código QR de Yape</label>
                                <?php if (!empty($usuario['yape_qr'])): ?>
                                    <div class="mb-2">QR Actual:</div>
                                    <img src="<?php echo LOCAL_IMAGE_URL . $usuario['yape_qr']; ?>" class="current-qr mb-2">
                                <?php endif; ?>
                                <input type="file" name="yape_qr" class="form-control" accept="image/*">
                                <small class="text-muted">Sube una captura de tu QR (JPG, PNG)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary" style="background-color: #740074; border-color: #740074;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const toggle = document.getElementById('yapeToggle');
    const formDetails = document.getElementById('yapeForm');

    toggle.addEventListener('change', function() {
        if(this.checked) {
            formDetails.classList.add('visible');
        } else {
            formDetails.classList.remove('visible');
        }
    });
</script>

<?php require_once 'aplicacion/Vistas/plantillas/footer.php'; ?>