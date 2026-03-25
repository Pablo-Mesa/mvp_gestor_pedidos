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
            <label style="display: block; margin-bottom: 0.5rem;">Nombre del Plato</label>
            <input type="text" name="name" required value="<?php echo $isEdit ? $product['name'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Categoría del Producto</label>
            <select name="category_id" id="category_selector" required style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Seleccione una categoría</option>
                <?php 
                foreach($categories as $category_option): 
                    $selected = ($isEdit && isset($product['category_id']) && $product['category_id'] == $category_option['id']) ? 'selected' : '';
                ?>
                    <option value="<?php echo $category_option['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($category_option['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Descripción</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;"><?php echo $isEdit ? $product['description'] : ''; ?></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Precio</label>
            <input type="number" step="0.01" name="price" required value="<?php echo $isEdit ? $product['price'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <!-- Opción de Medio Plato (Solo para Almuerzos) -->
        <div id="container_half_price" style="display: none; margin-bottom: 1rem; background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <label for="toggle_half_price" style="margin: 0; font-size: 0.95rem; font-weight: 500; color: #495057;">Habilitar "Medio plato"</label>
                <!-- Toggle Button -->
                <label class="switch">
                    <input type="checkbox" id="toggle_half_price" <?php echo ($isEdit && !empty($product['price_half'])) ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
            </div>
            
            <div id="input_half_price_wrapper" style="display: none; margin-top: 10px;">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: #666;">Precio (Medio plato)</label>
                <input type="number" step="0.01" name="price_half" id="price_half" value="<?php echo ($isEdit && isset($product['price_half'])) ? $product['price_half'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
            </div>
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

<style>
    /* Estilos para el Toggle Switch */
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e4e7; transition: .3s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    
    input:checked + .slider { background-color: #007bff; }
    input:checked + .slider:before { transform: translateX(20px); }
    
    /* Hover effect */
    .slider:hover { background-color: #d4d4d8; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_selector');
    const containerHalfPrice = document.getElementById('container_half_price');
    const toggleHalfPrice = document.getElementById('toggle_half_price');
    const inputWrapper = document.getElementById('input_half_price_wrapper');
    const inputHalfPrice = document.getElementById('price_half');

    // Función para mostrar/ocultar el contenedor según la categoría
    function checkCategory() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const catName = selectedOption ? selectedOption.text.toLowerCase() : '';

        // Se habilita solo si la categoría contiene "almuerzo"
        if (catName.includes('almuerzo')) {
            containerHalfPrice.style.display = 'block';
        } else {
            containerHalfPrice.style.display = 'none';
        }
    }

    // Función para mostrar/ocultar el input según el toggle
    function handleToggle() {
        inputWrapper.style.display = toggleHalfPrice.checked ? 'block' : 'none';
        
        // Si desactivamos el toggle, deshabilitamos el input para que no se envíe en el POST
        if (!toggleHalfPrice.checked) {
            inputHalfPrice.disabled = true;
        } else {
            inputHalfPrice.disabled = false;
        }
    }

    categorySelect.addEventListener('change', checkCategory);
    toggleHalfPrice.addEventListener('change', handleToggle);

    // Ejecutar al cargar por si es edición
    checkCategory();
    handleToggle();
});
</script>