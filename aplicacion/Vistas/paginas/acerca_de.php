<?php 
    $page_title = 'Acerca de Nosotros - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php'; 
?>
<!-- Botón para volver atrás -->
<a href="javascript:history.back()" class="back-link" title="Volver atrás">
    <i class="fas fa-arrow-left"></i>
</a>

<style>
    /* Similar styles to other pages for consistency */
    /* Estilos para el botón de volver */
    .back-link {
        position: fixed;
        top: 9rem;
        left: calc(50% - 600px - 5rem); /* Posiciona el botón a la izquierda del contenido */
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background-color: #f0f2f5;
        border-radius: 50%;
        color: var(--primary-color, #910202);
        font-size: 1.2rem;
        text-decoration: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .back-link:hover {
        background-color: #e4e6e9;
        transform: scale(1.05);
    }
    @media (max-width: 1400px) {
        .back-link {
            left: 2rem; /* Fallback para pantallas más pequeñas */
        }
    }
    @media (max-width: 768px) {
        .back-link {
            display: none; /* Ocultamos en móvil para no estorbar */
        }
    }

    .about-section {
        padding: 8rem 1rem 4rem;
        background: #fff;
    }
    .about-section .container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .about-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 3rem;
        align-items: center;
    }
    .about-content h1 {
        font-size: 2.75rem;
        margin-bottom: 1.5rem;
        color: #2c3e50; /* secondary-color */
        text-align: center;
        border-bottom: 3px solid #910202; /* primary-color */
        display: inline-block;
        padding-bottom: 0.5rem;
    }
    .about-content p {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        color: #666; /* text-light */
        line-height: 1.7;
        text-align: justify;
    }
    .about-features {
        margin: 2.5rem 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        background: #f8f9fa; /* bg-light */
        padding: 1.5rem;
        border-radius: 12px;
        border-left: 5px solid #910202; /* primary-color */
    }
    .feature-icon {
        background: #910202; /* primary-color */
        color: #fff;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.5rem;
    }
    .feature-text h4 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #2c3e50; /* secondary-color */
    }
    .feature-text p {
        margin: 0;
        font-size: 1rem;
        text-align: left;
    }
    body::before { display: none; }
</style>

<section class="about-section">
    <div class="container">
        <div class="about-container">
            <div class="about-content">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h1>Acerca de UniEmprende</h1>
                </div>
                <p>UniEmprende nació con la visión de crear un ecosistema vibrante donde los estudiantes universitarios puedan mostrar y comercializar sus creaciones, productos y servicios. Somos una plataforma dedicada al fomento del emprendimiento dentro de la comunidad universitaria.</p>
                <p>Nuestra misión es impulsar el talento joven y fomentar el espíritu emprendedor, proporcionando las herramientas necesarias para que los estudiantes puedan convertir sus ideas en proyectos reales y sostenibles, conectando a compradores y vendedores en un entorno seguro y de confianza.</p>
                
                <div class="about-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Comercio Seguro</h4>
                            <p>Transacciones protegidas y verificación de usuarios para tu tranquilidad.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Comunidad Verificada</h4>
                            <p>Todos nuestros usuarios son estudiantes universitarios verificados.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Crecimiento Constante</h4>
                            <p>Herramientas diseñadas para el crecimiento de tu emprendimiento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>