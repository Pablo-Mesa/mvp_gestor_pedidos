<?php
    $isCashOpen = isset($activeSession) && $activeSession !== false;
    $orderModel = new Order();
    $hasInvoice = $orderModel->hasInvoice($order['id']);
?>
<style>
    /* Quitar flechas de los input de número para una estética más limpia */
    input.payment-input::-webkit-outer-spin-button,
    input.payment-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input.payment-input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Finalizar Venta y Cobro - Pedido #<?php echo $order['id']; ?></h5>
                    <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-light">Volver</a>
                </div>
                <div class="card-body bg-light">
                    <?php if(!$isCashOpen): ?>
                        <div class="alert alert-warning d-flex align-items-center mb-4">
                            <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                            <div><strong>Caja cerrada:</strong> Puedes generar la factura o ticket, pero el registro de pagos requiere una sesión de caja activa.</div>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Cliente</p>
                            <h6><?php echo htmlspecialchars($order['user_name']); ?></h6>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Tipo Documento</p>
                            <select name="document_type" form="form-finalize" class="form-select fw-bold">
                                <option value="ticket" <?php echo empty($order['billing_ruc']) ? 'selected' : ''; ?>>📄 TICKET SIMPLE</option>
                                <option value="factura" <?php echo !empty($order['billing_ruc']) ? 'selected' : ''; ?>>⚖️ FACTURA LEGAL</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <p class="mb-1 text-muted">Total a Pagar</p>
                            <h2 class="text-success fw-bold" id="order-total" data-total="<?php echo $order['total']; ?>">
                                Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?>
                            </h2>
                        </div>
                    </div>

                    <form action="?route=orders_process_finalize" method="POST" id="form-finalize">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Método de Pago</th>
                                        <th style="width: 200px;">Monto (Gs.)</th>
                                        <th>Referencia (Nro. Boleta/Trans.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $methods = [
                                        'efectivo' => ['label' => '💵 Efectivo', 'class' => 'text-success'],
                                        'pos' => ['label' => '💳 Tarjeta (POS)', 'class' => 'text-primary'],
                                        'transferencia' => ['label' => '🏦 Transferencia', 'class' => 'text-info'],
                                        'qr' => ['label' => '📱 QR / Billetera', 'class' => 'text-warning']
                                    ];
                                    $i = 0;
                                    foreach($methods as $key => $m): 
                                    ?>
                                    <tr>
                                        <td class="align-middle <?php echo $m['class']; ?>">
                                            <div class="fw-bold mb-1"><?php echo $m['label']; ?></div>
                                            <input type="hidden" name="payments[<?php echo $i; ?>][metodo]" value="<?php echo $key; ?>">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="payments[<?php echo $i; ?>][monto]" 
                                                       class="form-control form-control-lg payment-input" 
                                                       id="input-<?php echo $key; ?>"
                                                       data-method="<?php echo $key; ?>" value="0" min="0" step="1"
                                                       <?php echo !$isCashOpen ? 'disabled' : ''; ?>>
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        title="Cobrar saldo pendiente"
                                                        onclick="fillRemaining('input-<?php echo $key; ?>')"
                                                        <?php echo !$isCashOpen ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-magic"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="payments[<?php echo $i; ?>][referencia]" 
                                                   class="form-control" placeholder="Opcional..."
                                                   <?php echo !$isCashOpen ? 'disabled' : ''; ?>>
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4 align-items-center">
                            <div class="col-md-6">
                                <div id="balance-card" class="p-3 border rounded <?php echo $isCashOpen ? 'bg-white' : 'bg-light'; ?> text-center shadow-sm" style="transition: all 0.3s ease; height: 105px; width: 100%; display: flex; flex-direction: column; justify-content: center; overflow: hidden;">
                                    <?php if($isCashOpen): ?>
                                    <p id="balance-label" class="mb-0 small text-uppercase fw-bold text-muted">Resta cobrar</p>
                                    <h3 id="balance-display" class="mb-0">Gs. 0</h3>
                                    <?php else: ?>
                                        <p class="mb-0 small text-uppercase fw-bold text-muted">Módulo de Pagos</p>
                                        <h5 class="mb-0 text-secondary">Inactivo (Caja Cerrada)</h5>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <?php 
                                    $btnDisabled = ($isCashOpen || $hasInvoice) ? 'disabled' : '';
                                    $btnIcon = $hasInvoice ? 'fa-lock' : ($isCashOpen ? 'fa-check-double' : 'fa-file-invoice');
                                    $btnText = $hasInvoice ? 'Venta ya registrada' : ($isCashOpen ? 'Confirmar y Generar Factura' : 'Generar Factura (Sin Pago)');
                                ?>
                                <button type="submit" id="btn-submit" class="btn btn-success btn-lg px-5 py-3 shadow" <?php echo $btnDisabled; ?>>
                                    <i class="fas <?php echo $btnIcon; ?> me-2"></i> <?php echo $btnText; ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const total = parseFloat(document.getElementById('order-total').dataset.total);
    const inputs = document.querySelectorAll('.payment-input');
    const balanceDisplay = document.getElementById('balance-display');
    const balanceCard = document.getElementById('balance-card');
    const balanceLabel = document.getElementById('balance-label');
    const btnSubmit = document.getElementById('btn-submit');
    const isCashOpen = <?php echo $isCashOpen ? 'true' : 'false'; ?>;

    // Si el pedido solo tenía un método, lo precargamos (Opcional)
    // inputs[0].value = total; 

    function calculate() {
        if (!isCashOpen) return;

        let paid = 0;
        inputs.forEach(input => {
            paid += parseFloat(input.value) || 0;
        });

        const remaining = total - paid;
        
        // Reset de estilos base
        balanceCard.style.backgroundColor = '#fff';
        balanceCard.className = 'p-3 border rounded text-center shadow-sm';
        balanceLabel.className = 'mb-0 small text-uppercase fw-bold text-muted';
        balanceDisplay.className = 'mb-0 fw-bold';

        if (remaining > 0) {
            balanceLabel.innerText = 'Resta cobrar';
            balanceDisplay.innerText = `Gs. ${new Intl.NumberFormat('es-PY').format(remaining)}`;
            balanceDisplay.className = 'mb-0 fw-bold text-danger';
            btnSubmit.disabled = true;
        } else if (remaining < 0) {
            // Resalte visual fuerte para el vuelto
            balanceLabel.innerText = 'Vuelto a entregar';
            balanceLabel.className = 'mb-0 small text-uppercase fw-bold text-white';
            balanceDisplay.innerText = `Gs. ${new Intl.NumberFormat('es-PY').format(Math.abs(remaining))}`;
            balanceDisplay.className = 'mb-0 fw-bold text-white'; 
            balanceCard.style.backgroundColor = '#0984e3'; // Azul llamativo
            balanceCard.classList.add('border-primary');
            btnSubmit.disabled = false;
        } else {
            balanceLabel.innerText = 'Estado';
            balanceDisplay.innerText = 'Monto Exacto ✅';
            balanceDisplay.className = 'mb-0 fw-bold text-success';
            balanceCard.style.backgroundColor = '#f8fff9';
            balanceCard.classList.add('border-success');
            btnSubmit.disabled = false;
        }
    }

    // Función para llenar automáticamente lo que falta cobrar
    window.fillRemaining = function(inputId) {
        let paidOthers = 0;
        inputs.forEach(inp => {
            if (inp.id !== inputId) paidOthers += parseFloat(inp.value) || 0;
        });
        const needed = Math.max(0, total - paidOthers);
        document.getElementById(inputId).value = needed;
        calculate();
    };

    inputs.forEach((input, index) => {
        input.addEventListener('input', calculate);
        // Limpiar el 0 al hacer click para facilitar el tipeo
        input.addEventListener('focus', function() { if(this.value == "0") this.value = ""; });
        input.addEventListener('blur', function() { if(this.value == "") this.value = "0"; });

        // Navegación fluida con la tecla Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const nextInput = inputs[index + 1];
                if (nextInput) {
                    nextInput.focus();
                } else {
                    btnSubmit.focus();
                }
            }
        });
    });
    calculate();

    // Enfocar automáticamente el campo de efectivo para agilizar la carga del cobro
    document.querySelector('input[data-method="efectivo"]')?.focus();

    const form = document.getElementById('form-finalize');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Detener el envío automático
        
        let paid = 0;
        inputs.forEach(input => paid += parseFloat(input.value) || 0);
        
        // 1. Validación de seguridad
        if (paid < total) {
            Toast.fire("El monto pagado es insuficiente", "warning");
            return;
        }

        // 2. Preparar mensaje de confirmación
        const change = paid - total;
        let msg = "Se registrará el pago y se generará el comprobante de venta.";
        if (change > 0) {
            msg = `<strong>VUELTO A ENTREGAR: Gs. ${new Intl.NumberFormat('es-PY').format(change)}</strong><br><br>` + msg;
        }

        // 3. Lanzar confirmación
        Swal.fire({
            title: '¿Confirmar cobro?',
            html: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00b894',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, finalizar venta',
            cancelButtonText: 'No, revisar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Ajuste automático: Si el cajero ingresó un billete grande (ej: 100k para deuda 86k),
                // ajustamos el valor del input al neto (86k) antes de enviar.
                // Esto garantiza que los reportes de ingresos de tesorería sean exactos.
                const cashInput = document.getElementById('input-efectivo');
                if (cashInput && change > 0) {
                    const cashVal = parseFloat(cashInput.value) || 0;
                    // Solo restamos si el vuelto sale efectivamente del efectivo
                    if (cashVal >= change) {
                        cashInput.value = cashVal - change;
                    }
                }
                
                form.submit(); // Enviar el formulario físicamente
            }
        });
    });
});
</script>