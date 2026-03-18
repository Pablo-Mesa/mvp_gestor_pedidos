<?php
$isEdit = isset($category);
$action = $isEdit ? '?route=categories_update' : '?route=categories_store';
?>

<div style="max-width: 600px; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2><?php echo $isEdit ? 'Editar Categoría' : 'Nueva Categoría'; ?></h2>
    
    <form action="<?php echo $action; ?>" method="POST">  
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Nombre de la Categoría</label>
            <input type="text" name="name" required value="<?php echo $isEdit ? $category['name'] : ''; ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <button type="submit" style="background: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer;">Guardar Categoría</button>
        <a href="?route=categories" style="margin-left: 1rem; color: #666; text-decoration: none;">Cancelar</a>
    </form>
</div>