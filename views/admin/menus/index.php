<style>
    * {
        margin: 0px;
        padding: 0px;
        box-sizing: border-box;
    }
    .menu-manager-grid { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: flex-start; }
    .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .card h2 { margin-top: 0; margin-bottom: 1rem; font-size: 1.2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
    .btn { display: inline-block; background: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; text-align: center; }
    .btn-danger { background-color: #dc3545; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }    
    .badge { padding: 0.25em 0.6em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
    .badge-success { color: #155724; background-color: #d4edda; }
    .badge-danger { color: #721c24; background-color: #f8d7da; }
    
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; }

    .date-selector { margin-bottom: 0.5rem; }
    
    .date-selector form {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        background: #ffffff;
        padding: 10px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .date-selector label {
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700 !important; /* Sobreescribe el inline si es necesario */
    }

    .date-selector input[type="date"] {
        appearance: none;
        -webkit-appearance: none;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        color: #1e293b;
        font-weight: 600;
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
        background: #f8fafc;
    }

    .date-selector input[type="date"]:hover {
        border-color: #3b82f6;
        background: #fff;
    }

    .date-selector input[type="date"]:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        background: #fff;
    }

    .contenedor-tabla {
        max-height: 250px;
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
    
    /* El contenedor flex ahora está dentro de un div, no en el td */
    .actions-wrapper {
        display: flex;
        gap: 8px;
        align-items: center; /* Alinea verticalmente los botones al centro */
        white-space: nowrap;
    }

    /* Estilos para el Buscador de Productos (Modal) */
    .product-picker-modal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding-top: 50px;
        z-index: 2000;
    }
    .modal-search-card {
        background: white;
        width: 90%;
        max-width: 600px;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    .product-picker-modal.active .modal-search-card { transform: translateY(0); }

    .modal-step { padding: 1.5rem; display: none; }
    .modal-step.active { display: block; animation: slideIn 0.3s ease-out; }
    
    .modal-search-body { max-height: 300px; overflow-y: auto; border-top: 1px solid #eee; }
    
    .search-input, .qty-modal-input { 
        width: 100%; 
        padding: 12px; 
        font-size: 1.1rem; 
        border: 2px solid #007bff; 
        border-radius: 8px; 
        outline: none;
        box-shadow: 0 4px 6px rgba(0,123,255,0.1);
    }

    .product-row { 
        padding: 14px 1rem; 
        border-bottom: 1px solid #f1f1f1; 
        cursor: pointer; 
        display: flex; 
        justify-content: space-between;
        align-items: center;
    }
    .product-row.selected { background-color: #e7f1ff; border-left: 4px solid #007bff; }
    .product-row:hover { background-color: #f8f9fa; }
    .product-row .price { font-weight: bold; color: #28a745; }

    .step-badge { background: #007bff; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; margin-bottom: 10px; display: inline-block; }
    
    /* Estilos para la selección del tipo de menú */
    .menu-type-options { display: flex; flex-direction: column; gap: 10px; margin-top: 15px; }
    .menu-type-option {
        display: flex; align-items: center; padding: 12px; border: 2px solid #e2e8f0;
        border-radius: 10px; cursor: pointer; transition: all 0.2s; background: #f8fafc;
    }
    .menu-type-option:hover { border-color: #3b82f6; }
    .menu-type-option.selected { border-color: #3b82f6; background: #eff6ff; }
    .menu-type-option input { display: none; }
    .menu-type-option i { font-size: 1.5rem; margin-right: 12px; color: #64748b; }
    .menu-type-option.selected i { color: #3b82f6; }
    .menu-type-option div h4 { font-size: 0.95rem; margin: 0; color: #1e293b; }
    .menu-type-option div p { font-size: 0.8rem; margin: 0; color: #64748b; }

    /* Efecto de carga para AJAX */
    .loading-row { opacity: 0.5; pointer-events: none; filter: grayscale(1); transition: 0.3s; }
    .menu-type-section h2 i { transition: transform 0.3s; }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(10px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Botón disparador mejorado */
    .assign-trigger-btn {
        width: 100%; height: 120px; 
        border: 2px dashed #007bff; 
        background: #f0f7ff; 
        color: #007bff;
        border-radius: 12px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; cursor: pointer; transition: all 0.2s;
    }
    .assign-trigger-btn:hover { background: #e1efff; transform: scale(1.02); }
    .assign-trigger-btn i { font-size: 2rem; }
    .assign-trigger-btn span { font-weight: bold; font-size: 1.1rem; }
    .assign-trigger-btn kbd { 
        background: #fff; border: 1px solid #007bff; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;
    }

    /* New styles for menu type grouping */
    .menu-type-section {
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .menu-type-section h3 {
        background-color: #f8f9fa;
        padding: 1rem 1.5rem;
        margin: 0;
        border-bottom: 1px solid #e0e0e0;
        font-size: 1.1rem;
        color: #343a40;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .menu-type-section h3 i {
        color: #6c757d;
    }

    /* Adjustments for the product picker modal steps */
    .modal-search-card {
        max-height: 90vh; /* Ensure modal doesn't overflow viewport */
        display: flex;
        flex-direction: column;
    }
    .modal-search-body {
        flex: 1; /* Allow body to grow and shrink */
        overflow-y: auto;
        border-top: 1px solid #eee;
    }
    .modal-step {
        padding: 1.5rem;
        display: none;
        flex-direction: column; /* Use flex for vertical alignment */
        flex: 1; /* Allow steps to fill available space */
    }
    .modal-step.active {
        display: flex; /* Change to flex */
    }
    .step-badge {
        margin-bottom: 15px; /* More space below badge */
    }
    .search-input, .qty-modal-input {
        margin-bottom: 15px; /* Add margin below inputs */
    }
    .modal-step-footer {
        margin-top: auto; /* Push footer to the bottom of the step */
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .modal-step-footer .btn {
        flex: 0 0 auto; /* Prevent buttons from stretching */
    }

    /* New styles for menu type selection */
    .menu-type-options {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
    }
    .menu-type-option {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #fdfdfd;
    }
    .menu-type-option:hover {
        border-color: #007bff;
        background-color: #e7f1ff;
    }
    .menu-type-option.selected {
        border-color: #007bff;
        background-color: #e7f1ff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
    }
    .menu-type-option input[type="radio"] {
        display: none; /* Hide default radio button */
    }
    .menu-type-option .icon {
        font-size: 1.8rem;
        margin-right: 15px;
        color: #6c757d;
    }
    .menu-type-option.selected .icon {
        color: #007bff;
    }
    .menu-type-option .details h4 {
        margin: 0;
        font-size: 1.1rem;
        color: #333;
    }
    .menu-type-option .details p {
        margin: 5px 0 0;
        font-size: 0.85rem;
        color: #666;
    }
</style>

<h1>Gestión de Menú del Día</h1>

<!-- Selector de Fecha -->
<div class="date-selector">
    <form method="GET">
        <input type="hidden" name="route" value="menus">
        <label for="date-picker" style="font-weight: 600;">Seleccionar Fecha:</label>
        <input type="date" id="date-picker" name="date" value="<?php echo htmlspecialchars($current_date); ?>" onchange="this.form.submit()">
    </form>
</div>

<div class="menu-manager-grid">
    <!-- Columna Izquierda: Menús Asignados -->
    <div>
        <!-- Menú Principal -->
        <div class="card" style="margin-bottom: 20px;">
            <h2 style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-star" style="color: #f1c40f;"></i> Menú Principal</h2>
            <?php if (empty($assigned_menus_primary)): ?>
                <p style="color: #666; font-style: italic;">No hay platos principales asignados.</p>
            <?php else: ?>
                <div class="contenedor-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width: 120px;">Stock</th>
                                <th style="width: 120px;">Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($assigned_menus_primary as $menu): ?>
                                <tr id="menu-row-<?php echo $menu['id']; ?>" data-product-id="<?php echo $menu['product_id']; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($menu['product_name']); ?></strong>
                                        <small style="display: block; color: #6c757d;">Gs. <?php echo number_format($menu['product_price'], 0); ?></small>
                                    </td>
                                    <td><?php echo $menu['daily_stock'] ?? '∞'; ?></td>
                                    <td>
                                        <span class="status-badge badge <?php echo $menu['is_available'] ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $menu['is_available'] ? 'Disponible' : 'Agotado'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-wrapper">
                                            <button onclick="ajaxToggleStatus(<?php echo $menu['id']; ?>, <?php echo $menu['is_available']; ?>)" class="btn btn-sm btn-toggle" style="background-color: #ffc107; color: #212529; min-width: 85px;">
                                                <?php echo $menu['is_available'] ? 'Agotar' : 'Habilitar'; ?>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="ajaxUnassign(<?php echo $menu['id']; ?>)">Quitar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Menú Secundario -->
        <div class="card">
            <h2 style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-utensils" style="color: #95a5a6;"></i> Menú Secundario (Fijos)</h2>
            <?php if (empty($assigned_menus_secondary)): ?>
                <p style="color: #666; font-style: italic;">No hay platos secundarios asignados.</p>
            <?php else: ?>
                <div class="contenedor-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width: 120px;">Stock</th>
                                <th style="width: 120px;">Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($assigned_menus_secondary as $menu): ?>
                                <tr id="menu-row-<?php echo $menu['id']; ?>" data-product-id="<?php echo $menu['product_id']; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($menu['product_name']); ?></strong>
                                        <small style="display: block; color: #6c757d;">Gs. <?php echo number_format($menu['product_price'], 0); ?></small>
                                    </td>
                                    <td><?php echo $menu['daily_stock'] ?? '∞'; ?></td>
                                    <td>
                                        <span class="status-badge badge <?php echo $menu['is_available'] ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $menu['is_available'] ? 'Disponible' : 'Agotado'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-wrapper">
                                            <button onclick="ajaxToggleStatus(<?php echo $menu['id']; ?>, <?php echo $menu['is_available']; ?>)" class="btn btn-sm btn-toggle" style="background-color: #ffc107; color: #212529; min-width: 85px;">
                                                <?php echo $menu['is_available'] ? 'Agotar' : 'Habilitar'; ?>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="ajaxUnassign(<?php echo $menu['id']; ?>)">Quitar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Derecha: Asignar Nuevo Menú -->
    <div class="card">
        <h2>Asignar Plato</h2>
        <form id="mainAssignForm" action="?route=menus_assign" method="POST">
            <input type="hidden" name="menu_date" value="<?php echo htmlspecialchars($current_date); ?>">
            <input type="hidden" name="product_id" id="final_product_id">
            <input type="hidden" name="daily_stock" id="final_daily_stock">
            <input type="hidden" name="menu_type" id="final_menu_type">
            
            <button type="button" class="assign-trigger-btn" onclick="openProductPicker()">
                <i class="fas fa-utensils"></i>
                <span>Asignar Plato</span>
                <kbd>F2</kbd>
            </button>
        </form>
    </div>
</div>

<!-- Modal de Búsqueda de Productos -->
<div id="productPickerModal" class="product-picker-modal" onclick="if(event.target === this) closeProductPicker()">
    <div class="modal-search-card">
        <div style="padding: 10px 1.5rem; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold; color: #444;">Asistente de Asignación</span>
            <button type="button" onclick="closeProductPicker()" style="border:none; background:none; cursor:pointer; color:#999; font-size:1.2rem;">&times;</button>
        </div>
        <!-- Paso 1: Selección -->
        <div id="step1" class="modal-step active">
            <span class="step-badge"><i class="fas fa-search"></i> Paso 1: Seleccionar Plato</span>
            <input type="text" id="productSearchInput" class="search-input" placeholder="Buscar por nombre o ID..." autocomplete="off">
            <div class="modal-search-body" id="productSearchResults" style="margin-top: 15px;">
                <!-- Se llena dinámicamente -->
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">↑↓ Navegar • <strong>Enter</strong> Cantidad • <strong>Shift+Enter</strong> Ilimitado</small>
        </div>

        <!-- Paso 2: Cantidad -->
        <div id="step2" class="modal-step">
            <span class="step-badge">Paso 2: Definir Stock</span>
            <h3 id="selectedProductName" style="margin-bottom: 1rem; color: #333;"></h3>
            <div class="form-group">
                <label>Cantidad para hoy (Opcional):</label>
                <input type="number" id="modalQtyInput" class="qty-modal-input" placeholder="Ej: 50 (Vacío = Ilimitado)" min="0">
            </div>
            <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                <button type="button" class="btn" style="flex: 1; font-weight: bold;" onclick="goToStep(3)">Siguiente <i class="fas fa-arrow-right"></i></button>
                <button type="button" class="btn btn-danger" onclick="goToStep(1)">Atrás</button>
            </div>
        </div>

        <!-- Paso 3: Tipo de Menú (Añadimos tabindex para capturar teclado) -->
        <div id="step3" class="modal-step" tabindex="0" style="outline: none;">
            <span class="step-badge">Paso 3: Tipo de Menú</span>
            <div class="menu-type-options">
                <label class="menu-type-option selected" id="labelPrimary" onclick="selectType('primary')">
                    <input type="radio" name="menu_type_select" value="primary" checked onchange="updateTypeUI()">
                    <i class="fas fa-star"></i> <span>(1)</span>
                    <div><h4>Menú Principal</h4><p>Cambia todos los días.</p></div>
                </label>
                <label class="menu-type-option" id="labelSecondary" onclick="selectType('secondary')">
                    <input type="radio" name="menu_type_select" value="secondary" onchange="updateTypeUI()">
                    <i class="fas fa-utensils"></i> <span>(2)</span>
                    <div><h4>Menú Secundario</h4><p>Platos fijos de la semana.</p></div>
                </label>
            </div>
            <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                <button type="button" class="btn" style="flex: 1; font-weight: bold;" onclick="processFinalAssign()">Finalizar Asignación</button>
                <button type="button" class="btn btn-danger" onclick="goToStep(2)">Atrás</button>
            </div>
        </div>
    </div>
</div>

<script>
    const assignedIds = <?php echo json_encode(array_column($assigned_menus, 'product_id')); ?>;
    const allProducts = <?php echo json_encode($available_products); ?>;
    
    // Filtrar solo los que corresponden a Almuerzo y no están asignados
    const filteredProducts = allProducts.filter(p => {
        const isLunch = p.category_name && p.category_name.toLowerCase().includes('almuerzo');
        return isLunch && !assignedIds.includes(p.id);
    });

    let selectedIndex = -1;
    let visibleProducts = [];
    let currentStep = 1;
    let selectedProduct = null;

    function openProductPicker() {
        document.getElementById('productPickerModal').style.display = 'flex';
        setTimeout(() => document.getElementById('productPickerModal').classList.add('active'), 10);
        goToStep(1);
    }

    function closeProductPicker() {
        document.getElementById('productPickerModal').style.display = 'none';
        document.getElementById('productPickerModal').classList.remove('active');
    }

    function goToStep(step) {
        currentStep = step;
        document.getElementById('step1').classList.toggle('active', step === 1);
        document.getElementById('step2').classList.toggle('active', step === 2);
        document.getElementById('step3').classList.toggle('active', step === 3);

        if (step === 1) {
            // Actualizamos la lista de disponibles antes de renderizar (profesionalismo 10/10)
            refreshFilteredProducts();
            const input = document.getElementById('productSearchInput');
            input.value = '';
            input.focus();
            renderProducts('');
        } else if (step === 2) {
            document.getElementById('selectedProductName').innerText = selectedProduct.name;
            const qtyInput = document.getElementById('modalQtyInput');
            qtyInput.value = '';
            qtyInput.focus();
        } else if (step === 3) {
            // Importante: Dar foco al div para que el teclado responda
            setTimeout(() => {
                document.getElementById('step3').focus();
            }, 50);
        }
    }

    /**
     * Recalcula qué productos pueden mostrarse en el buscador según lo asignado actualmente
     */
    function refreshFilteredProducts() {
        visibleProducts = allProducts.filter(p => {
            const isLunch = p.category_name && p.category_name.toLowerCase().includes('almuerzo');
            return isLunch && !assignedIds.includes(parseInt(p.id));
        });
    }

    function selectType(type) {
        const radio = document.querySelector(`input[name="menu_type_select"][value="${type}"]`);
        if (radio) {
            radio.checked = true;
            updateTypeUI();
        }
    }

    function updateTypeUI() {
        const checkedRadio = document.querySelector('input[name="menu_type_select"]:checked');
        if (!checkedRadio) return;
        
        const isPrimary = checkedRadio.value === 'primary';
        document.getElementById('labelPrimary').classList.toggle('selected', isPrimary);
        document.getElementById('labelSecondary').classList.toggle('selected', !isPrimary);
    }

    function renderProducts(filter = '') {
        const container = document.getElementById('productSearchResults');
        filter = filter.toLowerCase();
        
        visibleProducts = filteredProducts.filter(p => 
            p.name.toLowerCase().includes(filter) || p.id.toString().includes(filter)
        );

        selectedIndex = visibleProducts.length > 0 ? 0 : -1;
        
        updateTable();
    }

    function updateTable() {
        const container = document.getElementById('productSearchResults');
        container.innerHTML = visibleProducts.map((p, index) => `
            <div class="product-row ${index === selectedIndex ? 'selected' : ''}" onclick="selectProductByIndex(${index})">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="${p.image ? 'uploads/' + p.image : 'assets/placeholder-food.png'}" 
                         style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                    <span><strong>#${p.id}</strong> ${p.name}</span>
                </div>
                <div style="text-align: right;">
                    <span class="price">Gs. ${new Intl.NumberFormat('es-PY').format(p.product_price || p.price)}</span>
                    <?php /* Badge de categoría si existe */ ?>
                    <div style="font-size: 0.7rem; color: #888; margin-top: 2px;">
                        <i class="fas fa-tag"></i> ${p.category_name}
                    </div>
                </div>
            </div>
        `).join('') || '<div style="padding: 20px; text-align: center; color: #999;">No se encontraron resultados</div>';
        
        // Auto scroll al seleccionado
        const selectedRow = container.querySelector('.selected');
        if (selectedRow) selectedRow.scrollIntoView({ block: 'nearest' });
    }

    function selectProductByIndex(index) {
        selectedProduct = visibleProducts[index];
        if (!selectedProduct) return;
        goToStep(2);
    }

    function processFinalAssign() {
        if (!selectedProduct) return;
        
        const qty = document.getElementById('modalQtyInput').value;
        const typeRadio = document.querySelector('input[name="menu_type_select"]:checked');
        const type = typeRadio ? typeRadio.value : 'primary';
        
        document.getElementById('final_product_id').value = selectedProduct.id;
        document.getElementById('final_daily_stock').value = qty;
        document.getElementById('final_menu_type').value = type;
        document.getElementById('mainAssignForm').submit();
    }

    /**
     * AJAX: Cambiar disponibilidad sin recargar la página
     */
    async function ajaxToggleStatus(id, currentStatus) {
        const row = document.getElementById(`menu-row-${id}`);
        row.classList.add('loading-row');
        
        try {
            const response = await fetch(`?route=menus_toggle_availability&id=${id}&status=${currentStatus}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                const badge = row.querySelector('.status-badge');
                const btn = row.querySelector('.btn-toggle');
                
                const isNowAvailable = data.new_status == 1;
                badge.className = `status-badge badge ${isNowAvailable ? 'badge-success' : 'badge-danger'}`;
                badge.innerText = isNowAvailable ? 'Disponible' : 'Agotado';
                btn.innerText = isNowAvailable ? 'Agotar' : 'Habilitar';
                btn.setAttribute('onclick', `ajaxToggleStatus(${id}, ${data.new_status})`);
                
                Toast.fire("Estado actualizado", "success");
            }
        } catch (e) {
            Toast.fire("Error de conexión", "error");
        } finally {
            row.classList.remove('loading-row');
        }
    }

    /**
     * AJAX: Quitar plato con animación y confirmación
     */
    async function ajaxUnassign(id) {
        const result = await Swal.fire({
            title: '¿Quitar plato?',
            text: "Se eliminará de la lista de hoy.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        });

        if (result.isConfirmed) {
            const row = document.getElementById(`menu-row-${id}`);
            const productId = parseInt(row.getAttribute('data-product-id'));
            const tbody = row.parentElement;
            const card = row.closest('.card');
            
            row.classList.add('loading-row');
            try {
                await fetch(`?route=menus_unassign&id=${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                // Eliminar ID de la lista local para que reaparezca en el buscador
                const index = assignedIds.indexOf(productId);
                if (index > -1) assignedIds.splice(index, 1);

                row.style.transform = 'translateX(20px)';
                row.style.opacity = '0';
                
                setTimeout(() => {
                    row.remove();
                    
                    // Si la tabla se quedó vacía, inyectamos la leyenda
                    if (tbody.querySelectorAll('tr').length === 0) {
                        const isPrimary = card.querySelector('h2').innerText.includes('Principal');
                        const msg = isPrimary ? 'No hay platos principales asignados.' : 'No hay platos secundarios asignados.';
                        
                        card.querySelector('.contenedor-tabla').remove();
                        const p = document.createElement('p');
                        p.style.cssText = "color: #666; font-style: italic;";
                        p.innerText = msg;
                        card.appendChild(p);
                    }
                }, 300);

                Toast.fire("Plato quitado", "success");
            } catch (e) {
                Toast.fire("Error al quitar", "error");
                row.classList.remove('loading-row');
            }
        }
    }

    // Eventos de teclado
    document.getElementById('productSearchInput').addEventListener('input', (e) => renderProducts(e.target.value));
    
    // Prevenir que el Enter en el input de búsqueda haga submit al form de fondo
    document.getElementById('productSearchInput').addEventListener('keydown', (e) => { if(e.key === 'Enter') e.preventDefault(); });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            openProductPicker();
        }

        if (document.getElementById('productPickerModal').style.display === 'flex') {
            if (currentStep === 1) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (selectedIndex < visibleProducts.length - 1) {
                        selectedIndex++;
                        updateTable();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (selectedIndex > 0) {
                        selectedIndex--;
                        updateTable();
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex !== -1) selectProductByIndex(selectedIndex);
                }
            } else if (currentStep === 2) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToStep(3);
                }
            } else if (currentStep === 3) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    processFinalAssign();
                } else if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const current = document.querySelector('input[name="menu_type_select"]:checked').value;
                    selectType(current === 'primary' ? 'secondary' : 'primary');
                } else if (e.key === '1' || e.key === '2') {
                    // Atajos: 1 para Principal, 2 para Secundario
                    selectType(e.key === '1' ? 'primary' : 'secondary');
                }
            }
            
            if (e.key === 'Escape') {
                if (currentStep === 2) {
                    goToStep(1);
                } else {
                    closeProductPicker();
                }
            }
        }
    });
</script>