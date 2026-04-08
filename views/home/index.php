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
}

// Preparamos las promociones para el Hero (se necesita aquí arriba para el cálculo del CSS)
$promosList = !empty($promos) ? $promos : [
    ['type' => 'offer', 'title' => 'Bienvenido', 'content' => 'Explora nuestro menú del día.', 'css_class' => 'ambient', 'image' => '']
];
?>

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

    :root {
    --slide-width: 280px;
    --slide-height: 180px; /* Altura reducida a menos de la mitad */
    --gap: 15px;
    }

    .hero-promo {
    /* Break-out para ocupar todo el ancho de la pantalla ignorando el .container */
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: 0; 
    margin-bottom: 0;    
    overflow: hidden;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d3436 100%); /* Fondo oscuro para resaltar el cristal */
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    
    /* Efecto de transición para el colapso */
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease, margin 0.5s ease, transform 0.5s ease;
    max-height: 400px; 
    opacity: 1;
    transform: scaleY(1);
    transform-origin: top;

    /* Aseguramos que el Hero esté en una capa inferior al Header */
    position: relative; 
    z-index: 5; /* Menor que el header (1100) */
    }

    /* Clase que se activará al hacer scroll */
    .hero-promo.collapsed {
        max-height: 0 !important;
        opacity: 0;
        transform: scaleY(0);
        margin-top: 0; /* Eliminamos el margen negativo para evitar que succione el grid hacia arriba */
        margin-bottom: 0;
        padding: 0;
        pointer-events: none; /* Evita interacciones mientras está oculto */
        border: none;
    }

    /* Ajuste para la cuadrícula de productos para que no se pegue al header */
    .product-grid {
        margin-top: 2rem; 
        padding-top: 10px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }

    /* Compensación automática: Cuando el hero colapsa, aumentamos el margen del grid 
       para que la primera fila sea totalmente visible bajo el header fijo */
    .hero-promo.collapsed + .product-grid {
        margin-top: 4.5rem;
    }

    .carousel-container {
    display: flex;
    width: 100%;
    }

    .carousel-track {
    display: flex;
    gap: var(--gap);
    /* El ancho total del grupo original (4 slides * 400px + gaps) */
    animation: scroll 40s linear infinite;
    }

    .carousel-track:hover {
    animation-play-state: paused; /* Pausa al pasar el mouse */
    }

    @keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(calc(-1 * (var(--slide-width) + var(--gap)) * <?php echo count($promosList); ?>)); }
    }

    .slide {
    flex: 0 0 var(--slide-width);
    height: var(--slide-height);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    transition: transform 0.3s ease;
    }

    /* Imágenes de ejemplo locales (Data URI) para evitar 404s y errores de red */
    .ambient { background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22600%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23333%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23555%22%20font-family%3D%22sans-serif%22%20font-size%3D%2224%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3EAmbiente%3C%2Ftext%3E%3C%2Fsvg%3E'); }
    .food-1 { background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22600%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23333%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23555%22%20font-family%3D%22sans-serif%22%20font-size%3D%2224%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3EPlato%20D%C3%ADa%3C%2Ftext%3E%3C%2Fsvg%3E'); }
    .process { background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22600%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23333%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23555%22%20font-family%3D%22sans-serif%22%20font-size%3D%2224%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3EProceso%3C%2Ftext%3E%3C%2Fsvg%3E'); }

    .info-card {
    background: rgba(255, 255, 255, 0.1); /* Fondo semi-transparente */
    backdrop-filter: blur(12px); /* Efecto de cristal esmerilado */
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
    padding: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2); /* Borde sutil como el de tus botones */
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    .overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 20px;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    color: white;
    }

    .info-content h3 { color: #fff; margin-bottom: 10px; font-family: 'Segoe UI', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    .info-content p { font-size: 0.9rem; color: #eee; }
    .info-content ul { list-style: none; padding: 0; }
    .info-content li { margin: 8px 0; font-size: 0.9rem; color: rgba(255,255,255,0.9); }
    .info-content li strong { color: #d4a373; }

    /* Estilos para los pasos de "Cómo funciona" */
    .steps-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-top: 10px;
        gap: 10px;
    }
    .step-box { text-align: center; flex: 1; }
    .step-box i { display: block; font-size: 1.4rem; color: #d4a373; margin-bottom: 4px; }
    .step-box span { font-size: 0.65rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; color: #fff; }
    .step-sep { color: rgba(255,255,255,0.3); font-size: 0.8rem; }

    .badge {
    display: inline-block;
    margin-top: 8px;
    background: rgba(212, 163, 115, 0.8); /* Badge con transparencia */
    backdrop-filter: blur(4px);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    }

</style>

    <div class="hero-promo">
        <div class="carousel-container">
            <div class="carousel-track">
            <?php             
            // Duplicamos la lista para el efecto de scroll infinito seamless
            $displayItems = array_merge($promosList, $promosList); 
            
            foreach($displayItems as $index => $promo): 
                $isDuplicate = $index >= count($promosList);
                $bgStyle = !empty($promo['image']) ? "style='background-image: url(\"uploads/{$promo['image']}\")'" : "";
                $isInfoCard = ($promo['css_class'] === 'info-card');
            ?>
                <div class="slide <?php echo $promo['css_class']; ?>" <?php echo $bgStyle; ?> <?php echo $isDuplicate ? 'aria-hidden="true"' : ''; ?>>
                    <div class="<?php echo $isInfoCard ? 'info-content' : 'overlay'; ?>">
                        <h3><?php echo htmlspecialchars($promo['title']); ?></h3>
                        
                        <?php if($promo['type'] === 'reviews'): ?>
                            <div class="review-mini-slider">
                                <i class="fas fa-quote-left" style="opacity: 0.5; font-size: 0.8rem;"></i>
                                <p style="font-style: italic; font-size: 0.85rem; margin: 5px 0;">
                                    <?php echo htmlspecialchars($promo['content']); ?>
                                </p>
                                <div style="color: #ffa502; font-size: 0.7rem;">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        <?php else: ?>
                            <p><?php echo nl2br(htmlspecialchars($promo['content'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if($promo['type'] === 'hours' || $promo['type'] === 'location'): ?>
                            <span class="badge">¡Te esperamos!</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="product-grid">
        <?php if(empty($menu_items)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: white; border-radius: 8px;">
                <h3>No hay productos disponibles aquí 😔</h3>
                <p>Vuelve más tarde para ver las opciones.</p>
            </div>
        <?php else: ?>
            <?php $delay = 0; foreach($menu_items as $item): ?>
                <?php 
                        // Preparar datos para JS
                    $imgUrl = !empty($item['image']) ? $item['image'] : '';                 
                    // 1. Verificamos si el archivo existe físicamente (relativo a public/index.php)
                    $physicPath = 'uploads/' . $item['image'];                
                    // 2. Si existe, usamos 'uploads/'. Si no, usamos SVG local.
                    $localPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22300%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3ESin%20Imagen%3C%2Ftext%3E%3C%2Fsvg%3E";
                    $displayImg = (!empty($item['image']) && file_exists($physicPath)) ? 'uploads/' . rawurlencode($item['image']) . '?v=' . time() : $localPlaceholder;
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

// Lógica para colapsar el Hero al hacer scroll
let lastScrollY = window.scrollY;
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero-promo');
    if (!hero) return;

    const currentScroll = window.scrollY;

    // Umbral de colapso aumentado para dar margen de lectura inicial
    // Umbral de retorno mucho más bajo (20px) para que el hero no reaparezca 
    // mientras el usuario intenta ver o interactuar con las primeras tarjetas.
    if (currentScroll > 150) {
        hero.classList.add('collapsed');
    } else if (currentScroll < 20) {
        hero.classList.remove('collapsed');
    }
    
    lastScrollY = currentScroll;
}, { passive: true });
</script>