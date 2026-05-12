<?php 
    $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); 
    $siteLogo = $baseUrl . 'assets/icono_solver_nobg.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Solver Mozos - <?php echo $view_title ?? 'Panel'; ?></title>
    
    <!-- Configuración para App Web (PWA/Mobile) -->
    <meta name="theme-color" content="#3498db">
    <link rel="icon" type="image/png" href="<?php echo $siteLogo; ?>">
    <link rel="apple-touch-icon" href="<?php echo $siteLogo; ?>">
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/css_cubo.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/toast.css">
    
    <style>
        :root {
            --waiter-primary: #3498db;
            --waiter-dark: #2c3e50;
            --waiter-bg: #f8f9fa;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--waiter-bg);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Cabecera compacta */
        .waiter-header {
            background-color: white;
            color: var(--waiter-dark);
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-brand h1 { font-size: 1rem; margin: 0; font-weight: 800; color: var(--waiter-primary); text-transform: uppercase; letter-spacing: 1px; }
        
        .waiter-main {
            flex: 1;
            padding: 15px;
            padding-bottom: 90px; /* Espacio para el nav inferior */
        }

        /* Navegación Inferior Estilo App */
        .waiter-nav {
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -2px 15px rgba(0,0,0,0.08);
        }
        .nav-item {
            text-decoration: none;
            color: #95a5a6;
            text-align: center;
            font-size: 0.7rem;
            flex: 1;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .nav-item i { font-size: 1.5rem; margin-bottom: 2px; }
        .nav-item.active { color: var(--waiter-primary); font-weight: bold; }

        /* Sidebar para opciones secundarias */
        .side-menu {
            position: fixed;
            top: 0;
            right: -280px;
            width: 280px;
            height: 100%;
            background: white;
            z-index: 2000;
            transition: right 0.3s ease;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }
        .side-menu.open { right: 0; }
        .menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1999;
            display: none;
            backdrop-filter: blur(2px);
        }
        .menu-overlay.open { display: block; }
        
        .user-section {
            padding: 30px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>

    <header class="waiter-header">
        <div class="header-brand">
            <img src="<?php echo $siteLogo; ?>" alt="Logo Solver" style="height: 24px; width: auto; margin-right: 8px;">
            <h1>Solver Mozo</h1>
        </div>
        <button class="btn btn-link text-dark p-0" onclick="toggleMenu()">
            <i class="fas fa-user-circle fs-3"></i>
        </button>
    </header>

    <!-- Menú Lateral -->
    <div id="menuOverlay" class="menu-overlay" onclick="toggleMenu()"></div>
    <div id="sideMenu" class="side-menu">
        <div class="user-section text-center">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'M', 0, 1)); ?>
            </div>
            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Mozo'); ?></h5>
            <span class="badge bg-light text-dark border">Servicio de Salón</span>
        </div>
        <div class="list-group list-group-flush flex-grow-1">
            <?php if(($_SESSION['user_role'] ?? '') === 'admin'): ?>
                <a href="?route=admin" class="list-group-item list-group-item-action py-3">
                    <i class="fas fa-tachometer-alt me-3 text-secondary"></i> Panel Administrativo
                </a>
            <?php endif; ?>
            <a href="?route=mozo_profile" class="list-group-item list-group-item-action py-3">
                <i class="fas fa-chart-line me-3 text-success"></i> Mi Rendimiento
            </a>
        </div>
        <div class="p-3 border-top">
            <a href="?route=logout&type=admin" class="btn btn-outline-danger w-100 py-2">
                <i class="fas fa-power-off me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Contenido Dinámico -->
    <main class="waiter-main">
        <?php 
        if (isset($content_view) && file_exists($content_view)) {
            require_once $content_view; 
        } else {
            echo '<div class="text-center py-5 opacity-50"><i class="fas fa-folder-open fa-3x mb-3"></i><p>No hay una vista asignada.</p></div>';
        }
        ?>
    </main>

    <!-- Barra de Navegación Inferior -->
    <nav class="waiter-nav">
        <?php $current = $_GET['route'] ?? ''; ?>
        <a href="?route=mozo_pos" class="nav-item <?php echo $current === 'mozo_pos' ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i>
            Tomar Pedido
        </a>
        <a href="?route=mozo_tables" class="nav-item <?php echo $current === 'mozo_tables' ? 'active' : ''; ?>">
            <i class="fas fa-chair"></i>
            Mesas
        </a>
        <a href="?route=mozo_history" class="nav-item <?php echo $current === 'mozo_history' ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i>
            Mis Ventas
        </a>
    </nav>

    <!-- Scripts Comunes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $baseUrl; ?>js/toast.js"></script>
    <script src="<?php echo $baseUrl; ?>js/tool-kit-v002.js"></script>
    
    <script>
        // Control del menú lateral
        function toggleMenu() {
            document.getElementById('sideMenu').classList.toggle('open');
            document.getElementById('menuOverlay').classList.toggle('open');
        }
    </script>
</body>
</html>