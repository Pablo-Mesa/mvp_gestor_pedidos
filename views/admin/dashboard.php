<style>
        
    .dashboard-container {
        padding: 0px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 30px;
        text-align: left;
    }

    .dashboard-header h1 { color: #2d3436; font-size: 1.8rem; margin-bottom: 5px; }
    .dashboard-header p { color: #636e72; }

    /* Alertas */
    .alert-section { margin-bottom: 25px; }
    .alert-banner {
        background: #fff3cd;
        border-left: 5px solid #ffc107;
        padding: 15px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #856404;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-alert {
        margin-left: auto;
        background: #856404;
        color: white;
        padding: 5px 12px;
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
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 6px solid #dfe6e9;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .stat-card.pending { border-left-color: #ffc107; }
    .stat-card.income { border-left-color: #28a745; }
    .stat-card.sales { border-left-color: #17a2b8; }

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
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-end;
        width: 100%;
        height: auto;
    }

    .chart-container {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
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
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;   
        width: 100%;    
    }
    .quick-actions h2 { 

        text-align: left;
        font-size: 1.2rem; 
        color: #2d3436; 
        margin-bottom: 20px;
        font-weight: 600;
    }

    .actions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        background: white;
        border: 1px solid #dfe6e9;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #2d3436;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .action-btn i { font-size: 1.5rem; color: #0984e3; }
    .action-btn span { font-weight: 500; font-size: 0.95rem; }

    .action-btn:hover {
        border-color: #0984e3;
        background: #f0f7ff;
        transform: translateY(-3px);
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
        <h1><i class="fas fa-chart-line"></i> <?php echo $data['title']; ?></h1>
        <p>Resumen de actividad para el día de hoy.</p>
    </header>

    <!-- Sección de Alertas de Stock Bajo -->
    <?php if (!empty($data['low_stock_items'])): ?>
    <div class="alert-section">
        <?php foreach ($data['low_stock_items'] as $item): ?>
        <div class="alert-banner warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span><strong>Atención:</strong> Quedan pocas porciones de <strong><?php echo $item['product_name']; ?></strong> (Solo <?php echo $item['daily_stock']; ?> restantes).</span>
            <a href="?route=menus&date=<?php echo date('Y-m-d'); ?>" class="btn-alert">Gestionar</a>
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
                <h3>Pedidos Pendientes</h3>
                <p class="stat-value"><?php echo $data['pedidos_pendientes']; ?></p>
            </div>
        </div>
        
        <div class="stat-card income">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>Ingresos Hoy</h3>
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
                <h2><i class="fas fa-chart-pie"></i> Rendimiento de Hoy</h2>
            </div>
            <div class="chart-wrapper">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        <!-- Sección de Acciones Rápidas -->
        <section class="quick-actions">
            <h2>Acciones Rápidas</h2>
            <div class="actions-grid">
                <a href="?route=pos" class="action-btn">
                    <i class="fas fa-cash-register"></i>
                    <span>Punto de venta <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + P]</small></span>
                </a>
                <a href="?route=orders" class="action-btn">
                    <i class="fas fa-list-ul"></i>
                    <span>Ver Pedidos <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + O]</small></span>
                </a>
                <a href="?route=menus" class="action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Planificar Menú <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + K]</small></span>
                </a>
                <a href="?route=products_create" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nuevo Producto <small class="text-muted d-block" style="font-size: 0.7rem;">[Alt + N]</small></span>
                </a>
            </div>
        </section>

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
    
    // Datos desde PHP
    const pending = <?php echo (int)($data['pedidos_pendientes_hoy'] ?? 0); ?>;
    const completed = <?php echo (int)($data['pedidos_completados_hoy'] ?? 0); ?>;

    // Lógica para mostrar gráfico vacío si no hay datos
    const hasData = (pending + completed) > 0;
    const dataValues = hasData ? [pending, completed] : [1];
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