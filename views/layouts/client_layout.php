<!DOCTYPE html>
<?php
// Lógica para obtener categorías disponibles para la navegación
if (!class_exists('Category')) {
    // Intentar cargar el modelo si no está cargado
    $path = 'models/Category.php';
    if (file_exists($path)) require_once $path;
    elseif (file_exists('../' . $path)) require_once '../' . $path;
}
$navCategories = [];
if (class_exists('Category')) {
    $catModel = new Category();
    $stmt = $catModel->readAll();
    $navCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Solver delivery</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">    
    <link rel="stylesheet" href="css/css_cubo.css">
    <link rel="stylesheet" href="css/client_layout.css">   
    <link rel="stylesheet" href="css/toast.css"> <!-- Estilos de Alertas -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header fijo que agrupa Navbar y Categorías -->
    <header class="fixed-header">
    <!-- Barra Superior -->
    <nav class="navbar">

        <div class="container-logo-title">
            <div style="display: flex; flex-direction: row; justify-content: left; align-items: center; width: 100%">
                <div id="here_cube" class="mr-1"></div>            
            </div>
        </div>        
        
        <div class="container-control-nav">

            <!-- Botón del Carrito -->
            <button class="btn-std mr-3" onclick="toggleCart()">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge-count" id="cart-count" style="display: none;">0</span>
            </button>

            <!-- Botón de Login -->
            <div class="user-menu">

                <?php if(isset($_SESSION['client_id'])): ?>
                
                    <span class="span-user">Hola, <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['client_name'] ?? 'Cliente'); ?></span>                
                    
                    <a href="?route=my_orders" class="btn-std mr-1" title="Mis Pedidos">
                        <i class="fas fa-list-alt"></i>
                    </a>

                    <a href="?route=logout&type=client" class="btn-logout-solid">
                        <i class="fa fa-sign-out"></i> <span class="logout-text">Cerrar Sesión</span>
                    </a>

                <?php else: ?>
                    <!-- <a href="?route=login" class="">Iniciar Sesión</a> -->                    
                    <!-- Botón del usuario -->
                    <button id="openModal" class="btn-std">
                        <i class="fas fa-user"></i>
                    </button>
                <?php endif; ?>

            </div>

        </div>              
            
    </nav>
    
    <!-- Lista de Categorías -->
    <div class="scroll-container">
        <a href="?route=home" class="category-btn <?php echo !isset($_GET['category_id']) ? 'active' : ''; ?>">🏠 Menú del Día</a>
        
        <?php foreach($navCategories as $cat): ?>
            <?php 
                // Emojis simples según nombre (opcional)
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
    </header>
    
    <!-- Filtros de Categoría 
    <div class="category-filters">
        <button class="cat-pill active" onclick="filterCategory('all', this)">Todos</button>
        <?php foreach($categories as $cat): ?>
            <button class="cat-pill" onclick="filterCategory('<?php echo htmlspecialchars($cat); ?>', this)">
                <?php echo htmlspecialchars($cat); ?>
            </button>
        <?php endforeach; ?>
    </div> -->

    <!-- Overlay y Sidebar del Carrito -->
    <div class="cart-overlay" onclick="toggleCart()"></div>
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3>Tu Pedido</h3>
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
            <!-- Aquí podrías enviar a checkout -->
            <a href="#" id="btnCheckout" onclick="proceedToCheckout(event)" class="btn btn-success" style="display: block; text-align: center; width: 100%;">Finalizar Pedido</a>
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
                <!-- Formulario de Registro -->
                <form id="registerForm" class="auth-form" action="?route=client_register" method="POST">
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
    <script src="js/tool-kit-v002.js"></script>
    <script src="js/toast.js"></script> <!-- JS de Alertas -->
    
    <script>

        const isUserLoggedIn = <?php echo isset($_SESSION['client_id']) ? 'true' : 'false'; ?>;

        let cart = JSON.parse(localStorage.getItem('comedor_cart')) || [];

        function toggleCart() {
            document.querySelector('.cart-sidebar').classList.toggle('open');
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
            updateCartUI();
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
                // Fix image path for cart display
                const imgPath = item.image ? 'uploads/' + encodeURIComponent(item.image) : 'https://via.placeholder.com/50';
                
                html += `
                <div class="cart-item">
                    <img src="${imgPath}" alt="img">
                    <div class="cart-item-details">
                        <div style="font-weight: bold; font-size: 0.9rem;">${item.name}</div>
                        <div style="font-size: 0.85rem; color: #666;">${item.quantity} x Gs. ${new Intl.NumberFormat('es-PY').format(item.price)}</div>
                    </div>
                    <button class="btn-icon" onclick="removeFromCart('${item.id}')" style="color: #dc3545;">
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

        // Le asignamos la función de abrir el modal
        if (botonUsuario) {
            botonUsuario.addEventListener('click', openAuthModal);
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
                // Si no está logueado, abrir modal y mostrar login
                openAuthModal();
                switchTab('login');
                return;
            }

            // 3. Si está logueado, ir al checkout
            window.location.href = '?route=checkout';
        }

        drawCube("here_cube", true, "28px");

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