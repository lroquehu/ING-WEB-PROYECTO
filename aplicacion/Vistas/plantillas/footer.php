<?php
// aplicacion/Vistas/partials/pie.php
?>

    </main>

    <!-- Botón Back to Top -->
    <button class="scroll-to-top" id="scrollToTop" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-graduation-cap"></i>
                        UniEmprende
                    </div>
                    <p class="footer-description">
                        La plataforma líder para el emprendimiento universitario. 
                        Conectamos estudiantes emprendedores y facilitamos el comercio 
                        dentro de la comunidad universitaria.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h4>Explorar</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>publicaciones"><i class="fas fa-chevron-right"></i> Productos</a></li>
                        <li><a href="<?php echo BASE_URL; ?>publicaciones?tipo=Servicio"><i class="fas fa-chevron-right"></i> Servicios</a></li>
                        <li><a href="<?php echo BASE_URL; ?>categorias"><i class="fas fa-chevron-right"></i> Categorías</a></li>
                        <li><a href="<?php echo BASE_URL; ?>buscar"><i class="fas fa-chevron-right"></i> Búsqueda</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Cuenta</h4>
                    <ul class="footer-links">
                        <?php if ($usuario_autenticado): ?>
                            <li><a href="<?php echo BASE_URL; ?>perfil"><i class="fas fa-chevron-right"></i> Mi Perfil</a></li>
                            <li><a href="<?php echo BASE_URL; ?>perfil/publicaciones"><i class="fas fa-chevron-right"></i> Mis Publicaciones</a></li>
                            <li><a href="<?php echo BASE_URL; ?>perfil/favoritos"><i class="fas fa-chevron-right"></i> Favoritos</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>login"><i class="fas fa-chevron-right"></i> Iniciar Sesión</a></li>
                            <li><a href="<?php echo BASE_URL; ?>registro"><i class="fas fa-chevron-right"></i> Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Ayuda</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_URL; ?>acerca-de"><i class="fas fa-chevron-right"></i> Acerca de</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contacto"><i class="fas fa-chevron-right"></i> Contacto</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Preguntas Frecuentes</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Términos de Uso</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 UniEmprende. Todos los derechos reservados. | Desarrollado para la comunidad universitaria</p>
            </div>
        </div>
    </footer>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            const scrollToTop = document.getElementById('scrollToTop');
            
            if (window.scrollY > 100) {
                header.classList.add('header-scrolled');
                scrollToTop.classList.add('visible');
            } else {
                header.classList.remove('header-scrolled');
                scrollToTop.classList.remove('visible');
            }
        });

        // Back to top functionality
        document.getElementById('scrollToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Filtrado de productos por categoría
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            const productCards = document.querySelectorAll('.product-card');
            
            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    // Remover clase active de todos los filtros
                    categoryFilters.forEach(f => f.classList.remove('active'));
                    // Agregar clase active al filtro clickeado
                    this.classList.add('active');
                    
                    const categoria = this.getAttribute('data-categoria');
                    
                    // Mostrar/ocultar productos según categoría
                    let visibleCount = 0;
                    productCards.forEach(card => {
                        if (categoria === 'all' || card.getAttribute('data-categoria') === categoria) {
                            card.style.display = 'block';
                            visibleCount++;
                            // Animación de aparición
                            card.style.animation = 'fadeIn 0.5s ease';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Mostrar mensaje si no hay resultados
                    const productGrid = document.getElementById('product-grid');
                    let noResults = productGrid.querySelector('.no-results');
                    
                    if (visibleCount === 0) {
                        if (!noResults) {
                            noResults = document.createElement('div');
                            noResults.className = 'empty-state no-results';
                            noResults.innerHTML = `
                                <i class="fas fa-search"></i>
                                <h3>No se encontraron publicaciones</h3>
                                <p>No hay publicaciones en esta categoría en este momento.</p>
                            `;
                            productGrid.appendChild(noResults);
                        }
                    } else if (noResults) {
                        noResults.remove();
                    }
                });
            });

            // Favoritos functionality
            const favoriteButtons = document.querySelectorAll('.product-favorite');
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Evitar que el clic vaya a la tarjeta
                    
                    const productId = this.getAttribute('data-producto');
                    const icon = this.querySelector('i');
                    const btn = this;
                    
                    // Llamada AJAX
                    fetch('<?php echo BASE_URL; ?>favoritos/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id_publicacion: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error && data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        
                        if (data.success) {
                            // Toggle visual state
                            if (data.accion === 'agregado') {
                                btn.classList.add('favorited');
                                icon.className = 'fas fa-heart'; // Corazón lleno
                            } else {
                                btn.classList.remove('favorited');
                                icon.className = 'far fa-heart'; // Corazón vacío
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            // Smooth scroll para enlaces internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // CSS para animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .badge {
                background: rgba(255,255,255,0.2);
                padding: 0.2rem 0.5rem;
                border-radius: 10px;
                font-size: 0.7rem;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>