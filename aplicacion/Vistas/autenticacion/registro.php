<!-- Modal de Registro -->
<div id="register-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Crear Cuenta</h2>
            <button class="close-modal" onclick="closeModal('register-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <form class="modal-form" id="register-form">
                <div class="input-group">
                    <input type="text" id="register-name" placeholder=" " required>
                    <label for="register-name">Nombre completo</label>
                </div>
                <div class="input-group">
                    <input type="email" id="register-email" placeholder=" " required>
                    <label for="register-email">Correo electrónico</label>
                </div>
                <div class="input-group">
                    <input type="password" id="register-password" placeholder=" " required>
                    <label for="register-password">Contraseña</label>
                </div>
                <div class="input-group">
                    <input type="password" id="register-confirm" placeholder=" " required>
                    <label for="register-confirm">Confirmar contraseña</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="register-terms">
                    <label for="register-terms">Acepto los <a href="#">términos y condiciones</a></label>
                </div>
            </form>
            
            <div class="social-login">
                <p>o regístrate con</p>
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
            <button class="modal-submit" onclick="registrar()">Crear Cuenta</button>
            <p class="modal-switch">¿Ya tienes cuenta? <a onclick="switchModal('register-modal', 'login-modal')">Inicia sesión</a></p>
        </div>
    </div>
</div> 
