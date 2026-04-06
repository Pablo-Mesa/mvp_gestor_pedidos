<style>
    .menu-manager-grid { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: flex-start; }
    .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .card h2 { margin-top: 0; margin-bottom: 1rem; font-size: 1.2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
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

    .date-selector { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }

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
    <div class="card">
        <h2>Menú para el <?php echo date("d/m/Y", strtotime($current_date)); ?></h2>
        <?php if (empty($assigned_menus)): ?>
            <p>No hay menús asignados para esta fecha.</p>
        <?php else: ?>
            <div class="contenedor-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="width: 120px;">Stock Diario</th>
                            <th style="width: 120px;">Disponibilidad</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($assigned_menus as $menu): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($menu['product_name']); ?>
                                    <small style="display: block; color: #6c757d;">Gs. <?php echo number_format($menu['product_price'], 0); ?></small>
                                </td>
                                <td><?php echo $menu['daily_stock'] !== null ? htmlspecialchars($menu['daily_stock']) : 'Ilimitado'; ?></td>
                                <td>
                                    <?php if ($menu['is_available']): ?>
                                        <span class="badge badge-success">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Agotado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-wrapper">
                                        <!-- Se añade un min-width para evitar que el botón cambie de tamaño según el texto -->
                                        <a href="?route=menus_toggle_availability&id=<?php echo $menu['id']; ?>&date=<?php echo $current_date; ?>&status=<?php echo $menu['is_available']; ?>" 
                                           class="btn btn-sm" 
                                           style="background-color: #ffc107; color: #212529; min-width: 85px;">
                                            <?php echo $menu['is_available'] ? 'Agotar' : 'Habilitar'; ?>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm" 
                                                onclick="confirmAction('?route=menus_unassign&id=<?php echo $menu['id']; ?>&date=<?php echo $current_date; ?>', {
                                                    title: '¿Quitar plato?',
                                                    message: 'Vas a quitar este plato del menú del día.',
                                                    btnText: 'Sí, quitar'
                                                })">
                                            Quitar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Asignar Nuevo Menú -->
    <div class="card">
        <h2>Asignar Plato</h2>
        <form id="mainAssignForm" action="?route=menus_assign" method="POST">
            <input type="hidden" name="menu_date" value="<?php echo htmlspecialchars($current_date); ?>">
            <input type="hidden" name="product_id" id="final_product_id">
            <input type="hidden" name="daily_stock" id="final_daily_stock">
            
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
                <button type="button" class="btn" style="flex: 1; font-weight: bold;" onclick="processFinalAssign()">Confirmar Asignación</button>
                <button type="button" class="btn btn-danger" onclick="goToStep(1)">Atrás</button>
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

        if (step === 1) {
            const input = document.getElementById('productSearchInput');
            input.value = '';
            input.focus();
            renderProducts('');
        } else {
            document.getElementById('selectedProductName').innerText = selectedProduct.name;
            const qtyInput = document.getElementById('modalQtyInput');
            qtyInput.value = '';
            qtyInput.focus();
        }
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
                <span><strong>#${p.id}</strong> ${p.name}</span>
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

    function processFinalAssign(directProduct = null) {
        const targetProduct = directProduct || selectedProduct;
        const qty = directProduct ? '' : document.getElementById('modalQtyInput').value;
        
        document.getElementById('final_product_id').value = targetProduct.id;
        document.getElementById('final_daily_stock').value = qty;
        document.getElementById('mainAssignForm').submit();
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
                    if (selectedIndex !== -1) {
                        // Si presiona Shift + Enter, asignación directa ilimitada
                        if (e.shiftKey) {
                            processFinalAssign(visibleProducts[selectedIndex]);
                        } else {
                            selectProductByIndex(selectedIndex);
                        }
                    }
                }
            } else if (currentStep === 2) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    processFinalAssign();
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