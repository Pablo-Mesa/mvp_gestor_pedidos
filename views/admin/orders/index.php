<style>
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; }    

    .contenedor-tabla {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 8px; /* Movemos los bordes aquí para que enmarquen el scroll */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        background: white;
    }

        /* El secreto para el encabezado fijo */
    thead th {
        position: sticky;
        top: 0;           /* Se queda pegado arriba */
        z-index: 10;      /* Asegura que quede por encima del contenido del tbody */
        background-color: #f8f9fa; /* Usamos el mismo gris claro de tus th originales */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Opcional: añade una sombrita para dar profundidad */
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h2>📦 Gestión de Pedidos</h2>
</div>
<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($orders)): ?>
                <tr><td colspan="6" style="padding: 20px; text-align: center;">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>Gs. <?php echo number_format($order['total'], 0); ?></td>
                        <td>
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
                        <td>
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