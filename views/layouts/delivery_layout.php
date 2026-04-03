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
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Evita el scroll en el cuerpo principal */
        }
        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: var(--delivery-bg);
            color: var(--delivery-text);
            display: flex;
            flex-direction: column;
        }
        .delivery-header {
            background-color: #ffffff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            z-index: 100;
            flex-shrink: 0; /* No permite que el header se encoja */
        }
        .delivery-header h1 { font-size: 1.2rem; margin: 0; font-weight: 800; color: var(--delivery-primary); }
        .logout-link { color: #ff5252; text-decoration: none; font-size: 1.2rem; }
        
        .delivery-main { 
            flex: 1; /* Ocupa todo el espacio restante */
            overflow-y: auto; /* Único contenedor scrolleable */
            padding: 15px;
            -webkit-overflow-scrolling: touch; /* Scroll suave en iOS */
            background: var(--delivery-bg);
        }

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

    <div class="filter-container">
        <div class="delivery-select-wrapper">
            <i class="fas fa-filter"></i>
            <select id="statusFilter" class="delivery-select">
                <option value="all">1. Todos los pedidos</option>
                <option value="pending_group">2. Pendientes</option>
                <option value="completed">3. Entregados</option>
                <option value="cancelled">4. Cancelados</option>
                <option value="rejected">5. Rechazados</option>
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
                const cardStatus = card.getAttribute('data-status');
                if (status === 'all') {
                    card.style.display = ''; // Restablece al estilo original (flex/block)
                } else if (status === 'pending_group') {
                    // Muestra 'ready' (por retirar) y 'shipped' (en camino)
                    card.style.display = (cardStatus === 'ready' || cardStatus === 'shipped') ? '' : 'none';
                } else {
                    card.style.display = (cardStatus === status) ? '' : 'none';
                }
            });
        });
    </script>
</body>
</html>