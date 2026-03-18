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
            <label style="display: block; margin-bottom: 0.5rem;">Descripción</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;"><?php echo $isEdit ? $product['description'] : ''; ?></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Precio</label>
            <input type="number" step="0.01" name="price" required value="<?php echo $isEdit ? $product['price'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
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