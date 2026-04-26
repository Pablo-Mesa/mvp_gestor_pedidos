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
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 10px;
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

    .stat-card.pending { border-bottom: 4px solid #fab005; }
    .stat-card.income { border-bottom: 4px solid #40c057; }
    .stat-card.sales { border-bottom: 4px solid #228be6; }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: #f8f9fa;
    }

    .pending .stat-icon { color: #ffc107; background: #fff9db; }
    .income .stat-icon { color: #28a745; background: #ebfbee; }
    .sales .stat-icon { color: #17a2b8; background: #e3f2fd; }

    .stat-info h3 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #636e72;
        margin: 0 0 5px 0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #2d3436;
        margin: 0;
    }

    /* Layout Visual (Gráfico + Acciones) */
    .dashboard-visuals {
        display: flex;
        gap: 30px;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        margin-top: 20px;
    }

    .chart-container {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 350px;
    }

    .chart-header h2 {
        font-size: 1.1rem;
        color: #2d3436;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .chart-wrapper {
        position: relative;
        height: 210px;
    }

    /* Acciones Rápidas */
    .quick-actions {
        flex: 1.5;
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
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .action-btn {
        background: white;
        border: 1px solid #e9ecef;
        padding: 12px 15px;
        border-radius: 10px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #495057;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        text-align: left;
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
    .action-btn span { font-weight: 600; font-size: 0.85rem; line-height: 1.2; }

    .action-btn:hover {
        border-color: #0984e3;
        background: #f8fbff;
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .stats-grid, .actions-grid { grid-template-columns: 1fr; }
        .dashboard-visuals { 
            flex-direction: column;
            align-items: center;
         }
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

    <!-- Sección de Stats -->           
    <div class="stats-grid">
        
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?= $data['view_mode'] == 'daily' ? 'Pedidos Recibidos' : 'Total Pedidos' ?></h3>
                <p class="stat-value"><?php echo $data['pedidos_pendientes']; ?></p>
            </div>
        </div>
        
        <div class="stat-card income">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3><?= $data['view_mode'] == 'daily' ? 'Ingresos Hoy' : 'Ingresos Mes' ?></h3>
                <p class="stat-value">Gs. <?php echo number_format($data['ingresos_hoy'], 0, ',', '.'); ?></p>
            </div>
        </div>
        
        <div class="stat-card sales">
            <div class="stat-icon">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="stat-info">
                <h3>Platos Vendidos</h3>
                <p class="stat-value"><?php echo $data['platos_vendidos']; ?></p>
            </div>
        </div>
        
    </div>

    <div class="dashboard-visuals">
        
        <!-- Gráfico de Estado de Pedidos -->
        <div class="chart-container">
            <div class="chart-header">
                <h2><i class="fas <?= $data['view_mode'] == 'daily' ? 'fa-chart-pie' : 'fa-chart-bar' ?>"></i> <?= $data['view_mode'] == 'daily' ? 'Rendimiento del Día' : 'Ingresos por Día' ?></h2>
            </div>
            <div class="chart-wrapper">
                <canvas id="orderStatusChart"></canvas>
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
    <!-- Cierre de dashboard-visuals -->

</div>
<!-- Cierre de dashboard-container -->

<!-- Librería Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('orderStatusChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const viewMode = '<?= $data['view_mode'] ?>';
    
    if (viewMode === 'monthly') {
        const chartData = <?php echo json_encode($data['chart_data'] ?? []); ?>;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.day),
                datasets: [{
                    label: 'Ingresos (Gs.)',
                    data: chartData.map(d => d.income),
                    backgroundColor: '#0984e3',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-PY');
                            }
                        }
                    }
                }
            }
        });
        return;
    }

    // Datos desde PHP
    const pending = <?php echo (int)($data['pedidos_pendientes'] ?? 0); ?>;
    const completed = <?php echo (int)($data['pedidos_completados_hoy'] ?? 0); ?>;

    // Lógica para mostrar gráfico vacío si no hay datos
    const hasData = (pending + completed) > 0;
    const dataValues = hasData ? [pending, completed] : [0, 0];
    const bgColors = hasData ? ['#ffc107', '#28a745'] : ['#dfe6e9'];
    const labels = hasData ? ['Pendientes', 'Completados'] : ['Sin pedidos'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: bgColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>