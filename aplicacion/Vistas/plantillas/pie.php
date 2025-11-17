<?php
// pie.php - PARTIAL CORREGIDO
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:8000/ING-WEB-PROYECTO/');
}
?>


    </main>
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>UniEmprende</h3>
                <p>Plataforma de emprendimiento universitario.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Enlaces</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>categorias">Categorías</a></li>
                    <li><a href="<?php echo BASE_URL; ?>publicaciones">Productos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>contacto">Contacto</a></li>
                    <li><a href="<?php echo BASE_URL; ?>acerca-de">Sobre Nosotros</a></li>
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

    <!-- Scripts Globales -->
    <script>
    // Funciones globales básicas
    function scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Funcionalidad de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query) {
                        window.location.href = '<?php echo BASE_URL; ?>buscar?q=' + encodeURIComponent(query);
                    }
                }
            });
        }
    });
    </script>

</body>
</html>