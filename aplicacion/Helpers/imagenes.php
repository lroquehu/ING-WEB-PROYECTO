<?php

function obtenerImagenFinal($rutaRelativa) {
    if (empty($rutaRelativa)) {
        echo "No hay ruta relativa.\n";
        return null;
    }

    // Normalizar
    $clean = ltrim($rutaRelativa, '/\\');

    // Ruta física local
    $local_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . $clean;

    // --- Debug simple ---
    echo "Ruta relativa: $rutaRelativa<br>";
    echo "Ruta limpia: $clean<br>";
    echo "Ruta física local: $local_path<br>";
    echo "Archivo existe localmente? " . (file_exists($local_path) ? 'Sí' : 'No') . "<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    echo "PROD_IMAGE_URL: " . PROD_IMAGE_URL . "<br>";
    echo "<hr>";

    // Verificación principal
    if (file_exists($local_path) && strpos(BASE_URL, 'localhost') === !false) {
        return BASE_URL . $clean;
    }

    // Si no existe localmente o estamos en localhost → usar URL de producción
    return PROD_IMAGE_URL . $clean;
}

