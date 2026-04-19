<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-money-check-alt me-2"></i>Pagos Recibidos (Caja)</h1>
        <form action="index.php" method="GET" class="d-flex gap-2">
            <input type="hidden" name="route" value="payments_report">
            <input type="date" name="date" class="form-control" value="<?php echo $_GET['date'] ?? date('Y-m-d'); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Resumen de Totales por Método -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body py-2">
                    <small class="text-muted fw-bold text-uppercase">💵 Efectivo</small>
                    <h4 class="mb-0">Gs. <?php echo number_format($summary['efectivo'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body py-2">
                    <small class="text-muted fw-bold text-uppercase">💳 POS / Tarjetas</small>
                    <h4 class="mb-0">Gs. <?php echo number_format($summary['pos'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body py-2">
                    <small class="text-muted fw-bold text-uppercase">🏦 Transferencias</small>
                    <h4 class="mb-0">Gs. <?php echo number_format($summary['transferencia'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body py-2">
                    <small class="text-muted fw-bold text-uppercase">📱 QR / Billeteras</small>
                    <h4 class="mb-0">Gs. <?php echo number_format($summary['qr'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalle de Transacciones</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Fecha / Hora</th>
                            <th class="py-3">Método</th>
                            <th class="py-3">Referencia Documento</th>
                            <th class="py-3">Info. Referencia</th>
                            <th class="py-3 text-end px-4">Monto Cobrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No se registraron cobros en esta fecha.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $pay): 
                                $methodClass = 'secondary';
                                $methodLabel = strtoupper($pay['metodo_pago']);
                                switch($pay['metodo_pago']) {
                                    case 'efectivo': $methodClass = 'success'; break;
                                    case 'pos': $methodClass = 'primary'; break;
                                    case 'transferencia': $methodClass = 'info'; break;
                                    case 'qr': $methodClass = 'warning'; break;
                                }
                            ?>
                                <tr>
                                    <td class="px-4 align-middle">
                                        <?php echo date('H:i', strtotime($pay['fecha_pago'])); ?>
                                        <small class="text-muted d-block"><?php echo date('d/m/Y', strtotime($pay['fecha_pago'])); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $methodClass; ?>-subtle text-<?php echo $methodClass; ?> border border-<?php echo $methodClass; ?>-subtle">
                                            <?php echo $methodLabel; ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-dark fw-bold"><?php echo $pay['nro_factura']; ?></span>
                                        <a href="?route=orders_show&id=<?php echo $pay['order_ref']; ?>" class="text-muted small d-block">Pedido #<?php echo $pay['order_ref']; ?></a>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted small"><?php echo htmlspecialchars($pay['referencia'] ?: '-'); ?></span>
                                    </td>
                                    <td class="align-middle text-end fw-bold px-4">
                                        Gs. <?php echo number_format($pay['monto'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold py-3">TOTAL RECAUDADO:</td>
                                <td class="text-end fw-bold text-primary py-3 px-4">Gs. <?php echo number_format($summary['total'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>