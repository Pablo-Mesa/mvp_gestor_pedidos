<?php $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solver Logística - <?php echo $view_title ?? 'Panel'; ?></title>
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#00c853">
    <link rel="manifest" href="<?php echo $baseUrl; ?>manifest.json">
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/css_cubo.css" />
    <script src="<?php echo $baseUrl; ?>js/tool-kit-v002.js"></script>


    <style>
        :root {
            --delivery-bg: #f4f7f6;
            --delivery-card: #ffffff;
            --delivery-primary: #00c853;
            --delivery-text: #2d3436;
            --delivery-subtext: #636e72;
        }
        *{
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
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
        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .delivery-header h1 { font-size: 1.2rem; margin: 0; font-weight: 800; color: var(--delivery-primary); }

        /* Barra de Usuario */
        .user-info-bar {
            background-color: #ffffff;
            padding: 6px 15px;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            min-height: 45px;
        }
        .user-info-content {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--delivery-subtext);
            text-transform: uppercase;
        }
        .user-info-content i {
            color: var(--delivery-primary);
            font-size: 0.85rem;
        }
        
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
        
        /* Estilos de Submenú */
        .submenu { list-style: none; padding-left: 0; background: rgba(0,0,0,0.03); display: none; }
        .submenu.open { display: block; }
        .submenu .menu-item { padding-left: 50px; font-size: 0.9rem; border-bottom: none; }
        .menu-item i.arrow { margin-left: auto; transition: transform 0.3s ease; font-size: 0.8rem; }
        .menu-item.active i.arrow { transform: rotate(180deg); }

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
            display: flex;
            flex-direction: column;
            overflow: hidden; /* El scroll lo controlan las vistas internamente */
            padding: 15px;
            background: var(--delivery-bg);
        }

        /* Estilos del Filtro */
        .delivery-select-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            flex: 1;
            max-width: 200px;
        }
        .delivery-select-wrapper i {
            position: absolute;
            left: 10px;
            font-size: 0.8rem;
            color: var(--delivery-primary);
            pointer-events: none;
        }
        .delivery-select {
            width: 100%;
            padding: 6px 10px 6px 30px;
            background-color: var(--delivery-card);
            color: var(--delivery-text);
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
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

    <div id="offline-banner" style="display:none; background:#ff5252; color:white; text-align:center; padding:8px; font-size:0.85rem; font-weight:bold; flex-shrink:0; z-index:2000;">
        <i class="fas fa-exclamation-triangle"></i> Sin conexión a internet. Revisa tu señal.
    </div>

    <!-- Encabezado Principal -->
    <header class="delivery-header">
    
        <div class="header-brand">
            <div id="here_cube"></div>   
            <h1>SOLVER LOGÍSTICA</h1>
            <!--<i class="fas fa-route"></i>-->
        </div>

        <button class="menu-trigger" onclick="toggleMenu()" title="Menú">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Barra de Usuario Identificado -->
    <?php
        $c_all = 0; $c_p = 0; $c_e = 0; $c_c = 0; $c_r = 0;
        if (isset($orders) && is_array($orders)) {
            $c_all = count($orders);
            foreach ($orders as $o) {
                $st = $o['status'] ?? '';
                if ($st === 'confirmed' || $st === 'shipped') $c_p++;
                elseif ($st === 'completed') $c_e++;
                elseif ($st === 'cancelled') $c_c++;
                elseif ($st === 'rejected') $c_r++;
            }
        }
    ?>
    <div class="user-info-bar">
        <!-- Filtro de Pedidos (Lado Izquierdo) -->
        <div class="filter-side">
            <?php if (($_GET['route'] ?? 'delivery') === 'delivery'): ?>
            <div class="delivery-select-wrapper">
                <i class="fas fa-filter"></i>
                <select id="statusFilter" class="delivery-select">
                    <option value="all">Todos (<?php echo $c_all; ?>)</option>
                    <option value="pending_group">Pendientes (<?php echo $c_p; ?>)</option>
                    <option value="completed">Entregados (<?php echo $c_e; ?>)</option>
                    <option value="cancelled">Cancelados (<?php echo $c_c; ?>)</option>
                    <option value="rejected">Rechazados (<?php echo $c_r; ?>)</option>
                </select>
            </div>
            <?php elseif (($_GET['route'] ?? '') === 'delivery_history'): ?>
            <div class="delivery-select-wrapper">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" class="delivery-select" value="<?php echo $selectedDate ?? date('Y-m-d'); ?>" 
                       onchange="location.href='?route=delivery_history&date='+this.value">
            </div>
            <?php elseif (($_GET['route'] ?? '') === 'delivery_assists'): ?>
            <div class="delivery-select-wrapper">
                <i class="fas fa-calendar-day"></i>
                <input type="month" class="delivery-select" value="<?php echo $selectedMonth ?? date('Y-m'); ?>" 
                       onchange="location.href='?route=delivery_assists&month='+this.value">
            </div>
            <?php else: ?>
                <div style="flex:1"></div>
            <?php endif; ?>
        </div>

        <div class="user-info-content">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </div>
    </div>

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
            
            <!-- Grupo de Asistencias -->
            <div class="menu-group">
                <a href="javascript:void(0)" class="menu-item" onclick="toggleSubmenu('submenu-asistencias', this)">
                    <i class="fas fa-user-clock"></i> Asistencias
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div id="submenu-asistencias" class="submenu">
                    <a href="?route=delivery_checkin" class="menu-item"><i class="fas fa-map-marker-alt"></i> Marcar llegada</a>
                    <a href="?route=delivery_assists" class="menu-item"><i class="fas fa-list-ul"></i> Historial de asistencias</a>
                </div>
            </div>

            <a href="?route=delivery_production" class="menu-item"><i class="fas fa-coins"></i> Mi Producción</a>
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
    <script src="<?php echo $baseUrl; ?>js/toast.js"></script>    
    <script> drawCube("here_cube", false, "28px"); </script>
    
    <script>
        let deferredPrompt;

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo $baseUrl; ?>sw.js')
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

        // Detector de conexión a internet
        function updateOnlineStatus() {
            const banner = document.getElementById('offline-banner');
            if (navigator.onLine) {
                banner.style.display = 'none';
            } else {
                banner.style.display = 'block';
            }
        }
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>

    <script>
        /**
         * Actualiza dinámicamente los contadores del selector de estados
         */
        function updateFilterCounts(orders) {
            const filter = document.getElementById('statusFilter');
            if (!filter) return;

            const counts = { all: orders.length, p: 0, e: 0, c: 0, r: 0 };
            orders.forEach(o => {
                if (o.status === 'confirmed' || o.status === 'shipped') counts.p++;
                else if (o.status === 'completed') counts.e++;
                else if (o.status === 'cancelled') counts.c++;
                else if (o.status === 'rejected') counts.r++;
            });

            filter.options[0].text = `Todos (${counts.all})`;
            filter.options[1].text = `Pendientes (${counts.p})`;
            filter.options[2].text = `Entregados (${counts.e})`;
            filter.options[3].text = `Cancelados (${counts.c})`;
            filter.options[4].text = `Rechazados (${counts.r})`;
        }
    </script>

    <script>
        // Lógica del Menú Lateral
        function toggleMenu() {
            document.getElementById('sideMenu').classList.toggle('open');
            document.getElementById('menuOverlay').classList.toggle('open');
        }

        // Lógica de Submenús
        function toggleSubmenu(id, el) {
            el.classList.toggle('active');
            document.getElementById(id).classList.toggle('open');
        }

        // Lógica de filtrado en tiempo real
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                const status = this.value;
                const cards = document.querySelectorAll('.order-card');
                
                cards.forEach(card => {
                    const cardStatus = card.getAttribute('data-status');
                    if (status === 'all') {
                        card.style.display = ''; // Restablece al estilo original
                    } else if (status === 'pending_group') {
                        // Muestra 'confirmed' (asignado) y 'shipped' (en camino)
                        card.style.display = (cardStatus === 'confirmed' || cardStatus === 'shipped') ? '' : 'none';
                    } else {
                        card.style.display = (cardStatus === status) ? '' : 'none';
                    }
                });
            });
        }
    </script>
    
</body>
</html>