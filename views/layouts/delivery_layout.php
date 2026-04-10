<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solver Logística - <?php echo $view_title ?? 'Panel'; ?></title>
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#00c853">
    <link rel="manifest" href="manifest.json">
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
        
        /* Botón de Menú */
        .menu-trigger {
            background: none;
            border: none;
            color: var(--delivery-text);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s;
        }

        /* Sidebar Lateral */
        .menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
            backdrop-filter: blur(2px);
        }
        .side-menu {
            position: fixed;
            top: 0;
            right: -280px;
            width: 280px;
            height: 100%;
            background: white;
            z-index: 1001;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }
        .side-menu.open { right: 0; }
        .menu-overlay.open { display: block; }

        .side-menu-header {
            padding: 30px 20px;
            background: var(--delivery-bg);
            border-bottom: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .side-menu-header i { font-size: 2.5rem; color: var(--delivery-primary); }
        .side-menu-header span { font-weight: 700; color: var(--delivery-text); }

        .menu-nav { flex: 1; padding: 20px 0; }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            text-decoration: none;
            color: var(--delivery-text);
            font-weight: 600;
            transition: background 0.2s;
        }
        .menu-item:hover { background: #f8f9fa; }
        .menu-item i { width: 20px; color: var(--delivery-subtext); }
        
        .menu-footer {
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .logout-item {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #ff5252;
            text-decoration: none;
            font-weight: 700;
            padding: 10px;
        }
        
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

    <!-- Encabezado Principal -->
    <header class="delivery-header">
        <h1><i class="fas fa-route"></i> SOLVER LOGÍSTICA</h1>
        <div>
            <button class="menu-trigger" onclick="toggleMenu()" title="Menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Menú Lateral -->
    <div id="menuOverlay" class="menu-overlay" onclick="toggleMenu()"></div>
    <div id="sideMenu" class="side-menu">
        <div class="side-menu-header">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <small style="color: var(--delivery-subtext);">Repartidor</small>
        </div>
        <nav class="menu-nav">
            <a href="?route=delivery" class="menu-item"><i class="fas fa-clipboard-list"></i> Pedidos Activos</a>
            <a href="?route=delivery_history" class="menu-item"><i class="fas fa-history"></i> Historial y Cuentas</a>
            <a href="#" id="btnInstallDelivery" class="menu-item" style="display: none; color: var(--delivery-primary);">
                <i class="fas fa-mobile-alt"></i> Instalar App en Celular
            </a>
            <!-- Aquí podrás agregar más secciones en el futuro -->
        </nav>
        <div class="menu-footer">
            <a href="?route=logout&type=admin" class="logout-item">
                <i class="fas fa-power-off"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Contenedor Principal -->
    <main class="delivery-main">
        <?php if (isset($content_view) && file_exists($content_view)) require_once $content_view; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/toast.js"></script>

    <script>
        let deferredPrompt;

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('Solver Logística PWA activo'))
                    .catch(err => console.log('Error registro SW Logística', err));
            });
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const btnInstall = document.getElementById('btnInstallDelivery');
            if (btnInstall) {
                btnInstall.style.display = 'flex';
                btnInstall.addEventListener('click', async (ev) => {
                    ev.preventDefault();
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') btnInstall.style.display = 'none';
                    deferredPrompt = null;
                });
            }
        });
    </script>

    <script>
        // Lógica del Menú Lateral
        function toggleMenu() {
            document.getElementById('sideMenu').classList.toggle('open');
            document.getElementById('menuOverlay').classList.toggle('open');
        }

        // Lógica de filtrado en tiempo real
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value;
            const cards = document.querySelectorAll('.order-card');
            
            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (status === 'all') {
                    card.style.display = ''; // Restablece al estilo original (flex/block)
                } else if (status === 'pending_group') {
                    // Muestra 'confirmed' (asignado) y 'shipped' (en camino)
                    card.style.display = (cardStatus === 'confirmed' || cardStatus === 'shipped') ? '' : 'none';
                } else {
                    card.style.display = (cardStatus === status) ? '' : 'none';
                }
            });
        });
    </script>
    
</body>
</html>