<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Solver</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    
    <link rel="stylesheet" href="css/css_cubo.css">
    <link rel="stylesheet" href="css/client_layout.css">   

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Barra Superior -->
    <nav class="navbar">
        <div class="container-logo-title">
            <div style="display: flex; flex-direction: row; justify-content: left; align-items: center; width: 100%">
                <div id="here_cube" class="mr-1"></div>            
                <a href="?route=home" class="navbar-brand">Solver delivery</a>
            </div>
            <small style="color: #555; width: 100%; margin-left: 4px;">En servicio para: ???</small>
        </div>        
        <div class="container-control-nav">

            <!-- Botón del Carrito -->
            <button class="btn-std mr-3" onclick="toggleCart()">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge-count" id="cart-count" style="display: none;">0</span>
            </button>

            <!-- Botón de Login -->
            <div class="user-menu">

                <?php if(isset($_SESSION['user_id'])): ?>
                
                    <span class="span-user">Hola, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>                
                    
                    <a href="?route=logout" class="btn-logout-solid">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
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
    <div class="scroll-container mt-4">
        <button class="category-btn"> 🍽️ Almuerzos</button>
        <button class="category-btn"> 🥤 Bebidas</button>
        <button class="category-btn"> ☕ Desayunos</button>
        <button class="category-btn"> 🍔 Minutas</button>
        <button class="category-btn"> 🍦 Postres</button>
    </div>
    
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
            <a href="#" onclick="alert('Funcionalidad de Checkout pendiente')" class="btn btn-success" style="display: block; text-align: center; width: 100%;">Finalizar Pedido</a>
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
                <form id="loginForm" class="auth-form active">
                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <input type="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <input type="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-main">Iniciar Sesión</button>
                </form>
                <!-- Formulario de Registro -->
                <form id="registerForm" class="auth-form">
                    <div class="grid-inputs">
                        <div class="input-group">
                            <label>Nombre Completo</label>
                            <input type="text" placeholder="Juan Pérez" required>
                        </div>
                        <div class="input-group">
                            <label>Teléfono</label>
                            <input type="tel" placeholder="12345678" required>
                        </div>
                    </div>
                    
                    <div class="input-group toggle-group">
                        <label>¿Tiene WhatsApp?</label>
                        <div class="switch-wrapper">
                            <i class="fab fa-whatsapp whatsapp-icon" id="wsIcon"></i>
                            <label class="switch">
                                <input type="checkbox" id="hasWhatsapp" onchange="toggleWsIcon()">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <input type="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <input type="password" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <button type="submit" class="btn-main">Crear mi cuenta</button>
                </form>
            </div>
            
            <button class="close-modal" onclick="closeAuthModal()">&times;</button>
        </div>

    </div>


    <!-- Lógica del Carrito (JS Puro) -->
    <script src="js/tool-kit-v002.js"></script>
    
    <script>

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
            toggleCart(); // Abrir carrito al agregar
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
                    <button class="btn-icon" onclick="removeFromCart(${item.id})" style="color: #dc3545;">
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
        botonUsuario.addEventListener('click', openAuthModal);
        
        function toggleWsIcon() {
            const checkbox = document.getElementById('hasWhatsapp');
            const icon = document.getElementById('wsIcon');
            
            if (checkbox.checked) {
                icon.classList.add('visible');
            } else {
                icon.classList.remove('visible');
            }
        }
        
        drawCube("here_cube", false, "28px");

        // Cargar carrito al iniciar
        document.addEventListener('DOMContentLoaded', updateCartUI);
    </script>
</body>
</html>