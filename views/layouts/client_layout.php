<!DOCTYPE html>
<?php
// Calcular la ruta base para que los recursos carguen siempre bien en PC y Móvil
$baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);

// Lógica para obtener categorías disponibles para la navegación
if (!class_exists('Setting')) {
    $path = 'models/Setting.php';
    if (file_exists($path)) require_once $path;
    elseif (file_exists('../' . $path)) require_once '../' . $path;
}
if (!class_exists('Category')) {
    // Intentar cargar el modelo si no está cargado
    $path = 'models/Category.php';
    if (file_exists($path)) require_once $path;
    elseif (file_exists('../' . $path)) require_once '../' . $path;
}
if (!class_exists('Product')) {
    $path = 'models/Product.php';
    if (file_exists($path)) require_once $path;
    elseif (file_exists('../' . $path)) require_once '../' . $path;
}

$navCategories = [];
if (class_exists('Category') && class_exists('Product')) {
    $catModel = new Category();
    $prodModel = new Product();
    $clientId = $_SESSION['client_id'] ?? null;

    // Obtenemos todas las categorías y los productos activos para validar
    $allCategories = $catModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
    $activeProds = $prodModel->readAllActive($clientId)->fetchAll(PDO::FETCH_ASSOC);
    
    // Extraemos los IDs de categorías que tienen al menos un producto activo
    $activeCategoryIds = array_unique(array_column($activeProds, 'category_id'));

    // Filtramos la lista: Solo incluimos categorías con contenido disponible y excluimos "Almuerzos"
    $navCategories = array_filter($allCategories, function($cat) use ($activeCategoryIds) {
        $isAlmuerzo = (stripos($cat['name'], 'almuerzo') !== false);
        return in_array($cat['id'], $activeCategoryIds) && !$isAlmuerzo;
    });
}

// Detectar si estamos en la Home para mostrar categorías
$currentRoute = $_GET['route'] ?? 'home';
$showCategories = ($currentRoute === 'home');

// Cargar Ajustes de Identidad (Siempre disponible)
$settingModel = new Setting();
$siteSettings = $settingModel->getAll();
$siteName = !empty($siteSettings['site_name']) ? $siteSettings['site_name'] : 'Solver';
$siteLogo = !empty($siteSettings['site_logo']) ? $baseUrl . 'uploads/' . $siteSettings['site_logo'] : $baseUrl . 'assets/icono_solver_nobg.png';
?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Solver - Home</title>
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2d3436">
    <link rel="manifest" href="<?php echo $baseUrl; ?>manifest.json">
    <link rel="apple-touch-icon" href="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png">
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png">    
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/css_cubo.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/client_layout.css">   
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/toast.css"> <!-- Estilos de Alertas -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="<?php echo !$showCategories ? 'is-compact' : ''; ?>">

    <!-- Header fijo que agrupa Navbar y Categorías -->
    <header class="fixed-header">
        <div class="header-content">
            <!-- Barra Superior -->
            <nav class="navbar">
                <div class="container-logo-title">
                    <a href="?route=home" class="brand-link">
                        <img src="<?php echo $siteLogo; ?>" alt="Logo" class="brand-logo">
                        <span class="brand-text"><?php echo htmlspecialchars($siteName); ?></span>
                    </a>
                </div>        
                
                <div class="container-control-nav">
                    <!-- Botón del Carrito -->
                    <button class="btn-std" onclick="toggleCart()">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge-count" id="cart-count" style="display: none;">0</span>
                    </button>

                    <!-- Botón de Usuario / Hamburguesa -->
                    <div class="user-menu">
                        <?php if(isset($_SESSION['client_id'])): ?>                                                
                            <button class="btn-std" onclick="toggleUserSidebar()" title="Menú de usuario">
                                <i class="fas fa-bars"></i>
                            </button>
                        <?php else: ?>
                            <button id="openModal" class="btn-std">
                                <i class="far fa-user"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>              
            </nav>

            <?php if ($showCategories): ?>
            <!-- Lista de Categorías -->
            <div class="scroll-container">
                <a href="?route=home" class="category-btn <?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">🏠 Menú del Día</a>
                
                <?php foreach($navCategories as $cat): ?>
                    <?php 
                        $emoji = '🍽️';
                        if (stripos($cat['name'], 'bebida') !== false) $emoji = '🥤';
                        if (stripos($cat['name'], 'postre') !== false) $emoji = '🍦';
                        if (stripos($cat['name'], 'desayuno') !== false) $emoji = '☕';
                        if (stripos($cat['name'], 'minuta') !== false || stripos($cat['name'], 'hamburguesa') !== false) $emoji = '🍔';
                        $isActive = (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'active' : '';
                    ?>
                    <a href="?route=home&category_id=<?php echo $cat['id']; ?>" class="category-btn <?php echo $isActive; ?>">
                        <?php echo $emoji . ' ' . htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar de Usuario (Movido fuera del wrapper de ancho limitado para que sea fixed a la derecha) -->
        <div class="user-sidebar-overlay" onclick="toggleUserSidebar()"></div>
        <div class="user-sidebar" id="userSidebar">
            <?php if(isset($_SESSION['client_id'])): ?>
                <div class="user-sidebar-header">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['client_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <span style="display:block; font-weight: 700; font-size: 1.1rem; color: #2d3436;">
                            <?php echo htmlspecialchars($_SESSION['client_name']); ?>
                        </span>
                        <small style="color: #636e72;">Cliente Verificado</small>
                    </div>
                </div>
                <div class="user-sidebar-content">
                    <a href="?route=home" class="sidebar-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="?route=my_orders" class="sidebar-link">
                        <i class="fas fa-receipt"></i> Mis Pedidos
                    </a>
                    <a href="?route=my_locations" class="sidebar-link">
                        <i class="fas fa-map-marker-alt"></i> Mis Direcciones
                    </a>
                    <a href="#" class="sidebar-link" id="btnSidebarInstall" style="display:none; color: #0984e3;">
                        <i class="fas fa-download"></i> Instalar Aplicación
                    </a>
                </div>
                <div class="sidebar-footer">
                    <a href="?route=logout&type=client" class="btn-logout-sidebar">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Overlay y Sidebar del Carrito -->
    <div class="cart-overlay" onclick="toggleCart()"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3> <i class="fas fa-shopping-cart"></i> Tu Pedido</h3>
            <button class="btn-icon" onclick="toggleCart()"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cart-items-container">
            <p style="text-align: center; color: #888; margin-top: 2rem;">Tu carrito está vacío</p>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cart-total-price">Gs. 0</span>
            </div>
            <a href="#" id="btnCheckout" onclick="proceedToCheckout(event)" class="btn btn-success" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%;">
                <i class="fas fa-shopping-bag"></i>
                <span>Finalizar Pedido</span>
            </a>
            <button type="button" onclick="toggleCart()" class="btn-back-link">
                <i class="fas fa-arrow-left"></i> Seguir Comprando
            </button>
        </div>
    </div>
    
    <!-- Contenido Principal -->
    <main class="container">
        <?php 
        if (isset($content_view) && file_exists($content_view)) {
            require_once $content_view; 
        } else {
            echo '<div style="text-align:center; padding: 2rem;"><h3>Página no encontrada</h3><p>La vista solicitada no existe.</p></div>';
        }
        ?>
    </main>
     
    <!-- Modal de Login y Registro -->
    <div id="btnAbrirLogin" class="modal-overlay">
        <!-- Contenido del Modal -->
        <div class="modal-card" >
            <!-- Selector de pestañas -->
            <div class="modal-tabs">
                <button class="tab-btn active" onclick="switchTab('login')">Entrar</button>
                <button class="tab-btn" onclick="switchTab('register')">Registrarse</button>
            </div>
            <!-- Pestañas de formulario-->
            <div class="modal-content">
                <!-- Formulario de Login -->
                <form id="loginForm" class="auth-form active" action="?route=client_login" method="POST">
                    <!-- Campo oculto para redirección -->
                    <input type="hidden" name="redirect_to" id="loginRedirect" value="home">
                    <div id="loginError" style="display:none; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; font-size: 0.9rem;"></div>
                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-main">Iniciar Sesión</button>
                </form>
                
                <div style="text-align: center; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                    <a href="?route=login" style="font-size: 0.8rem; color: #888; text-decoration: none;">
                        <i class="fas fa-id-badge"></i> Acceso para Repartidores y Staff
                    </a>
                </div>

                <!-- Formulario de Registro -->
                <form id="registerForm" class="auth-form" action="?route=client_register" method="POST">
                    <!-- Campo oculto para redirección -->
                    <input type="hidden" name="redirect_to" id="registerRedirect" value="home">
                    <div class="grid-inputs">
                        <div class="input-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="name" placeholder="Juan Pérez" required>
                        </div>
                        <div class="input-group">
                            <label>Teléfono</label>
                            <input type="tel" name="phone" placeholder="12345678" required>
                        </div>
                    </div>
                    
                    <div class="input-group toggle-group">
                        <label>¿Tiene WhatsApp?</label>
                        <div class="switch-wrapper">
                            <i class="fab fa-whatsapp whatsapp-icon" id="wsIcon"></i>
                            <label class="switch">
                                <input type="checkbox" name="has_whatsapp" id="hasWhatsapp" onchange="toggleWsIcon()">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <button type="submit" class="btn-main">Crear mi cuenta</button>
                </form>
            </div>
            
            <button class="close-modal" onclick="closeAuthModal()">&times;</button>
        </div>

    </div>

    <!-- Lógica del Carrito (JS Puro) -->
    <script src="<?php echo $baseUrl; ?>js/tool-kit-v002.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $baseUrl; ?>js/toast.js"></script> <!-- JS de Alertas -->

    <script>
        // Variable para capturar el evento de instalación
        let deferredPrompt;

        // Registrar el Service Worker para habilitar PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo $baseUrl; ?>sw.js')
                    .then(reg => console.log('Solver PWA: Service Worker registrado con éxito'))
                    .catch(err => console.log('Error registro SW', err));
            });
        }

        // Detectar si el navegador permite la instalación
        window.addEventListener('beforeinstallprompt', (e) => {
            // Evitar que el mini-infobar aparezca en móviles automáticamente
            e.preventDefault();
            deferredPrompt = e;
            
            // Mostrar el botón de instalación en el menú lateral
            const btnInstall = document.getElementById('btnSidebarInstall');
            if (btnInstall) {
                btnInstall.style.display = 'inline-flex';
                btnInstall.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            btnInstall.style.display = 'none';
                        }
                        deferredPrompt = null;
                    }
                });
            }
        });

        if (window.location.protocol === 'http:' && window.location.hostname !== 'localhost') {
            console.warn('Solver PWA: La instalación no funcionará en móviles sin HTTPS.');
        }
    </script>
    <script>
        const isUserLoggedIn = <?php echo isset($_SESSION['client_id']) ? 'true' : 'false'; ?>;

        function toggleUserSidebar() {
            document.getElementById('userSidebar').classList.toggle('open');
            document.querySelector('.user-sidebar-overlay').classList.toggle('open');
        }

        let cart = JSON.parse(localStorage.getItem('comedor_cart')) || [];

        function toggleCart() {
            const sidebar = document.querySelector('.cart-sidebar');
            const isOpening = !sidebar.classList.contains('open');

            // Validación: Si intentamos abrir el carrito y está vacío, avisamos sutilmente
            if (isOpening && cart.length === 0) {
                Toast.fire("Tu carrito aún está vacío 🛒", "info");
                return;
            }

            sidebar.classList.toggle('open');
            document.querySelector('.cart-overlay').classList.toggle('open');
        }

        function addToCart(id, name, price, image, quantity) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += parseInt(quantity);
            } else {
                cart.push({ id, name, price, image, quantity: parseInt(quantity) });
            }
            updateCartUI();
            //toggleCart(); // Abrir carrito al agregar
            Toast.fire("Producto agregado al carrito", "success");
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);

            // Si después de eliminar el item el carrito queda vacío, lo ocultamos
            if (cart.length === 0) {
                document.querySelector('.cart-sidebar').classList.remove('open');
                document.querySelector('.cart-overlay').classList.remove('open');
                Toast.fire("Tu carrito ahora está vacío 🛒", "info");
            }

            updateCartUI();
        }

        function changeQuantity(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    updateCartUI();
                }
            }
        }

        function updateCartUI() {
            localStorage.setItem('comedor_cart', JSON.stringify(cart));
            
            // Actualizar Contador
            const count = cart.reduce((acc, item) => acc + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            badge.innerText = count;
            badge.style.display = count > 0 ? 'block' : 'none';

            // Actualizar Lista Visual
            const container = document.getElementById('cart-items-container');
            const totalPriceEl = document.getElementById('cart-total-price');
            
            if (cart.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #888; margin-top: 2rem;">Tu carrito está vacío</p>';
                totalPriceEl.innerText = 'Gs. 0';
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach(item => {
                total += item.price * item.quantity;
                // Reemplazo de placeholder externo por SVG local para evitar errores de red/PWA
                const placeholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2250%22%20height%3D%2250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2050%2050%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eeeeee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaaaaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2210%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3E🍴%3C%2Ftext%3E%3C%2Fsvg%3E";
                const imgPath = item.image ? '<?php echo $baseUrl; ?>uploads/' + encodeURIComponent(item.image) : placeholder;
                
                html += `
                <div class="cart-item">
                    <img src="${imgPath}" alt="img">
                    <div class="cart-item-details">
                        <div style="font-weight: bold; font-size: 0.9rem; margin-bottom: 2px;">${item.name}</div>
                        <div style="font-size: 0.8rem; color: #636e72; margin-bottom: 4px;">Gs. ${new Intl.NumberFormat('es-PY').format(item.price)}</div>
                        <div class="cart-qty-control">
                            <button onclick="changeQuantity('${item.id}', -1)"><i class="fas fa-minus"></i></button>
                            <span>${item.quantity}</span>
                            <button onclick="changeQuantity('${item.id}', 1)"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <button class="btn-icon" onclick="removeFromCart('${item.id}')" style="color: #dc3545; margin-left: auto;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>`;
            });

            container.innerHTML = html;
            totalPriceEl.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(total);
        }

        function switchTab(type) {
            // 1. Manejar botones de pestañas
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(btn => btn.classList.remove('active'));
            
            // 2. Manejar formularios
            const forms = document.querySelectorAll('.auth-form');
            forms.forEach(form => form.classList.remove('active'));

            if(type === 'login') {
                btns[0].classList.add('active');
                document.getElementById('loginForm').classList.add('active');
            } else {
                btns[1].classList.add('active');
                document.getElementById('registerForm').classList.add('active');
            }
        }

        function closeAuthModal() {
            document.getElementById('btnAbrirLogin').style.display = 'none';
        }

        // Para abrirlo (puedes llamar esto desde tu botón de usuario)
        function openAuthModal() {
            document.getElementById('btnAbrirLogin').style.display = 'flex';
        }

        // Seleccionamos el botón por su ID
        const botonUsuario = document.getElementById('openModal');

        // Le asignamos la función de abrir el modal capturando la vista actual
        if (botonUsuario) {
            botonUsuario.addEventListener('click', () => {
                const urlParams = new URLSearchParams(window.location.search);
                let currentRoute = urlParams.get('route') || 'home';
                
                // Reconstruimos la ruta con sus parámetros (ej: category_id) para no perder el contexto
                urlParams.forEach((value, key) => {
                    if (key !== 'route' && key !== 'error') {
                        currentRoute += `&${key}=${value}`;
                    }
                });

                document.getElementById('loginRedirect').value = currentRoute;
                document.getElementById('registerRedirect').value = currentRoute;
                openAuthModal();
            });
        }
        
        function toggleWsIcon() {
            const checkbox = document.getElementById('hasWhatsapp');
            const icon = document.getElementById('wsIcon');
            
            if (checkbox.checked) {
                icon.classList.add('visible');
            } else {
                icon.classList.remove('visible');
            }
        }
        
        function proceedToCheckout(event) {
            event.preventDefault();
            
            // 1. Validar que el carrito no esté vacío
            if (cart.length === 0) {
                Toast.fire("Tu carrito está vacío. Agrega productos para continuar.", "warning");
                return;
            }

            // 2. Verificar autenticación
            if (!isUserLoggedIn) {
                // Si viene del carrito, forzamos la redirección al checkout tras el login
                document.getElementById('loginRedirect').value = 'checkout';
                document.getElementById('registerRedirect').value = 'checkout';
                openAuthModal();
                switchTab('login');
                return;
            }

            // 3. Si está logueado, ir al checkout
            window.location.href = '?route=checkout';
        }

        // Cargar carrito al iniciar
        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();

            // Detectar errores de login en la URL (ej: credenciales de admin en login de cliente)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('error') === 'login_failed') {
                openAuthModal(); // Abrir el modal automáticamente
                switchTab('login'); // Asegurar que esté en la pestaña de login
                const errDiv = document.getElementById('loginError');
                errDiv.innerText = "Credenciales incorrectas";
                errDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>