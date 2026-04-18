<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Solver</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --gray-dark: #2d3436;
            --gray-medium: #636e72;
            --gray-light: #b2bec3;
            --gray-lighter: #dfe6e9;
            --blue-accent: #0984e3;
            --blue-hover: #0773c7;
            --green-success: #00b894;
            --green-hover: #00a07d;
            --warning-orange: #fdcb6e;
            --warning-bg: rgba(253, 203, 110, 0.15);
            --white: #ffffff;
            --background: #f8f9fa;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --shadow-sm: 0 2px 8px rgba(45, 52, 54, 0.08);
            --shadow-md: 0 4px 16px rgba(45, 52, 54, 0.12);
            --shadow-lg: 0 8px 32px rgba(45, 52, 54, 0.16);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--gray-dark);
            min-height: 100vh;
            line-height: 1.6;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Header */
        .dashboard-header {
            margin-bottom: 32px;
        }

        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-header h1 i {
            color: var(--blue-accent);
        }

        /* Alerts Section */
        .alerts-section {
            margin-bottom: 24px;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            font-size: 0.925rem;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }

        .alert-warning {
            background: var(--warning-bg);
            border: 1px solid var(--warning-orange);
            color: #b57d00;
        }

        .alert-warning i {
            color: #e09800;
            font-size: 1.1rem;
        }

        .alert-stock {
            font-weight: 600;
            color: #996a00;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Metric Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-md);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .metric-card.pedidos::before {
            background: var(--blue-accent);
        }

        .metric-card.ingresos::before {
            background: var(--green-success);
        }

        .metric-card.platos::before {
            background: var(--gray-medium);
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        .metric-card.pedidos .metric-icon {
            background: rgba(9, 132, 227, 0.12);
            color: var(--blue-accent);
        }

        .metric-card.ingresos .metric-icon {
            background: rgba(0, 184, 148, 0.12);
            color: var(--green-success);
        }

        .metric-card.platos .metric-icon {
            background: rgba(99, 110, 114, 0.12);
            color: var(--gray-medium);
        }

        .metric-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-medium);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-dark);
            line-height: 1.2;
        }

        /* Chart Section */
        .chart-section {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title i {
            color: var(--blue-accent);
        }

        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 400px;
            margin: 0 auto;
        }

        #orderStatusChart {
            max-width: 100%;
            max-height: 300px;
        }

        /* Quick Actions */
        .actions-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--blue-accent);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 28px 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
            color: var(--gray-dark);
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            cursor: pointer;
            min-height: 120px;
        }

        .action-btn:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .action-btn i {
            font-size: 1.75rem;
            transition: transform 0.2s ease;
        }

        .action-btn:hover i {
            transform: scale(1.1);
        }

        .action-btn.pos {
            border-bottom: 3px solid var(--blue-accent);
        }

        .action-btn.pos i {
            color: var(--blue-accent);
        }

        .action-btn.pos:hover {
            background: rgba(9, 132, 227, 0.08);
        }

        .action-btn.orders {
            border-bottom: 3px solid var(--green-success);
        }

        .action-btn.orders i {
            color: var(--green-success);
        }

        .action-btn.orders:hover {
            background: rgba(0, 184, 148, 0.08);
        }

        .action-btn.menus {
            border-bottom: 3px solid #a55eea;
        }

        .action-btn.menus i {
            color: #a55eea;
        }

        .action-btn.menus:hover {
            background: rgba(165, 94, 234, 0.08);
        }

        .action-btn.products {
            border-bottom: 3px solid #fd79a8;
        }

        .action-btn.products i {
            color: #fd79a8;
        }

        .action-btn.products:hover {
            background: rgba(253, 121, 168, 0.08);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 16px;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .metric-card {
                padding: 20px;
            }

            .metric-value {
                font-size: 1.875rem;
            }

            .chart-section {
                padding: 20px;
            }

            .chart-container {
                max-width: 280px;
            }

            .actions-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .action-btn {
                flex-direction: row;
                justify-content: flex-start;
                padding: 20px 24px;
                min-height: auto;
            }

            .action-btn i {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header h1 {
                font-size: 1.25rem;
            }

            .alert {
                font-size: 0.85rem;
                padding: 12px 14px;
            }

            .metric-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .metric-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <h1><i class="fas fa-chart-line"></i> <?php echo $data['title']; ?></h1>
        </header>

        <!-- Low Stock Alerts -->
        <?php if (!empty($data['low_stock_items'])): ?>
        <section class="alerts-section">
            <?php foreach ($data['low_stock_items'] as $item): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>
                    <strong><?php echo $item['product_name']; ?></strong> tiene stock bajo: 
                    <span class="alert-stock"><?php echo $item['daily_stock']; ?> unidades</span>
                </span>
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- Metric Cards -->
        <section class="metrics-grid">
            <div class="metric-card pedidos">
                <div class="metric-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <p class="metric-label">Pedidos Pendientes</p>
                <p class="metric-value"><?php echo $data['pedidos_pendientes']; ?></p>
            </div>

            <div class="metric-card ingresos">
                <div class="metric-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <p class="metric-label">Ingresos de Hoy</p>
                <p class="metric-value">Gs. <?php echo number_format($data['ingresos_hoy'], 0, ',', '.'); ?></p>
            </div>

            <div class="metric-card platos">
                <div class="metric-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <p class="metric-label">Platos Vendidos</p>
                <p class="metric-value"><?php echo $data['platos_vendidos']; ?></p>
            </div>
        </section>

        <!-- Chart Section -->
        <section class="chart-section">
            <div class="chart-header">
                <h2 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Estado de Pedidos de Hoy
                </h2>
            </div>
            <div class="chart-container">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="actions-section">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Acciones Rápidas
            </h2>
            <div class="actions-grid">
                <a href="?route=pos" class="action-btn pos">
                    <i class="fas fa-cash-register"></i>
                    <span>Punto de Venta</span>
                </a>
                <a href="?route=orders" class="action-btn orders">
                    <i class="fas fa-receipt"></i>
                    <span>Ver Pedidos</span>
                </a>
                <a href="?route=menus" class="action-btn menus">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Planificar Menú</span>
                </a>
                <a href="?route=products_create" class="action-btn products">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nuevo Producto</span>
                </a>
            </div>
        </section>
    </div>
</body>
</html>
</content>
<parameter name="taskNameActive">Creating dashboard
