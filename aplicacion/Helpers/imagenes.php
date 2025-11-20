<?php

function obtenerImagenFinal($rutaRelativa) {
    if (empty($rutaRelativa)) {
        return null;
    }

    // Normalizar
    $clean = ltrim($rutaRelativa, '/\\');

    // Documento root (ruta física)
    $local_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . $clean;
    echo($local_path);
    // 1. Si existe localmente → devolver URL local
    if (file_exists($local_path)) {
        return LOCAL_IMAGE_URL . $clean;
    }
    echo(file_exists($local_path));
    echo(LOCAL_IMAGE_URL . $clean);
    echo(PROD_IMAGE_URL . $clean);
    // 2. Si no existe → usar URL de producción
    return PROD_IMAGE_URL . $clean;
}
