<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Gestión de Productos</h1>
    <a href="?route=products_create" style="background: #28a745; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px;">+ Nuevo Producto</a>
</div>

<style>
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 0.2rem; text-decoration: none; color: white; margin-right: 5px; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    

    .contenedor-tabla {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 8px; /* Movemos los bordes aquí para que enmarquen el scroll */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        background: white;
    }

        /* El secreto para el encabezado fijo */
    thead th {
        position: sticky;
        top: 0;           /* Se queda pegado arriba */
        z-index: 10;      /* Asegura que quede por encima del contenido del tbody */
        background-color: #f8f9fa; /* Usamos el mismo gris claro de tus th originales */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Opcional: añade una sombrita para dar profundidad */
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
</style>

<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
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
                        <?php  
                            // Preparar datos para JS
                            $imgUrl = !empty($prod['image']) ? $prod['image'] : '';                 
                            // 1. Verificamos si el archivo existe físicamente (relativo a public/index.php)
                            $physicPath = 'uploads/' . $prod['image'];                
                            // 2. Si existe, usamos 'uploads/' como ruta web. Si no, usamos placeholder.
                            $displayImg = (!empty($prod['image']) && file_exists($physicPath)) ? 'uploads/' . rawurlencode($prod['image']) . '?v=' . time() : 'https://via.placeholder.com/300?text=Sin+Imagen';    
                        ?>
                        <img src="<?php echo $displayImg; ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                    <?php else: ?>
                        <span style="color: #ccc;">Sin img</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($prod['name']); ?></td>
                <td><span class="badge" style="background: #e9ecef; color: #495057;"><?php echo htmlspecialchars($prod['category_name'] ?? 'Sin categoría'); ?></span></td>
                <td>Gs. <?php echo number_format($prod['price'], 0); ?></td>
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
</div>