<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solver - Panel de Control</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    <link rel="stylesheet" href="css/css_cubo.css">
    <!-- Bootstrap 5 para la interfaz administrativa -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/toast.css"> <!-- Estilos de Alertas -->
    <style> 
        /* Reset y Estilos Base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f4f6f9;
        }

        /* Sidebar (Menú Lateral) */
        .sidebar {
            width: 250px; 
            background-color: #343a40;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 1050;
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background-color: #212529;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .sidebar-menu { list-style: none; padding: 0; margin-top: 1rem; }
        .sidebar-menu li a { display: block; padding: 1rem 1.5rem; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; transition: 0.3s; }
        .sidebar-menu li a:hover { background-color: #495057; color: #fff; padding-left: 2rem; }

        /* Contenido Principal */
        .main-content { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            min-width: 0; /* Evita que el contenido desborde el flex container */
            background-color: transparent;
        }

        /* Topbar (Barra Superior) */
        .topbar { 
            background-color: #fff; 
            padding: 0.75rem 1.5rem; 
            border-bottom: 1px solid #dee2e6; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .topbar-left { display: flex; align-items: center; gap: 15px; }
        
        .menu-toggle {
            display: none; /* Oculto en escritorio */
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #343a40;
            cursor: pointer;
        }

        .user-info { display: flex; align-items: center; }
        .btn-logout { background-color: #dc3545; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }

        /* Overlay para móviles */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -250px;
                height: 100vh;
            }
            .sidebar.active { left: 0; }
            .sidebar-overlay.active { display: block; }
            .menu-toggle { display: block; }
            .topbar h2 { font-size: 1.1rem; margin: 0; }
        }

        /* Área de Contenido */
        .content-wrapper { padding: 1rem; overflow-y: auto; }
        
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

        /* Estilos para el Modal de Confirmación Reutilizable */
        .modal-confirm-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-confirm-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 90%;
            text-align: center;
            animation: modalSlideUp 0.3s ease-out;
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- 1. Menú Lateral -->
    <nav class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div id="here_cube" class="h1-cf-dark"></div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?route=admin">📊 Dashboard</a></li>
            <li><a href="?route=pos">🖥️ Punto de Venta</a></li>
            <li><a href="?route=orders">📦 Pedidos</a></li>
            <li><a href="?route=menus">📅 Menú del Día</a></li>
            <li><a href="?route=products">🍔 Menú / Productos</a></li>
            <li><a href="?route=categories">🏷️ Categorías</a></li> 
            <li><a href="?route=hero_promos">🎨 Hero Promo</a></li>
            <li><a href="?route=settings">⚙️ Ajustes Marca</a></li>
            <li><a href="?route=users">👥 Staff / Usuarios</a></li>
        </ul>
    </nav>

    <!-- 2. Cuerpo Principal -->
    <div class="main-content">
        <!-- Barra Superior -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Panel de Control</h2>
            </div>
            <div class="user-info">                
                <span class="span-user d-none d-md-inline-flex">Hola, <i class="fas fa-user"></i> <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <a href="?route=logout&type=admin" class="btn-logout ms-2"><i class="fas fa-sign-out-alt"></i> <span class="d-none d-sm-inline">Salir</span></a>
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

    <!-- Modal de Confirmación Global -->
    <div id="globalConfirmModal" class="modal-confirm-overlay">
        <div class="modal-confirm-card">
            <div id="globalConfirmIcon" style="color: #dc3545; font-size: 3.5rem; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 id="globalConfirmTitle" style="margin: 0 0 0.5rem 0; color: #212529;">¿Estás seguro?</h2>
            <p id="globalConfirmMessage" style="color: #6c757d; margin-bottom: 2rem;"></p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button onclick="closeConfirmModal()" class="btn" style="background-color: #6c757d; border: none; color: white;">Cancelar</button>
                <a id="globalConfirmLink" href="#" class="btn">Confirmar</a>
            </div>
        </div>
    </div>

    <script src="js/tool-kit-v002.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/toast.js"></script> <!-- JS de Alertas -->
    <script>
        if(document.getElementById('here_cube')) {
            // En el admin, si no hay logo, dibujamos el cubo un poco más pequeño para el sidebar
            drawCube("here_cube", true, "24px");
        }

        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        /**
         * Función Global de Confirmación
         */
        function confirmAction(url, options = {}) {
            const modal = document.getElementById('globalConfirmModal');
            document.getElementById('globalConfirmTitle').innerText = options.title || '¿Estás seguro?';
            document.getElementById('globalConfirmMessage').innerText = options.message || 'Esta acción no se puede deshacer.';
            
            const link = document.getElementById('globalConfirmLink');
            link.href = url;
            link.innerText = options.btnText || 'Confirmar';
            link.className = 'btn ' + (options.btnClass || 'btn-danger');
            
            modal.style.display = 'flex';
        }

        function closeConfirmModal() {
            document.getElementById('globalConfirmModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera del card
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('globalConfirmModal');
            if (e.target === modal) closeConfirmModal();
        });
    </script>
</body>
</html>