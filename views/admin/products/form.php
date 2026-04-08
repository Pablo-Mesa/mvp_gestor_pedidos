<?php
$isEdit = isset($product);
$action = $isEdit ? '?route=products_update' : '?route=products_store';
?>

<div class="product-form-container">
    <h2 class="form-title"><?php echo $isEdit ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
    
    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
        <?php endif; ?>

        <div class="form-group mb-3">
            <label class="form-label">Categoría</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">-- Seleccione Categoría --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Nombre del Plato</label>
            <input type="text" name="name" class="form-control" required value="<?php echo $isEdit ? htmlspecialchars($product['name']) : ''; ?>" placeholder="Ej: Milanesa con Puré">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Detalles del plato..."><?php echo $isEdit ? htmlspecialchars($product['description']) : ''; ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Precio Entero (Gs.)</label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" required value="<?php echo $isEdit ? $product['price'] : ''; ?>">
        </div>

        <div id="half_price_toggle_container" style="margin-bottom: 1rem; display: none;">
            <label class="form-check-label d-flex align-items-center gap-2 cursor-pointer fw-medium">
                <input type="checkbox" id="enable_half_price" class="form-check-input" <?php echo ($isEdit && !empty($product['price_half'])) ? 'checked' : ''; ?>> 
                Habilitar opción de Medio Plato
            </label>
        </div>

        <div id="half_price_container" class="mb-3" style="display: <?php echo ($isEdit && !empty($product['price_half'])) ? 'block' : 'none'; ?>;">
            <label class="form-label text-secondary">Precio Medio Plato (Gs.)</label>
            <input type="number" step="0.01" id="price_half" name="price_half" class="form-control" value="<?php echo ($isEdit && isset($product['price_half'])) ? $product['price_half'] : ''; ?>">
            <small id="price_error" style="color: #dc3545; display: none; margin-top: 5px; font-weight: 500;">
                <i class="fas fa-exclamation-triangle"></i> El precio de medio plato debe ser menor al precio del plato entero.
            </small>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Imagen del Producto</label>
            <div class="custom-file-wrapper">
                <input type="file" name="image" class="form-control" id="product_image">
                <?php if($isEdit && $product['image']): ?>
                    <div class="current-image-preview mt-2 d-flex align-items-center gap-2">
                        <img src="uploads/<?php echo $product['image']; ?>" width="40" height="40" class="rounded border">
                        <small class="text-muted">Actual: <?php echo $product['image']; ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!$isEdit || $product['is_active']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active">
                Disponible para venta
            </label>
        </div>

        <div class="form-actions d-flex align-items-center gap-3">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Guardar Producto
            </button>
            <a href="?route=products" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<style>
    .product-form-container {
        max-width: 600px;
        background: white;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin: 1rem 0;
    }

    .form-title { margin-bottom: 2rem; font-weight: 700; color: #2d3436; font-size: 1.5rem; }
    .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 0.5rem; }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #dee2e6;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }

    .btn-save { 
        background: #007bff; color: white; border: none; padding: 0.75rem 1.5rem; 
        border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s;
    }
    .btn-save:hover { background: #0056b3; transform: translateY(-1px); }
    .btn-cancel { color: #6c757d; text-decoration: none; font-weight: 500; }
    .btn-cancel:hover { color: #343a40; text-decoration: underline; }
    
    /* Estilo para el input file moderno */
    .form-control::file-selector-button {
        background-color: #f1f3f5;
        color: #495057;
        border: none;
        border-inline-end: 1px solid #dee2e6;
        padding: 0.6rem 1rem;
        margin-inline-start: -1rem;
        margin-inline-end: 1rem;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    .form-control:hover::file-selector-button { background-color: #e9ecef; }
</style>

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