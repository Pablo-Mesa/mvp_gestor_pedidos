<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Finalizar Venta y Cobro - Pedido #<?php echo $order['id']; ?></h5>
                    <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-light">Volver</a>
                </div>
                <div class="card-body bg-light">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Cliente</p>
                            <h6><?php echo htmlspecialchars($order['user_name']); ?></h6>
                        </div>
                        <div class="col-md-6 text-end">
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
                                        <td class="align-middle fw-bold <?php echo $m['class']; ?>">
                                            <?php echo $m['label']; ?>
                                            <input type="hidden" name="payments[<?php echo $i; ?>][metodo]" value="<?php echo $key; ?>">
                                        </td>
                                        <td>
                                            <input type="number" name="payments[<?php echo $i; ?>][monto]" 
                                                   class="form-control form-control-lg payment-input" 
                                                   data-method="<?php echo $key; ?>" value="0" min="0" step="1">
                                        </td>
                                        <td>
                                            <input type="text" name="payments[<?php echo $i; ?>][referencia]" 
                                                   class="form-control" placeholder="Opcional...">
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4 align-items-center">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-white text-center shadow-sm">
                                    <p class="mb-0 small text-uppercase fw-bold">Resta cobrar</p>
                                    <h3 id="balance-display" class="mb-0">Gs. 0</h3>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" id="btn-submit" class="btn btn-success btn-lg px-5 py-3 shadow" disabled>
                                    <i class="fas fa-check-double me-2"></i> Confirmar y Generar Factura
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
    const btnSubmit = document.getElementById('btn-submit');

    // Si el pedido solo tenía un método, lo precargamos (Opcional)
    // inputs[0].value = total; 

    function calculate() {
        let paid = 0;
        inputs.forEach(input => {
            paid += parseFloat(input.value) || 0;
        });

        const remaining = total - paid;
        
        if (remaining > 0) {
            balanceDisplay.innerText = `Gs. ${new Intl.NumberFormat('es-PY').format(remaining)}`;
            balanceDisplay.className = 'mb-0 text-danger fw-bold';
            btnSubmit.disabled = true;
        } else if (remaining < 0) {
            balanceDisplay.innerText = `Vuelto: Gs. ${new Intl.NumberFormat('es-PY').format(Math.abs(remaining))}`;
            balanceDisplay.className = 'mb-0 text-primary fw-bold';
            btnSubmit.disabled = false;
        } else {
            balanceDisplay.innerText = 'Cerrado ✅';
            balanceDisplay.className = 'mb-0 text-success fw-bold';
            btnSubmit.disabled = false;
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', calculate);
        // Limpiar el 0 al hacer click para facilitar el tipeo
        input.addEventListener('focus', function() { if(this.value == "0") this.value = ""; });
        input.addEventListener('blur', function() { if(this.value == "") this.value = "0"; });
    });

    calculate();

    document.getElementById('form-finalize').onsubmit = function() {
        let paid = 0;
        inputs.forEach(input => paid += parseFloat(input.value) || 0);
        if(paid < total) {
            alert("El monto pagado no puede ser menor al total del pedido.");
            return false;
        }
    };
});
</script>