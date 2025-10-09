 
<?php include 'aplicacion/Vistas/plantillas/encabezado.php'; ?>

<!-- Hero Section -->
<section id="hero" class="hero">
    <div class="hero-content">
        <h1>Conectando Emprendedores Universitarios</h1>
        <p>Descubre productos y servicios creados por estudiantes emprendedores de todas las universidades.</p>
    </div>
</section>

<!-- Categorías -->
<section id="categories" class="categories">
    <h2 class="section-title">Categorías</h2>
    <div class="category-filters">
        <div class="category-filter active">Todos</div>
        <div class="category-filter">Tecnologia</div>
        <div class="category-filter">Moda</div>
        <div class="category-filter">Accesorios</div>
        <div class="category-filter">Alimentacion</div>
        <div class="category-filter">Arte</div>
        <div class="category-filter">Servicios</div>
    </div>
</section>

<!-- Productos -->
<section id="products" class="products">
    <h2 class="section-title">Productos Destacados</h2>
    <div class="product-grid">

    </div>
</section>

<!-- Contacto Section -->
    <section id="contact" class="contact">
        <div class="contact-container">
            <div class="contact-content">
                <h2>Contacto</h2>
                <p>¿Tienes alguna pregunta o necesitas ayuda? No dudes en contactarnos. Estamos aquí para ayudarte en todo lo que necesites.</p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Av. Miraflores, Tacna, Perú</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+51 935 812 499</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@uniemprende.pe</span>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h3>Envíanos un mensaje</h3>
                <form>
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" id="name" placeholder="ingrese su nombre">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="ingese su e-mail">
                    </div>
                    <div class="form-group">
                        <label for="message">Mensaje</label>
                        <textarea id="message" placeholder="Escribe tu mensaje aquí"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>


<!-- Sobre Nosotros Section -->
<section id="about" class="about">

</section>

<?php include 'aplicacion/Vistas/plantillas/pie.php'; ?>