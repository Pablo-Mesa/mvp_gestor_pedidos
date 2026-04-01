<style>
    .pos-container {
        display: flex;
        gap: 15px;
        height: calc(100vh - 160px);
        margin-top: -10px;
    }

    /* Panel Izquierdo: Productos */
    .pos-products {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .pos-search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .pos-search-bar input {
        flex: 1;
        padding: 12px;
        border: 2px solid #eee;
        border-radius: 8px;
        font-size: 1rem;
    }

    .pos-category-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .pos-category-pills button {
        padding: 6px 15px;
        border-radius: 20px;
        border: 1px solid #ddd;
        background: white;
        white-space: nowrap;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .pos-category-pills button.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 10px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .pos-item-card {
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
        position: relative;
    }

    .pos-item-card:hover { border-color: #007bff; background: #f8f9ff; }
    .pos-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; display: block; }
    .pos-item-price { color: #28a745; font-weight: bold; font-size: 0.85rem; }

    /* Acciones de Porción */
    .pos-item-actions {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }
    .btn-portion {
        flex: 1;
        padding: 4px;
        font-size: 0.7rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #f8f9fa;
        cursor: pointer;
        line-height: 1.2;
    }
    .btn-portion:hover { background: #e9ecef; border-color: #007bff; color: #007bff; }
    .btn-portion small { color: #28a745; font-weight: bold; display: block; }

    .btn-show-img {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.05);
        border: none;
        border-radius: 4px;
        padding: 4px 6px;
        color: #666;
        cursor: pointer;
    }

    .btn-show-img:hover { background: #eee; color: #007bff; }

    /* Panel Derecho: Ticket */
    .pos-ticket {
        width: 380px;
        background: #343a40;
        color: white;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .ticket-header { 
        border-bottom: 1px dashed rgba(255,255,255,0.2); 
        padding-bottom: 10px; 
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .btn-clear-cart {
        background: none;
        border: 1px solid rgba(255,255,255,0.2);
        color: #ff4757;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-clear-cart:hover {
        background: #ff4757;
        color: white;
        border-color: #ff4757;
    }

    .ticket-items { flex: 1; overflow-y: auto; margin-bottom: 15px; }

    .ticket-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .ticket-item-info { flex: 1; }
    .ticket-item-qty { background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px; margin-right: 8px; }

    .ticket-footer { border-top: 2px solid rgba(255,255,255,0.1); padding-top: 15px; }
    .ticket-total { display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: bold; margin-bottom: 15px; color: #2ecc71; }

    .btn-confirm-sale {
        width: 100%;
        padding: 15px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-confirm-sale:hover { background: #218838; transform: scale(1.02); }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pos-container { flex-direction: column; height: auto; }
        .pos-ticket { width: 100%; height: auto; }
        .pos-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="pos-container">
    <!-- Panel de Selección -->
    <div class="pos-products">
        <div class="pos-search-bar">
            <input type="text" id="posSearch" placeholder="Buscar plato por nombre..." onkeyup="filterPOS()">
            <button class="btn btn-std" onclick="clearPOS()"><i class="fas fa-sync"></i></button>
        </div>

        <div class="pos-category-pills">
            <button class="active" onclick="filterByCat('all', this)">Todos</button>
            <?php foreach($categories as $cat): ?>
                <button onclick="filterByCat('<?php echo $cat['id']; ?>', this)">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="pos-grid" id="posGrid">
            <?php foreach($products as $p): 
                $hasHalf = !empty($p['price_half']) && $p['price_half'] > 0;
            ?>
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
            <button class="btn-confirm-sale" onclick="submitPOS()">
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
        document.querySelectorAll('.pos-category-pills button').forEach(b => b.classList.remove('active'));
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
