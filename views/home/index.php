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

    /* Grid responsivo para Mapa y Horarios */
    .footer-info-grid {
        display: flex;
        flex-direction: row;
        gap: 30px;
        margin-top: 2rem;
        align-items: flex-start; /* Evita que las columnas se estiren innecesariamente */
        flex-wrap: wrap;
    }
    .footer-info-col {
        flex: 1;
        min-width: 320px; /* Punto de ruptura para el wrap */
    }
    @media (max-width: 768px) {
        .footer-info-grid { flex-direction: column; gap: 20px; }
        .footer-info-col { min-width: 100%; }
    }

    /* Carrusel Infinito de Categorías */
    .marquee-container {
        width: 100%;
        overflow: hidden;
        padding: 20px 0;
        position: relative;
        background: linear-gradient(to right, #fbfbfb, #ffffff, #fbfbfb);
    }
    .marquee-content {
        display: flex;
        width: max-content;
        animation: marquee-scroll 40s linear infinite;
    }
    .marquee-item {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 24px;
        margin: 0 10px;
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 100px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .marquee-icon {
        font-size: 1.5rem;
    }
    .marquee-text {
        font-weight: 700;
        color: #2d3436;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }
    @keyframes marquee-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* Carrusel de Reacciones (Ranking) */
    .marquee-container-reactions {
        width: 100%;
        overflow: hidden;
        padding: 10px 0;
        position: relative;
    }
    .marquee-content-reactions {
        display: flex;
        width: max-content;
        animation: marquee-scroll 35s linear infinite;
    }
    .reaction-item-card {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 20px;
        margin: 0 10px;
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        width: 240px; /* Ancho fijo para consistencia */
    }
    /* Pausar carruseles al pasar el mouse para facilitar la lectura */
    .marquee-container:hover .marquee-content,
    .marquee-container-reactions:hover .marquee-content-reactions {
        animation-play-state: paused;
    }
</style>    

<!-- vista local cerrado -->
<?php if (!isset($_SESSION['client_id'])): ?>
    <?php 
        $contactChannels = !empty($siteSettings['contact_channels']) ? json_decode($siteSettings['contact_channels'], true) : [];
        $mainPhone = !empty($siteSettings['store_phone']) ? $siteSettings['store_phone'] : '';

        // Fallback: Si el teléfono principal está vacío, buscamos el primero disponible en los canales configurados
        if (empty($mainPhone) && !empty($contactChannels)) {
            foreach ($contactChannels as $channel) {
                if (!empty($channel['phone'])) {
                    $mainPhone = $channel['phone'];
                    break;
                }
            }
        }
        $displayPhone = !empty($mainPhone) ? $mainPhone : 'Consultar al local';
    ?>
    <!-- Contenedor de Vista de Invitado / Local Cerrado -->
    <div id="closed-view-container" class="guest-container" style="padding: 1rem 0;">
        
        <header class="closed-hero" style="text-align: center; margin-bottom: 2rem;">
            <h1 id="display-site-name" style="font-weight: 800; color: #2d3436; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($siteName); ?></h1>
            <div id="display-next-opening" style="color: #856404; font-weight: 700; font-size: 1.1rem; background: #fff3cd; padding: 8px 15px; border-radius: 10px; display: inline-block;">
                <?php echo $isStoreOpen ? '¡Estamos Abiertos!' : 'Local Cerrado - ' . $nextOpeningMsg; ?>
            </div>
        </header>

        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 2rem;">
            
            <!-- Contacto -->
            <div class="contact-card" style="background: white; padding: 1.5rem; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
                <h4 style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; margin-bottom: 1rem; letter-spacing: 1px;">Contacto</h4>
                <div style="margin-bottom: 1.2rem; border-bottom: 1px solid #f8f9fa; padding-bottom: 0.8rem;">
                    <span style="display: block; font-size: 0.7rem; color: #888; text-transform: uppercase; margin-bottom: 4px;">Teléfono Oficial</span>
                    <span style="font-weight: 800; color: #2d3436; font-size: 1.4rem; letter-spacing: 1px;"><?php echo htmlspecialchars($displayPhone); ?></span>
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 1rem;">
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $mainPhone); ?>" id="btn-contact-whatsapp" class="btn" style="background: #25D366; color: white; flex: 1; text-align: center; text-decoration: none; border-radius: 12px; padding: 12px; font-weight: 700; font-size: 0.9rem;">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="tel:<?php echo $mainPhone; ?>" id="btn-contact-phone" class="btn" style="background: #0984e3; color: white; flex: 1; text-align: center; text-decoration: none; border-radius: 12px; padding: 12px; font-weight: 700; font-size: 0.9rem;">
                        <i class="fas fa-phone-alt"></i> Llamar
                    </a>
                </div>
                <p style="font-weight: 600; color: #2d3436; font-size: 0.9rem; margin-top: 0.5rem;">
                    <i class="fas fa-map-marker-alt" style="color: #ff4757;"></i> <?php echo htmlspecialchars($siteSettings['store_address'] ?? 'Asunción, Paraguay'); ?>
                </p>
            </div>

            <!-- Horarios -->
            <?php if (!empty($weeklySchedule)): ?>
            <div class="schedule-card" style="background: white; padding: 1.5rem; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
                <h4 style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; margin-bottom: 1rem; letter-spacing: 1px;">Horario de Atención</h4>
                <div class="schedule-list">
                    <?php foreach ($weeklySchedule as $day => $hours): ?>
                        <div class="schedule-list-item" style="display: flex; justify-content: space-between; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid #f8f9fa;">
                            <strong style="color: #2d3436;"><?php echo htmlspecialchars($day); ?></strong>
                            <span style="color: #636e72;"><?php echo htmlspecialchars($hours); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mapa -->
        <?php if (!empty($siteSettings['store_lat']) && !empty($siteSettings['store_lng'])): ?>
        <div id="map-iframe-container" style="height: 300px; border-radius: 24px; overflow: hidden; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <iframe width="100%" height="100%" frameborder="0" style="border:0;" 
                src="https://maps.google.com/maps?q=<?php echo $siteSettings['store_lat']; ?>,<?php echo $siteSettings['store_lng']; ?>&z=15&output=embed" 
                allowfullscreen>
            </iframe>
        </div>
        <?php endif; ?>
        
        <!-- Área de Reseñas / Testimonios -->
        <?php if (!empty($recentReviews)): ?>
        <div class="products-reviews" style="margin: 2rem 0;">
            <h4 style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; margin-bottom: 15px; text-align: center; letter-spacing: 1.5px; font-weight: 700;">Lo que dicen nuestros comensales</h4>
            <div class="horizontal-slider" style="padding-bottom: 15px; gap: 15px;">
                <?php foreach ($recentReviews as $rev): ?>
                    <div class="review-card" style="flex: 0 0 280px; background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; text-align: left;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #0984e3; font-size: 0.9rem; border: 1px solid #eee;">
                                <?php echo strtoupper(substr($rev['client_name'], 0, 1)); ?>
                            </div>
                            <div style="overflow: hidden;">
                                <span style="display: block; font-size: 0.9rem; font-weight: 700; color: #2d3436; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($rev['client_name']); ?></span>
                                <small style="color: #0984e3; font-size: 0.75rem; font-weight: 600;">Sobre: <?php echo htmlspecialchars($rev['product_name']); ?></small>
                            </div>
                        </div>
                        <p style="font-size: 0.85rem; color: #636e72; line-height: 1.5; font-style: italic; position: relative; padding-left: 10px; border-left: 3px solid #00b894;">
                            "<?php echo htmlspecialchars($rev['comment']); ?>"
                        </p>
                        <div style="margin-top: auto; display: flex; gap: 3px; color: #ffa502; font-size: 0.8rem;">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Social Proof: Reacciones Populares -->
        <?php if (!empty($popularProducts)): ?>
        <div class="social-proof-section" style="margin: 2rem 0;">
            <h4 style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; margin-bottom: 20px; text-align: center; letter-spacing: 1.5px; font-weight: 700;">Los favoritos de la comunidad</h4>
            <div class="marquee-container-reactions">
                <div class="marquee-content-reactions">
                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <?php foreach ($popularProducts as $index => $pop): ?>
                            <div class="reaction-item-card" style="flex: 0 0 240px; background: white; padding: 12px; margin: 0 10px; border-radius: 16px; border: 1px solid #f0f0f0; display: flex; align-items: center; gap: 12px;">
                                <img src="<?php echo !empty($pop['image']) ? $baseUrl.'uploads/'.$pop['image'] : $localPlaceholder; ?>" style="width: 45px; height: 45px; border-radius: 10px; object-fit: cover;">
                                <div style="overflow: hidden;">
                                    <span style="display: block; font-size: 0.85rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #2d3436;"><?php echo htmlspecialchars($pop['name']); ?></span>
                                    <div style="display: flex; gap: 8px; font-size: 0.75rem; color: #636e72;">
                                        <span><i class="fas fa-thumbs-up" style="color: #1e90ff;"></i> <?php echo $pop['total_likes']; ?></span>
                                        <span><i class="fas fa-heart" style="color: #ff4757;"></i> <?php echo $pop['total_favs']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Carrusel Automático de Categorías -->
        <?php if (!empty($navCategories)): ?>
        <div style="margin: 3rem 0;">
            <h4 style="font-size: 0.75rem; text-transform: uppercase; color: #aaa; margin-bottom: 20px; text-align: center; letter-spacing: 1.5px; font-weight: 700;">Nuestra Especialidad</h4>
            <div class="marquee-container">
                <div class="marquee-content">
                    <?php 
                    // Duplicamos el contenido para el efecto de bucle infinito
                    for ($i = 0; $i < 2; $i++): 
                        foreach ($navCategories as $cat): 
                            $emoji = '🍽️';
                            if (stripos($cat['name'], 'bebida') !== false) $emoji = '🥤';
                            if (stripos($cat['name'], 'postre') !== false) $emoji = '🍦';
                            if (stripos($cat['name'], 'desayuno') !== false) $emoji = '☕';
                            if (stripos($cat['name'], 'minuta') !== false || stripos($cat['name'], 'hamburguesa') !== false) $emoji = '🍔';
                            if (stripos($cat['name'], 'almuerzo') !== false) $emoji = '🍲';
                    ?>
                        <div class="marquee-item">
                            <span class="marquee-icon"><?php echo $emoji; ?></span>
                            <span class="marquee-text"><?php echo htmlspecialchars($cat['name']); ?></span>
                        </div>
                    <?php 
                        endforeach; 
                    endfor; 
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Acceso Principal -->
        <div class="auth-trigger-section" style="text-align: center; margin-top: 3rem; background: #f8f9fa; padding: 2.5rem; border-radius: 24px; border: 1px dashed #ddd;">
            <p style="color: #636e72; font-size: 0.95rem; margin-bottom: 1.5rem; font-weight: 500;">Inicia sesión para descubrir nuestro menú completo y realizar tu pedido.</p>
            <button onclick="openAuthModal()" class="btn-main" style="background: #2d3436; color: white; border: none; padding: 16px 45px; font-size: 1.1rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
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
                <div class="product-card <?php echo !$isStoreOpen ? 'is-closed-mode' : ''; ?>" data-category="<?php echo htmlspecialchars($item['category_name'] ?? 'all'); ?>" style="animation-delay: <?php echo $delay; ?>s;">                
                    <img src="<?php echo $displayImg; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-img">
                                
                    <div class="product-body">

                        <!-- categoria del producto -->
                        <div class="product-category">
                            <!-- categoria del producto -->
                            <span><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></span>
                            <!-- botones reaccion -->
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
                        
                        <!-- nombre/descripcion del producto -->
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

                        <!-- acciones productos -->
                        <div class="product-actions">
                            <!-- botones control cantidad -->
                            <div class="qty-control">
                                <button class="qty-btn" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)">-</button>
                                <input type="number" id="qty_<?php echo $item['id']; ?>" class="qty-input" value="1" min="1" readonly>
                                <button class="qty-btn" onclick="this.previousElementSibling.value = parseInt(this.previousElementSibling.value) + 1">+</button>
                            </div>
                            
                            <!-- Botón con ID dinámico y data attributes para que JS lea el estado actual -->
                            <?php if ($isStoreOpen): ?>
                                <button class="btn btn-primary" 
                                id="btn_add_<?php echo $item['id']; ?>"
                                data-id="<?php echo $item['product_id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['product_name']); ?>"
                                data-price="<?php echo $item['product_price']; ?>"
                                data-image="<?php echo htmlspecialchars($item['image']); ?>"
                                onclick="handleAddToCart(this, this.dataset.id, this.dataset.name, this.dataset.price, this.dataset.image, document.getElementById('qty_<?php echo $item['id']; ?>').value)">
                                Agregar <i class="fas fa-plus"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" style="background: #dfe6e9; color: #b2bec3; border-color: #b2bec3; cursor: not-allowed;" disabled>
                                    Cerrado <i class="fas fa-clock"></i>
                                </button>
                            <?php endif; ?>
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