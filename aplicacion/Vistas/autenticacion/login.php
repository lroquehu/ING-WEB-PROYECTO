 
<!-- Modal de Login -->
<div id="login-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Iniciar Sesión</h2>
            <button class="close-modal" onclick="closeModal('login-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <form class="modal-form" id="login-form">
                <div class="input-group">
                    <input type="email" id="login-email" placeholder=" " required>
                    <label for="login-email">Correo electrónico</label>
                </div>
                <div class="input-group">
                    <input type="password" id="login-password" placeholder=" " required>
                    <label for="login-password">Contraseña</label>
                </div>
                <div class="forgot-password">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
            
            <div class="social-login">
                <p>o inicia sesión con</p>
                <div class="social-buttons">
                    <div class="social-btn google">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-submit" onclick="login()">Iniciar Sesión</button>
            <p class="modal-switch">¿No tienes cuenta? <a onclick="switchModal('login-modal', 'register-modal')">Regístrate</a></p>
        </div>
    </div>
</div>