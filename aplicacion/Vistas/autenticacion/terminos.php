<?php
    // Definir BASE_URL si no está definida (para acceso directo)
    if (!defined('BASE_URL')) {
        // Asume que la estructura es htdocs/ING-WEB-PROYECTO/
        $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '/ING-WEB-PROYECTO/';
        define('BASE_URL', $baseURL);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - UniEmprende</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #910202;
            border-bottom: 2px solid #e1e1e1;
            padding-bottom: 0.5rem;
            margin-top: 1.5rem;
        }
        h1 {
            text-align: center;
            font-size: 2rem;
            border-bottom: 3px solid #910202;
        }
        p, ul {
            margin-bottom: 1rem;
            text-align: justify;
        }
        ul {
            padding-left: 1.5rem;
        }
        .last-updated {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-bottom: 2rem;
        }
        .btn-back {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.7rem 1.5rem;
            background-color: #910202;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-back:hover {
            background-color: #700101;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Términos y Condiciones de Uso</h1>
        <p class="last-updated">Última actualización: 25 de noviembre de 2025</p>

        <p><strong>POR FAVOR, LEA ESTOS TÉRMINOS Y CONDICIONES DETENIDAMENTE ANTES DE UTILIZAR NUESTRA PLATAFORMA.</strong></p>

        <h2>1. Aceptación de los Términos</h2>
        <p>Al acceder y utilizar la plataforma UniEmprende (en adelante, "el Servicio"), usted acepta y se compromete a cumplir con los términos y condiciones aquí descritos. Si no está de acuerdo con alguna parte de los términos, no podrá acceder al Servicio.</p>

        <h2>2. Descripción del Servicio</h2>
        <p>UniEmprende es una plataforma diseñada para conectar a la comunidad universitaria, fomentando la colaboración en proyectos, la búsqueda de oportunidades y el desarrollo de emprendimientos. El Servicio incluye perfiles de usuario, publicación de proyectos, mensajería y otras herramientas de networking.</p>

        <h2>3. Obligaciones del Usuario</h2>
        <p>Usted se compromete a:</p>
        <ul>
            <li>Proporcionar información veraz, precisa y actualizada durante el registro y en su perfil.</li>
            <li>Utilizar el Servicio de manera ética y profesional, respetando a los demás miembros de la comunidad.</li>
            <li>No utilizar el Servicio para fines ilegales, fraudulentos o no autorizados.</li>
            <li>Mantener la confidencialidad de su contraseña y ser responsable de todas las actividades que ocurran en su cuenta.</li>
        </ul>

        <h2>4. Propiedad Intelectual</h2>
        <p>El Servicio y su contenido original (excluyendo el contenido proporcionado por los usuarios), características y funcionalidades son y seguirán siendo propiedad exclusiva de UniEmprende y sus licenciantes. El Servicio está protegido por derechos de autor, marcas registradas y otras leyes tanto nacionales como extranjeras.</p>
        <p>Respecto al contenido que usted publique (como proyectos, ideas o documentos): Usted conserva todos los derechos de autor sobre su contenido. Sin embargo, al publicarlo en la plataforma, concede a UniEmprende una licencia limitada, no exclusiva y libre de regalías para usar, modificar, mostrar y distribuir dicho contenido únicamente con el propósito de operar y promocionar el Servicio dentro de la comunidad.</p>

        <h2>5. Limitación de Responsabilidad</h2>
        <p>En la máxima medida permitida por la ley aplicable, UniEmprende, sus directores, empleados, socios o agentes no serán responsables de ningún daño indirecto, incidental, especial, consecuente o punitivo, incluyendo, sin limitación, pérdida de beneficios, datos, uso, fondo de comercio u otras pérdidas intangibles, resultantes de:</p>
        <ul>
            <li>(i) Su acceso o uso o la imposibilidad de acceder o utilizar el Servicio;</li>
            <li>(ii) Cualquier conducta o contenido de cualquier tercero en el Servicio;</li>
            <li>(iii) Cualquier contenido obtenido del Servicio; y</li>
            <li>(iv) El acceso no autorizado, uso o alteración de sus transmisiones o contenido.</li>
        </ul>
        <p>UniEmprende actúa como un intermediario para conectar usuarios y no garantiza el éxito, la legalidad o la viabilidad de los proyectos o colaboraciones iniciadas a través de la plataforma.</p>

        <h2>6. Modificaciones a los Términos</h2>
        <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Le notificaremos de cualquier cambio publicando los nuevos términos en esta página. Se le aconseja revisar esta página periódicamente para cualquier cambio.</p>

        <h2>7. Contacto</h2>
        <p>Si tiene alguna pregunta sobre estos Términos y Condiciones, puede contactarnos a través de <a href="mailto:soporte@uniemprende.com" style="color: #910202;">soporte@uniemprende.com</a>.</p>

        <div style="text-align: center;">
            <a href="javascript:window.close();" class="btn-back">Volver</a>
        </div>
    </div>
</body>
</html>