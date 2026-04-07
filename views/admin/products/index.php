<div class="header-actions">
    <h1 class="page-title">Gestión de Productos</h1>

    <div class="filter-scroll-wrapper">
        <?php 
        if (isset($categories)) {
            $current_cat = $_GET['category'] ?? 'all';
            echo '<div class="filter-group">';
            
            echo '<a href="?route=products&category=all" class="filter-pill ' . ($current_cat === 'all' ? 'active' : '') . '">Todos</a>';
            
            foreach ($categories as $cat) {
                $isActive = ($current_cat == $cat['id']) ? 'active' : '';
                echo '<a href="?route=products&category=' . $cat['id'] . '" class="filter-pill ' . $isActive . '">' . htmlspecialchars($cat['name']) . '</a>';
            }
            
            echo '</div>';
        }
        ?>
    </div>

    <a href="?route=products_create" class="btn-add-product"><i class="fas fa-plus"></i> Nuevo Producto</a>
</div>

<style>
    /* Estilos Generales de Tabla */
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 0.2rem; text-decoration: none; color: white; margin-right: 5px; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger { background: #f8d7da; color: #721c24; }

    /* Header y Filtros */
    .header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .page-title { margin: 0; font-size: 1.5rem; color: #333; white-space: nowrap; }

    /* Contenedor central de filtros */
    .filter-scroll-wrapper {
        flex: 1;
        display: flex;
        justify-content: center;
        min-width: 0; /* Crucial para permitir scroll en flex items */
        padding: 0 1rem;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 4px; /* Espacio para scrollbar sutil */
        scrollbar-width: thin;
        white-space: nowrap;
        max-width: 100%;
    }

    /* Estilo de los botones filtro (Píldoras) */
    .filter-pill {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        text-decoration: none;
        color: #555;
        font-size: 0.85rem;
        background-color: #f1f3f5;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .filter-pill:hover { background-color: #e9ecef; color: #333; }
    
    .filter-pill.active {
        background-color: #007bff;
        color: white;
        box-shadow: 0 2px 5px rgba(0,123,255,0.3);
    }

    .btn-add-product { background: #28a745; color: white; padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 6px; font-weight: 500; white-space: nowrap; transition: 0.2s; }
    .btn-add-product:hover { background: #218838; box-shadow: 0 2px 5px rgba(40,167,69,0.3); }

    /* Tabla con Scroll */
    .contenedor-tabla {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 8px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        background: white;
    }
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

    @media (max-width: 768px) {
        .header-actions { flex-direction: column; align-items: stretch; }
        .filter-scroll-wrapper { justify-content: flex-start; padding: 0; }
        .page-title { text-align: center; }
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    
    if (success === 'created') {
        Toast.fire('¡Producto creado con éxito!', 'success');
    } else if (success === 'updated') {
        Toast.fire('¡Producto actualizado con éxito!', 'success');
    } else if (success === 'deleted') {
        Toast.fire('El producto ha sido eliminado.', 'info');
    }
});
</script>