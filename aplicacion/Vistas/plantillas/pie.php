 
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>UniEmprende</h3>
                <p>Plataforma de emprendimiento universitario.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Enlaces</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?c=Producto&a=categorias">Categorías</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?c=Producto&a=index">Productos</a></li>
                    <li><a href="#contact">Contacto</a></li>
                    <li><a href="#about">Sobre Nosotros</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <ul class="footer-links">
                    <li>info@uniemprende.pe</li>
                    <li>+51 935 812 499</li>
                    <li>Tacna, Perú</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 UniEmprende - Todos los derechos reservados</p>
        </div>
    </footer>

    <!-- Modales -->
    <?php include 'aplicacion/Vistas/autenticacion/login.php'; ?>
    <?php include 'aplicacion/Vistas/autenticacion/registro.php'; ?>
</body>
</html>