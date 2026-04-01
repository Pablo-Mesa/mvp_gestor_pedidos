<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solver Logística - <?php echo $view_title ?? 'Panel'; ?></title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --delivery-bg: #f4f7f6;
            --delivery-card: #ffffff;
            --delivery-primary: #00c853;
            --delivery-text: #2d3436;
            --delivery-subtext: #636e72;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: var(--delivery-bg);
            color: var(--delivery-text);
        }
        .delivery-header {
            background-color: #ffffff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .delivery-header h1 { font-size: 1.2rem; margin: 0; font-weight: 800; color: var(--delivery-primary); }
        .logout-link { color: #ff5252; text-decoration: none; font-size: 1.2rem; }
        .delivery-main { padding: 15px; }

        /* Estilos del Filtro */
        .filter-container {
            padding: 10px 15px;
            background-color: var(--delivery-bg);
        }
        .delivery-select-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .delivery-select-wrapper i {
            position: absolute;
            left: 12px;
            color: var(--delivery-primary);
            pointer-events: none;
        }
        .delivery-select {
            width: 100%;
            padding: 12px 12px 12px 40px;
            background-color: var(--delivery-card);
            color: var(--delivery-text);
            border: 1px solid #dcdde1;
            border-radius: 10px;
            font-size: 0.95rem;
            appearance: none;
            outline: none;
            transition: border-color 0.3s;
        }
        .delivery-select:focus {
            border-color: var(--delivery-primary);
        }
        .delivery-section-title {
            font-size: 1rem;
            font-weight: bold;
            color: var(--delivery-subtext);
            margin: auto 0;
            display: flex;
            align-items: center;
            width: 100%;
            gap: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <header class="delivery-header">
        <h1><i class="fas fa-route"></i> SOLVER LOGÍSTICA</h1>
        <div style="display: flex; gap: 20px; align-items: center;">
            <span style="font-size: 0.8rem; color: var(--delivery-subtext);"><?php echo htmlspecialchars($_SESSION['user_name']); ?> (Repartidor)</span>
            <!-- Usamos type=admin porque ambos pertenecen a la tabla 'users' de administración -->
            <a href="?route=logout&type=admin" class="logout-link" title="Cerrar Sesión"><i class="fas fa-power-off"></i></a>
        </div>
    </header>

    <h2 class="delivery-section-title"><i class="fas fa-truck"></i> Mis Entregas Activas</h2>

    <div class="filter-container">
        <div class="delivery-select-wrapper">
            <i class="fas fa-filter"></i>
            <select id="statusFilter" class="delivery-select">
                <option value="all">Ver todos los pedidos</option>
                <option value="ready">Pendientes (Para retirar)</option>
                <option value="shipped">En curso (En camino)</option>
                <option value="completed">Finalizados (Entregados)</option>
            </select>
        </div>
    </div>

    <main class="delivery-main">
        <?php if (isset($content_view) && file_exists($content_view)) require_once $content_view; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/toast.js"></script>

    <script>
        // Lógica de filtrado en tiempo real
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const cards = document.querySelectorAll('.order-card');
            
            cards.forEach(card => {
                if (status === 'all') {
                    card.style.display = ''; // Restablece al estilo original (flex/block)
                } else {
                    // Comparación estricta con el atributo data-status
                    card.style.display = (card.getAttribute('data-status') === status) ? '' : 'none';
                }
            });
        });
    </script>
</body>
</html>