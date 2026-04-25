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
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                    <thead class="bg-light sticky-top" style="top: 0; z-index: 10; background-color: #f8f9fa !important; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
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

                                // Identificar si la transacción corresponde a una factura anulada (Pedido rechazado/cancelado)
                                $isAnnulled = (isset($pay['estado']) && $pay['estado'] == 0);
                            ?>
                                <tr <?php echo $isAnnulled ? 'style="background-color: #fff5f5;"' : ''; ?>>
                                    <td class="px-4 align-middle">
                                        <?php echo date('H:i', strtotime($pay['fecha_pago'])); ?>
                                        <small class="text-muted d-block"><?php echo date('d/m/Y', strtotime($pay['fecha_pago'])); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $methodClass; ?>-subtle text-<?php echo $methodClass; ?> border border-<?php echo $methodClass; ?>-subtle" <?php echo $isAnnulled ? 'style="opacity: 0.6;"' : ''; ?>>
                                            <?php echo $methodLabel; ?>
                                        </span>
                                        <?php if ($isAnnulled): ?>
                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">ANULADO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-dark fw-bold"><?php echo $pay['nro_factura']; ?></span>
                                        <a href="?route=orders_show&id=<?php echo $pay['order_ref']; ?>" class="text-muted small d-block">Pedido #<?php echo $pay['order_ref']; ?></a>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted small"><?php echo htmlspecialchars($pay['referencia'] ?: '-'); ?></span>
                                    </td>
                                    <td class="align-middle text-end fw-bold px-4 <?php echo $isAnnulled ? 'text-danger' : ''; ?>">
                                        Gs. <?php echo number_format($pay['monto'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light sticky-bottom" style="bottom: 0; z-index: 5; background-color: #f8f9fa !important; box-shadow: 0 -2px 2px -1px rgba(0,0,0,0.1);">
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