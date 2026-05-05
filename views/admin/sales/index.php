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
                                        <button type="button"
                                                class="btn btn-sm btn-dark main-table-actions"
                                                title="Acciones Rápidas"
                                                onclick='openSaleQuickActions(<?php echo json_encode($sale); ?>)'
                                                onfocus="tableBtnIndex = Array.from(document.querySelectorAll('.main-table-actions')).indexOf(this)">
                                            <i class="fas fa-ellipsis-v"></i> Acciones
                                        </button>
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
                                    <div class="btn-group">
                                        <button type="button" onclick="generateInvoiceWithComandaCheck(<?php echo $p['id']; ?>, '<?php echo $p['status']; ?>', 'ticket')"
                                           class="btn btn-sm btn-outline-secondary btn-generate-ticket" title="Emitir Ticket Interno">
                                            <i class="fas fa-receipt"></i> Ticket
                                        </button>
                                        <button type="button" onclick="generateInvoiceWithComandaCheck(<?php echo $p['id']; ?>, '<?php echo $p['status']; ?>', 'factura')"
                                           class="btn btn-sm btn-primary btn-generate-ticket" title="Emitir Factura Legal">
                                            <i class="fas fa-file-invoice"></i> Factura
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
</div>

<!-- Modal de Acciones Rápidas para Ventas -->
<div class="modal fade" id="salesQuickActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="sales-qa-title">Venta #000</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" id="sales-qa-btn-factura">
                        <i class="fas fa-file-invoice me-2"></i> Imprimir Factura
                    </button>
                    <button type="button" class="btn btn-secondary" id="sales-qa-btn-ticket">
                        <i class="fas fa-receipt me-2"></i> Imprimir Ticket
                    </button>
                    <button type="button" class="btn btn-success" id="sales-qa-btn-cobrar" style="display: none;">
                        <i class="fas fa-cash-register me-2"></i> Cobrar Venta
                    </button>
                    <button type="button" class="btn btn-outline-dark" id="sales-qa-btn-view">
                        <i class="fas fa-eye me-2"></i> Ver Detalle
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- logica javascript -->
<script>
// Variable global para almacenar los datos de las ventas, necesaria para viewSaleDetail
const salesData = <?php echo json_encode($sales ?? []); ?>;

const isCashOpen = <?php echo ($isCashOpen ?? false) ? 'true' : 'false'; ?>;
// Índice para navegación por teclado en la tabla principal
let tableBtnIndex = -1;

/**
 * Valida si el pedido tiene comanda impresa antes de facturar.
 * Si no la tiene, fuerza la impresión y luego procede.
 */
async function generateInvoiceWithComandaCheck(orderId, status, docType) {
    const finalizeUrl = `?route=orders_finalize&id=${orderId}&quick=1&doc_type=${docType}`;
    
    if (status === 'pending') {
        const result = await Swal.fire({
            title: 'Paso Previo Requerido',
            text: "El pedido aún no tiene comanda impresa para cocina. Se imprimirá la comanda automáticamente antes de generar el comprobante.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Imprimir y Continuar',
            cancelButtonText: 'Cancelar',
            allowEscapeKey: true,
            allowOutsideClick: true,
            keydownListenerCapture: true,
            didOpen: () => { Swal.getConfirmButton().focus(); }
        });

        if (result.isConfirmed) {
            // 1. Disparar impresión de comanda (esto actualiza el estado a confirmed en el server)
            printOrderDirectly(orderId, '80mm');
            // 2. Pequeña pausa para asegurar que el trigger de impresión se procese y redirigir
            setTimeout(() => { window.location.href = finalizeUrl; }, 1200);
        }
    } else {
        window.location.href = finalizeUrl;
    }
}

// Foco inicial al cargar la página para agilizar flujo con [Alt + F] o navegación directa
window.addEventListener('load', () => {
    setTimeout(() => {
        const mainBtn = document.getElementById('btn-open-facturar');
        if (mainBtn) mainBtn.focus();
    }, 600);
});

function viewSaleDetail(id) {
    // Por ahora, redirigimos al pedido vinculado ya que el detalle es el mismo.
    const sale = salesData.find(s => s.id == id);
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
});

let salesQaModal = null;

function openSaleQuickActions(sale) {
    if (!salesQaModal) {
        const modalEl = document.getElementById('salesQuickActionsModal');
        salesQaModal = new bootstrap.Modal(modalEl);

        modalEl.addEventListener('shown.bs.modal', () => {
            // Enfocar el primer botón visible al abrir el modal
            const firstBtn = modalEl.querySelector('.modal-body .btn:not([style*="display: none"])');
            if (firstBtn) firstBtn.focus();
        });
    }

    document.getElementById('sales-qa-title').innerText = `Venta #${sale.nro_factura}`;

    // Lógica del botón "Cobrar Venta"
    const cobrarBtn = document.getElementById('sales-qa-btn-cobrar');
    if (sale.is_paid == 0 && sale.estado == 1) {
        cobrarBtn.style.display = 'block';
        if (!isCashOpen) {
            cobrarBtn.onclick = (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Sesión de Caja Cerrada',
                    text: 'No puedes procesar cobros sin una sesión de caja abierta. Por favor, realiza la apertura de caja primero.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ir a Tesorería',
                    cancelButtonText: 'Cerrar',
                    allowEscapeKey: true,
                    allowOutsideClick: true,
                    keydownListenerCapture: true,
                    didOpen: () => { Swal.getConfirmButton().focus(); }
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = '?route=cash';
                });
            };
            cobrarBtn.classList.add('btn-warning'); // Opcional: cambiar color para indicar advertencia
            cobrarBtn.innerHTML = '<i class="fas fa-lock me-2"></i> Caja Cerrada';
        } else {
            cobrarBtn.classList.remove('btn-warning');
            cobrarBtn.innerHTML = '<i class="fas fa-cash-register me-2"></i> Cobrar Venta';
            cobrarBtn.onclick = () => { window.location.href = `?route=orders_finalize&id=${sale.order_id_display}`; };
        }
    } else {
        cobrarBtn.style.display = 'none';
    }

    // Lógica del botón "Imprimir Factura" con validación de duplicados
    const facturaBtn = document.getElementById('sales-qa-btn-factura');
    const isAlreadyFactura = sale.nro_factura.startsWith('FAC-');
    
    if (isAlreadyFactura) {
        // Bloqueo total para evitar la emisión de duplicados contables
        facturaBtn.innerHTML = '<i class="fas fa-check-double me-2"></i> FACTURA YA EMITIDA';
        facturaBtn.classList.remove('btn-primary');
        facturaBtn.classList.add('btn-secondary', 'disabled');
        facturaBtn.onclick = (e) => { 
            e.preventDefault(); 
            Toast.fire("Este documento ya cuenta con validez legal y no puede emitirse nuevamente.", "info"); 
        };
    } else {
        // Habilitar la emisión de factura legal si el registro actual es solo un Ticket (TK-)
        facturaBtn.innerHTML = '<i class="fas fa-file-invoice me-2"></i> Emitir Factura Legal';
        facturaBtn.classList.remove('btn-secondary', 'disabled');
        facturaBtn.classList.add('btn-primary');
        facturaBtn.onclick = async () => {
            const result = await Swal.fire({
                title: 'Confirmar Emisión Legal',
                text: "Está a punto de generar un documento con validez contable por primera vez. ¿Desea continuar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'Sí, generar factura',
                cancelButtonText: 'Cancelar',
                allowEscapeKey: true,
                keydownListenerCapture: true,
                didOpen: () => { Swal.getConfirmButton().focus(); }
            });

            if (result.isConfirmed) {
                try {
                    // 1. Persistir el cambio de TK a FAC en la base de datos vía API
                    const resp = await fetch('?route=update_sale_doc_type_api', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: sale.id, doc_type: 'factura' })
                    });
                    const res = await resp.json();
                    
                    if (res.success) {
                        // 2. Disparar la impresión (usará el nro actualizado)
                        printSaleTicket(sale.id, '80mm', 'factura');
                        salesQaModal.hide();
                        
                        // 3. Notificar y recargar la página para actualizar los badges FAC en la tabla
                        Toast.fire("Documento legal emitido y registrado", "success");
                        setTimeout(() => { window.location.reload(); }, 1500);
                    } else {
                        Toast.fire(res.message || "Error al formalizar factura", "error");
                    }
                } catch (e) {
                    Toast.fire("Error de conexión al emitir factura", "error");
                }
            }
        };
    }

    // Botón "Imprimir Ticket"
    document.getElementById('sales-qa-btn-ticket').onclick = () => {
        printSaleTicket(sale.id, '80mm', 'ticket');
        salesQaModal.hide();
    };

    // Botón "Ver Detalle"
    document.getElementById('sales-qa-btn-view').onclick = () => {
        viewSaleDetail(sale.id);
        salesQaModal.hide();
    };

    salesQaModal.show();
}

// Manejador de teclado unificado para la vista de Facturación
document.addEventListener('keydown', function(e) {
    // Si hay un SweetAlert abierto, pausamos nuestra navegación por teclado
    if (window.Swal && Swal.isVisible()) return;

    const modalPending = document.getElementById('modalPendingOrders');
    const modalActions = document.getElementById('salesQuickActionsModal');
    
    const isPendingOpen = modalPending && modalPending.classList.contains('show');
    const isActionsOpen = modalActions && modalActions.classList.contains('show');

    // 1. Navegación en Modal de Pedidos Pendientes de Facturar
    if (isPendingOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
        e.preventDefault();
        const btns = Array.from(modalPending.querySelectorAll('.btn-generate-ticket'));
        if (btns.length === 0) return;
        const currentIndex = btns.indexOf(document.activeElement);
        let nextIndex = (e.key === 'ArrowDown') ? (currentIndex + 1) % btns.length : (currentIndex - 1 + btns.length) % btns.length;
        btns[nextIndex].focus();
        return;
    }

    // 2. Navegación en Modal de Acciones Rápidas (Ventas Registradas)
    if (isActionsOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
        e.preventDefault();
        const buttons = Array.from(modalActions.querySelectorAll('.modal-body .btn:not([style*="display: none"])'));
        if (buttons.length === 0) return;
        const currentIndex = buttons.indexOf(document.activeElement);
        let nextIndex = (e.key === 'ArrowDown') ? (currentIndex + 1) % buttons.length : (currentIndex - 1 + buttons.length) % buttons.length;
        buttons[nextIndex].focus();
        return;
    }

    // 3. Atajo global Alt + F para abrir Facturar Pedido
    if (e.altKey && e.key.toLowerCase() === 'f') {
        e.preventDefault();
        e.stopImmediatePropagation();
        const btn = document.getElementById('btn-open-facturar');
        if (btn) btn.click();
        return;
    }

    // 4. Navegación en la Tabla Principal (Flechas Arriba/Abajo)
    if (!isPendingOpen && !isActionsOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
        const tableBtns = Array.from(document.querySelectorAll('.main-table-actions'));
        const mainBtn = document.getElementById('btn-open-facturar');
        
        e.preventDefault();

        if (e.key === 'ArrowDown') {
            if (document.activeElement === mainBtn) {
                tableBtnIndex = 0;
            } else {
                tableBtnIndex++;
            }

            if (tableBtnIndex >= tableBtns.length) {
                if (mainBtn) mainBtn.focus();
                tableBtnIndex = -1;
            } else if (tableBtns[tableBtnIndex]) {
                tableBtns[tableBtnIndex].focus();
                tableBtns[tableBtnIndex].closest('tr').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } else {
            if (document.activeElement === mainBtn) {
                tableBtnIndex = tableBtns.length - 1;
            } else {
                tableBtnIndex--;
            }

            if (tableBtnIndex < 0) {
                if (mainBtn) mainBtn.focus();
                tableBtnIndex = -1;
            } else if (tableBtns[tableBtnIndex]) {
                tableBtns[tableBtnIndex].focus();
                tableBtns[tableBtnIndex].closest('tr').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }
});
</script>