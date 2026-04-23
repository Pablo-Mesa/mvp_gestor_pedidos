<?php
// Aseguramos que el modelo Order esté cargado, ya que se usa en el modal.
// Esto evita el error "Class Order not found" si el controlador no lo cargó.
if (!class_exists('Order')) {
    require_once '../models/Order.php';
}
$orderModel = new Order();
$pendingInvoices = $orderModel->getOrdersAwaitingInvoice();
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar me-2"></i>Facturación / Tickets</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" id="btn-open-facturar" data-bs-toggle="modal" data-bs-target="#modalPendingOrders">
                <i class="fas fa-plus-circle me-1"></i> Facturar Pedido <small style="font-size: 0.7rem; opacity: 0.8;">[Alt + F]</small>
            </button>
            <form action="index.php" method="GET" class="d-flex gap-2">
            <input type="hidden" name="route" value="sales_history">
            <input type="date" name="date" class="form-control" value="<?php echo $_GET['date'] ?? date('Y-m-d'); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead class="bg-light sticky-top" style="top: 0; z-index: 10; background-color: #f8f9fa !important; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
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
                                        <?php if ($sale['estado'] == 0): ?>
                                            <span class="badge bg-danger">Anulada</span>
                                        <?php elseif ($sale['is_paid'] > 0): ?>
                                            <span class="badge bg-success">Pagada</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pendiente Pago</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 align-middle text-center">
                                        <div class="btn-group">
                                            <?php if ($sale['is_paid'] == 0 && $sale['estado'] == 1): ?>
                                                <a href="?route=orders_finalize&id=<?php echo $sale['order_id_display']; ?>" class="btn btn-sm btn-success" title="Registrar Pago">
                                                    <i class="fas fa-cash-register"></i> Cobrar
                                                </a>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Re-imprimir Ticket de Venta" onclick="printSaleTicket(<?php echo $sale['id']; ?>, '80mm')">
                                                <i class="fas fa-print"></i>
                                            </button>
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

<!-- Modal de Pedidos Pendientes de Facturar -->
<div class="modal fade" id="modalPendingOrders" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pedidos pendientes de Facturación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead class="table-light sticky-top" style="top: 0; z-index: 10; background-color: #f8f9fa !important; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                            <tr>
                                <th class="ps-3">Pedido</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingInvoices)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No hay pedidos pendientes de facturar.</td></tr>
                            <?php else: ?>
                            <?php foreach($pendingInvoices as $p): ?>
                            <tr>
                                <td class="ps-3">#<?php echo $p['id']; ?></td>
                                <td><?php echo htmlspecialchars($p['client_name']); ?></td>
                                <td><small class="badge bg-info"><?php echo $p['delivery_type']; ?></small></td>
                                <td>Gs. <?php echo number_format($p['total'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <form action="index.php" method="GET" class="d-inline">
                                        <input type="hidden" name="route" value="orders_finalize">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="quick" value="1">
                                        <button type="submit" class="btn btn-sm btn-primary btn-generate-ticket">Generar Factura/Ticket</button>
                                    </form>
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
</div>

<script>
function viewSaleDetail(id) {
    // Por ahora, redirigimos al pedido vinculado ya que el detalle es el mismo.
    const sales = <?php echo json_encode($sales ?? []); ?>;
    const sale = sales.find(s => s.id == id);
    if (sale && sale.order_id_display) {
        window.location.href = `?route=orders_show&id=${sale.order_id_display}`;
    } else {
        Toast.fire("El detalle ampliado se habilitará en el módulo de facturación legal avanzada.", "info");
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modalPending = document.getElementById('modalPendingOrders');
    if (!modalPending) return;

    // Al abrir el modal, enfocar el primer botón disponible para flujo rápido
    modalPending.addEventListener('shown.bs.modal', function () {
        const firstBtn = modalPending.querySelector('.btn-generate-ticket');
        if (firstBtn) firstBtn.focus();
    });

    // Navegación por teclado dentro del modal (Flechas Arriba/Abajo)
    modalPending.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            const btns = Array.from(modalPending.querySelectorAll('.btn-generate-ticket'));
            if (btns.length === 0) return;

            e.preventDefault();
            const currentIndex = btns.indexOf(document.activeElement);
            let nextIndex;

            if (e.key === 'ArrowDown') {
                nextIndex = (currentIndex + 1) % btns.length;
            } else {
                nextIndex = (currentIndex - 1 + btns.length) % btns.length;
            }
            btns[nextIndex].focus();
        }
    });

    // Atajo global Alt + F para abrir el modal de Facturar Pedido
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key.toLowerCase() === 'f') {
            e.preventDefault();
            const btn = document.getElementById('btn-open-facturar');
            if (btn) btn.click();
        }
    });
});
</script>