<?php
// Configuración dinámica de estilos según el formato solicitado
$is58mm = (isset($format) && $format === '58mm');
$ticketWidth = $is58mm ? '48mm' : '300px'; // 48mm es el área segura para impresoras de 58mm
$baseFontSize = $is58mm ? '11px' : '14px';
$containerPadding = $is58mm ? '2px' : '15px';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solver | Ticket #<?php echo $order['id']; ?></title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #eee;
            display: flex;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .ticket {
            width: <?php echo $ticketWidth; ?>;
            background: white;
            padding: <?php echo $containerPadding; ?>;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-size: <?php echo $baseFontSize; ?>;
        }
        h2, h3 { text-align: center; margin: 5px 0; text-transform: uppercase; font-size: 1.2em; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .item-row { display: flex; margin-bottom: 5px; }
        .qty { width: <?php echo $is58mm ? '20px' : '30px'; ?>; font-weight: bold; }
        .name { flex: 1; }
        .price { text-align: right; }
        .total { text-align: right; font-weight: bold; font-size: 1.2em; margin-top: 10px; }
        
        .obs-box {
            border: 2px solid #000;
            padding: 5px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 1.1em;
            text-align: center;
        }

        /* Ocultar elementos al imprimir si es necesario */
        @media print {
            body { background: white; padding: 0; }
            .ticket { width: 100%; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="ticket">
        <h2>COMEDOR APP</h2>
        <p style="text-align: center; font-size: 0.8rem;">Ticket de Cocina</p>
        
        <div class="divider"></div>

        <div class="info-row">
            <span>Pedido:</span>
            <strong>#<?php echo $order['id']; ?></strong>
        </div>
        <div class="info-row">
            <span>Fecha:</span>
            <span><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></span>
        </div>
        <div class="info-row">
            <span>Cliente:</span>
            <span><?php echo htmlspecialchars($order['user_name']); ?></span>
        </div>
        <div class="info-row">
            <span>Tipo:</span>
            <strong>
                <?php 
                    if($order['delivery_type'] == 'delivery') echo 'DELIVERY 🛵';
                    elseif($order['delivery_type'] == 'pickup') echo 'RETIRO 🛍️';
                    else echo 'MESA 🍽️';
                ?>
            </strong>
        </div>

        <div class="divider"></div>

        <?php foreach($details as $item): ?>
            <div class="item-row">
                <div class="qty"><?php echo $item['quantity']; ?></div>
                <div class="name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                <!-- Opcional: mostrar precio en cocina, a veces no se necesita -->
                <!-- <div class="price"><?php echo number_format($item['price'] * $item['quantity'], 0); ?></div> -->
            </div>
        <?php endforeach; ?>

        <div class="divider"></div>

        <!-- Observaciones resaltadas para la cocina -->
        <?php if($order['observation']): ?>
            <div class="obs-box">
                <?php echo htmlspecialchars($order['observation']); ?>
            </div>
        <?php endif; ?>

        <?php if($order['delivery_type'] == 'delivery'): ?>
            <p style="margin-top: 10px; font-size: 0.85rem;">
                <strong>Dirección:</strong><br>
                <?php echo htmlspecialchars($order['delivery_address']); ?>
            </p>
        <?php endif; ?>

        <div class="divider"></div>
        <p style="text-align: center;">*** FIN TICKET ***</p>
    </div>

</body>
</html>