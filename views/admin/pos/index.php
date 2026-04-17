<!-- Leaflet para el mapa de entrega -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
        scrollbar-width: none;
        flex-shrink: 0;
        padding-bottom: 12px;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        line-height: 1;
        flex-shrink: 0;
    }

    .btn-pos-filter:focus, .btn-pos-filter.active {
        box-shadow: none !important;
        outline: none !important;
    }

    .btn-pos-filter.active {
        background: #2d3436 !important;
        color: white !important;
        border-color: #2d3436 !important;
    }

    .pos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 15px;
        overflow-y: auto;
        padding-right: 8px;
        flex: 1;
        min-height: 0;
        align-items: start; /* Evita que las tarjetas se estiren verticalmente */
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
        display: flex; /* Convierte la tarjeta en un contenedor flex */
        flex-direction: column; /* Apila los elementos internos verticalmente */
        min-height: 120px; /* Altura mínima para mantener consistencia */
    }
    .pos-item-card:hover { border-color: #0984e3; transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    
    .pos-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; display: block; }
    .pos-item-price { color: #00b894; font-weight: 700; font-size: 0.9rem; display: block; } /* Asegura que el precio ocupe su propia línea */

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

    /* Estilos para el mapa en el modal */
    #swal-pos-map {
        height: 180px;
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
        border: 1px solid #ddd;
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
            <button type="button" class="btn btn-pos-filter active" onclick="filterByCat('all', this)">Todos</button>
            <?php foreach($categories as $cat): ?>
                <button type="button" class="btn btn-pos-filter" onclick="filterByCat('<?php echo $cat['id']; ?>', this)">
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
                <span id="posTotal">0</span>
            </div>
            <button id="btnOpenFinalize" class="btn-confirm-sale" onclick="openFinalizeModal()">
                FINALIZAR <small style="font-size: 0.7em; opacity: 0.8;">[F2]</small> <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
</div>

<script>
    let posCart = [];
    let selectedClientId = 1; // 1 = Cliente Ocasional por defecto
    let posDeliveryLat = null;
    let posDeliveryLng = null;
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
            totalEl.innerText = "0";
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
        totalEl.innerText = new Intl.NumberFormat('es-PY').format(total);
    }

    /**
     * Abre el modal de finalización con los campos requeridos
     */
    async function openFinalizeModal(preData = {}) {
        if(posCart.length === 0) return Toast.fire("Agrega productos al ticket", "warning");

        const { 
            clientId = 1, 
            clientName = '', 
            observation = document.getElementById('posObservation').value,
            deliveryType = 'local',
            paymentMethod = 'efectivo'
        } = preData;

        const totalFormatted = totalEl.innerText;

        const { value: formValues } = await Swal.fire({
            title: 'Finalizar Venta',
            html: `
                <div class="text-start" style="font-size: 0.9rem;">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-user"></i> Cliente (Opcional)</label>
                        <div class="input-group">
                            <input type="text" id="swal-client-search" class="form-control form-control-sm" placeholder="Buscar por nombre o tel..." oninput="window.debounceSearchClient()">
                            <button class="btn btn-dark btn-sm" type="button" onclick="searchClientInModal()" title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-success btn-sm" type="button" onclick="quickCreateClient()" title="Nuevo Cliente (F4)">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                        <div id="swal-search-feedback" class="text-muted mt-1" style="font-size: 0.7rem; min-height: 1rem;"></div>
                        <select id="swal-client-id" class="form-select form-select-sm mt-1" onchange="window.loadClientLocations(this.value)">
                            <option value="1">-- Cliente Ocasional --</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fas fa-truck"></i> Entrega</label>
                            <select id="swal-delivery-type" class="form-select form-select-sm" onchange="window.togglePosDeliveryFields(this.value)">
                                <option value="local" ${deliveryType === 'local' ? 'selected' : ''}>Consumo Local</option>
                                <option value="pickup" ${deliveryType === 'pickup' ? 'selected' : ''}>Para Retirar</option>
                                <option value="delivery" ${deliveryType === 'delivery' ? 'selected' : ''}>Envío / Teléfono</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fas fa-wallet"></i> Pago</label>
                            <select id="swal-payment-method" class="form-select form-select-sm">
                                <option value="efectivo" ${paymentMethod === 'efectivo' ? 'selected' : ''}>Efectivo</option>
                                <option value="pos" ${paymentMethod === 'pos' ? 'selected' : ''}>Tarjeta / POS</option>
                                <option value="transferencia" ${paymentMethod === 'transferencia' ? 'selected' : ''}>Transferencia</option>
                            </select>
                        </div>
                    </div>

                    <div id="pos-delivery-extra" style="display: ${deliveryType === 'delivery' ? 'block' : 'none'};">
                        <div id="saved-locations-container" class="mb-2" style="display: none;">
                            <label class="form-label fw-bold small"><i class="fas fa-bookmark"></i> Usar ubicación guardada</label>
                            <select id="swal-location-id" class="form-select form-select-sm" onchange="window.handleSavedLocationSelect(this)">
                                <option value="">-- Seleccionar o ingresar nueva --</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label id="label-manual-location" class="form-label fw-bold small text-primary"><i class="fas fa-map-marker-alt"></i> URL Ubicación Google Maps</label>
                            <input type="text" id="swal-location-url" class="form-control form-control-sm" placeholder="Pegue el link aquí o use el mapa..." oninput="window.processLocationUrl(this.value)">
                        </div>
                        <div id="swal-pos-map"></div>
                        <input type="hidden" id="swal-lat">
                        <input type="hidden" id="swal-lng">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Notas del pedido</label>
                        <textarea id="swal-observation" class="form-control form-control-sm" rows="2">${observation}</textarea>
                    </div>

                    <div class="alert alert-success d-flex justify-content-between align-items-center p-2 mb-0">
                        <span class="fw-bold">TOTAL A COBRAR:</span>
                        <span class="fs-4 fw-bold">Gs. ${totalFormatted}</span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmar Venta <i class="fas fa-check"></i>',
            cancelButtonText: 'Seguir cargando',
            confirmButtonColor: '#00b894',
            focusConfirm: false,
            didOpen: () => {
                const searchInput = document.getElementById('swal-client-search');
                searchInput?.focus();

                // Al presionar Enter, saltar al siguiente campo (Tipo de Entrega)
                searchInput?.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('swal-delivery-type')?.focus();
                    }
                });
                
                // Inicialización con un retraso de seguridad para que el DOM esté 100% listo
                setTimeout(() => {
                    window.initPosMap();
                }, 100);

                // Si venimos de registrar un cliente nuevo, lo inyectamos manualmente en el select
                if (clientId != 1 && clientName) {
                    const select = document.getElementById('swal-client-id');
                    const newOpt = new Option(clientName, clientId, true, true);
                    select.add(newOpt);
                    window.loadClientLocations(clientId);
                }
            },
            preConfirm: () => {
                return {
                    clientId: document.getElementById('swal-client-id').value,
                    deliveryType: document.getElementById('swal-delivery-type').value,
                    paymentMethod: document.getElementById('swal-payment-method').value,
                    observation: document.getElementById('swal-observation').value,
                    locationId: document.getElementById('swal-location-id')?.value || null,
                    lat: document.getElementById('swal-lat')?.value,
                    lng: document.getElementById('swal-lng')?.value
                }
            }
        });

        if (formValues) {
            submitPOS(formValues);
        }
    }

    /**
     * Controla la visibilidad de los campos de ubicación y mapa
     */
    window.togglePosDeliveryFields = function(val) {
        const container = document.getElementById('pos-delivery-extra');
        if(container) {
            const isDelivery = (val === 'delivery');
            container.style.display = isDelivery ? 'block' : 'none';
            if(isDelivery) {
                setTimeout(() => {
                    if(!window.posMap) window.initPosMap();
                    if(window.posMap && typeof window.posMap.invalidateSize === 'function') window.posMap.invalidateSize();
                }, 300);
            }
        }
    }

    /**
     * Carga las ubicaciones del cliente seleccionado en el POS
     */
    window.loadClientLocations = async function(clientId) {
        const container = document.getElementById('saved-locations-container');
        const select = document.getElementById('swal-location-id');
        if(!select) return;

        if(clientId == 1) {
            container.style.display = 'none';
            select.innerHTML = '<option value="">-- Seleccionar --</option>';
            return;
        }

        try {
            const resp = await fetch(`?route=admin_client_locations&id=${clientId}`);
            const locations = await resp.json();
            
            if(locations.length > 0) {
                container.style.display = 'block';
                select.innerHTML = '<option value="">-- Usar Nueva Ubicación --</option>';
                locations.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.text = `${loc.title} (${loc.address})`;
                    opt.dataset.lat = loc.lat;
                    opt.dataset.lng = loc.lng;
                    opt.dataset.address = loc.address;
                    select.appendChild(opt);
                });
            } else {
                container.style.display = 'none';
            }
        } catch (e) { console.error("Error cargando ubicaciones:", e); }
    }

    /**
     * Maneja la selección de una ubicación guardada
     */
    window.handleSavedLocationSelect = function(select) {
        const opt = select.options[select.selectedIndex];
        if(opt.value) {
            window.updatePosMapMarker(parseFloat(opt.dataset.lat), parseFloat(opt.dataset.lng));
            document.getElementById('swal-location-url').value = opt.dataset.address;
            document.getElementById('label-manual-location').innerHTML = '<i class="fas fa-check-circle text-success"></i> Dirección seleccionada';
        } else {
            document.getElementById('swal-location-url').value = '';
            document.getElementById('label-manual-location').innerHTML = '<i class="fas fa-map-marker-alt"></i> URL Ubicación Google Maps';
        }
    }

    /**
     * Inicializa el mapa de Leaflet dentro del modal
     */
    window.posMap = null;
    window.posMarker = null;

    window.initPosMap = function() {
        const mapContainer = document.getElementById('swal-pos-map');
        if (!mapContainer || mapContainer.offsetHeight === 0) return;

        if (window.posMap) {
            try {
                window.posMap.off();
                window.posMap.remove();
            } catch(e) { console.error("Error al limpiar mapa:", e); }
        }

        window.posMap = L.map(mapContainer).setView([-25.3006, -57.6359], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(window.posMap);

        // Forzamos el renderizado de los cuadros (tiles)
        setTimeout(() => { if(window.posMap) window.posMap.invalidateSize(); }, 400);
    }

    /**
     * Extrae coordenadas de una URL de Google Maps y actualiza el mapa
     */
    window.processLocationUrl = async function(url) {
        if (!url || url.trim().length < 10) return;
        
        // Regex flexible para capturar coordenadas
        const regex = /(?:@|query=|!3d)(-?\d+\.\d+)(?:,|!4d)(-?\d+\.\d+)/;
        const match = url.match(regex);

        if (match) {
            window.updatePosMapMarker(parseFloat(match[1]), parseFloat(match[2]));
        } else if (url.includes('goo.gl') || url.includes('maps.app.goo.gl')) {
            // Resolver link corto vía backend
            try {
                const feedback = document.getElementById('swal-search-feedback');
                if(feedback) feedback.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resolviendo ubicación...';
                
                const resp = await fetch(`?route=admin_resolve_map_url&url=${encodeURIComponent(url)}`);
                const res = await resp.json();
                if (res.success) {
                    console.log("Servidor resolvió coordenadas:", res.lat, res.lng);
                    window.updatePosMapMarker(parseFloat(res.lat), parseFloat(res.lng));
                } else {
                    console.error("Servidor no pudo resolver el link:", res.message);
                    if(feedback) feedback.innerText = "⚠️ Link inválido";
                }
            } catch (e) {
                console.error("Error al resolver URL:", e);
            }
        }
    }

    window.updatePosMapMarker = function(lat, lng) {
        const latInput = document.getElementById('swal-lat');
        const lngInput = document.getElementById('swal-lng');
        
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;

        if (window.posMap && !isNaN(lat) && !isNaN(lng)) {
            // Asegurar que el contenedor es visible antes de centrar
            const container = document.getElementById('pos-delivery-extra');
            if(container) container.style.display = 'block';
            
            if(typeof window.posMap.invalidateSize === 'function') window.posMap.invalidateSize();
            window.posMap.setView([lat, lng], 16);
            if (window.posMarker) window.posMap.removeLayer(window.posMarker);
            window.posMarker = L.marker([lat, lng]).addTo(window.posMap);
            Toast.fire("Ubicación extraída correctamente", "success");
        }
    }

    /**
     * Debounce para búsqueda de clientes
     */
    let searchTimeout;
    window.debounceSearchClient = function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchClientInModal();
        }, 400); // 400ms de espera tras dejar de escribir
    };

    /**
     * Función auxiliar para buscar clientes registrados dentro del modal
     */
    window.searchClientInModal = async function() {
        const term = document.getElementById('swal-client-search').value;
        const feedback = document.getElementById('swal-search-feedback');
        
        if(term.length < 3) {
            if(feedback) feedback.innerText = term.length > 0 ? "Escribe al menos 3 caracteres..." : "";
            return;
        }

        if(feedback) feedback.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';

        try {
            const resp = await fetch(`?route=admin_clients_search&term=${term}`);
            const clients = await resp.json();
            const select = document.getElementById('swal-client-id');
            
            // Mantener la opción por defecto y agregar resultados
            select.innerHTML = '<option value="1">-- Cliente Ocasional --</option>';
            clients.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.name} (${c.phone || 'Sin tel'})</option>`;
            });
            
            if(clients.length > 0) {
                select.selectedIndex = 1;
                if(feedback) feedback.innerText = `✅ ${clients.length} resultados encontrados`;
            } else {
                if(feedback) feedback.innerText = "❌ No se encontraron coincidencias";
            }
        } catch(e) { 
            console.error(e); 
            if(feedback) feedback.innerText = "⚠️ Error en la búsqueda";
        }
    }

    /**
     * Abre un sub-modal para registrar un cliente sin perder el progreso del POS
     */
    window.quickCreateClient = async function() {
        // Capturar estado actual de los inputs antes de que el modal se cierre
        const currentObs = document.getElementById('swal-observation')?.value || '';
        const currentDelivery = document.getElementById('swal-delivery-type')?.value || 'local';
        const currentPayment = document.getElementById('swal-payment-method')?.value || 'efectivo';

        const result = await Swal.fire({
            title: 'Nuevo Cliente',
            html: `
                <div class="text-start">
                    <label class="form-label small fw-bold">Nombre Completo</label>
                    <input id="q-name" class="form-control mb-2" placeholder="Ej: Juan Pérez">
                    
                    <label class="form-label small fw-bold">Teléfono / WhatsApp</label>
                    <input id="q-phone" class="form-control mb-1" placeholder="Ej: 0981222333">
                    <div id="phone-feedback" class="small mb-2" style="display:none;"></div>

                    <label class="form-label small fw-bold">Email (Opcional)</label>
                    <input id="q-email" class="form-control" placeholder="cliente@correo.com">
                </div>
            `,
            focusConfirm: false,
            didOpen: () => {
                const nameInput = document.getElementById('q-name');
                const phoneInput = document.getElementById('q-phone');
                const emailInput = document.getElementById('q-email');
                const feedback = document.getElementById('phone-feedback');

                nameInput?.focus();

                // Flujo de navegación con Enter
                nameInput?.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') { e.preventDefault(); phoneInput?.focus(); }
                });

                phoneInput?.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') { e.preventDefault(); emailInput?.focus(); }
                });

                emailInput?.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') { e.preventDefault(); Swal.clickConfirm(); }
                });
                
                phoneInput?.addEventListener('input', async (e) => {
                    const phone = e.target.value;
                    if (phone.length >= 6) {
                        const resp = await fetch(`?route=admin_clients_check_phone&phone=${phone}`);
                        const res = await resp.json();
                        
                        if (res.exists) {
                            phoneInput.classList.add('is-invalid');
                            phoneInput.classList.remove('is-valid');
                            feedback.style.display = 'block';
                            feedback.style.color = '#dc3545';
                            feedback.innerText = '⚠️ Este teléfono ya está registrado';
                        } else {
                            phoneInput.classList.remove('is-invalid');
                            phoneInput.classList.add('is-valid');
                            feedback.style.display = 'block';
                            feedback.style.color = '#198754';
                            feedback.innerText = '✅ Teléfono disponible';
                        }
                    }
                });
            },
            showCancelButton: true,
            confirmButtonText: 'Registrar y Seleccionar',
            preConfirm: () => {
                return {
                    name: document.getElementById('q-name').value,
                    phone: document.getElementById('q-phone').value,
                    email: document.getElementById('q-email').value
                }
            }
        });

        if (result.isConfirmed) {
            const formValues = result.value;
            try {
                const resp = await fetch('?route=admin_clients_store_api', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formValues)
                });
                const res = await resp.json();
                
                if(res.success) {
                    Toast.fire("Cliente registrado", "success");
                    // Relanzamos el modal de finalizar inyectando el nuevo cliente y preservando lo escrito
                    openFinalizeModal({
                        clientId: res.id,
                        clientName: `${res.name} (${formValues.phone})`,
                        observation: currentObs,
                        deliveryType: currentDelivery,
                        paymentMethod: currentPayment
                    });
                } else {
                    Swal.fire("Error", res.message, "error");
                }
            } catch(e) { console.error(e); }
        } else {
            // Si se cancela o presiona Esc, volvemos al modal de Finalizar preservando los datos
            openFinalizeModal({
                observation: currentObs,
                deliveryType: currentDelivery,
                paymentMethod: currentPayment
            });
        }
    }

    async function submitPOS(data) {
        const response = await fetch('?route=pos_store', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                cart: posCart, 
                client_id: data.clientId,
                location_id: data.locationId,
                delivery_type: data.deliveryType,
                payment_method: data.paymentMethod,
                observation: data.observation,
                lat: data.lat,
                lng: data.lng,
                delivery_address: document.getElementById('swal-location-url')?.value
            })
        });

        const res = await response.json();
        if(res.success) {
            Toast.fire(res.message, "success");
            // Impresión automática de la comanda
            printOrderDirectly(res.order_id, '80mm');
            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
        } else {
            Toast.fire(res.message, "error");
        }
    }

    /**
     * Atajo de teclado F2 para abrir el modal de finalización rápidamente
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            openFinalizeModal();
        }
        // F4 para Registrar Nuevo Cliente
        if (e.key === 'F4' || e.code === 'F4') {
            e.preventDefault();
            quickCreateClient();
        }
    }, true); // Usamos capture para asegurar que el evento se detecte antes de que el modal lo bloquee

    // Autofocus en el buscador al cargar la vista
    document.getElementById('posSearch').focus();
</script>
