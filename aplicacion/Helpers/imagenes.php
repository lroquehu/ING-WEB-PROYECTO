<?php

function obtenerImagenFinal($rutaRelativa) {
    if (empty($rutaRelativa)) {
        echo "No hay ruta relativa.\n";
        return null;
    }

    // Normalizar
    $clean = ltrim($rutaRelativa, '/\\');
    echo "Ruta limpia: $clean\n";

    // Documento root (ruta física)
    $local_path = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . $clean;
    echo "Ruta física completa: $local_path\n";

    // Verificar si el archivo existe
    if (file_exists($local_path)) {
        echo "Archivo encontrado localmente.\n";
        echo "URL local: " . LOCAL_IMAGE_URL . $clean . "\n";
        return LOCAL_IMAGE_URL . $clean;
    } else {
        echo "Archivo NO encontrado localmente.\n";
        echo "Usando URL de producción: " . PROD_IMAGE_URL . $clean . "\n";
    }

    // URL de producción
    return PROD_IMAGE_URL . $clean;
}

}
