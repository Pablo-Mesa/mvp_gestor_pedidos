<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Gestión de Productos</h1>
    <a href="?route=products_create" style="background: #28a745; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px;">+ Nuevo Producto</a>
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
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($products as $prod): ?>
        <tr>
            <td>
                <?php if($prod['image']): ?>
                    <img src="../public/uploads/<?php echo $prod['image']; ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                <?php else: ?>
                    <span style="color: #ccc;">Sin img</span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($prod['name']); ?></td>
            <td>$<?php echo number_format($prod['price'], 2); ?></td>
            <td>
                <span class="badge <?php echo $prod['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                    <?php echo $prod['is_active'] ? 'Activo' : 'Inactivo'; ?>
                </span>
            </td>
            <td>
                <a href="?route=products_edit&id=<?php echo $prod['id']; ?>" class="btn-sm btn-edit">Editar</a>
                <a href="?route=products_delete&id=<?php echo $prod['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>