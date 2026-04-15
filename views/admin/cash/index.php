<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cash-register me-2"></i>Gestión de Caja</h1>
            <p class="text-muted">Operando como: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
        </div>
        <?php if (!$activeSession): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalOpenCash">
                <i class="fas fa-play me-2"></i>Abrir Caja
            </button>
        <?php else: ?>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCloseCash">
                    <i class="fas fa-stop me-2"></i>Cerrar Caja (Arqueo)
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMovement">
                    <i class="fas fa-plus me-2"></i>Nuevo Movimiento
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($activeSession): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Monto de Apertura</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Gs. <?php echo number_format($activeSession['opening_amount'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Ingresos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Gs. <?php echo number_format($totals['ingress'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Egresos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Gs. <?php echo number_format($totals['egress'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Saldo Esperado</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Gs. <?php echo number_format($activeSession['opening_amount'] + $totals['ingress'] - $totals['egress'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Movimientos de la Sesión Actual</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hora</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Origen</th>
                                <th>Tipo</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $m): ?>
                                <tr>
                                    <td><?php echo date('H:i', strtotime($m['created_at'])); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($m['user_name'] ?? $_SESSION['user_name']); ?></small></td>
                                    <td><?php echo htmlspecialchars($m['description']); ?></td>
                                    <td><span class="badge bg-light text-dark"><?php echo strtoupper($m['source']); ?></span></td>
                                    <td>
                                        <i class="fas <?php echo $m['type'] === 'ingress' ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger'; ?> me-1"></i>
                                        <?php echo $m['type'] === 'ingress' ? 'Ingreso' : 'Egreso'; ?>
                                    </td>
                                    <td class="text-end font-weight-bold">Gs. <?php echo number_format($m['amount'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-lock fa-4x text-gray-300 mb-3"></i>
            <h3>Caja Cerrada</h3>
            <p class="text-muted">Debes abrir una sesión de caja para registrar movimientos y ventas en efectivo.</p>
        </div>
    <?php endif; ?>

    <!-- Historial de Sesiones (Arqueos Recientes) -->
    <?php if (isset($recentSessions) && !empty($recentSessions)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history me-2"></i>Historial de Sesiones / Arqueos</h6>
                <small class="text-muted">Últimas 10 sesiones</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.85rem;">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Cajero</th>
                                <th>Apertura</th>
                                <th>Cierre</th>
                                <th class="text-end">Monto Inicial</th>
                                <th class="text-end">Esperado</th>
                                <th class="text-end">Real (Arqueo)</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSessions as $session): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($session['user_name']); ?></strong></td>
                                    <td><?php echo date('d/m H:i', strtotime($session['opened_at'])); ?></td>
                                    <td><?php echo $session['closed_at'] ? date('d/m H:i', strtotime($session['closed_at'])) : '<span class="text-primary">En curso...</span>'; ?></td>
                                    <td class="text-end">Gs. <?php echo number_format($session['opening_amount'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-end">Gs. <?php echo number_format($session['expected_amount'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-end">Gs. <?php echo number_format($session['closing_amount'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $session['status'] === 'open' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo strtoupper($session['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Abrir Caja -->
<div class="modal fade" id="modalOpenCash" tabindex="-1">
    <div class="modal-dialog">
        <form action="?route=cash_open" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apertura de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cajero Responsable</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly>
                </div>
                <label class="form-label">Monto Inicial (Gs.)</label>
                <input type="number" name="opening_amount" class="form-control" placeholder="0" required autofocus>
                <small class="text-muted">Monto físico disponible en caja al iniciar el turno.</small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Movimiento Manual -->
<div class="modal fade" id="modalMovement" tabindex="-1">
    <div class="modal-dialog">
        <form action="?route=cash_movement_store" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select" required>
                        <option value="ingress">Ingreso (Extra)</option>
                        <option value="egress">Egreso (Gasto/Pago)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Monto (Gs.)</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="description" class="form-control" placeholder="Ej: Pago de hielo, Aporte capital..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">Guardar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Arqueo de Cierre -->
<div class="modal fade" id="modalCloseCash" tabindex="-1">
    <div class="modal-dialog">
        <form action="?route=cash_close" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cierre de Caja y Arqueo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ingress_total" value="<?php echo $totals['ingress']; ?>">
                <input type="hidden" name="egress_total" value="<?php echo $totals['egress']; ?>">
                
                <div class="alert alert-info">
                    El sistema espera que tengas: <br>
                    <strong>Gs. <?php echo number_format($activeSession['opening_amount'] + $totals['ingress'] - $totals['egress'], 0, ',', '.'); ?></strong>
                </div>
                
                <label class="form-label font-weight-bold">Monto Físico Real (Gs.)</label>
                <input type="number" name="physical_balance" class="form-control form-control-lg" placeholder="Cuenta el dinero en caja..." required>
                <small class="text-muted">Si hay diferencia, se registrará como faltante o sobrante.</small>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger w-100">Finalizar Jornada</button>
            </div>
        </form>
    </div>
</div>