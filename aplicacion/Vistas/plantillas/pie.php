 
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

    <script>
        const adsData = {
            'tecnologia': [
                {
                    title: 'Smartwatch Inteligente Premium',
                    description: 'Reloj inteligente con monitor de actividad avanzado, GPS incorporado y resistencia al agua hasta 50m.',
                    price: '€129.99',
                    category: 'Tecnología',
                    image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=500&q=80',
                    likes: 28,
                    available: 15,
                    seller: { name: 'Carlos M.', university: 'UPV' }
                },
                {
                    title: 'Auriculares Bluetooth',
                    description: 'Auriculares inalámbricos con cancelación de ruido y 30 horas de batería.',
                    price: '€89.99',
                    category: 'Tecnología',
                    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&q=80',
                    likes: 35,
                    available: 12,
                    seller: { name: 'Laura G.', university: 'UAB' }
                },
                {
                    title: 'Soporte para Laptop Ergonómico',
                    description: 'Soporte ajustable de aluminio para mejorar la postura durante largas horas de trabajo.',
                    price: '€42.75',
                    category: 'Tecnología',
                    image: 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=500&q=80',
                    likes: 22,
                    available: 6,
                    seller: { name: 'Miguel R.', university: 'UC3M' }
                }
            ],
            'moda': [
                {
                    title: 'Bolso Artesanal Ecológico',
                    description: 'Fabricado con materiales 100% reciclados, diseño único y espacioso.',
                    price: '€59.99',
                    category: 'Moda',
                    image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80',
                    likes: 42,
                    available: 3,
                    seller: { name: 'Ana S.', university: 'UCM' }
                },
                {
                    title: 'Gafas de Sol Vintage',
                    description: 'Gafas de sol con montura de acetato y lentes polarizadas UV400.',
                    price: '€35.00',
                    category: 'Moda',
                    image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=500&q=80',
                    likes: 31,
                    available: 18,
                    seller: { name: 'Carlos M.', university: 'UPV' }
                },
                {
                    title: 'Zapatos Deportivos Sostenibles',
                    description: 'Fabricados con materiales reciclados, suela antideslizante y diseño moderno.',
                    price: '€79.99',
                    category: 'Moda',
                    image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80',
                    likes: 27,
                    available: 7,
                    seller: { name: 'Laura G.', university: 'UAB' }
                }
            ],
            'accesorios': [
                {
                    title: 'Cargador Portátil Solar',
                    description: 'Cargador ecológico con panel solar integrado y batería de 10000mAh.',
                    price: '€49.99',
                    category: 'Accesorios',
                    image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=500&q=80',
                    likes: 38,
                    available: 10,
                    seller: { name: 'David L.', university: 'UAM' }
                },
                {
                    title: 'Organizador de Cable Multiusos',
                    description: 'Mantén tus cables ordenados con este práctico organizador de escritorio.',
                    price: '€15.50',
                    category: 'Accesorios',
                    image: 'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=500&q=80',
                    likes: 16,
                    available: 25,
                    seller: { name: 'Ana S.', university: 'UCM' }
                },
                {
                    title: 'Funda para Laptop Hecha a Mano',
                    description: 'Funda protectora tejida a mano con lana natural y diseños únicos.',
                    price: '€32.00',
                    category: 'Accesorios',
                    image: 'https://images.unsplash.com/photo-1556655842-7ef43aeb3eec?auto=format&fit=crop&w=500&q=80',
                    likes: 29,
                    available: 4,
                    seller: { name: 'Miguel R.', university: 'UC3M' }
                }
            ],
            'alimentacion': [
                {
                    title: 'Kit de Café Premium',
                    description: 'Incluye granos de café de especialidad de Colombia, molinillo manual y prensa francesa.',
                    price: '€45.50',
                    category: 'Alimentación',
                    image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=500&q=80',
                    likes: 19,
                    available: 8,
                    seller: { name: 'David L.', university: 'UAM' }
                },
                {
                    title: 'Miel Orgánica Local',
                    description: 'Miel 100% natural producida por abejas en entornos libres de pesticidas.',
                    price: '€18.75',
                    category: 'Alimentación',
                    image: 'https://images.unsplash.com/photo-1587049633312-d628ae50a8ae?auto=format&fit=crop&w=500&q=80',
                    likes: 24,
                    available: 14,
                    seller: { name: 'Carlos M.', university: 'UPV' }
                },
                {
                    title: 'Box de Snacks Saludables',
                    description: 'Selección de snacks orgánicos y sin conservantes para toda la semana.',
                    price: '€29.99',
                    category: 'Alimentación',
                    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=500&q=80',
                    likes: 33,
                    available: 9,
                    seller: { name: 'Laura G.', university: 'UAB' }
                }
            ],
            'arte': [
                {
                    title: 'Ilustración Personalizada',
                    description: 'Ilustración digital personalizada según tus preferencias, ideal para regalos.',
                    price: '€35.00',
                    category: 'Arte',
                    image: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=500&q=80',
                    likes: 45,
                    available: 0,
                    seller: { name: 'Ana S.', university: 'UCM' }
                },
                {
                    title: 'Lienzo Pintado a Mano',
                    description: 'Pintura original en lienzo con técnicas mixtas y marco incluido.',
                    price: '€120.00',
                    category: 'Arte',
                    image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?auto=format&fit=crop&w=500&q=80',
                    likes: 18,
                    available: 2,
                    seller: { name: 'Miguel R.', university: 'UC3M' }
                },
                {
                    title: 'Fotografía Artística',
                    description: 'Impresión de alta calidad en papel fotográfico premium, edición limitada.',
                    price: '€65.00',
                    category: 'Arte',
                    image: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?auto=format&fit=crop&w=500&q=80',
                    likes: 26,
                    available: 5,
                    seller: { name: 'David L.', university: 'UAM' }
                }
            ],
            'servicios': [
                {
                    title: 'Asesoría Académica Personalizada',
                    description: 'Tutorías individuales en matemáticas, física y programación por estudiante destacado.',
                    price: '€15/hora',
                    category: 'Servicios',
                    image: 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=500&q=80',
                    likes: 37,
                    available: 0,
                    seller: { name: 'Carlos M.', university: 'UPV' }
                },
                {
                    title: 'Diseño Gráfico para Redes',
                    description: 'Creación de contenido visual para tus redes sociales, ideal emprendedores.',
                    price: '€25/publicación',
                    category: 'Servicios',
                    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=500&q=80',
                    likes: 41,
                    available: 0,
                    seller: { name: 'Laura G.', university: 'UAB' }
                },
                {
                    title: 'Clases de Instrumentos Musicales',
                    description: 'Clases personalizadas de guitarra, piano o ukelele para todos los niveles.',
                    price: '€20/hora',
                    category: 'Servicios',
                    image: 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=500&q=80',
                    likes: 30,
                    available: 0,
                    seller: { name: 'Ana S.', university: 'UCM' }
                }
            ]
        };

        let cart = [];

        // Función para obtener todos los productos
        function getAllProducts() {
            const allProducts = [];
            Object.values(adsData).forEach(categoryProducts => {
                allProducts.push(...categoryProducts);
            });
            return allProducts;
        }

        // Función para obtener todos los productos de todas las categorías
        function getAllProducts() {
            const allProducts = [];
            Object.values(adsData).forEach(categoryProducts => {
                allProducts.push(...categoryProducts);
            });
            return allProducts;
        }

        // Función para desplazarse a una sección específica
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Funcionalidad para los modales
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function switchModal(fromModalId, toModalId) {
            closeModal(fromModalId);
            setTimeout(() => openModal(toModalId), 300);
        }

        // Cerrar modal al hacer clic fuera del contenido
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeModal(overlay.id);
                }
            });
        });

        // Funciones de autenticación
        async function login() {
            const Correo = document.getElementById('login-email').value;
            const Contrasenia = document.getElementById('login-password').value;

            if (!Correo || !Contrasenia) {
                alert('Completa todos los campos.');
                return;
            }

            const data = { Correo, Contrasenia };

            try {
                const res = await fetch('http://localhost:7187/api/Usuarios/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    alert(`Bienvenido, ${result.usuario}`);
                    closeModal('login-modal');
                    window.location.reload();
                } else {
                    alert(result.mensaje || 'Error al iniciar sesión');
                }

            } catch (error) {
                console.error('Error al iniciar sesión:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        }

        async function registrar() {
            const Nombre = document.getElementById('register-name').value;
            const Correo = document.getElementById('register-email').value;
            const Contrasenia = document.getElementById('register-password').value;
            const Confirm = document.getElementById('register-confirm').value;
            const Terms = document.getElementById('register-terms').checked;

            if (!Nombre || !Correo || !Contrasenia || !Confirm) {
                alert('Completa todos los campos.');
                return;
            }

            if (Contrasenia !== Confirm) {
                alert('Las contraseñas no coinciden.');
                return;
            }

            if (!Terms) {
                alert('Debes aceptar los términos y condiciones.');
                return;
            }

            const data = { Nombre, Correo, Contrasenia };

            try {
                const res = await fetch('http://localhost:7187/api/Usuarios/registro', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    alert(result.mensaje || 'Usuario registrado con éxito');
                    closeModal('register-modal');
                    // Cambiar al modal de login
                    setTimeout(() => openModal('login-modal'), 300);
                } else {
                    alert(result.mensaje || 'Error al registrar');
                }

            } catch (error) {
                console.error('Error al registrar:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        }

        // Función para renderizar productos
        function renderProducts(category) {
            const productGrid = document.querySelector('.product-grid');
            let products = [];
            
            if (category === 'todos') {
                products = getAllProducts();
            } else {
                products = adsData[category.toLowerCase()] || [];
            }
            
            productGrid.innerHTML = '';
            
            if (products.length === 0) {
                productGrid.innerHTML = `
                    <div class="no-products-message" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--gray-dark); margin-bottom: 1.5rem;"></i>
                        <h3 style="color: var(--text); margin-bottom: 1rem; font-size: 1.5rem;">No hay productos en esta categoría</h3>
                        <p style="color: var(--text-light); font-size: 1.1rem;">Prueba con otra categoría o vuelve más tarde.</p>
                    </div>
                `;
                return;
            }
            
            products.forEach(product => {
                const stockText = product.available === 0 ? 
                                 'Agotado' : 
                                 product.available < 5 ? 
                                 `Últimas ${product.available} unidades` : 
                                 `Disponible: ${product.available}`;
                                 
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.innerHTML = `
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.title}">
                        <button class="like-btn" onclick="toggleLike(this)">
                            <i class="fas fa-heart"></i>
                            <span class="like-count">${product.likes}</span>
                        </button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">${product.category}</div>
                        <h3 class="product-title">${product.title}</h3>
                        <p class="product-description">${product.description}</p>
                        <div class="product-meta">
                            <div class="product-price">${product.price}</div>
                            <div class="product-stock ${product.available === 0 ? 'out-of-stock' : ''}">${stockText}</div>
                        </div>
                        <div class="product-seller">
                            <div class="seller-avatar"></div>
                            <span class="seller-name">${product.seller.name}</span>
                            <span class="seller-university">${product.seller.university}</span>
                        </div>
                        <div class="product-actions">
                            <button class="action-btn buy-btn" onclick="buyProduct('${product.title}')">
                                <i class="fas fa-shopping-bag"></i> Comprar
                            </button>
                            <button class="action-btn cart-btn" onclick="addToCart(this, '${product.title}')">
                                <i class="fas fa-shopping-cart"></i> Carrito
                            </button>
                        </div>
                    </div>
                `;
                productGrid.appendChild(productCard);
            });
        }

        // Función para alternar like
        function toggleLike(button) {
            const likeCount = button.querySelector('.like-count');
            let count = parseInt(likeCount.textContent);
            
            if (button.classList.contains('liked')) {
                button.classList.remove('liked');
                count--;
            } else {
                button.classList.add('liked');
                count++;
            }
            
            likeCount.textContent = count;
        }

        // Funcionalidad para los filtros de categoría
        document.querySelectorAll('.category-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                document.querySelector('.category-filter.active').classList.remove('active');
                this.classList.add('active');
                
                const category = this.textContent.toLowerCase();
                renderProducts(category);
            });
        });

        // Inicializar productos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            renderProducts('todos');
        });

        // Cerrar menú al hacer clic en un enlace (en dispositivos móviles)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {

            });
        });

        // Validación básica de formularios
        document.querySelectorAll('.modal-submit').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const modalId = this.closest('.modal-overlay').id;
                
                if (modalId === 'login-modal') {
                    const email = document.getElementById('login-email').value;
                    const password = document.getElementById('login-password').value;
                    
                    if (email && password) {
                        console.log('Inicio de sesión simulado con éxito!');
                        closeModal('login-modal');
                    } else {
                        alert('Por favor, completa todos los campos');
                    }
                } else if (modalId === 'register-modal') {
                    const name = document.getElementById('register-name').value;
                    const email = document.getElementById('register-email').value;
                    const password = document.getElementById('register-password').value;
                    const confirm = document.getElementById('register-confirm').value;
                    const terms = document.getElementById('register-terms').checked;
                    
                    if (name && email && password && confirm && terms) {
                        if (password !== confirm) {
                            alert('Las contraseñas no coinciden');
                        } else {
                            console.log('Registro simulado con éxito!');
                            closeModal('register-modal');
                        }
                    } else {
                        alert('Por favor, completa todos los campos y acepta los términos');
                    }
                }
            });
        });

        // Efecto para labels flotantes en todos los inputs
        document.querySelectorAll('.input-group input').forEach(input => {
            if (input.value) {
                input.parentNode.classList.add('filled');
            }
            
            input.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentNode.classList.remove('focused');
                if (this.value) {
                    this.parentNode.classList.add('filled');
                } else {
                    this.parentNode.classList.remove('filled');
                }
            });
        });
        
        const API_URL = 'https://localhost:7187/api/Usuarios';

        // BOTÓN: Iniciar Sesión
        document.querySelector('#login-modal .modal-submit').addEventListener('click', async () => {
            const Correo = document.getElementById('login-email').value;
            const Contrasenia = document.getElementById('login-password').value;

            if (!Correo || !Contrasenia) {
                alert('Completa todos los campos.');
                return;
            }

            const data = { Correo, Contrasenia };

            try {
                const res = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    alert(`Bienvenido, ${result.usuario}`);
                    closeModal('login-modal'); 
                } else {
                    alert(result.mensaje || 'Error al iniciar sesión');
                }

            } catch (error) {
                console.error('Error al iniciar sesión:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });

        document.querySelector('#register-modal .modal-submit').addEventListener('click', async () => {
            const Nombre = document.getElementById('register-name').value;
            const Correo = document.getElementById('register-email').value;
            const Contrasenia = document.getElementById('register-password').value;
            const Confirm = document.getElementById('register-confirm').value;
            const Terms = document.getElementById('register-terms').checked;

            if (!Nombre || !Correo || !Contrasenia || !confirm) {
                alert('Completa todos los campos.');
                return;
            }

            if (Contrasenia !== Confirm) {
                alert('Las contraseñas no coinciden.');
                return;
            }

            if (!Terms) {
                alert('Debes aceptar los términos y condiciones.');
                return;
            }

            const data = { Nombre, Correo, Contrasenia };

            try {
                const res = await fetch(`${API_URL}/registro`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    alert(result.mensaje || 'Usuario registrado con éxito');
                    closeModal('register-modal'); 
                } else {
                    alert(result.mensaje || 'Error al registrar');
                }

            } catch (error) {
                console.error('Error al registrar:', error);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });

    </script>
</body>
</html>