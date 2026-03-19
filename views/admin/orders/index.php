<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>📦 Gestión de Pedidos</h2>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8f9fa; text-align: left;">
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">ID</th>
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Cliente</th>
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Fecha</th>
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Total</th>
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Estado</th>
                <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($orders)): ?>
                <tr><td colspan="6" style="padding: 20px; text-align: center;">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">#<?php echo $order['id']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">$<?php echo number_format($order['total'], 2); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                            <?php 
                                $statusColors = [
                                    'pending' => '#ffc107', 
                                    'completed' => '#28a745', 
                                    'cancelled' => '#dc3545'
                                ];
                                $color = $statusColors[$order['status']] ?? '#6c757d';
                                $statusText = ucfirst($order['status']);
                            ?>
                            <span style="background-color: <?php echo $color; ?>; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo $statusText; ?>
                            </span>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                            <a href="?route=orders_show&id=<?php echo $order['id']; ?>" 
                               style="background-color: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9rem;">
                                👁️ Ver Detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>