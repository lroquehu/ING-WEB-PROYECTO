<?php 
// Asegúrate de que $datos_recibo esté disponible aquí desde el controlador
// Si no, deberás obtenerlo aquí (aunque lo ideal es en el controlador)
if(!isset($datos_recibo)) {
    // Lógica de emergencia si se accede directo (mejor usar controlador)
    echo "Cargando recibo..."; 
    // Aquí deberías redirigir o mostrar error si no hay datos
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Venta #<?php echo $datos_recibo['mp_payment_id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px; }
        .ticket {
            background: white; max-width: 400px; margin: 0 auto; padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 8px;
            border-top: 5px solid #910202;
        }
        h1 { text-align: center; font-size: 1.5rem; color: #333; }
        .divider { border-bottom: 2px dashed #eee; margin: 20px 0; }
        .item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { font-weight: bold; font-size: 1.2rem; color: #910202; }
        .footer { text-align: center; font-size: 0.8rem; color: #777; margin-top: 20px; }
        .btn-print { display: block; width: 100%; padding: 10px; background: #333; color: white; text-align: center; text-decoration: none; margin-top: 20px; border-radius: 5px; cursor: pointer;}
    </style>
</head>
<body>
    <div class="ticket">
        <h1>UniEmprende<br><small style="font-size:0.9rem; color:#777;">Comprobante de Pago</small></h1>
        
        <div class="divider"></div>
        
        <div class="item">
            <span>Fecha:</span>
            <span><?php echo date('d/m/Y H:i', strtotime($datos_recibo['fecha_pago'])); ?></span>
        </div>
        <div class="item">
            <span>Operación:</span>
            <span>#<?php echo $datos_recibo['mp_payment_id']; ?></span>
        </div>
        <div class="item">
            <span>Vendedor:</span>
            <span><?php echo htmlspecialchars($datos_recibo['vendedor_nombre']); ?></span>
        </div>
        <div class="item">
            <span>Comprador:</span>
            <span><?php echo htmlspecialchars($datos_recibo['comprador_nombre']); ?></span>
        </div>

        <div class="divider"></div>

        <div class="item" style="font-weight: bold;">
            <span>Producto</span>
            <span>Importe</span>
        </div>
        <div class="item">
            <span><?php echo htmlspecialchars($datos_recibo['titulo']); ?></span>
            <span>S/ <?php echo number_format($datos_recibo['monto'], 2); ?></span>
        </div>

        <div class="divider"></div>

        <div class="item total">
            <span>TOTAL</span>
            <span>S/ <?php echo number_format($datos_recibo['monto'], 2); ?></span>
        </div>

        <div class="footer">
            <p>Este es un comprobante electrónico generado automáticamente.</p>
        </div>

        <button onclick="window.print()" class="btn-print">Imprimir / Guardar PDF</button>
    </div>
</body>
</html>