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
    <title>Política de Privacidad - UniEmprende</title>
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
        <h1>Política de Privacidad</h1>
        <p class="last-updated">Última actualización: 25 de noviembre de 2025</p>

        <p>En UniEmprende, respetamos su privacidad y nos comprometemos a proteger sus datos personales. Esta política de privacidad le informará sobre cómo cuidamos sus datos personales cuando visita nuestra plataforma y le informará sobre sus derechos de privacidad y cómo la ley lo protege.</p>

        <h2>1. Información que Recopilamos</h2>
        <p>Recopilamos y procesamos los siguientes datos sobre usted:</p>
        <ul>
            <li><strong>Información que usted nos proporciona:</strong> Esto incluye la información que proporciona al registrarse, como su nombre, apellidos, correo electrónico institucional, código universitario, facultad y escuela.</li>
            <li><strong>Información de su perfil:</strong> Cualquier información adicional que decida compartir en su perfil, como habilidades, proyectos, intereses, etc.</li>
            <li><strong>Datos de uso:</strong> Información sobre cómo utiliza nuestro Servicio, como las páginas que visita y las interacciones que realiza.</li>
        </ul>

        <h2>2. Cómo Usamos su Información</h2>
        <p>Utilizamos la información que tenemos sobre usted de las siguientes maneras:</p>
        <ul>
            <li>Para proporcionar, mantener y mejorar nuestro Servicio.</li>
            <li>Para gestionar su cuenta y perfil.</li>
            <li>Para permitirle comunicarse con otros usuarios.</li>
            <li>Para notificarle sobre cambios en nuestro Servicio.</li>
        </ul>

        <h2>3. Divulgación de su Información</h2>
        <p>Nos comprometemos a no vender, comercializar ni alquilar su información de identificación personal a terceros. Podemos compartir información genérica agregada (no vinculada a ninguna información de identificación personal) con nuestros socios comerciales, afiliados de confianza y anunciantes para los fines descritos anteriormente.</p>
        <p>Podemos divulgar su información personal solo en las siguientes circunstancias limitadas:</p>
        <ul>
            <li><strong>Con su consentimiento:</strong> Cuando usted acepta explícitamente compartir sus datos con terceros (por ejemplo, al aplicar a una convocatoria externa desde la plataforma).</li>
            <li><strong>Proveedores de servicios:</strong> Podemos emplear empresas e individuos externos para facilitar nuestro Servicio, quienes tienen acceso a su información personal solo para realizar estas tareas en nuestro nombre y están obligados a no divulgarla ni utilizarla para ningún otro fin.</li>
            <li><strong>Requerimientos legales:</strong> Si es requerido por ley o en respuesta a solicitudes válidas de las autoridades públicas (por ejemplo, un tribunal o una agencia gubernamental).</li>
        </ul>

        <h2>4. Seguridad de los Datos</h2>
        <p>Hemos implementado medidas de seguridad apropiadas para evitar que sus datos personales se pierdan accidentalmente, se usen o se accedan de forma no autorizada, se alteren o se divulguen. Sin embargo, ningún método de transmisión por Internet o de almacenamiento electrónico es 100% seguro.</p>

        <h2>5. Sus Derechos de Protección de Datos</h2>
        <p>Dependiendo de su ubicación y las leyes aplicables (como el RGPD o leyes locales de protección de datos), usted puede tener los siguientes derechos sobre sus datos personales:</p>
        <ul>
            <li><strong>Derecho de acceso:</strong> Tiene derecho a solicitar copias de sus datos personales.</li>
            <li><strong>Derecho de rectificación:</strong> Tiene derecho a solicitar que corrijamos cualquier información que crea que es inexacta o que completemos la información que crea que está incompleta.</li>
            <li><strong>Derecho al olvido (supresión):</strong> Tiene derecho a solicitar que borremos sus datos personales, bajo ciertas condiciones (por ejemplo, al cerrar su cuenta universitaria).</li>
            <li><strong>Derecho a restringir el procesamiento:</strong> Tiene derecho a solicitar que restrinjamos el procesamiento de sus datos personales.</li>
            <li><strong>Derecho a la portabilidad de datos:</strong> Tiene derecho a solicitar que transfiramos los datos que hemos recopilado a otra organización, o directamente a usted.</li>
        </ul>
        <p>Para ejercer cualquiera de estos derechos, por favor contáctenos a través del correo proporcionado a continuación.</p>

        <h2>6. Contacto</h2>
        <p>Si tiene alguna pregunta sobre esta Política de Privacidad, puede contactarnos a través de <a href="mailto:privacidad@uniemprende.com" style="color: #910202;">privacidad@uniemprende.com</a>.</p>

        <div style="text-align: center;">
            <a href="javascript:window.close();" class="btn-back">Volver</a>
        </div>
    </div>
</body>
</html>