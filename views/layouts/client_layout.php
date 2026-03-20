<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Comedor App - Cliente</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">

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

        .mt-4{
            margin-top: 1.5rem;
        }

        @media (max-width: 600px) {
            .cart-sidebar { width: 100%; }
            .navbar { padding: 10px; }
            .container { padding: 0 1rem; }
            .user-menu span { margin-right: 0px;}
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
                padding: 8px 20px;
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

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="?route=home" class="navbar-brand">🍽️ Comedor App</a>
        <div class="user-menu">
            <!-- Botón del Carrito -->
            <button class="btn-icon" onclick="toggleCart()" style="margin-right: 15px;">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge-count" id="cart-count" style="display: none;">0</span>
            </button>

            <?php if(isset($_SESSION['user_id'])): ?>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>
                <a href="?route=logout" class="btn btn-danger">Cerrar Sesión</a>
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

    <!-- Lógica del Carrito (JS Puro) -->
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

        // Cargar carrito al iniciar
        document.addEventListener('DOMContentLoaded', updateCartUI);
    </script>
</body>
</html>