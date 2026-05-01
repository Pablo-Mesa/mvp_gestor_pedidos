<?php 
$localPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22300%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3ESin%20Imagen%3C%2Ftext%3E%3C%2Fsvg%3E"; 

// Definimos el bloque de la tarjeta aquí mismo para que el archivo sea funcional
if (!function_exists('renderCard')) {
    function renderCard($item, $baseUrl, $localPlaceholder, $isStoreOpen, $delay = 0) {
        $physicPath = 'uploads/' . $item['image'];                
        $displayImg = (!empty($item['image']) && file_exists($physicPath)) ? $baseUrl . 'uploads/' . rawurlencode($item['image']) : $localPlaceholder;
        
        // Normalizar nombres si vienen de la base de datos directamente
        $pName = $item['product_name'] ?? $item['name'] ?? 'Producto';
        $pPrice = $item['product_price'] ?? $item['price'] ?? 0;
        $pId = $item['product_id'] ?? $item['id'] ?? 0;
        ?>
        <div class="product-card <?php echo !$isStoreOpen ? 'is-closed-mode' : ''; ?>" style="animation-delay: <?php echo $delay; ?>s;">                
            <img src="<?php echo $displayImg; ?>" class="product-img">
            <div class="product-body">
                <div class="product-category">
                    <span><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></span>
                </div>
                <div class="product-title"><?php echo htmlspecialchars($pName); ?></div>
                <div class="product-price">Gs. <?php echo number_format($pPrice, 0, ',', '.'); ?></div>
                
                <div class="product-actions">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)">-</button>
                        <input type="number" id="qty_<?php echo $pId; ?>" class="qty-input" value="1" min="1" readonly>
                        <button class="qty-btn" onclick="this.previousElementSibling.value = parseInt(this.previousElementSibling.value) + 1">+</button>
                    </div>
                    <?php if ($isStoreOpen): ?>
                        <button class="btn btn-primary" 
                            data-id="<?php echo $pId; ?>"
                            data-name="<?php echo htmlspecialchars($pName); ?>"
                            data-price="<?php echo $pPrice; ?>"
                            data-image="<?php echo htmlspecialchars($item['image']); ?>"
                            onclick="handleAddToCart(this, this.dataset.id, this.dataset.name, this.dataset.price, this.dataset.image, this.parentElement.querySelector('.qty-input').value)">
                            Agregar <i class="fas fa-plus"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-primary" style="background: #dfe6e9; color: #b2bec3; cursor: not-allowed;" disabled>Cerrado</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<div class="favorites-container" style="padding-bottom: 3rem;">
    
    <!-- Encabezado -->
    <div style="margin-bottom: 2rem; text-align: left;">
        <h2 style="font-weight: 800; color: #2d3436; margin-bottom: 0.5rem;"> <i class="fas fa-heart" style="color: #ff4757;"></i> Mis Favoritos</h2>
        <p style="color: #636e72; font-size: 0.95rem;">Los platos que más te gustan, en un solo lugar.</p>
    </div>

    <!-- Sección 1: Marcados como Favoritos -->
    <section style="margin-bottom: 3rem;">
        <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #aaa; margin-bottom: 1.5rem; letter-spacing: 1px;">Guardados con ❤️</h4>
        
        <?php if(empty($favorites)): ?>
            <div style="background: white; padding: 2rem; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                <i class="far fa-heart" style="font-size: 2.5rem; color: #eee; margin-bottom: 1rem; display: block;"></i>
                <p style="color: #999; font-size: 0.9rem;">Aún no has marcado ningún plato como favorito.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php $delay = 0; foreach($favorites as $item): ?>
                    <?php renderCard($item, $baseUrl, $localPlaceholder, $isStoreOpen, $delay); ?>
                <?php $delay += 0.08; endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Sección 2: Lo que más pides -->
    <section>
        <h4 style="font-size: 0.8rem; text-transform: uppercase; color: #aaa; margin-bottom: 1.5rem; letter-spacing: 1px;">Tus Habituales (Lo que más pides)</h4>
        
        <?php if(empty($frequent)): ?>
            <div style="background: white; padding: 2rem; border-radius: 20px; text-align: center; border: 1px dashed #ddd;">
                <i class="fas fa-utensils" style="font-size: 2.5rem; color: #eee; margin-bottom: 1rem; display: block;"></i>
                <p style="color: #999; font-size: 0.9rem;">Cuando realices pedidos, aparecerán aquí tus platos frecuentes.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php $delay = 0; foreach($frequent as $item): ?>
                    <?php renderCard($item, $baseUrl, $localPlaceholder, $isStoreOpen, $delay); ?>
                <?php $delay += 0.08; endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<script>
/**
 * Maneja la adición al carrito con feedback visual.
 * Implementada aquí para resolver el ReferenceError.
 */
function handleAddToCart(btn, id, name, price, image, qty) {
    // 1. Llamar a la función global original (en tool-kit-v002.js / client_layout.php)
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
</script>