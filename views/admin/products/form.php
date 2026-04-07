<?php
$isEdit = isset($product);
$action = $isEdit ? '?route=products_update' : '?route=products_store';
?>

<div style="max-width: 600px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2><?php echo $isEdit ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
    
    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
        <?php endif; ?>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Categoría</label>
            <select name="category_id" id="category_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Seleccione Categoría --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Nombre del Plato</label>
            <input type="text" name="name" required value="<?php echo $isEdit ? $product['name'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Descripción</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;"><?php echo $isEdit ? $product['description'] : ''; ?></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Precio</label>
            <input type="number" step="0.01" id="price" name="price" required value="<?php echo $isEdit ? $product['price'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div id="half_price_toggle_container" style="margin-bottom: 1rem; display: none;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                <input type="checkbox" id="enable_half_price" <?php echo ($isEdit && !empty($product['price_half'])) ? 'checked' : ''; ?>> 
                Habilitar opción de Medio Plato
            </label>
        </div>

        <div id="half_price_container" style="margin-bottom: 1rem; display: <?php echo ($isEdit && !empty($product['price_half'])) ? 'block' : 'none'; ?>;">
            <label style="display: block; margin-bottom: 0.5rem; color: #555;">Precio Medio Plato (Gs.)</label>
            <input type="number" step="0.01" id="price_half" name="price_half" value="<?php echo ($isEdit && isset($product['price_half'])) ? $product['price_half'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
            <small id="price_error" style="color: #dc3545; display: none; margin-top: 5px; font-weight: 500;">
                <i class="fas fa-exclamation-triangle"></i> El precio de medio plato debe ser menor al precio del plato entero.
            </small>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Imagen</label>
            <input type="file" name="image">
            <?php if($isEdit && $product['image']): ?>
                <p style="font-size: 0.8rem; color: #666;">Actual: <?php echo $product['image']; ?></p>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label>
                <input type="checkbox" name="is_active" <?php echo (!$isEdit || $product['is_active']) ? 'checked' : ''; ?>> Disponible para venta
            </label>
        </div>

        <button type="submit" style="background: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer;">Guardar Producto</button>
        <a href="?route=products" style="margin-left: 1rem; color: #666; text-decoration: none;">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const categorySelect = document.getElementById('category_id');
    const priceInput = document.getElementById('price');
    const halfPriceInput = document.getElementById('price_half');
    const enableCheck = document.getElementById('enable_half_price');
    const toggleContainer = document.getElementById('half_price_toggle_container');
    const halfPriceContainer = document.getElementById('half_price_container');
    const priceError = document.getElementById('price_error');

    // Controla si el checkbox de habilitación debe ser visible según la categoría
    function checkCategory() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const categoryName = selectedOption ? selectedOption.text.toLowerCase() : '';

        if (categoryName.includes('almuerzo')) {
            toggleContainer.style.display = 'block';
        } else {
            toggleContainer.style.display = 'none';
            enableCheck.checked = false; // Desactivar si se cambia a otra categoría
            halfPriceContainer.style.display = 'none';
            halfPriceInput.value = '';
            halfPriceInput.removeAttribute('required');
            validatePrices();
        }
    }

    // Maneja la visibilidad del campo según el checkbox
    enableCheck.addEventListener('change', function() {
        if (this.checked) {
            halfPriceContainer.style.display = 'block';
            halfPriceInput.setAttribute('required', 'required');
        } else {
            halfPriceContainer.style.display = 'none';
            halfPriceInput.removeAttribute('required');
            halfPriceInput.value = ''; // Limpiamos el valor al deshabilitar
            validatePrices();
        }
    });

    // Validación de coherencia de precios
    function validatePrices() {
        const fullPrice = parseFloat(priceInput.value) || 0;
        const halfPrice = parseFloat(halfPriceInput.value) || 0;

        if (enableCheck.checked && halfPrice > 0 && halfPrice >= fullPrice) {
            priceError.style.display = 'block';
            halfPriceInput.style.borderColor = '#dc3545';
            return false;
        } else {
            priceError.style.display = 'none';
            halfPriceInput.style.borderColor = '#ddd';
            return true;
        }
    }

    // Escuchar cambios en ambos campos de precio
    categorySelect.addEventListener('change', checkCategory);
    priceInput.addEventListener('input', validatePrices);
    halfPriceInput.addEventListener('input', validatePrices);
    checkCategory(); // Ejecución inicial al cargar

    // Bloquear envío si la información es incorrecta
    form.addEventListener('submit', function(e) {
        if (!validatePrices()) {
            e.preventDefault();
            Toast.fire('El precio de medio plato no puede ser mayor o igual al plato entero.', 'error');
        }
    });
});
</script>