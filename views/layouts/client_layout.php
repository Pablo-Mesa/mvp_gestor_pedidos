<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Solver</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    
    <link rel="stylesheet" href="css/css_cubo.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; }
        
        /* Navbar Estilo */
        .navbar { background-color: #ffffff; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100;  }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: #333; text-decoration: none; }
        .user-menu span { margin-right: 1rem; color: #555; }
        
        /* Botones y Utilidades */
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-icon { background: none; border: none; font-size: 1.2rem; cursor: pointer; position: relative; color: #333; }
        
        .container { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        
        /* Estilos de Tarjetas de Producto (Usados en la vista) */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; }
        .product-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; transition: transform 0.2s; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .product-img { width: 100%; height: 180px; object-fit: cover; background-color: #eee; }
        .product-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .product-category { font-size: 0.75rem; text-transform: uppercase; color: #888; letter-spacing: 0.5px; margin-bottom: 4px; }
        .product-title { font-size: 1.1rem; font-weight: bold; margin-bottom: 0.5rem; color: #333; }
        .product-price { font-size: 1.2rem; color: #28a745; font-weight: bold; margin-bottom: 1rem; }
        .product-actions { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        
        /* Controles de Cantidad */
        .qty-control { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; }
        .qty-btn { width: 30px; height: 32px; background: #f8f9fa; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .qty-btn:hover { background: #e2e6ea; }
        .qty-input { width: 40px; height: 32px; border: none; text-align: center; font-size: 0.9rem; -moz-appearance: textfield; outline: none; }

        /* Filtros de Categoría */
        .category-filters { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 1.5rem; scrollbar-width: none; }
        .cat-pill { padding: 0.5rem 1rem; background: white; border: 1px solid #ddd; border-radius: 20px; white-space: nowrap; cursor: pointer; transition: 0.2s; color: #555; }
        .cat-pill.active, .cat-pill:hover { background: #007bff; color: white; border-color: #007bff; }

        /* Estilos del Carrito Lateral (Sidebar) */
        .cart-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 998; display: none; opacity: 0; transition: opacity 0.3s; }
        .cart-overlay.open { display: block; opacity: 1; }
        
        .cart-sidebar { position: fixed; top: 0; right: -400px; width: 350px; height: 100vh; background: white; z-index: 999; transition: right 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: -5px 0 15px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        .cart-sidebar.open { right: 0; }
        
        .cart-header { padding: 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; }
        .cart-items { flex: 1; overflow-y: auto; padding: 1.5rem; }
        .cart-footer { padding: 1.5rem; border-top: 1px solid #eee; background: #fff; }
        
        .cart-item { display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center; border-bottom: 1px solid #f0f0f0; padding-bottom: 1rem; }
        .cart-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .cart-item-details { flex: 1; }
        .cart-total { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem; }
        
        .badge-count { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 50%; min-width: 18px; text-align: center; }

        .mt-4{ margin-top: 1.5rem; }
        .mr-1{ margin-right: 0.4rem; }
        .mr-4{ margin-right: 1.5rem; }

        .ml-1{ margin-left: 0.4rem; }
        .ml-4{ margin-left: 1.5rem; }
        
        @media (max-width: 600px) {
            .cart-sidebar { width: 100%; }
            .navbar { padding: 10px; }
            .container { padding: 0 1rem; }
            .user-menu span { margin-right: 0px;}
            .container-logo-title{
                width: 100%;
                background-color: orange !important;
            }
        }

        /* Estilos base para móviles (se activan debajo de 768px) */
        @media (max-width: 768px) {
            .navbar {
                display: flex;
                flex-direction: column; /* Apila el logo y el menú */
                align-items: center;
                padding: 10px;
                gap: 15px; /* Espacio entre el logo y los botones */
            }

            .navbar-brand {
                font-size: 1.2rem;
                margin-bottom: 5px;
            }

            .user-menu {
                display: flex;
                flex-wrap: wrap; /* Permite que los elementos bajen si no caben */
                justify-content: center;
                align-items: center;
                width: 100%;
                gap: 10px;
            }

            /* Ajuste de textos para que no ocupen tanto espacio */
            .user-menu span {
                font-size: 0.9rem;
                width: 100%; /* Hace que el "Hola, Usuario" ocupe toda la línea */
                text-align: center;
                order: -1; /* Lo pone arriba de los botones en el menú de usuario */
            }

            /* Botones más cómodos para tocar con el dedo */
            .btn, .btn-icon {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .btn-icon {
                margin-right: 0 !important; /* Quitamos el margen manual que pusiste en el HTML */
            }            
        }


        /* Contenedor principal */
        .category-nav {
            width: 100%;
            background-color: #999fff;
            border-bottom: 1px solid #eee;
            padding: 6px 0;
            /* Efecto de desvanecido en los bordes para indicar scroll */
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }

        /* Contenedor del scroll */
        .scroll-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 12px;
            padding: 0 20px;
            /* Ocultar barra de scroll en Chrome/Safari */
            scrollbar-width: none; /* Firefox */
        }

        .scroll-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Estilo de los botones */
        .category-btn {
            flex: 0 0 auto; /* Evita que los botones se encojan */
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            font-family: sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-btn:hover {
            background-color: #000;
            color: #fff;
            border-color: #000;
        }

        .btn-icon-highlight-blue {
            width: 36px;
            height: 36px;
            border-radius: 50%; /* Esto crea el círculo */
            border: none;
            background: #007bff; /* Color moderno (azul)*/
            color: white;            
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-icon-highlight-green {
            width: 36px;
            height: 36px;
            border-radius: 50%; /* Esto crea el círculo */
            border: none;
            background: #10b981; /* Color moderno (verde) azul->#007bff */
            color: white;            
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-icon-highlight i {
            font-size: 1.2rem; /* Tamaño del ícono */
            transition: transform 0.3s ease;
        }

        /* Efecto al pasar el mouse */
        .btn-icon-highlight:hover {
            background: #4f46e5;
            transform: translateY(-2px); /* Pequeño salto */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        .btn-icon-highlight:hover i {
            transform: scale(1.1); /* Resalta el ícono */
        }

        :root {
            --primary: #2d3436; /* Gris muy oscuro/sobrio */
            --accent: #0984e3;  /* Azul sutil */
            --bg-light: #f9f9f9;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            display: none; /* Se activa con JS */
            justify-content: center;
            align-items: center;
            z-index: 2000;
            padding: 20px;
        }

        .modal-card {
            background: white;
            width: 100%;
            max-width: 450px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Pestañas */
        .modal-tabs {
            display: flex;
            background: var(--bg-light);
            border-bottom: 1px solid #eee;
        }

        .tab-btn {
            flex: 1;
            padding: 18px;
            border: none;
            background: none;
            font-weight: 600;
            color: #999;
            cursor: pointer;
            transition: 0.3s;
        }

        .tab-btn.active {
            color: var(--primary);
            background: white;
            border-bottom: 2px solid var(--primary);
        }

        /* Formularios */
        .modal-content { padding: 30px; }

        .auth-form { display: none; }
        .auth-form.active { 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
            color: #555;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #eee;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-group input:focus { border-color: var(--accent); }

        .btn-main {
            background: var(--primary);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: opacity 0.3s;
        }

        .btn-main:hover { opacity: 0.9; }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #ccc;
            z-index: 10;
        }

        /* Responsive Grid */
        @media (min-width: 600px) {
            .grid-inputs {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
        }

        /* Contenedor del grupo para alinearlo horizontalmente */
        .toggle-group {
            display: flex;
            align-items: center;
            justify-content: left; /* Etiqueta a la izquierda, switch a la derecha */
            margin-bottom: 15px;
            padding: 5px 0;
        }

        .toggle-group label {
            margin-bottom: 0; /* Quitamos el margen inferior del label de texto */
        }

        /* Estructura del Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        /* Escondemos el checkbox original */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* El "Track" o fondo del switch */
        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #e4e4e7; /* Gris suave (apagado) */
            transition: .3s;
            border-radius: 24px;
        }

        /* El círculo blanco */
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Color cuando está activo (Checked) */
        input:checked + .slider {
            background-color: #2563eb; /* Azul (puedes usar tu --accent) */
        }

        /* Movimiento del círculo al activar */
        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Efecto sutil al pasar el mouse */
        .slider:hover {
            background-color: #d4d4d8;
        }

        .switch-wrapper {
            display: flex;
            align-items: center;
            gap: 10px; /* Espacio entre el icono y el switch */
        }

        .whatsapp-icon {
            color: #25D366;
            font-size: 1.2rem;
            opacity: 0; /* Oculto por defecto */
            transform: translateX(10px);
            transition: all 0.3s ease;
            pointer-events: none; /* Para que no interfiera con el click */
        }

        /* Cuando el checkbox esté marcado, mostramos el icono */
        input:checked ~ .whatsapp-icon, /* Si el icono fuera hermano posterior */
        #hasWhatsapp:checked + .slider + .whatsapp-icon { /* Dependiendo de tu orden */ }

        /* Usaremos una clase controlada por JS para máxima compatibilidad */
        .whatsapp-icon.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Ajuste al color del switch cuando es WhatsApp */
        input:checked + .slider {
            background-color: #25D366 !important;
        }

        .container-logo-title{
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: auto;
            background-color: blue;            
        }        
        
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">

        <div class="container-logo-title">
            <div style="display: flex; flex-direction: row; align-items: center; width: auto">
                <div id="here_cube" class="mr-1"></div>            
                <a href="?route=home" class="navbar-brand"> Menú del Día</a>
            </div>            
            <small style="color: #555;">Selecciona tus platos favoritos para hoy.</small>
        </div>
        
        <!-- Botón del Carrito -->
        <button class="btn-icon btn-icon-highlight btn-icon-highlight-blue" onclick="toggleCart()" style="margin-right: 15px;">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge-count" id="cart-count" style="display: none;">0</span>
        </button>

        <div class="user-menu">
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Botón del usuario -->
                <button id="openModal" class="btn-icon btn-icon-highlight btn-icon-highlight-green">
                    <i class="fas fa-user"></i>
                </button>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>                
                <a href="?route=logout" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt mr-1"></i>Cerrar Sesión
                </a>
            <?php else: ?>
                <a href="?route=login" class="btn btn-primary">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
        
    </nav>
    
    <div class="scroll-container mt-4">
        <button class="category-btn">Almuerzos</button>
        <button class="category-btn">Bebidas</button>
        <button class="category-btn">Desayunos</button>
        <button class="category-btn">Minutas</button>
        <button class="category-btn">Postres</button>
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

    <main class="container">
        <?php 
        if (isset($content_view) && file_exists($content_view)) {
            require_once $content_view; 
        } else {
            echo '<div style="text-align:center; padding: 2rem;"><h3>Página no encontrada</h3><p>La vista solicitada no existe.</p></div>';
        }
        ?>
    </main>

    <div id="btnAbrirLogin" class="modal-overlay">
        <div class="modal-card">
            <!-- Selector de pestañas -->
            <div class="modal-tabs">
                <button class="tab-btn active" onclick="switchTab('login')">Entrar</button>
                <button class="tab-btn" onclick="switchTab('register')">Registrarse</button>
            </div>

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
                    <button class="btn-icon" onclick="removeFromCart(${item.id})" style="color: #dc3545;"><i class="fas fa-trash"></i></button>
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