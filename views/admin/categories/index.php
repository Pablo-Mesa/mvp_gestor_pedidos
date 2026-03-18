<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Gestión de Categorías</h1>
    <a href="?route=categories_create" style="background: #28a745; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px;">+ Nueva Categoría</a>
</div>

<style>
    table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 0.2rem; text-decoration: none; color: white; margin-right: 5px; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
</style>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Fecha Creación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr>
                <td colspan="4">No hay categorías registradas.</td>
            </tr>
        <?php else: ?>
            <?php foreach($categories as $cat): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat['id']); ?></td>
                <td><?php echo htmlspecialchars($cat['name']); ?></td>
                <td><?php echo htmlspecialchars($cat['created_at']); ?></td>
                <td>
                    <a href="?route=categories_edit&id=<?php echo $cat['id']; ?>" class="btn-sm btn-edit">Editar</a>
                    <a href="?route=categories_delete&id=<?php echo $cat['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta categoría? Esto podría afectar a los productos asociados.')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>