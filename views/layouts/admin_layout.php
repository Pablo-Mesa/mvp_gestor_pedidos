<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Comedor App</title>
    <style>
        /* Reset y Estilos Base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; min-height: 100vh; background-color: #f4f6f9; }
        
        /* Sidebar (Menú Lateral) */
        .sidebar { width: 250px; background-color: #343a40; color: #fff; display: flex; flex-direction: column; }
        .sidebar-header { padding: 1.5rem; background-color: #212529; text-align: center; font-weight: bold; font-size: 1.2rem; }
        .sidebar-menu { list-style: none; padding: 0; margin-top: 1rem; }
        .sidebar-menu li a { display: block; padding: 1rem 1.5rem; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; transition: 0.3s; }
        .sidebar-menu li a:hover { background-color: #495057; color: #fff; padding-left: 2rem; }
        
        /* Contenido Principal */
        .main-content { flex: 1; display: flex; flex-direction: column; }
        
        /* Topbar (Barra Superior) */
        .topbar { background-color: #fff; padding: 1rem 2rem; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
        .user-info span { font-weight: bold; color: #333; margin-right: 1rem; }
        .btn-logout { background-color: #dc3545; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }
        
        /* Área de Contenido */
        .content-wrapper { padding: 2rem; overflow-y: auto; }
        
        /* Utilidades para Dashboard (Cards) */
        .card-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #007bff; }
        .card h3 { margin-bottom: 0.5rem; color: #666; font-size: 0.9rem; text-transform: uppercase; }
        .card p { font-size: 1.8rem; font-weight: bold; color: #333; }
    </style>
</head>
<body>

    <!-- 1. Menú Lateral -->
    <nav class="sidebar">
        <div class="sidebar-header">Comedor App</div>
        <ul class="sidebar-menu">
            <li><a href="?route=admin">📊 Dashboard</a></li>
            <li><a href="?route=orders">📦 Pedidos</a></li>
            <li><a href="?route=menus">📅 Menú del Día</a></li>
            <li><a href="?route=categories">🏷️ Categorías</a></li>
            <li><a href="?route=products">🍔 Menú / Productos</a></li>
            <li><a href="#">👥 Usuarios</a></li>
        </ul>
    </nav>

    <!-- 2. Cuerpo Principal -->
    <div class="main-content">
        <!-- Barra Superior -->
        <header class="topbar">
            <h2>Panel de Control</h2>
            <div class="user-info">
                <span>Hola, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <a href="?route=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Vista Dinámica -->
        <main class="content-wrapper">
            <?php 
            // Aquí se incluye la vista específica (ej: dashboard.php)
            if (file_exists($content_view)) {
                include $content_view; 
            } else {
                echo "Error: No se encontró la vista.";
            }
            ?>
        </main>
    </div>

</body>
</html>