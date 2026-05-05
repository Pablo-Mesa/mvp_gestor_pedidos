<?php
$ticketWidth = (isset($format) && $format === '58mm') ? '48mm' : '300px';
$fontSize = (isset($format) && $format === '58mm') ? '11px' : '13px';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Courier New', monospace; font-size: <?php echo $fontSize; ?>; padding: 0; margin: 0; background: #eee; display: flex; justify-content: center; }
        .ticket { width: <?php echo $ticketWidth; ?>; background: white; padding: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        .text-end { text-align: right; }
        .header-info { margin-bottom: 10px; line-height: 1.2; }
        @media print { body { background: white; } .ticket { box-shadow: none; width: 100%; } }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="center header-info">
            <h2 style="margin:0; font-size: 1.4em;"><?php echo htmlspecialchars($empresa['razon_social'] ?? 'SOLVER POS'); ?></h2>
            <?php if(!empty($empresa['ruc'])): ?>
                <div>RUC: <?php echo $empresa['ruc']; ?>-<?php echo $empresa['dv']; ?></div>
            <?php endif; ?>
            <div><?php echo htmlspecialchars($empresa['direccion'] ?? ''); ?></div>
            <div>Tel: <?php echo htmlspecialchars($empresa['telefono'] ?? ''); ?></div>
        </div>

        <?php 
            $isFactura = strpos($sale['nro_factura'], 'FAC-') === 0;
        ?>
        <div class="center bold" style="margin: 10px 0;">
            <?php if($isFactura): ?>
                FACTURA COMERCIAL
            <?php else: ?>
                COMPROBANTE DE VENTA<br>
                (CONTROL INTERNO)
            <?php endif; ?>
        </div>

        <div class="divider"></div>
        
        <div>Ticket Nro: <span class="bold"><?php echo $sale['nro_factura']; ?></span></div>
        <div>Fecha/Hora: <?php echo date('d/m/Y H:i', strtotime($sale['fecha_hora'])); ?></div>
        <div>Cliente: <?php echo htmlspecialchars($sale['client_name'] ?? 'Cliente Ocasional'); ?></div>
        <?php if(!empty($sale['client_ruc'])): ?>
            <div>RUC/CI: <?php echo $sale['client_ruc']; ?></div>
        <?php endif; ?>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">DESCRIPCIÓN</th>
                    <th class="text-end">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($details as $item): ?>
                    <tr>
                        <td colspan="2"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px; font-size: 0.9em;">
                            <?php echo $item['cantidad']; ?> x <?php echo number_format($item['precio_unitario_venta'], 0, ',', '.'); ?>
                        </td>
                        <td class="text-end"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="bold" style="font-size: 1.2em;">
            <tr>
                <td>TOTAL Gs.</td>
                <td class="text-end"><?php echo number_format($sale['total_venta'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <div style="font-size: 0.85em;">
            <div>LIQUIDACIÓN DEL IVA:</div>
            <table>
                <tr>
                    <td>Grav. 10%: <?php echo number_format($sale['gravada_10'], 0, ',', '.'); ?></td>
                    <td class="text-end">IVA 10%: <?php echo number_format($sale['iva_10'], 0, ',', '.'); ?></td>
                </tr>
            </table>
            <div class="bold">TOTAL IVA: Gs. <?php echo number_format($sale['iva_10'], 0, ',', '.'); ?></div>
        </div>

        <div class="divider"></div>

        <div class="center">
            <p>¡Gracias por su preferencia!</p>
            <p style="font-size: 0.8em;">*** Software desarrollado por Solver ***</p>
        </div>
    </div>
</body>
</html>