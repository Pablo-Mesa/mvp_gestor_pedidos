<div class="header-actions">
    <h1 class="page-title">Gestión de Pedidos</h1>
</div>

<style>
    /* Reutilizamos estilos de products/index.php para consistencia */
    .header-actions {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem; background-color: #fff; padding: 1rem;
        border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .page-title { margin: 0; font-size: 1.5rem; color: #333; }
    
    .contenedor-tabla {
        max-height: 600px; overflow-y: auto; border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); background: white;
    }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; position: sticky; top: 0; }
    
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
    /* Colores de estado */
    .status-pending { background: #ffc107; color: #333; } /* Amarillo */
    .status-confirmed { background: #17a2b8; color: #fff; } /* Azul Info */
    .status-completed { background: #28a745; color: #fff; } /* Verde */
    .status-cancelled { background: #dc3545; color: #fff; } /* Rojo */

    .btn-view { padding: 5px 10px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem; }
    .btn-view:hover { background: #0056b3; }
</style>

<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Entrega</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($orders)): ?>
                <tr><td colspan="7" style="text-align:center; padding: 2rem;">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['user_name']); ?>
                        </td>
                        <td>
                            <?php if($order['delivery_type'] == 'delivery'): ?>
                                <span style="color:#d63384;"><i class="fas fa-motorcycle"></i> Delivery</span>
                            <?php elseif($order['delivery_type'] == 'pickup'): ?>
                                <span style="color:#0d6efd;"><i class="fas fa-walking"></i> Retiro</span>
                            <?php else: ?>
                                <span style="color:#fd7e14;"><i class="fas fa-utensils"></i> Local</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: bold;">
                            Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </td>
                        <td>
                            <?php 
                                $statusClass = 'status-pending';
                                switch($order['status']) {
                                    case 'confirmed': $statusClass = 'status-confirmed'; break;
                                    case 'completed': $statusClass = 'status-completed'; break;
                                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                                }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php 
                                    $statusMap = ['pending'=>'Pendiente', 'confirmed'=>'Confirmado', 'completed'=>'Entregado', 'cancelled'=>'Cancelado'];
                                    echo $statusMap[$order['status']] ?? $order['status']; 
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn-view">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>