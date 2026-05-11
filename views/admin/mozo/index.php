<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chair text-primary me-2"></i> Monitoreo de Mesas (Salón)
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                    <span class="badge bg-primary rounded-pill"><?php echo count($tableOrders); ?> Activas</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Mesa</th>
                                <th>Horario</th>
                                <th>Cliente / Canal</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tableOrders as $order): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                                                <?php echo $order['table_number']; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            <i class="far fa-clock me-1"></i> 
                                            <?php echo date('H:i', strtotime($order['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($order['user_name'] ?? 'Cliente Ocasional'); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($order['channel_name'] ?? 'Salón'); ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $statusLabel = 'Pendiente';
                                            $statusColor = 'warning';
                                            switch($order['status']) {
                                                case 'confirmed': $statusLabel = 'Confirmado'; $statusColor = 'info'; break;
                                                case 'preparing': $statusLabel = 'En Cocina'; $statusColor = 'primary'; break;
                                                case 'ready': $statusLabel = 'Listo'; $statusColor = 'success'; break;
                                                case 'completed': $statusLabel = 'Pagado'; $statusColor = 'secondary'; break;
                                                case 'cancelled': case 'rejected': $statusLabel = 'Anulado'; $statusColor = 'danger'; break;
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo $statusColor; ?> bg-opacity-10 text-<?php echo $statusColor; ?> border border-<?php echo $statusColor; ?> px-3">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-eye me-1"></i> Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($tableOrders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-utensils fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No hay pedidos registrados en mesas para hoy.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small py-3">
                <i class="fas fa-info-circle me-1"></i> Esta lista muestra todos los pedidos vinculados a una mesa específica realizados durante el día de hoy.
            </div>
        </div>
    </div>
</div>