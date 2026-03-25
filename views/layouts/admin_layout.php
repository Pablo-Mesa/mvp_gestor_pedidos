<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Comedor App</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    <link rel="stylesheet" href="css/css_cubo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset y Estilos Base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; min-height: 100vh; background-color: #f4f6f9; }
        
        /* Sidebar (Menú Lateral) */
        .sidebar {
            width: 250px; 
            background-color: #343a40;
            color: #fff;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background-color: #212529;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

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

        .span-user {
            font-family: 'Segoe UI', Roboto, sans-serif; /* Tipografía moderna */
            font-size: 0.95rem;                          /* Tamaño equilibrado */
            color: #495057;                              /* Gris oscuro elegante */
            font-weight: 500;                            /* Peso medio para que no sea muy grueso */
            padding: 4px 12px;                           /* Espaciado interno */
            background-color: #f8f9fa;                   /* Fondo muy sutil */
            border-radius: 20px;                         /* Bordes redondeados (estilo pastilla) */
            border: 1px solid #e9ecef;                   /* Borde suave para dar estructura */
            display: inline-flex;                        /* Para que alinee bien con los iconos */
            align-items: center;
            white-space: nowrap;    
            gap: 0.5rem;                     /* Evita que el nombre se parta en dos líneas */
        }

        /* Efecto opcional: Resaltar el nombre específicamente si quieres */
        .span-user strong {
            color: #007bff;                              /* El nombre en azul */
            margin-left: 4px;
        }

        .h1-cf-dark h1{
            color: #f4f6f9; /*#212529*/
        }

        /* Filtros de Categoría */
        .filter-container {
            display: flex; 
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 6px;
            width: 40%;
            overflow: hidden;
            overflow-x: auto;
            padding: 0 20px;                        
        }
        .btn-filter { padding: 6px 16px; background-color: #fff; border: 1px solid #ced4da; border-radius: 50px; text-decoration: none; color: #495057; font-size: 0.9rem; transition: all 0.2s; }
        .btn-filter:hover { background-color: #e9ecef; border-color: #adb5bd; }
        .btn-filter.active { background-color: #007bff; color: #fff; border-color: #007bff; }
    </style>
</head>
<body>

    <!-- 1. Menú Lateral -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <div id="here_cube" class="mr-1 h1-cf-dark"></div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?route=admin">📊 Dashboard</a></li>
            <li><a href="?route=orders">📦 Pedidos</a></li>
            <li><a href="?route=menus">📅 Menú del Día</a></li>
            <li><a href="?route=products">🍔 Menú / Productos</a></li>
            <li><a href="?route=categories">🏷️ Categorías</a></li>            
            <!-- <li><a href="#">👥 Usuarios</a></li> -->
        </ul>
    </nav>

    <!-- 2. Cuerpo Principal -->
    <div class="main-content">
        <!-- Barra Superior -->
        <header class="topbar">
            <h2>Panel de Control</h2>
            <div class="user-info">                
                <span class="span-user">Hola, <i class="fas fa-user"></i> <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <a href="?route=logout&type=admin" class="btn-logout">Cerrar Sesión</a>
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
    
    <script src="js/tool-kit-v002.js"></script>
    <script>
        drawCube("here_cube", true, "28px");
    </script>
</body>
</html>