<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Comedor App - Cliente</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; }
        .navbar { background-color: #ffffff; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: #333; text-decoration: none; }
        .user-menu span { margin-right: 1rem; color: #555; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .container { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); padding: 1.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="?route=home" class="navbar-brand">🍽️ Comedor App</a>
        <div class="user-menu">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>
                <a href="?route=logout" class="btn btn-danger">Cerrar Sesión</a>
            <?php else: ?>
                <a href="?route=login" class="btn btn-primary">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <?php 
        if (isset($content_view) && file_exists($content_view)) {
            require_once $content_view; 
        } else {
            echo '<div class="card"><h3>Página no encontrada</h3><p>La vista solicitada no existe.</p></div>';
        }
        ?>
    </main>
</body>
</html>