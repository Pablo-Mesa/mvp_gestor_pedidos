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
            --delivery-bg: #121212;
            --delivery-card: #1e1e1e;
            --delivery-primary: #00e676;
            --delivery-text: #ffffff;
            --delivery-subtext: #b0b0b0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: var(--delivery-bg);
            color: var(--delivery-text);
        }
        .delivery-header {
            background-color: var(--delivery-card);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--delivery-primary);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .delivery-header h1 { font-size: 1.2rem; margin: 0; font-weight: 800; color: var(--delivery-primary); }
        .logout-link { color: #ff5252; text-decoration: none; font-size: 1.2rem; }
        .delivery-main { padding: 15px; }
    </style>
</head>
<body>
    <header class="delivery-header">
        <h1><i class="fas fa-route"></i> SOLVER LOGÍSTICA</h1>
        <div style="display: flex; gap: 20px; align-items: center;">
            <span style="font-size: 0.8rem; color: var(--delivery-subtext);"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="?route=logout&type=admin" class="logout-link"><i class="fas fa-power-off"></i></a>
        </div>
    </header>

    <main class="delivery-main">
        <?php if (isset($content_view) && file_exists($content_view)) require_once $content_view; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/toast.js"></script>
</body>
</html>