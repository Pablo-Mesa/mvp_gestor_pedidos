<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cash-register me-2"></i>Gestión de Caja</h1>
            <p class="text-muted">Operando como: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <!-- El administrador siempre puede ver el botón de apertura para asignar a otros -->
            <?php if (!$activeSession || $_SESSION['user_role'] === 'admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalOpenCash">
                    <i class="fas fa-play me-2"></i>Nueva Apertura
                </button>
            <?php endif; ?>

            <?php if ($activeSession): ?>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCloseCash"
                        onclick="prepareCloseModal('<?php echo $activeSession['id']; ?>', '<?php echo $activeSession['cash_station']; ?>', '<?php echo $activeSession['opening_amount'] + $totals['ingress'] - $totals['egress']; ?>')">
                    <i class="fas fa-stop me-2"></i>Cerrar Caja (Arqueo)
                </button>
            <?php endif; ?>
        </div>
    </div>
    <!-- /Cierre de d-flex header -->

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
                                <th>Estación</th>
                                <th>Cierre</th>
                                <th class="text-end">Monto Inicial</th>
                                <th class="text-end">Esperado</th>
                                <th class="text-end">Real (Arqueo)</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSessions as $session): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($session['user_name']); ?></strong></td>
                                    <td><?php echo date('d/m H:i', strtotime($session['opened_at'])); ?></td>
                                    <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($session['cash_station']); ?></span></td>
                                    <td><?php echo $session['closed_at'] ? date('d/m H:i', strtotime($session['closed_at'])) : '<span class="text-primary">En curso...</span>'; ?></td>
                                    <td class="text-end">Gs. <?php echo number_format($session['opening_amount'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-end text-primary">Gs. <?php echo number_format($session['status'] === 'open' ? $session['current_expected'] : $session['expected_amount'], 0, ',', '.'); ?></td>
                                    <td class="text-end">Gs. <?php echo number_format($session['closing_amount'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <?php if ($session['status'] === 'open'): ?>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalCloseCash"
                                                    onclick="prepareCloseModal('<?php echo $session['id']; ?>', '<?php echo $session['cash_station']; ?>', '<?php echo $session['current_expected']; ?>')">
                                                Cerrar
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">CERRADA</span>
                                        <?php endif; ?>
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
                    <label class="form-label">Asignar Cajero Responsable</label>
                    <?php if ($_SESSION['user_role'] === 'admin' && isset($cashiers)): ?>
                        <select name="user_id" class="form-select" required>
                            <?php foreach ($cashiers as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $_SESSION['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['name']); ?> (<?php echo ucfirst($u['role']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Como administrador, puedes abrir la caja para ti o para otro colega.</small>
                    <?php else: ?>
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Punto de Venta / Caja Física</label>
                    <select name="cash_station" class="form-select" required>
                        <option value="Caja Principal">Caja Principal</option>
                        <option value="Barra / Bebidas">Barra / Bebidas</option>
                        <option value="Caja Delivery">Caja Delivery</option>
                    </select>
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

<!-- Modal Arqueo de Cierre -->
<div class="modal fade" id="modalCloseCash" tabindex="-1">
    <div class="modal-dialog">
        <form action="?route=cash_close" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cierre de Caja y Arqueo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="session_id" id="close_session_id">
                
                <div class="alert alert-info">
                    Cerrando: <strong id="close_station_label"></strong><br>
                    Saldo esperado: <strong id="close_expected_label"></strong>
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

<script>
function prepareCloseModal(id, station, expected) {
    document.getElementById('close_session_id').value = id;
    document.getElementById('close_station_label').innerText = station;
    document.getElementById('close_expected_label').innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(expected);
}
</script>