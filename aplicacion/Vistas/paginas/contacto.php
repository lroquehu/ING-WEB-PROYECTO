<?php 
    $page_title = 'Contacto - UniEmprende';
    require_once 'aplicacion/Vistas/plantillas/header.php'; 
?>
<!-- Botón para volver atrás -->
<a href="javascript:history.back()" class="back-link" title="Volver atrás">
    <i class="fas fa-arrow-left"></i>
</a>

<style>
    /* Estilos para el botón de volver (copiados de acerca_de.php) */
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

    /* Estilos de la sección de contacto */
    .contact-section {
        padding: 8rem 1rem 4rem;
        background: #fff;
    }
    .contact-section .container {
        max-width: 900px;
        margin: 0 auto;
    }
    .contact-content h1 {
        font-size: 2.75rem;
        margin-bottom: 1rem;
        color: #2c3e50;
        text-align: center;
    }
    .contact-content p {
        font-size: 1.2rem;
        margin-bottom: 3rem;
        color: #666;
        text-align: center;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    .contact-form {
        background: #f8f9fa;
        padding: 2.5rem;
        border-radius: 12px;
    }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
    }
    .btn-submit {
        background: #910202;
        color: #fff;
        padding: 0.8rem 2rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.1rem;
        width: 100%;
    }
</style>

<section class="contact-section">
    <div class="container">
        <div class="contact-content">
            <h1>Contáctanos</h1>
            <p>¿Tienes alguna pregunta, sugerencia o necesitas soporte? Nuestro equipo está listo para ayudarte.</p>
            
            <div class="contact-form">
                <p style="text-align:center; font-size: 1.1rem;">Para cualquier consulta, puedes escribirnos a nuestro correo de soporte: <strong>soporte.uniemprende@gmail.com</strong></p>
            </div>
        </div>
    </div>
</section>

<?php 
    require_once 'aplicacion/Vistas/plantillas/footer.php'; 
?>