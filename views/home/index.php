<?php
// Obtener categorías únicas del menú del día para los filtros
$categories = [];
$menu_items = isset($daily_menus) ? $daily_menus : [];

foreach ($menu_items as $item) {
    if (!empty($item['category_name'])) {
        $categories[$item['category_name']] = true;
    }
}
$categories = array_keys($categories);
?>

<style>
    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }
</style>

<!-- Grid de Productos -->
<div class="product-grid">
    <?php if(empty($menu_items)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: white; border-radius: 8px;">
            <h3>No hay menú disponible hoy 😔</h3>
            <p>Vuelve más tarde para ver las opciones.</p>
        </div>
    <?php else: ?>
        <?php foreach($menu_items as $item): ?>
            <?php 
                // Preparar datos para JS
                $imgUrl = !empty($item['image']) ? $item['image'] : '';                 
                // 1. Verificamos si el archivo existe físicamente (relativo a public/index.php)
                $physicPath = 'uploads/' . $item['image'];                
                // 2. Si existe, usamos 'uploads/' como ruta web. Si no, usamos placeholder.
                $displayImg = (!empty($item['image']) && file_exists($physicPath)) ? 'uploads/' . rawurlencode($item['image']) . '?v=' . time() : 'https://via.placeholder.com/300?text=Sin+Imagen';
            ?>
            <div class="product-card" data-category="<?php echo htmlspecialchars($item['category_name'] ?? 'all'); ?>">                
                <img src="<?php echo $displayImg; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-img">
                               
                <div class="product-body">
                    <div class="product-category"><?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?></div>
                    <div class="product-title"><?php echo htmlspecialchars($item['product_name']); ?></div>
                    <div class="product-price">Gs. <?php echo number_format($item['product_price'], 0, ',', '.'); ?></div>
                    
                    <div class="product-actions">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)">-</button>
                            <input type="number" id="qty_<?php echo $item['id']; ?>" class="qty-input" value="1" min="1" readonly>
                            <button class="qty-btn" onclick="this.previousElementSibling.value = parseInt(this.previousElementSibling.value) + 1">+</button>
                        </div>
                        <button class="btn btn-primary" onclick="addToCart(
                            <?php echo $item['product_id']; ?>, 
                            '<?php echo addslashes($item['product_name']); ?>', 
                            <?php echo $item['product_price']; ?>, 
                            '<?php echo addslashes($item['image']); ?>', 
                            document.getElementById('qty_<?php echo $item['id']; ?>').value
                        )">
                            Agregar <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
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