<?php
// aplicacion/Vistas/plantillas/footer.php
?>

    </main> <button class="scroll-to-top" id="scrollToTop" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
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
                        <?php if (isset($_SESSION['usuario_id'])): ?>
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
                <p>&copy; 2025 UniEmprende. Todos los derechos reservados. | Desarrollado para la comunidad universitaria</p>
            </div>
        </div>
    </footer>

    <style>
        /* Footer */
        .main-footer {
            background: var(--secondary-color, #2c3e50);
            color: var(--bg-white, #ffffff);
            padding: 3rem 0 1rem;
            position: relative; 
            z-index: 2;
            margin-top: auto;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-description {
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-link {
            color: #ffffff;
            text-decoration: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: var(--primary-color, #910202);
            transform: translateY(-2px);
        }
        
        .footer-column h4 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-links a:hover {
            color: #ffffff;
            transform: translateX(5px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
        }

        /* Botón de desplazamiento hacia arriba */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--primary-color, #910202);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            opacity: 0;
            visibility: hidden;
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background: var(--primary-dark, #510200);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(145, 2, 2, 0.4);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .footer-content { grid-template-columns: 1fr; gap: 2rem; }
        }
    </style>

    <script>
        // Funcionalidad Back to Top y Header Scroll
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTop = document.getElementById('scrollToTop');
            const header = document.querySelector('.main-header'); // Asegúrate que el header tenga esta clase

            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    if(header) header.classList.add('header-scrolled');
                    if(scrollToTop) scrollToTop.classList.add('visible');
                } else {
                    if(header) header.classList.remove('header-scrolled');
                    if(scrollToTop) scrollToTop.classList.remove('visible');
                }
            });

            if(scrollToTop) {
                scrollToTop.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
        
        // CSS para animaciones básicas
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>