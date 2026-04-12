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
        flex-direction: column;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
        background-color: #fff;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .page-title { margin: 0; font-size: 1.6rem; font-weight: 700; color: #2d3436; letter-spacing: -0.5px; }

    .filter-container-admin {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Buscador Moderno */
    .search-box-admin {
        flex: 1;
        min-width: 280px;
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 0 12px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .search-box-admin:focus-within {
        background: #fff;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    .search-box-admin i { color: #adb5bd; font-size: 0.9rem; }
    
    .search-box-admin input {
        flex: 1;
        border: none;
        background: none;
        padding: 10px;
        font-size: 0.9rem;
        color: #495057;
        outline: none;
    }

    .clear-search { color: #adb5bd; transition: color 0.2s; }
    .clear-search:hover { color: #dc3545; }

    /* Scroll de Categorías Estilo Admin */
    .category-scroll-admin {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 2px 0;
    }
    .category-scroll-admin::-webkit-scrollbar { display: none; }

    .pill-admin {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        color: #636e72;
        font-size: 0.85rem;
        font-weight: 500;
        background: #f1f3f5;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .pill-admin:hover { background: #e9ecef; color: #2d3436; }
    .pill-admin.active { background: #2d3436; color: #fff; border-color: #2d3436; }

    .btn-add-product { 
        background: #28a745; 
        color: white; 
        padding: 0.75rem 1.25rem; 
        text-decoration: none; 
        border-radius: 10px; 
        font-weight: 600; 
        font-size: 0.9rem;
        white-space: nowrap; 
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add-product:hover { 
        background: #218838; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40,167,69,0.2); 
    }

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

    /* Efecto de enfoque en filas (Consistencia con Pedidos) */
    table tbody:hover tr {
        filter: blur(1px);
        opacity: 0.6;
        transition: all 0.3s;
    }
    table tbody tr:hover {
        filter: blur(0);
        opacity: 1;
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .header-actions { padding: 1rem; gap: 1rem; }
        .page-title { font-size: 1.4rem; }
        .search-box-admin { min-width: 100%; order: 2; }
        .category-scroll-admin { order: 3; width: 100%; }
        .header-main { order: 1; }
    }
</style>

<div class="header-actions">
    <div class="header-main">
        <h1 class="page-title">Gestión de Productos</h1>
        <a href="?route=products_create" class="btn-add-product">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Nuevo Producto</span>
        </a>
    </div>

    <div class="filter-container-admin">
        <form id="adminProductSearchForm" action="index.php" method="GET" class="search-box-admin">
            <input type="hidden" name="route" value="products">
            <?php if(isset($_GET['category'])): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
            <?php endif; ?>
            <i class="fas fa-search"></i>
            <input type="text" 
                   id="adminProductSearch" 
                   name="search" 
                   placeholder="Buscar por nombre o ID..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                   autocomplete="off">
            <?php if(!empty($_GET['search'])): ?>
                <a href="?route=products<?php echo isset($_GET['category']) ? '&category='.$_GET['category'] : ''; ?>" class="clear-search">
                    <i class="fas fa-times-circle"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="category-scroll-admin">
            <?php 
            $current_cat = $_GET['category'] ?? 'all';
            $search_val = !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : '';
            ?>
            <a href="?route=products&category=all<?php echo $search_val; ?>" class="pill-admin <?php echo $current_cat === 'all' ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?route=products&category=<?php echo $cat['id']; ?><?php echo $search_val; ?>" 
                   class="pill-admin <?php echo $current_cat == $cat['id'] ? 'active' : ''; ?>">
                   <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

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
        <tbody id="productTableBody">
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
                    <a href="javascript:void(0)" 
                       class="btn-sm btn-delete" 
                       onclick="confirmAction('?route=products_delete&id=<?php echo $prod['id']; ?>', {title: '¿Eliminar producto?', message: 'Se eliminará <?php echo addslashes(htmlspecialchars($prod['name'])); ?> del catálogo permanentemente.'})">
                       Eliminar
                    </a>
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

<script>
/**
 * Lógica de Búsqueda Live (AJAX) para Productos
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('adminProductSearch');
    const searchForm = document.getElementById('adminProductSearchForm');
    const tableBody = document.getElementById('productTableBody');
    const categoryPills = document.querySelectorAll('.pill-admin');
    let debounceTimer;

    // 1. Evitar que el formulario recargue la página al dar Enter
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => e.preventDefault());
    }

    // 2. Escuchar escritura en el buscador
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            // Esperamos 300ms antes de disparar la búsqueda para optimizar recursos
            debounceTimer = setTimeout(() => {
                executeSearch();
            }, 300);
        });
    }

    // 3. Escuchar clics en categorías (AJAX)
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Actualizar UI de los botones
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            // Actualizar URL sin recargar (opcional, para poder copiar el link)
            window.history.pushState({}, '', this.href);
            
            executeSearch();
        });
    });

    function executeSearch() {
        const query = searchInput.value.trim();
        // Obtenemos la categoría de la píldora activa
        const activePill = document.querySelector('.pill-admin.active');
        const url = new URL(activePill.href, window.location.origin);
        const category = url.searchParams.get('category') || 'all';

        fetchProducts(query, category);
    }

    async function fetchProducts(search, category) {
        try {
            const response = await fetch(`?route=products_api&search=${encodeURIComponent(search)}&category=${category}`);
            const products = await response.json();
            renderTable(products);
        } catch (error) {
            console.error('Error en la búsqueda:', error);
        }
    }

    function renderTable(products) {
        if (products.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2rem; color: #636e72;">No se encontraron resultados.</td></tr>';
            return;
        }

        let html = '';
        products.forEach(prod => {
            const imgPath = prod.image ? `uploads/${prod.image}` : 'https://via.placeholder.com/50?text=Sin+Img';
            const statusBadge = prod.is_active 
                ? '<span class="badge badge-success">Activo</span>' 
                : '<span class="badge badge-danger">Inactivo</span>';
            const price = new Intl.NumberFormat('es-PY').format(prod.price);

            html += `
            <tr>
                <td><img src="${imgPath}" width="50" height="50" style="object-fit: cover; border-radius: 4px;"></td>
                <td>${escapeHtml(prod.name)}</td>
                <td><span class="badge" style="background: #e9ecef; color: #495057;">${escapeHtml(prod.category_name || 'Sin categoría')}</span></td>
                <td>Gs. ${price}</td>
                <td>${statusBadge}</td>
                <td>
                    <a href="?route=products_edit&id=${prod.id}" class="btn-sm btn-edit">Editar</a>
                    <a href="javascript:void(0)" 
                       class="btn-sm btn-delete" 
                       onclick="confirmAction('?route=products_delete&id=${prod.id}', {
                           title: '¿Eliminar producto?', 
                           message: 'Se eliminará ${escapeHtml(prod.name)} permanentemente.'
                       })">
                       Eliminar
                    </a>
                </td>
            </tr>`;
        });
        tableBody.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>