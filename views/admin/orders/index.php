<h2 class="mb-4">Gestión de Pedidos</h2>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo Entrega</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($orders) > 0): ?>
                        <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td>
                                    <?php if($order['delivery_type'] == 'delivery'): ?>
                                        <span class="badge bg-info text-dark">Delivery</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Retiro</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ucfirst($order['payment_method']); ?></td>
                                <td class="fw-bold">$<?php echo number_format($order['total'], 2); ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?php echo ucfirst($order['status']); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary">Ver Detalles</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay pedidos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>