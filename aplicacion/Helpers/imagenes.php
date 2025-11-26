<?php

function obtenerImagenFinal($rutaRelativa) {
    if (empty($rutaRelativa)) {
        return null;
    }
    $clean = ltrim($rutaRelativa, '/\\');

    // Ruta física local
    $local_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . $clean;

    // Verificación principal
    if (file_exists($local_path) && strpos(BASE_URL, 'localhost') !== false) {
        // Existe localmente y no estamos en localhost → usar URL local
        return LOCAL_IMAGE_URL . $clean;
    }
    return PROD_IMAGE_URL . $clean;
}


