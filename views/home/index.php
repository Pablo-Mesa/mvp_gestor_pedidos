<?php
// Lógica para determinar qué mostrar (Menú del día o Categoría específica)
$filter_category_id = $_GET['category_id'] ?? null;
$view_title = "Menú del Día"; // Título por defecto
$clientId = $_SESSION['client_id'] ?? null;

if ($filter_category_id) {
    // Si hay categoría seleccionada, buscamos todos los productos de esa categoría
    if (!class_exists('Product')) {
        $path = 'models/Product.php';
        if (file_exists($path)) require_once $path;
        elseif (file_exists('../' . $path)) require_once '../' . $path;
    }
    
    $prodModel = new Product();
    // Usamos readAllActive para traer productos disponibles (fuera del menú del día)
    $stmt = $prodModel->readAllActive($clientId); 
    $all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $menu_items = [];
    foreach($all_products as $p) {
        if ($p['category_id'] == $filter_category_id) {
            // Mapeamos al formato que espera la vista
            $menu_items[] = [
                'id' => $p['id'], // ID único para inputs
                'product_id' => $p['id'],
                'product_name' => $p['name'],
                'product_price' => $p['price'],
                'price_half' => $p['price_half'],
                'image' => $p['image'],
                'category_name' => $p['category_name'] ?? 'Cat',
                'fav_count' => $p['fav_count'] ?? 0,
                'likes_count' => $p['likes_count'] ?? 0,
                'reviews_count' => $p['reviews_count'] ?? 0,
                'is_favorite' => $p['is_favorite'] ?? false,
                'is_liked' => $p['is_liked'] ?? false
            ];
        }
    }
    $view_title = "Categoría Seleccionada";
} else {
    // Si no hay filtro, usamos la variable $daily_menus que viene del controlador
    $menu_items = isset($daily_menus) ? $daily_menus : [];

    // Fallback: Si el menú del día está vacío, mostramos automáticamente la categoría "Almuerzos"
    if (empty($menu_items)) {
        if (!class_exists('Product')) {
            $path = 'models/Product.php';
            if (file_exists($path)) require_once $path;
            elseif (file_exists('../' . $path)) require_once '../' . $path;
        }
        $prodModel = new Product();
        $fallback_prods = $prodModel->readAllActive($clientId)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($fallback_prods as $p) {
            if (stripos($p['category_name'] ?? '', 'almuerzo') !== false) {
                $menu_items[] = [
                    'id' => $p['id'],
                    'product_id' => $p['id'],
                    'product_name' => $p['name'],
                    'product_price' => $p['price'],
                    'price_half' => $p['price_half'],
                    'image' => $p['image'],
                    'category_name' => $p['category_name'] ?? 'Almuerzos',
                    'fav_count' => $p['fav_count'] ?? 0,
                    'likes_count' => $p['likes_count'] ?? 0,
                    'reviews_count' => $p['reviews_count'] ?? 0,
                    'is_favorite' => $p['is_favorite'] ?? false,
                    'is_liked' => $p['is_liked'] ?? false
                ];
            }
        }
        if (!empty($menu_items)) $view_title = "Almuerzos Disponibles";
    }
}
?>
<?php // Definimos el placeholder globalmente para evitar errores de variable indefinida
$localPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22300%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3ESin%20Imagen%3C%2Ftext%3E%3C%2Fsvg%3E"; ?>


<style>
    body {
        overflow-x: hidden; /* Evita que el ancho del hero genere scroll lateral */
    }

    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }

    .qty-control {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px; /* Espacio entre botones e input */
        border: none;
    }

    /* Animación para las tarjetas de productos */
    .product-card {
        opacity: 0; /* Empiezan invisibles */
        animation: cardFadeIn 0.6s ease-out forwards;
    }

    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .qty-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%; /* Botón circular */
        border: 1px solid #ddd;
        background-color: #fff;
        color: #333;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .qty-btn:hover {
        background-color: #f8f9fa;
        border-color: #bbb;
    }

    .qty-btn:active {
        transform: scale(0.9); /* Efecto de pulsado */
    }

    .qty-input {
        width: 40px;
        border: none;
        font-size: 1rem;
        text-align: center;
        font-weight: bold;
        background: transparent;
    }

    .btn-primary {

        display: inline-flex;       /* Alinea texto e icono internamente */
        align-items: center;        /* Centra verticalmente ambos */
        justify-content: center;    /* Centra horizontalmente */
        gap: 8px;                   /* Separa el texto del icono sin usar margin */
        white-space: nowrap;        

        font-family: 'Courier New', Courier, monospace;
        font: 0.875rem sans-serif;
        font-weight: bold;
        color: #444;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);        
        padding: 10px 25px;
        border: 1px solid rgba(20, 20, 255, 0.3);
        border-radius: 12px;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .btn-success-animate {
        background: #28a745 !important;
        color: white !important;
        border-color: #28a745 !important;
    }

    /* Estilos para la Barra de Reacciones */
    .product-reactions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 5px;
        padding: 5px 0;
        border-bottom: 1px solid rgba(0,0,0,0.03);
        margin-bottom: 8px;
    }

    .reaction-item {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #b2bec3;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: none;
        border: none;
        padding: 0;
        position: relative;
    }

    /* Animación de Pulso para Reacciones */
    @keyframes reaction-pop {
        0% { transform: scale(1); }
        50% { transform: scale(1.4); }
        100% { transform: scale(1); }
    }
    .animate-pop { animation: reaction-pop 0.4s ease-out; }

    /* Colores de marca para reacciones */
    .reaction-item.fav:hover, .reaction-item.fav.active { color: #ff4757; transform: scale(1.25); filter: drop-shadow(0 0 5px rgba(255,71,87,0.3)); }
    .reaction-item.like:hover, .reaction-item.like.active { color: #1e90ff; transform: scale(1.25); filter: drop-shadow(0 0 5px rgba(30,144,255,0.3)); }
    .reaction-item.comment:hover { color: #ffa502; transform: scale(1.25); }

    .reaction-count {
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 4px;
        color: #636e72;
    }

    .social-legend {
        font-size: 0.72rem;
        color: #7f8c8d;
        margin-bottom: 10px;
        min-height: 1rem;
    }

    @media (max-width: 768px) {
        :root {
            --slide-width: 240px;
            --slide-height: 140px;
            --gap: 10px;
        }   

        .product-reactions { gap: 8px; justify-content: space-between; }
        .portion-selector { gap: 4px; margin-top: 8px; margin-bottom: 12px; }
        .portion-label { font-size: 0.75rem; }        
        .qty-control { justify-content: space-between; }

        .info-content h3 { font-size: 0.9rem !important; margin-bottom: 5px !important; }
        .info-content li { font-size: 0.75rem !important; margin: 2px 0 !important; }
        .overlay h3 { font-size: 0.9rem; }
        .overlay p { font-size: 0.75rem; }
        .step-box i { font-size: 1.2rem !important; }

    }

    /* Pequeño feedback al hacer clic */
    .reaction-item:active {
        transform: scale(0.9);
    }

    /* Estilos para el selector de porción */
    .portion-selector {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 0.85rem;
    }
    .portion-label {
        cursor: pointer; display: flex; align-items: center; gap: 4px; color: #555;
    }

    /* Ajuste para la cuadrícula de productos para que no se pegue al header */
    .product-grid {
        margin-top: 1rem; 
        padding-top: 10px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }
</style>    

<?php if (!isset($_SESSION['client_id'])): ?>
    <!-- VISTA PARA USUARIOS NO LOGUEADOS (GUESTS) -->
    <?php $contactChannels = !empty($siteSettings['contact_channels']) ? json_decode($siteSettings['contact_channels'], true) : []; ?>
    <div class="guest-container" style="padding: 2rem 0;">
        
        <?php if ($isStoreOpen): ?>
            <!-- CASO: LOCAL ABIERTO - Banners Promocionales (Espacio para tu nuevo diseño) -->
            <div class="promo-banners-placeholder" style="text-align: center; padding: 3rem; background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 24px; border: 2px dashed #ccc;">
                <i class="fas fa-ad" style="font-size: 3rem; color: #0984e3; margin-bottom: 1.5rem;"></i>
                <h2 style="font-weight: 800; color: #2d3436; margin-bottom: 1rem;">¡Bienvenido a <?php echo htmlspecialchars($siteName); ?>!</h2>
                <p style="color: #636e72; margin-bottom: 2rem;">Inicia sesión para descubrir nuestras ofertas exclusivas y realizar tu pedido.</p>
                
                <!-- Aquí puedes empezar a diseñar tus banners -->
                <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none;">
                    <div style="min-width: 280px; height: 150px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-weight: bold; color: #aaa;">Placeholder Banner 1</div>
                    <div style="min-width: 280px; height: 150px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-weight: bold; color: #aaa;">Placeholder Banner 2</div>
                </div>
            </div>

        <?php else: ?>
            <!-- CASO: LOCAL CERRADO - Info Operativa y Ubicación -->
            <div class="closed-info-container">
                <div style="background: white; padding: 2.5rem; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: #fff3cd; color: #856404; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2rem;">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h2 style="font-weight: 800; color: #2d3436; margin-bottom: 0.5rem;">Local Cerrado</h2>
                    <p style="color: #856404; font-weight: 700; font-size: 1.1rem; margin-bottom: 1.5rem;"><?php echo $nextOpeningMsg; ?></p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: left; margin-top: 2rem; border-top: 1px solid #eee; padding-top: 2rem;">
                        <div>
                            <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #aaa; margin-bottom: 12px;">Contacto</h4>
                            <?php if (empty($contactChannels)): ?>
                                <p style="font-weight: 600; color: #2d3436;"><i class="fas fa-phone-alt" style="color: #0984e3;"></i> <?php echo htmlspecialchars($siteSettings['store_phone'] ?? 'Consultar al local'); ?></p>
                            <?php else: ?>
                                <?php foreach ($contactChannels as $channel): ?>
                                    <div style="margin-bottom: 12px;">
                                        <span style="display: block; font-size: 0.7rem; color: #888; text-transform: uppercase; margin-bottom: 2px;"><?php echo htmlspecialchars($channel['label']); ?></span>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-weight: 700; color: #2d3436; font-size: 1rem;"><?php echo htmlspecialchars($channel['phone']); ?></span>
                                            <div style="display: flex; gap: 6px; font-size: 0.85rem;">
                                                <?php if(!empty($channel['calls'])): ?><i class="fas fa-phone-alt" style="color: #0984e3;" title="Soporta llamadas"></i><?php endif; ?>
                                                <?php if(!empty($channel['sms'])): ?><i class="fas fa-comment-alt" style="color: #636e72;" title="Soporta mensajes"></i><?php endif; ?>
                                                <?php if(!empty($channel['whatsapp'])): ?><i class="fab fa-whatsapp" style="color: #25D366;" title="WhatsApp habilitado"></i><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #aaa; margin-bottom: 10px;">Dirección</h4>
                            <p style="font-weight: 600; color: #2d3436;"><i class="fas fa-map-marker-alt" style="color: #ff4757;"></i> <?php echo htmlspecialchars($siteSettings['store_address'] ?? 'Asunción, Paraguay'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Mapa de Ubicación Real -->
                <?php if (!empty($siteSettings['store_lat']) && !empty($siteSettings['store_lng'])): ?>
                <div class="location-map" style="height: 350px; border-radius: 24px; overflow: hidden; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        style="border:0; filter: grayscale(0.1);" 
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1000!2d<?php echo $siteSettings['store_lng']; ?>!3d<?php echo $siteSettings['store_lat']; ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2spy!4v<?php echo time(); ?>" 
                        allowfullscreen>
                    </iframe>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Botón de acceso para invitados -->
        <div style="margin-top: 3rem; text-align: center;">
            <p style="color: #aaa; font-size: 0.9rem; margin-bottom: 1rem;">Inicia sesión para ver nuestra variedad de platos</p>
            <button onclick="openAuthModal()" class="btn-primary" style="background: #2d3436; color: white; border: none; padding: 15px 40px; font-size: 1rem;">
                Ingresar Ahora <i class="fas fa-sign-in-alt" style="margin-left: 10px;"></i>
            </button>
        </div>
    </div>

<?php else: ?>
    <!-- Grid de Productos -->
    <div class="product-grid">
        <?php if(empty($menu_items)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem; color: #636e72;">
                <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                <h3 style="font-weight: 700; color: #2d3436;">Menú no disponible</h3>
                <p style="font-size: 0.95rem;">Estamos actualizando nuestra lista de platos. <br>¡Vuelve a revisar en unos minutos!</p>
            </div>
        <?php else: ?>
            <?php $delay = 0; foreach($menu_items as $item): ?>
                <?php 
                    $physicPath = 'uploads/' . $item['image'];                
                    $displayImg = (!empty($item['image']) && file_exists($physicPath)) ? $baseUrl . 'uploads/' . rawurlencode($item['image']) . '?v=' . time() : $localPlaceholder;
                    // Validar si tiene medio plato
                    $hasHalf = !empty($item['price_half']) && $item['price_half'] > 0;
                ?>
                <div class="product-card" data-category="<?php echo htmlspecialchars($item['category_name'] ?? 'all'); ?>" style="animation-delay: <?php echo $delay; ?>s;">                
                    <img src="<?php echo $displayImg; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-img">
                                
                    <div class="product-body">
                        <div class="product-category">
                            <span><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></span>
                            <div class="product-reactions">
                                <button class="reaction-item fav <?php echo ($item['is_favorite'] ?? false) ? 'active' : ''; ?>" 
                                    onclick="toggleReaction(this, 'fav', '<?php echo $item['product_id']; ?>')" title="Añadir a favoritos">
                                    <i class="<?php echo (isset($item['is_favorite']) && $item['is_favorite']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    <span class="reaction-count"><?php echo $item['fav_count'] ?? 0; ?></span>
                                </button>
                                <button class="reaction-item like <?php echo ($item['is_liked'] ?? false) ? 'active' : ''; ?>" 
                                    onclick="toggleReaction(this, 'like', '<?php echo $item['product_id']; ?>')" title="Me gusta">
                                    <i class="<?php echo ($item['is_liked'] ?? false) ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                    <span class="reaction-count"><?php echo $item['likes_count'] ?? 0; ?></span>
                                </button>
                                <button class="reaction-item comment" onclick="openReviewModal(this, '<?php echo $item['product_id']; ?>')" title="Dejar una reseña">
                                    <i class="far fa-comment"></i>
                                    <span class="reaction-count"><?php echo $item['reviews_count'] ?? 0; ?></span>
                                </button>
                            </div>
                        </div>
                        <div class="product-title"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        
                        <!-- Leyenda de Prueba Social -->
                        <div class="social-legend" id="legend-<?php echo $item['product_id']; ?>">
                            <?php 
                                $likes = $item['likes_count'] ?? 0;
                                $isLiked = $item['is_liked'] ?? false;
                                if ($likes > 0) {
                                    if ($isLiked) {
                                        echo ($likes == 1) ? "Tú reaccionaste a esto" : "A ti y a " . ($likes - 1) . " personas les gusta esto";
                                    } else {
                                        echo "A " . $likes . ($likes == 1 ? " persona le gusta" : " personas les gusta") . " esto";
                                    }
                                }
                            ?>
                        </div>
                        
                        <!-- Precio Dinámico -->
                        <div class="product-price" id="price_display_<?php echo $item['id']; ?>">
                            Gs. <?php echo number_format($item['product_price'], 0, ',', '.'); ?>
                        </div>
                        
                        <!-- Selector de Porción (Solo si existe precio medio) -->
                        <?php if($hasHalf): ?>
                        <div class="portion-selector">
                            <label class="portion-label">
                                <input type="radio" name="portion_<?php echo $item['id']; ?>" value="full" checked 
                                    onchange="updatePrice(<?php echo $item['id']; ?>, <?php echo $item['product_price']; ?>, '<?php echo addslashes($item['product_name']); ?>', '<?php echo $item['product_id']; ?>')"> 
                                Entero
                            </label>
                            <label class="portion-label">
                                <input type="radio" name="portion_<?php echo $item['id']; ?>" value="half"
                                    onchange="updatePrice(<?php echo $item['id']; ?>, <?php echo $item['price_half']; ?>, '<?php echo addslashes($item['product_name']); ?> (Medio)', '<?php echo $item['product_id']; ?>_half')"> 
                                Medio
                            </label>
                        </div>
                        <?php endif; ?>
                        
                        <div class="product-actions">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)">-</button>
                                <input type="number" id="qty_<?php echo $item['id']; ?>" class="qty-input" value="1" min="1" readonly>
                                <button class="qty-btn" onclick="this.previousElementSibling.value = parseInt(this.previousElementSibling.value) + 1">+</button>
                            </div>
                            
                            <!-- Botón con ID dinámico y data attributes para que JS lea el estado actual -->
                            <button class="btn btn-primary" 
                                id="btn_add_<?php echo $item['id']; ?>"
                                data-id="<?php echo $item['product_id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['product_name']); ?>"
                                data-price="<?php echo $item['product_price']; ?>"
                                data-image="<?php echo htmlspecialchars($item['image']); ?>"
                                onclick="handleAddToCart(this, this.dataset.id, this.dataset.name, this.dataset.price, this.dataset.image, document.getElementById('qty_<?php echo $item['id']; ?>').value)">
                                Agregar <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php $delay += 0.08; endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    
function handleAddToCart(btn, id, name, price, image, qty) {
    // 1. Llamar a la función global original (en client_layout.php)
    addToCart(id, name, price, image, qty);

    // 2. Aplicar micro-interacción visual
    const originalHTML = btn.innerHTML;
    btn.classList.add('btn-success-animate');
    btn.innerHTML = '¡Listo! <i class="fas fa-check"></i>';
    btn.disabled = true;

    // 3. Revertir el botón después de 1.2 segundos
    setTimeout(() => {
        btn.classList.remove('btn-success-animate');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }, 1200);
}

/**
 * Función para desplazar el slider de recomendados
 */
function scrollSlider(btn, distance) {
    const slider = btn.parentElement.querySelector('.horizontal-slider');
    slider.scrollBy({ left: distance, behavior: 'smooth' });
}

// --- Lógica de Reacciones ---

/**
 * Actualiza el texto de prueba social debajo de los iconos
 */
function updateSocialText(productId, type, count, isActive) {
    const legend = document.getElementById(`legend-${productId}`);
    if (!legend || type !== 'like') return; // Enfocamos la leyenda principal en los Likes por ahora

    if (count <= 0) {
        legend.innerText = "";
        return;
    }

    let text = "";
    if (isActive) {
        text = (count === 1) ? "Tú reaccionaste a esto" : `A ti y a ${count - 1} ${count - 1 === 1 ? 'persona' : 'personas'} les gusta esto`;
    } else {
        text = `A ${count} ${count === 1 ? 'persona le gusta' : 'personas les gusta'} esto`;
    }
    legend.innerText = text;
}

async function toggleReaction(btn, type, productId) {
    if (!isUserLoggedIn) {
        Toast.fire("Inicia sesión para reaccionar 🧡", "info");
        openAuthModal();
        return;
    }

    const icon = btn.querySelector('i');
    const countSpan = btn.querySelector('.reaction-count');
    let currentCount = parseInt(countSpan.innerText) || 0;
    const isAdding = !btn.classList.contains('active');

    // Interacción Optimista: Actualizamos la interfaz antes de esperar al servidor
    btn.classList.toggle('active');
    const newCount = isAdding ? currentCount + 1 : Math.max(0, currentCount - 1);
    countSpan.innerText = newCount;
    
    // Cambiar iconos de FontAwesome (far = regular, fas = solid)
    icon.classList.toggle('fas');
    icon.classList.toggle('far');

    // Animación y Leyenda
    icon.classList.add('animate-pop');
    setTimeout(() => icon.classList.remove('animate-pop'), 400);
    
    updateSocialText(productId, type, newCount, isAdding);
    if(isAdding) Toast.fire("¡Gracias por tu reacción!", "success");

    try {
        const response = await fetch('?route=product_reaction_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, type: type })
        });
        const result = await response.json();
        if (!result.success) throw new Error(result.message);
    } catch (e) {
        // Revertir cambios si falla la red o el servidor
        btn.classList.toggle('active');
        countSpan.innerText = currentCount;
        icon.classList.toggle('fas');
        icon.classList.toggle('far');
        updateSocialText(productId, type, currentCount, !isAdding);
        Toast.fire(e.message || "Error al conectar con el servidor", "error");
    }
}

async function openReviewModal(btn, productId) {
    if (!isUserLoggedIn) {
        Toast.fire("Inicia sesión para comentar", "info");
        openAuthModal();
        return;
    }

    const { value: text } = await Swal.fire({
        title: '¿Qué te pareció este plato?',
        input: 'textarea',
        inputPlaceholder: 'Escribe tu reseña aquí...',
        showCancelButton: true,
        confirmButtonText: 'Enviar Comentario',
        cancelButtonText: 'Cancelar'
    });

    if (text) {
        const response = await fetch('?route=product_review_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, comment: text })
        });
        const result = await response.json();
        if (result.success) {
            Toast.fire("¡Gracias por tu reseña!", "success");
            
            // Animación en el icono de comentario
            const icon = btn.querySelector('i');
            icon.classList.add('animate-pop');
            setTimeout(() => icon.classList.remove('animate-pop'), 400);
            
            // Actualización instantánea del contador
            const countSpan = btn.querySelector('.reaction-count');
            if (countSpan) {
                let currentCount = parseInt(countSpan.innerText) || 0;
                countSpan.innerText = currentCount + 1;
            }
        }
    }
}

function updatePrice(itemId, newPrice, newName, newId) {
    // 1. Actualizar texto visual del precio
    const display = document.getElementById('price_display_' + itemId);
    display.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(newPrice);
    
    // 2. Actualizar datos del botón Agregar
    const btn = document.getElementById('btn_add_' + itemId);
    btn.dataset.price = newPrice;
    btn.dataset.name = newName;
    btn.dataset.id = newId; // CORRECTO: Actualiza el atributo data-id. NO usar setAttribute('onclick', ...)
}

function filterCategory(cat, btn) {
    // Lógica visual simple para ocultar/mostrar tarjetas
    document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.product-card').forEach(card => {
        if (cat === 'all' || card.dataset.category === cat) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>