<style>
    .pos-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 145px);
    }

    /* Panel Izquierdo: Productos */
    .pos-products {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .pos-search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .pos-search-bar input {
        flex: 1;
        padding: 12px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }
    .pos-search-bar input:focus { border-color: #0984e3; background-color: #fff; outline: none; box-shadow: 0 0 0 3px rgba(9, 132, 227, 0.1); }

    .pos-category-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 15px;
        scrollbar-width: none;
    }
    .pos-category-pills::-webkit-scrollbar { display: none; }
    
    .btn-pos-filter {
        padding: 8px 18px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #fff;
        white-space: nowrap;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
    }

    .btn-pos-filter.active {
        background: #2d3436;
        color: white;
        border-color: #2d3436;
    }

    .pos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 15px;
        overflow-y: auto;
        padding-right: 8px;
    }
    .pos-grid::-webkit-scrollbar { width: 5px; }
    .pos-grid::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    .pos-item-card {
        border: 1px solid #f1f5f9;
        padding: 15px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: #fff;
    }
    .pos-item-card:hover { border-color: #0984e3; transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    
    .pos-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; display: block; }
    .pos-item-price { color: #00b894; font-weight: 700; font-size: 0.9rem; }

    /* Acciones de Porción */
    .pos-item-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }
    .btn-portion {
        flex: 1;
        padding: 6px;
        font-size: 0.65rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
        line-height: 1.2;
        transition: all 0.2s;
    }
    .btn-portion:hover { border-color: #0984e3; color: #0984e3; background: #f0f7ff; }
    .btn-portion small { color: #00b894; font-weight: 800; display: block; margin-top: 2px; }

    .btn-show-img {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #f1f5f9;
        border: none;
        border-radius: 4px;
        padding: 5px 7px;
        color: #64748b;
        cursor: pointer;
        font-size: 0.75rem;
    }
    .btn-show-img:hover { background: #e2e8f0; color: #0984e3; }

    /* Panel Derecho: Ticket */
    .pos-ticket {
        width: 400px;
        background: #2d3436;
        color: white;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .ticket-header { 
        border-bottom: 2px dashed rgba(255,255,255,0.1); 
        padding-bottom: 15px; 
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .ticket-header h3 { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.5px; }

    .btn-clear-cart {
        background: none;
        border: 1px solid rgba(255,255,255,0.15);
        color: #ff4757;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-clear-cart:hover { background: #ff4757; color: white; border-color: #ff4757; }

    .ticket-items { flex: 1; overflow-y: auto; margin-bottom: 15px; padding-right: 5px; }
    .ticket-items::-webkit-scrollbar { width: 4px; }
    .ticket-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .ticket-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 0.9rem;
        background: rgba(255,255,255,0.03);
        padding: 8px 12px;
        border-radius: 8px;
    }

    .ticket-item-info { flex: 1; }
    .ticket-item-qty { background: #0984e3; color: white; padding: 2px 8px; border-radius: 4px; margin-right: 10px; font-weight: 700; font-size: 0.8rem; }

    .ticket-footer { border-top: 2px dashed rgba(255,255,255,0.1); padding-top: 20px; }
    .ticket-total { display: flex; justify-content: space-between; font-size: 1.6rem; font-weight: 800; margin-bottom: 20px; color: #00b894; letter-spacing: -1px; }

    .btn-confirm-sale {
        width: 100%;
        padding: 18px;
        background: #00b894;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.2s;
        box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3);
    }
    .btn-confirm-sale:hover { background: #00a887; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 184, 148, 0.4); }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pos-container { flex-direction: column; height: auto; }
        .pos-ticket { width: 100%; height: auto; }
        .pos-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<!-- Vista para el Punto de Venta (POS) -->
<div class="pos-container">

    <!-- Panel de Selección -->
    <div class="pos-products">
        
        <!-- Barra de búsqueda y filtros -->
        <div class="pos-search-bar">
            <input type="text" id="posSearch" placeholder="Buscar plato por nombre..." onkeyup="filterPOS()">
            <button class="btn btn-std" onclick="clearPOS()"><i class="fas fa-sync"></i></button>
        </div>
        
        <!-- Filtros por categoría -->
        <div class="pos-category-pills">
            <button class="btn-pos-filter active" onclick="filterByCat('all', this)">Todos</button>
            <?php foreach($categories as $cat): ?>
                <button class="btn-pos-filter" onclick="filterByCat('<?php echo $cat['id']; ?>', this)">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid de productos -->        
        <div class="pos-grid" id="posGrid">
            <?php foreach($products as $p): 
                $hasHalf = !empty($p['price_half']) && $p['price_half'] > 0;
            ?>
                <!-- Si el producto tiene opción de medio plato, se muestra un diseño diferente con botones para cada porción -->
                <div class="pos-item-card"
                     style="<?php echo $hasHalf ? 'cursor: default;' : ''; ?>"
                     data-name="<?php echo strtolower(htmlspecialchars($p['name'])); ?>"
                     data-cat="<?php echo $p['category_id']; ?>"
                     <?php if(!$hasHalf): ?>
                     onclick="addToTicket(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)">
                     <?php else: ?> >
                     <?php endif; ?>
                    
                    <?php if(!empty($p['image'])): ?>
                        <button class="btn-show-img" onclick="event.stopPropagation(); showProductImg('<?php echo $p['image']; ?>', '<?php echo addslashes($p['name']); ?>')">
                            <i class="fas fa-eye"></i>
                        </button>
                    <?php endif; ?>

                    <span class="pos-item-name"><?php echo htmlspecialchars($p['name']); ?></span>
                    
                    <?php if($hasHalf): ?>
                        <div class="pos-item-actions">
                            <button class="btn-portion" onclick="event.stopPropagation(); addToTicket(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)">Entero <small>Gs. <?php echo number_format($p['price'], 0, ',', '.'); ?></small></button>
                            <button class="btn-portion" onclick="event.stopPropagation(); addToTicket('<?php echo $p['id']; ?>_half', '<?php echo addslashes($p['name']); ?> (Medio)', <?php echo $p['price_half']; ?>)">Medio <small>Gs. <?php echo number_format($p['price_half'], 0, ',', '.'); ?></small></button>
                        </div>
                    <?php else: ?>
                        <span class="pos-item-price">Gs. <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>

    <!-- Panel de Ticket -->
    <div class="pos-ticket">
        <div class="ticket-header">
            <div>
                <h3><i class="fas fa-shopping-cart"></i> Nuevo Pedido</h3>
                <small id="current-time"></small>
            </div>
            <button class="btn-clear-cart" onclick="confirmClearCart()" title="Vaciar Pedido">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>

        <div class="ticket-items" id="ticketItems">
            <div style="text-align: center; color: rgba(255,255,255,0.3); margin-top: 40px;">
                <i class="fas fa-receipt fa-3x"></i>
                <p>Cargue productos para vender</p>
            </div>
        </div>

        <div class="ticket-footer">
            <div style="margin-bottom: 10px;">
                <label style="font-size: 0.75rem; color: #aaa;">Observaciones:</label>
                <input type="text" id="posObservation" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid #444; color: white; padding: 5px; border-radius: 4px;">
            </div>
            <div class="ticket-total">
                <span>TOTAL:</span>
                <span id="posTotal">Gs. 0</span>
            </div>
            <button id="btnSubmitPOS" class="btn-confirm-sale" onclick="submitPOS()">
                CONFIRMAR VENTA <i class="fas fa-check-circle"></i>
            </button>
        </div>
    </div>
</div>

<script>
    let posCart = [];
    const totalEl = document.getElementById('posTotal');
    const itemsEl = document.getElementById('ticketItems');

    function updateTime() {
        document.getElementById('current-time').innerText = new Date().toLocaleString();
    }
    setInterval(updateTime, 1000);

    function showProductImg(img, name) {
        Swal.fire({
            title: name,
            imageUrl: 'uploads/' + img,
            imageWidth: 400,
            imageAlt: name,
            confirmButtonText: 'Cerrar'
        });
    }

    /**
     * Vacía el carrito actual con confirmación de SweetAlert
     */
    async function confirmClearCart() {
        if(posCart.length === 0) return;

        const result = await Swal.fire({
            title: '¿Vaciar pedido?',
            text: "Se eliminarán todos los productos cargados en el ticket actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff4757',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
            Toast.fire("Pedido vaciado", "info");
        }
    }

    function clearPOS() {
        document.getElementById('posSearch').value = '';
        filterByCat('all', document.querySelector('.pos-category-pills button'));
    }

    function filterPOS() {
        const val = document.getElementById('posSearch').value.toLowerCase();
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = card.dataset.name.includes(val) ? 'block' : 'none';
        });
    }

    function filterByCat(catId, btn) {
        document.querySelectorAll('.btn-pos-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = (catId === 'all' || card.dataset.cat === catId) ? 'block' : 'none';
        });
    }

    function addToTicket(id, name, price) {
        const exists = posCart.find(i => i.id === id);
        if(exists) {
            exists.quantity++;
        } else {
            posCart.push({ id, name, price, quantity: 1 });
        }
        renderTicket();
    }

    function removeFromTicket(id) {
        posCart = posCart.filter(i => i.id !== id);
        renderTicket();
    }

    function renderTicket() {
        if(posCart.length === 0) {
            itemsEl.innerHTML = '<div style="text-align: center; color: rgba(255,255,255,0.3); margin-top: 40px;"><i class="fas fa-receipt fa-3x"></i><p>Cargue productos</p></div>';
            totalEl.innerText = "Gs. 0";
            return;
        }

        let html = '';
        let total = 0;
        posCart.forEach(item => {
            total += item.price * item.quantity;
            html += `
                <div class="ticket-item">
                    <div class="ticket-item-info">
                        <span class="ticket-item-qty">${item.quantity}</span> ${item.name}
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>${new Intl.NumberFormat('es-PY').format(item.price * item.quantity)}</span>
                        <button onclick="removeFromTicket(${item.id})" style="background:none; border:none; color:#ff4757; cursor:pointer;"><i class="fas fa-times"></i></button>
                    </div>
                </div>`;
        });
        itemsEl.innerHTML = html;
        totalEl.innerText = "Gs. " + new Intl.NumberFormat('es-PY').format(total);
    }

    async function submitPOS() {
        if(posCart.length === 0) return Toast.fire("Agrega productos al ticket", "warning");

        const observation = document.getElementById('posObservation').value;

        const response = await fetch('?route=pos_store', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart: posCart, observation: observation })
        });

        const res = await response.json();
        if(res.success) {
            Toast.fire(res.message, "success");
            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
        } else {
            Toast.fire(res.message, "error");
        }
    }
</script>
