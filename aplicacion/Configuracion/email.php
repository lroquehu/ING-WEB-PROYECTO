<?php
// aplicacion/Configuracion/email.php

// Configuración para el envío de correos con PHPMailer
// RECOMENDACIÓN: Usar una "Contraseña de Aplicación" si usas Gmail.
// Búscala en la configuración de seguridad de tu cuenta de Google.

define('MAIL_HOST', 'smtp.gmail.com');          // Servidor SMTP. Para Gmail es 'smtp.gmail.com'
define('MAIL_USERNAME', 'soporte.uniemprende@gmail.com');    // Tu dirección de correo de Gmail
define('MAIL_PASSWORD', 'eslk pghc jkyw wqxh'); // La contraseña de aplicación generada en Google
define('MAIL_PORT', 587);                        // Puerto SMTP (587 para TLS, que es lo más común)
define('MAIL_ENCRYPTION', 'tls');                // Tipo de encriptación: 'tls' o 'ssl'

define('MAIL_FROM_ADDRESS', 'soporte.uniemprende@gmail.com'); // El correo que aparecerá como remitente
define('MAIL_FROM_NAME', 'Soporte UniEmprende');   // El nombre que aparecerá como remitente
?>