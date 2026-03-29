<?php
// Lógica para determinar qué mostrar (Menú del día o Categoría específica)
$filter_category_id = $_GET['category_id'] ?? null;
$view_title = "Menú del Día"; // Título por defecto

if ($filter_category_id) {
    // Si hay categoría seleccionada, buscamos todos los productos de esa categoría
    if (!class_exists('Product')) {
        $path = 'models/Product.php';
        if (file_exists($path)) require_once $path;
        elseif (file_exists('../' . $path)) require_once '../' . $path;
    }
    
    $prodModel = new Product();
    // Usamos readAllActive para traer productos disponibles (fuera del menú del día)
    $stmt = $prodModel->readAllActive(); 
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
                'category_name' => $p['category_name'] ?? 'Cat'
            ];
        }
    }
    $view_title = "Categoría Seleccionada";
} else {
    // Si no hay filtro, usamos la variable $daily_menus que viene del controlador
    $menu_items = isset($daily_menus) ? $daily_menus : [];
}
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
        gap: 18px;
        margin-top: 8px;
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

    /* Colores de marca para reacciones */
    .reaction-item.fav:hover, .reaction-item.fav.active { color: #ff4757; transform: scale(1.25); filter: drop-shadow(0 0 5px rgba(255,71,87,0.3)); }
    .reaction-item.like:hover, .reaction-item.like.active { color: #1e90ff; transform: scale(1.25); filter: drop-shadow(0 0 5px rgba(30,144,255,0.3)); }
    .reaction-item.comment:hover { color: #ffa502; transform: scale(1.25); }
    .reaction-item.share:hover { color: #2ed573; transform: scale(1.2); }

    .reaction-count {
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 4px;
        color: #636e72;
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

    @media (max-width: 768px) {
        :root {
            --slide-width: 240px;
            --slide-height: 140px;
            --gap: 10px;
        }
        .info-content h3 { font-size: 0.9rem !important; margin-bottom: 5px !important; }
        .info-content li { font-size: 0.75rem !important; margin: 2px 0 !important; }
        .overlay h3 { font-size: 0.9rem; }
        .overlay p { font-size: 0.75rem; }
        .step-box i { font-size: 1.2rem !important; }

        .btn-primary {
            display: inline;
        }

    }

    .hero-promo {
    /* Break-out para ocupar todo el ancho de la pantalla ignorando el .container */
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: -2.1rem; /* Ajuste para eliminar cualquier gap con las categorías */
    margin-bottom: 0.5rem;
    overflow: hidden;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d3436 100%); /* Fondo oscuro para resaltar el cristal */
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
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
    100% { transform: translateX(calc(-1 * (var(--slide-width) + var(--gap)) * 4)); }
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

    /* Imágenes de ejemplo realistas */
    .ambient { background-image: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=600'); }
    .food-1 { background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=600'); }
    .process { background-image: url('https://images.unsplash.com/photo-1526367790999-0150786484a2?q=80&w=600'); }

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

    <!-- hero -->
    <div class="hero-promo">
        <div class="carousel-container">
            <div class="carousel-track">
            <!-- Grupo 1 -->
            <div class="slide ambient">
                <div class="overlay">
                <h3>Nuestro Comedor</h3>
                <p>Sabor casero hecho con amor todos los días.</p>
                </div>
            </div>
            <div class="slide process">
                <div class="overlay">
                    <h3>¿Cómo funciona?</h3>
                    <div class="steps-container">
                        <div class="step-box">
                            <i class="fas fa-utensils"></i>
                            <span>Elige</span>
                        </div>
                        <div class="step-sep"><i class="fas fa-chevron-right"></i></div>
                        <div class="step-box">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Pide</span>
                        </div>
                        <div class="step-sep"><i class="fas fa-chevron-right"></i></div>
                        <div class="step-box">
                            <i class="fas fa-smile-beam"></i>
                            <span>Disfruta</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide info-card">
                <div class="info-content">
                <h3>Horarios de Atención</h3>
                <ul>
                    <li><strong>Lun - Vie:</strong> 12:00 - 15:30 / 20:00 - 00:00</li>
                    <li><strong>Sáb - Dom:</strong> 12:00 - 16:30 / 20:00 - 01:00</li>
                </ul>
                <span class="badge">¡Te esperamos!</span>
                </div>
            </div>
            <div class="slide food-1">
                <div class="overlay">
                <h3>Ingredientes Frescos</h3>
                <p>Directo del mercado a tu mesa.</p>
                </div>
            </div>

            <!-- Duplicado para efecto infinito (Seamless Loop) -->
            <div class="slide ambient" aria-hidden="true">
                <div class="overlay">
                <h3>Nuestro Comedor</h3>
                </div>
            </div>
            <div class="slide process" aria-hidden="true">
                <div class="overlay">
                    <h3>¿Cómo funciona?</h3>
                    <div class="steps-container">
                        <div class="step-box">
                            <i class="fas fa-utensils"></i>
                            <span>Elige</span>
                        </div>
                        <div class="step-sep"><i class="fas fa-chevron-right"></i></div>
                        <div class="step-box">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Pide</span>
                        </div>
                        <div class="step-sep"><i class="fas fa-chevron-right"></i></div>
                        <div class="step-box">
                            <i class="fas fa-smile-beam"></i>
                            <span>Disfruta</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide info-card" aria-hidden="true">
                <div class="info-content">
                <h3>Horarios de Atención</h3>
                </div>
            </div>
            <div class="slide food-1" aria-hidden="true">
                <div class="overlay">
                <h3>Ingredientes Frescos</h3>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- hero -->

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
                // 2. Si existe, usamos 'uploads/' como ruta web. Si no, usamos placeholder.
                $displayImg = (!empty($item['image']) && file_exists($physicPath)) ? 'uploads/' . rawurlencode($item['image']) . '?v=' . time() : 'https://via.placeholder.com/300?text=Sin+Imagen';
                // Validar si tiene medio plato
                $hasHalf = !empty($item['price_half']) && $item['price_half'] > 0;
            ?>
            <div class="product-card" data-category="<?php echo htmlspecialchars($item['category_name'] ?? 'all'); ?>" style="animation-delay: <?php echo $delay; ?>s;">                
                <img src="<?php echo $displayImg; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-img">
                               
                <div class="product-body">
                    <div class="product-category">
                        <span><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></span>
                        <div class="product-reactions">
                            <button class="reaction-item fav" onclick="toggleReaction(this)" title="Añadir a favoritos">
                                <i class="<?php echo (isset($item['is_favorite']) && $item['is_favorite']) ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                            <button class="reaction-item like" onclick="toggleReaction(this)" title="Me gusta">
                                <i class="far fa-thumbs-up"></i>
                                <span class="reaction-count">0</span>
                            </button>
                            <button class="reaction-item comment" onclick="openReviewModal('<?php echo $item['id']; ?>')" title="Dejar una reseña">
                                <i class="far fa-comment"></i>
                            </button>
                            <button class="reaction-item share" onclick="shareProduct('<?php echo addslashes($item['product_name']); ?>')" title="Recomendar">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-title"><?php echo htmlspecialchars($item['product_name']); ?></div>
                    
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