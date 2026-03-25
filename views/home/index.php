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
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Estilos base para móviles (se activan debajo de 768px) */
    @media (max-width: 768px) {

        .btn-primary {
            display: inline;
        }

    }

</style>

<!-- Grid de Productos -->
<div class="product-grid">
    <?php if(empty($menu_items)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: white; border-radius: 8px;">
            <h3>No hay productos disponibles aquí 😔</h3>
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