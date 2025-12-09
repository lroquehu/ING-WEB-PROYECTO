<?php 
    $page_title = 'Preguntas Frecuentes - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php'; 
?>
<!-- Botón para volver atrás -->
<a href="javascript:history.back()" class="back-link" title="Volver atrás">
    <i class="fas fa-arrow-left"></i>
</a>

<style>
    /* Estilos para el botón de volver */
    .back-link {
        position: fixed;
        top: 9rem;
        left: calc(50% - 600px - 5rem);
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
    @media (max-width: 1400px) { .back-link { left: 2rem; } }
    @media (max-width: 768px) { .back-link { display: none; } }
    body::before { display: none; }

    /* Estilos de la sección de FAQ */
    .faq-section {
        padding: 8rem 1rem 4rem;
        background: #fff;
    }
    .faq-section .container {
        max-width: 900px;
        margin: 0 auto;
    }
    .faq-content h1 {
        font-size: 2.75rem;
        margin-bottom: 3rem;
        color: #2c3e50;
        text-align: center;
    }
    .accordion-item {
        border-bottom: 1px solid #e5e7eb;
    }
    .accordion-header {
        padding: 1.5rem 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .accordion-header i {
        transition: transform 0.3s ease;
    }
    .accordion-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out, padding 0.4s ease;
        padding: 0 1rem;
    }
    .accordion-body p {
        padding-bottom: 1.5rem;
        color: #666;
        line-height: 1.7;
    }
    .accordion-item.active .accordion-header i {
        transform: rotate(180deg);
    }
</style>

<section class="faq-section">
    <div class="container">
        <div class="faq-content">
            <h1>Preguntas Frecuentes (FAQ)</h1>
            
            <div class="accordion">
                <div class="accordion-item">
                    <div class="accordion-header">
                        <span>¿Qué es UniEmprende?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-body">
                        <p>UniEmprende es una plataforma exclusiva para la comunidad universitaria, diseñada para que estudiantes puedan comprar, vender y ofrecer productos y servicios, fomentando el espíritu emprendedor.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <span>¿Quién puede registrarse?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-body">
                        <p>El registro está limitado a estudiantes con un correo institucional verificado de una universidad asociada. Esto garantiza un entorno seguro y de confianza.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <span>¿Cómo puedo vender un producto?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-body">
                        <p>Una vez registrado y con tu sesión iniciada, haz clic en "Publicar Producto", completa el formulario con los detalles de tu artículo o servicio, sube algunas fotos y ¡listo! Tu publicación estará visible para toda la comunidad.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <span>¿Es seguro comprar en la plataforma?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-body">
                        <p>Sí. Fomentamos un comercio seguro a través de la verificación de usuarios y un sistema de valoraciones. Recomendamos siempre coordinar entregas en lugares públicos y seguros, como el campus universitario.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        header.addEventListener('click', () => {
            const currentlyActive = document.querySelector('.accordion-item.active');
            if (currentlyActive && currentlyActive !== item) {
                currentlyActive.classList.remove('active');
                currentlyActive.querySelector('.accordion-body').style.maxHeight = 0;
            }

            item.classList.toggle('active');
            const body = item.querySelector('.accordion-body');
            if (item.classList.contains('active')) {
                body.style.maxHeight = body.scrollHeight + 'px';
            } else {
                body.style.maxHeight = 0;
            }
        });
    });
});
</script>

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>