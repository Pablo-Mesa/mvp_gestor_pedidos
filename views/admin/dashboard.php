<style>
        
    .dashboard-container {
        padding: 0px 20px;
        max-width: 1200px;
        margin: 0 auto;
        animation: fadeIn 0.5s ease-in-out;
    }

    .dashboard-header {
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
    }

    .date-filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 8px 15px;
        border-radius: 12px;
        border: 1px solid #dfe6e9;
    }

    .view-switcher {
        background: #f1f3f5;
        padding: 4px;
        border-radius: 10px;
        display: inline-flex;
    }
    .view-switcher a {
        padding: 6px 16px;
        border-radius: 8px;
        text-decoration: none;
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .view-switcher a.active { background: white; color: #0984e3; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    .dashboard-header h1 { color: #2d3436; font-size: 2.2rem; font-weight: 800; margin-bottom: 5px; letter-spacing: -1px; }
    .dashboard-header p { color: #636e72; }

    /* Alertas */
    .alert-section { margin-bottom: 25px; }
    .alert-banner {
        background: #fff9db;
        border: 1px solid #ffe066;
        border-left: 5px solid #fcc419;
        padding: 12px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #856404;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-alert {
        margin-left: auto;
        background: #fcc419;
        color: white;
        padding: 6px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.85rem;
        transition: opacity 0.2s;
    }
    .btn-alert:hover { opacity: 0.9; }

    /* Grid de Stats */
    .stats-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 25px;
    }

    .stats-grid.summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
        background: white;
        padding: 0;
        border-radius: 12px;
        border: 1px solid #f1f3f5;
        margin-bottom: 30px;
    }

    .stats-grid.channels {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .section-area-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f1f3f5;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
    }

    .stats-grid.summary .stat-card {
        flex: 1;
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 12px 25px;
        border-right: 1px solid #f1f3f5;
        border-radius: 0;
        min-width: 200px;
    }

    .stats-grid.summary .stat-card:last-child {
        border-right: none;
    }

    .stat-card.pending { border-left: 4px solid #fab005; }
    .stat-card.income { border-left: 4px solid #40c057; }
    .stat-card.sales { border-bottom: 4px solid #228be6; }
    .stat-card.commission { border-bottom: 4px solid #7950f2; }
    .stat-card.web { border-bottom: 4px solid #0984e3; }
    .stat-card.local { border-bottom: 4px solid #00b894; }
    .stat-card.waiter { border-bottom: 4px solid #6c5ce7; }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: #f8f9fa;
    }
    
    .stat-badge {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .pending .stat-icon { color: #ffc107; background: #fff9db; }
    .income .stat-icon { color: #28a745; background: #ebfbee; }
    .sales .stat-icon { color: #17a2b8; background: #e3f2fd; }
    .web .stat-icon { color: #0984e3; background: #e3f2fd; }
    .local .stat-icon { color: #00b894; background: #ebfbee; }
    .waiter .stat-icon { color: #6c5ce7; background: #f3f0ff; }

    .stat-info h3 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #636e72;
        margin: 0 0 5px 0;
    }

    .stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3436;
        margin: 0;
    }

    /* Acciones Rápidas */
    .quick-actions {
        width: 100%;
        margin-top: 20px;
    }
    .quick-actions h2 { 
        text-align: left;
        font-size: 1.2rem; 
        color: #2d3436; 
        margin-bottom: 15px;
        font-weight: 600;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }

    .action-btn {
        background: white;
        border: 1px solid #e9ecef;
        padding: 12px 15px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #495057;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        text-align: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .action-btn i { 
        font-size: 1.1rem; 
        color: #0984e3; 
        width: 20px;
        text-align: center;
    }
    .action-btn span { font-weight: 600; font-size: 0.8rem; line-height: 1.2; }

    .action-btn:hover {
        border-color: #0984e3;
        background: #f8fbff;
        transform: translateY(-4px);
    }

    /* Estilos para tabla con scroll interno y cabecera fija */
    .table-fixed-scroll {
        max-height: 350px;
        overflow-y: auto;
        border-radius: 0 0 12px 12px;
    }

    .table-fixed-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa !important; /* Color table-light */
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        box-shadow: inset 0 -1px 0 #dee2e6;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .actions-grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); }
         .quick-actions h2 {
            text-align: center;
            margin-top: 10px;
         }
    }
</style>

<!-- Contenido específico del Dashboard -->
<div class="dashboard-container">
    <!-- Encabezado del Dashboard -->
    <header class="dashboard-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> <?php echo $data['title']; ?></h1>
            <p>Resumen de actividad del sistema.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="view-switcher">
                <a href="?route=admin&view_mode=daily" class="<?= $data['view_mode'] == 'daily' ? 'active' : '' ?>">Día</a>
                <a href="?route=admin&view_mode=monthly" class="<?= $data['view_mode'] == 'monthly' ? 'active' : '' ?>">Mes</a>
            </div>
            <form action="" method="GET" class="date-filter-form">
                <input type="hidden" name="route" value="admin">
                <input type="hidden" name="view_mode" value="<?= $data['view_mode'] ?>">
                <?php if($data['view_mode'] == 'daily'): ?>
                    <input type="date" name="date" class="form-control form-control-sm" value="<?php echo $data['selected_date']; ?>" onchange="this.form.submit()">
                <?php else: ?>
                    <input type="month" name="month" class="form-control form-control-sm" value="<?php echo $data['selected_month']; ?>" onchange="this.form.submit()">
                <?php endif; ?>
            </form>
        </div>
    </header>

    <!-- Sección de Alertas de Stock Bajo -->
    <?php if (!empty($data['low_stock_items'])): ?>
    <div class="alert-section">
        <?php foreach ($data['low_stock_items'] as $item): ?>
        <div class="alert-banner warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span><strong>Atención:</strong> Quedan pocas porciones de <strong><?php echo $item['product_name']; ?></strong> (Solo <?php echo $item['daily_stock']; ?> restantes).</span>
            <a href="?route=menus&date=<?php echo $data['selected_date']; ?>" class="btn-alert">Gestionar</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Desempeño por Canal -->
    <h2 class="section-area-title">
        <i class="fas fa-project-diagram"></i> Rendimiento por Fuente
    </h2>
    
    <!-- Estadisticas Canales Recepcion Pedidos -->
    <div class="stats-grid channels">
        <!-- web -->
        <div class="stat-card web">
            <div class="stat-icon"><i class="fas fa-globe"></i></div>
            <div class="stat-info">
                <h3>Ingresos Canal Web</h3>
                <p class="stat-value">Gs. <?php echo number_format($data['web_income'] ?? 0, 0, ',', '.'); ?></p>
                <small style="color: #7950f2; font-weight: bold; font-size: 0.75rem;">
                    Comisión Solver (10%): Gs. <?php echo number_format(($data['web_income'] ?? 0) * 0.10, 0, ',', '.'); ?><br />(Solo Pagados)
                </small>
            </div>
        </div>

        <!-- mostrador -->
        <div class="stat-card local">
            <div class="stat-icon"><i class="fas fa-cash-register"></i></div>
            <div class="stat-info">
                <h3>Ingresos Mostrador</h3>
                <p class="stat-value">Gs. <?php echo number_format($data['local_income'] ?? 0, 0, ',', '.'); ?></p>
                <small class="text-success" style="font-size: 0.7rem; font-weight: bold;">
                    <i class="fas fa-check-circle"></i> Comisión Solver (0%).<br />No genera costos al local.
                </small>                
            </div>
        </div>

        <!-- mozos -->
        <div class="stat-card waiter">
            <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            <div class="stat-info">
                <h3>Ingresos Mozos (Mesas)</h3>
                <p class="stat-value">Gs. <?php echo number_format($data['waiter_income'] ?? 0, 0, ',', '.'); ?></p>
                <small class="text-success" style="font-size: 0.7rem; font-weight: bold;">
                    <i class="fas fa-check-circle"></i> Comisión Solver (0%).<br />No genera costos al local.
                </small>                
            </div>
        </div>
    </div>

    <!-- Monitor Global de Tesorería -->
    <h2 class="section-area-title"><i class="fas fa-vault"></i> Monitor Global de Cajas Abiertas</h2>
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Estación / Punto</th>
                            <th>Cajero Responsable</th>
                            <th class="text-end">Fondo Inicial</th>
                            <th class="text-end">Ingresos</th>
                            <th class="text-end">Egresos</th>
                            <th class="text-end pe-4">Saldo en Efectivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($data['active_session']): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border py-2 px-3">
                                        <i class="fas fa-desktop me-1"></i> <?php echo htmlspecialchars($data['active_session']['cash_station']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon me-2" style="width: 30px; height: 30px; font-size: 0.8rem; background: #e3f2fd; color: #0984e3;"><i class="fas fa-user-tie"></i></div>
                                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                    </div>
                                </td>
                                <td class="text-end text-muted">Gs. <?php echo number_format($data['active_session']['opening_amount'] ?? 0, 0, ',', '.'); ?></td>
                                <td class="text-end text-success fw-bold">+ Gs. <?php echo number_format($data['session_ingress'] ?? 0, 0, ',', '.'); ?></td>
                                <td class="text-end text-danger fw-bold">- Gs. <?php echo number_format($data['session_egress'] ?? 0, 0, ',', '.'); ?></td>
                                <td class="text-end pe-4 fw-bold text-primary" style="font-size: 1rem;">Gs. <?php echo number_format($data['session_expected'] ?? 0, 0, ',', '.'); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-lock fa-2x mb-3 d-block"></i>
                                    <p class="mb-0 fw-bold">No se detectan sesiones de caja abiertas en este momento.</p>
                                    <a href="?route=cash" class="btn btn-sm btn-outline-primary mt-2">Gestionar Aperturas</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Listado de Movimientos Recientes -->
    <?php if($data['active_session'] && !empty($data['recent_movements'])): ?>
    <div class="stats-grid" style="grid-template-columns: 1fr;">
         <div class="stat-card" style="display: block; padding: 0;">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold" style="font-size: 0.85rem; text-transform: uppercase; color: #636e72;">Registro Global de Movimientos (Recientes)</h3>
                <a href="?route=cash" class="btn btn-sm btn-outline-secondary" style="font-size: 0.7rem;">Ver Tesorería</a>
            </div>
            <div class="table-responsive table-fixed-scroll">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 100px;">Hora</th>
                            <th>Cajero</th>
                            <th>Descripción / Concepto</th>
                            <th>Origen</th>
                            <th class="text-end pe-4">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['recent_movements'] as $mov): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="text-muted"><?php echo date('H:i', strtotime($mov['created_at'])); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas <?php echo $mov['type'] === 'ingress' ? 'fa-circle-arrow-up text-success' : 'fa-circle-arrow-down text-danger'; ?> me-2"></i>
                                        <span class="fw-bold"><?php echo htmlspecialchars($mov['user_name'] ?? 'Staff'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark"><?php echo htmlspecialchars($mov['description']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border"><?php echo ucfirst($mov['source']); ?></span>
                                </td>
                                <td class="text-end pe-4 py-2 fw-bold <?php echo $mov['type'] === 'ingress' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $mov['type'] === 'ingress' ? '+' : '-'; ?> Gs. <?php echo number_format($mov['amount'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
         </div>
    </div>
    <?php endif; ?>


    <!-- Resumen Operativo (Estilo Barra Compacta) -->
    <div class="stats-grid summary">
        <div class="stat-card pending">
            <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1rem;"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 0.75rem; margin-bottom: 2px;">Total Pedidos</h3>
                <p class="stat-value" style="font-size: 1.2rem;"><?php echo ($data['view_mode'] == 'daily') ? (($data['web_orders_count'] ?? 0) + ($data['local_orders_count'] ?? 0)) : $data['pedidos_pendientes']; ?></p>
                <small class="<?= ($data['cash_status'] ?? 'Cerrada') === 'Abierta' ? 'text-success' : 'text-danger' ?>" style="font-size: 0.65rem; font-weight: 700;">
                    ● Caja <?= $data['cash_status'] ?? 'Cerrada' ?>
                </small>
            </div>
        </div>
        <div class="stat-card income">
            <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1rem;"><i class="fas fa-wallet"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 0.75rem; margin-bottom: 2px;">Venta Total Bruta</h3>
                <p class="stat-value" style="font-size: 1.2rem;">Gs. <?php echo number_format($data['ingresos_hoy'], 0, ',', '.'); ?></p>
                <small class="text-muted" style="font-size: 0.65rem;">Todos los canales</small>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones Rápidas -->
    <?php if ($data['view_mode'] === 'daily'): ?>
    <section class="quick-actions">
        <h2>Acciones Rápidas</h2>
        <div class="actions-grid">
            <a href="?route=cash" class="action-btn">
                <i class="fas fa-key"></i>
                <span>Apertura de Caja <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + C]</small></span>
            </a>
            <a href="?route=orders" class="action-btn">
                <i class="fas fa-list"></i>
                <span>Pedidos <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + O]</small></span>
            </a>
            <a href="?route=sales_history" class="action-btn">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Facturacion/Ticket <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + F]</small></span>
            </a>
            <a href="?route=payments_report" class="action-btn">
                <i class="fas fa-money-check-alt"></i>
                <span>Pagos <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + R]</small></span>
            </a>
            <a href="?route=cash" class="action-btn">
                <i class="fas fa-vault"></i>
                <span>Movimientos Caja <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + C]</small></span>
            </a>
            <a href="?route=pos" class="action-btn">
                <i class="fas fa-cash-register"></i>
                <span>Punto de Venta <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + P]</small></span>
            </a>
        </div>
    </section>
    <?php endif; ?>

</div>
<!-- Cierre de dashboard-container -->