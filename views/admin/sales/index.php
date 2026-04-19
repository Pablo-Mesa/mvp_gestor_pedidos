<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar me-2"></i>Facturación / Tickets</h1>
        <form action="index.php" method="GET" class="d-flex gap-2">
            <input type="hidden" name="route" value="sales_history">
            <input type="date" name="date" class="form-control" value="<?php echo $_GET['date'] ?? date('Y-m-d'); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Nro. Factura</th>
                            <th class="py-3">Fecha/Hora</th>
                            <th class="py-3">Pedido</th>
                            <th class="py-3">Cliente</th>
                            <th class="py-3">Cajero</th>
                            <th class="py-3 text-end">Total</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No se encontraron ventas para la fecha seleccionada.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td class="px-4 align-middle fw-bold"><?php echo $sale['nro_factura']; ?></td>
                                    <td class="align-middle"><?php echo date('d/m/Y H:i', strtotime($sale['fecha_hora'])); ?></td>
                                    <td class="align-middle text-center">
                                        <?php if ($sale['order_id_display']): ?>
                                            <a href="?route=orders_show&id=<?php echo $sale['order_id_display']; ?>" class="badge bg-light text-dark text-decoration-none">
                                                #<?php echo $sale['order_id_display']; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle"><?php echo htmlspecialchars($sale['client_name'] ?? 'Cliente Ocasional'); ?></td>
                                    <td class="align-middle small text-muted"><?php echo htmlspecialchars($sale['cashier_name'] ?? 'Sistema'); ?></td>
                                    <td class="align-middle text-end fw-bold text-success">
                                        Gs. <?php echo number_format($sale['total_venta'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($sale['estado'] == 1): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Emitida</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Anulada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 align-middle text-center">
                                        <div class="btn-group">
                                            <?php if ($sale['order_id_display']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Re-imprimir Ticket" onclick="printOrderDirectly(<?php echo $sale['order_id_display']; ?>, '80mm')">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Ver Detalle" onclick="viewSaleDetail(<?php echo $sale['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function viewSaleDetail(id) {
    // Por ahora, redirigimos al pedido vinculado ya que el detalle es el mismo.
    const sales = <?php echo json_encode($sales); ?>;
    const sale = sales.find(s => s.id == id);
    if (sale && sale.order_id_display) {
        window.location.href = `?route=orders_show&id=${sale.order_id_display}`;
    } else {
        Toast.fire("El detalle ampliado se habilitará en el módulo de facturación legal avanzada.", "info");
    }
}
</script>