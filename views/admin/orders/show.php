<div style="margin-bottom: 1rem;">
    <a href="?route=orders" style="color: #007bff; text-decoration: none;">&larr; Volver a Pedidos</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    
    <!-- Columna Izquierda: Detalles del Pedido -->
    <div class="card">
        <h3>📋 Detalle del Pedido #<?php echo $order['id']; ?></h3>
        <p style="color: #666; margin-bottom: 1rem;">Realizado el: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr style="background-color: #f1f1f1;">
                    <th style="padding: 8px; text-align: left;">Producto</th>
                    <th style="padding: 8px; text-align: center;">Cant.</th>
                    <th style="padding: 8px; text-align: right;">Precio Unit.</th>
                    <th style="padding: 8px; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($details as $item): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">$<?php echo number_format($item['price'], 2); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.2rem;">$<?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Columna Derecha: Info Cliente y Estado -->
    <div>
        <div class="card" style="margin-bottom: 1rem;">
            <h3>👤 Cliente</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
            
            <h3>🚚 Entrega</h3>
            <p><strong>Tipo:</strong> <?php echo ($order['delivery_type'] == 'delivery') ? 'Domicilio' : 'Retiro en Local'; ?></p>
            <?php if($order['delivery_type'] == 'delivery'): ?>
                <p><strong>Dirección:</strong><br><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
            <?php endif; ?>
            <p><strong>Pago:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
        </div>

        <div class="card" style="border-left: 4px solid #ffc107;">
            <h3>⚙️ Gestión</h3>
            <form action="?route=orders_update_status" method="POST">
                <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Estado del Pedido:</label>
                    <select name="status" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>🟡 Pendiente</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>🟢 Completado / Entregado</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>🔴 Cancelado</option>
                    </select>
                </div>

                <button type="submit" style="width: 100%; background-color: #007bff; color: white; border: none; padding: 0.7rem; border-radius: 4px; cursor: pointer;">
                    Actualizar Estado
                </button>
            </form>
        </div>
    </div>
</div>