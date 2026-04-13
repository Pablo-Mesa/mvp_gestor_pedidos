<style>
    /* Layout General */
    .checkout-wrapper { max-width: 1000px; margin: 0 auto; padding: 1rem; }
    .page-title { margin-bottom: 1.5rem; color: #333; font-size: 1.8rem; }
    
    .checkout-grid { display: grid; grid-template-columns: 1fr 350px; gap: 2rem; }
    
    @media (max-width: 768px) {
        .checkout-grid { grid-template-columns: 1fr; }
        .checkout-wrapper { padding: 0.5rem; }
    }

    /* Tarjetas de Sección */
    .section-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 1.5rem; border: 1px solid #eee; }
    .section-card h3 { margin-bottom: 1rem; color: #444; font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
    .hint-text { font-size: 0.85rem; color: #666; margin-bottom: 0.5rem; }

    /* Grid de Direcciones */
    .locations-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 1rem; }
    .location-card { 
        border: 1px solid #eee; padding: 12px; border-radius: 10px; cursor: pointer; 
        display: flex; flex-direction: column; gap: 4px; transition: 0.2s; font-size: 0.85rem;
    }
    .location-card i { font-size: 1.2rem; color: #aaa; margin-bottom: 5px; }
    .location-card.selected { border-color: #28a745; background: #f0fdf4; border-width: 2px; }
    .btn-add-location { background: #007bff; color: white; border: none; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; }
    
    .btn-delete-checkout {
        position: absolute; top: 5px; right: 5px; background: transparent; border: none;
        color: #ff4757; cursor: pointer; opacity: 0; transition: 0.3s; padding: 5px;
    }
    .location-card:hover .btn-delete-checkout { opacity: 1; }

    .btn-edit-inline { 
        position: absolute; top: 5px; right: 5px; background: #f8f9fa; border: 1px solid #ddd; 
        width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; 
        justify-content: center; font-size: 0.7rem; color: #666; cursor: pointer; transition: 0.2s;
    }
    .btn-edit-inline:hover { background: #e9ecef; color: #007bff; border-color: #007bff; }
    .location-card { position: relative; }

    /* Radio Buttons Visuales */
    .delivery-options, .payment-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; }
    .radio-card input { display: none; } /* Ocultar radio real */
    .card-content { 
        border: 2px solid #eee; border-radius: 8px; padding: 1rem; 
        text-align: center; cursor: pointer; transition: 0.2s;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: #666;
    }
    .card-content i { font-size: 1.5rem; }
    /* Estado seleccionado */
    .radio-card input:checked + .card-content { border-color: #28a745; background-color: #f0fdf4; color: #28a745; font-weight: bold; }

    /* Mapa */
    .map-container { height: 300px; width: 100%; border-radius: 8px; z-index: 1; }

    /* Inputs */
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; outline: none; }
    .form-control:focus { border-color: #007bff; }
    .mt-3 { margin-top: 1rem; }

    /* Resumen */
    .summary-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); position: sticky; top: 100px; }
    .summary-list { max-height: 300px; overflow-y: auto; margin-bottom: 1rem; border-bottom: 2px dashed #eee; }
    .summary-item { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.9rem; }
    .summary-total { display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: bold; margin-bottom: 1.5rem; color: #333; }
    
    .btn-confirm { width: 100%; padding: 14px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: 0.2s; }
    .btn-confirm:hover { background: #218838; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(40,167,69,0.3); }
    
    .btn-back { 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 8px; 
        margin-top: 1.2rem; 
        color: #636e72; 
        text-decoration: none; 
        font-size: 0.95rem; 
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-back:hover { color: #2d3436; }
    .btn-back i { transition: transform 0.3s ease; }
    .btn-back:hover i { transform: translateX(-5px); }
</style>

<!-- Leaflet CSS & JS (Mapas OpenSource) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="checkout-wrapper">
    <h1 class="page-title"><i class="fas fa-cash-register"></i> Finalizar Pedido</h1>

    <div class="checkout-grid">
        
        <!-- Columna Izquierda: Datos del Pedido -->
        <div class="checkout-form-col">
            <form id="orderForm">
                
                <!-- 1. Tipo de Entrega -->
                <div class="section-card">
                    <h3><i class="fas fa-truck"></i> Tipo de Entrega</h3>
                    <div class="delivery-options">
                        <label class="radio-card">
                            <input type="radio" name="delivery_type" value="pickup" checked onchange="toggleMap(false)">
                            <div class="card-content">
                                <i class="fas fa-walking"></i>
                                <span>Pasar a buscar</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="delivery_type" value="delivery" onchange="toggleMap(true)">
                            <div class="card-content">
                                <i class="fas fa-motorcycle"></i>
                                <span>Delivery</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="delivery_type" value="local" onchange="toggleMap(false)">
                            <div class="card-content">
                                <i class="fas fa-utensils"></i>
                                <span>Comer aquí</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Ubicación (Solo Delivery) -->
                <div id="deliverySection" class="section-card" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="margin:0;"><i class="fas fa-map-marked-alt"></i> Mis Direcciones</h3>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-add-location" style="background: #ffc107; color: #212529;" onclick="editSelectedLocation()">
                                <i class="fas fa-pen"></i> Editar
                            </button>
                            <button type="button" class="btn-add-location" onclick="openLocationModal()">
                                <i class="fas fa-plus"></i> Nueva
                            </button>
                        </div>
                    </div>
                    
                    <div id="locationsList" class="locations-grid">
                        <?php if(empty($savedLocations)): ?>
                            <p class="empty-msg">No tienes direcciones guardadas.</p>
                        <?php else: ?>
                            <?php $firstLocationSelected = false; ?>
                            <?php foreach($savedLocations as $loc): ?>
                                <div class="location-card <?php if(!$firstLocationSelected) { echo 'selected'; $firstLocationSelected = true; } ?>" onclick="selectLocation(this)"
                                     data-id="<?= $loc['id'] ?>"
                                     data-lat="<?= $loc['lat'] ?>" data-lng="<?= $loc['lng'] ?>" data-addr="<?= htmlspecialchars($loc['address']) ?>">
                                    <i class="fas fa-home"></i>
                                    <strong><?= htmlspecialchars($loc['title']) ?></strong>
                                    <small><?= htmlspecialchars($loc['address']) ?></small>
                                    <button type="button" class="btn-delete-checkout" 
                                            onclick="event.stopPropagation(); confirmDeleteLocation(<?= $loc['id'] ?>, '<?= addslashes($loc['title']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Inputs Ocultos para Lat/Lng -->
                    <input type="hidden" name="delivery_lat" id="lat">
                    <input type="hidden" name="delivery_lng" id="lng">
                    <input type="hidden" name="delivery_address" id="selected_address">
                </div>

                <!-- 3. Método de Pago -->
                <div class="section-card">
                    <h3><i class="fas fa-wallet"></i> Método de Pago</h3>
                    <div class="payment-options">
                        <label class="radio-card">
                            <input type="radio" name="payment_method" value="efectivo" checked>
                            <div class="card-content">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Efectivo</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="payment_method" value="pos">
                            <div class="card-content">
                                <i class="fas fa-credit-card"></i>
                                <span>Pos</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="payment_method" value="transferencia">
                            <div class="card-content">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Transferencia</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 4. Observaciones (Nuevo) -->
                <div class="section-card">
                    <h3><i class="fas fa-comment-alt"></i> Observaciones</h3>
                    <textarea name="observation" class="form-control" rows="2" placeholder="Ej: Sin cebolla, extra servilletas, el timbre no funciona..."></textarea>
                </div>

            </form>
        </div>

        <!-- Columna Derecha: Resumen -->
        <div class="checkout-summary-col">
            <div class="summary-card">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1rem; width: 100%;">
                    <i class="fas fa-receipt"></i><h3>Resumen</h3>
                </div>
                <div id="checkout-items" class="summary-list">
                    <!-- Items inyectados por JS -->
                </div>
                
                <div id="delivery-cost-row" class="summary-item" style="display: none; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <span>Costo de Envío (Est.)</span>
                    <span id="checkout-delivery-price">Gs. 0</span>
                </div>

                <div class="summary-total">
                    <span>Total a Pagar</span>
                    <span id="checkout-total">Gs. 0</span>
                </div>

                <button type="button" class="btn-confirm" onclick="submitOrder()">
                    Confirmar Pedido <i class="fas fa-check-circle"></i>
                </button>
                <a href="?route=home" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Seguir comprando
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Ubicación (Flujo profesional) -->
<div id="locationModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="max-width: 600px;">
        <div class="modal-tabs">
            <button class="tab-btn active" id="modalTitle">Guardar Nueva Ubicación</button>
        </div>
        <div class="modal-content">
            <input type="hidden" id="edit_loc_id" value="">
            <div class="input-group">
                <label>Título (Ej: Mi Casa, Oficina)</label>
                <input type="text" id="new_loc_title" placeholder="Casa, Trabajo..." class="form-control">
            </div>
            
            <div id="mapSection">
                <div class="hint-text mt-3">Ubica el marcador exactamente sobre el lugar de entrega:</div>
                <div id="map" class="map-container" style="height: 250px;"></div>
            </div>
            
            <div class="input-group mt-3">
                <label id="addrLabel">Referencia Detallada (Opcional)</label>
                <textarea id="new_loc_addr" class="form-control" rows="2" placeholder="Nro de casa, color de reja, etc."></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-main" style="flex:1; background:#6c757d;" onclick="closeLocationModal()">Cancelar</button>
                <button type="button" class="btn-main" style="flex:2;" onclick="saveNewLocation()">Guardar Dirección</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuración del local y tarifas cargadas desde el sistema
    const storeConfig = {
        lat: <?= $siteSettings['store_lat'] ?? -25.3006 ?>,
        lng: <?= $siteSettings['store_lng'] ?? -57.6359 ?>,
        rates: <?= $siteSettings['delivery_rates_json'] ?? '[]' ?>
    };

    /**
     * Calcula la distancia en KM entre dos puntos (Fórmula de Haversine)
     */
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radio de la tierra en km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // --- 1. Lógica del Mapa (Leaflet) ---
    let map, marker;

    function initMap() {
        // Coordenadas iniciales (Ej: Centro de Asunción o genérico)
        // Si tienes las coordenadas de tu local, ponlas aquí.
        const initialLat = -25.2865; 
        const initialLng = -57.6470; 

        // Inicializar mapa
        map = L.map('map').setView([initialLat, initialLng], 13);

        // Cargar tiles (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Evento de clic en el mapa
        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });

        // Intentar obtener ubicación del usuario al cargar (opcional)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                map.setView([userLat, userLng], 15);
                // No ponemos marcador automáticamente para no ser invasivos, 
                // dejamos que el usuario toque.
            });
        }
    }

    function updateMarker(lat, lng) {
        // Si ya existe marcador, lo movemos
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            // Si no, creamos uno nuevo
            marker = L.marker([lat, lng]).addTo(map);
        }
        
        // Actualizar inputs ocultos
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    }

    function toggleMap(show) {
        const container = document.getElementById('deliverySection');
        container.style.display = show ? 'block' : 'none';
        // Desactivamos los inputs si no es delivery para que no se envíen en el form
        document.getElementById('lat').disabled = !show;
        document.getElementById('lng').disabled = !show;
        document.getElementById('selected_address').disabled = !show;
        loadCheckoutItems(); // Recalcular subtotales al cambiar tipo de entrega
    }

    function openLocationModal(isEdit = false) {
        document.getElementById('locationModal').style.display = 'flex';

        if (isEdit) {
            document.getElementById('modalTitle').innerText = 'Editar Ubicación';
            if(document.getElementById('mapSection')) {
                document.getElementById('mapSection').style.display = 'none';
            }
            if(document.getElementById('addrLabel')) {
                document.getElementById('addrLabel').innerText = 'Dirección / Referencia';
            }
        } else {
            document.getElementById('modalTitle').innerText = 'Guardar Nueva Ubicación';
            if(document.getElementById('mapSection')) document.getElementById('mapSection').style.display = 'block';
            if(document.getElementById('addrLabel')) document.getElementById('addrLabel').innerText = 'Referencia Detallada (Opcional)';
            
            // El mapa necesita un pequeño delay para cargar correctamente
            setTimeout(() => {  
                if(!map) initMap(); 
                else map.invalidateSize(); 
            }, 300);

            // Limpiar campos para nueva ubicación
            document.getElementById('edit_loc_id').value = '';
            document.getElementById('new_loc_title').value = '';
            document.getElementById('new_loc_addr').value = '';
            // Limpiar inputs ocultos de lat/lng para nueva ubicación
            document.getElementById('lat').value = '';
            document.getElementById('lng').value = '';

            if (marker) { // Eliminar marcador si se abre para una nueva ubicación
                map.removeLayer(marker);
                marker = null;
            }
            // Restablecer la vista del mapa a la ubicación inicial o actual del usuario si está disponible
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    map.setView([position.coords.latitude, position.coords.longitude], 15);
                });
            } else {
                map.setView([-25.2865, -57.6470], 13); // Vista predeterminada
            }
        }
    }

    function closeLocationModal() {
        document.getElementById('locationModal').style.display = 'none';
        
        // Si cerramos el modal, nos aseguramos de que el formulario principal 
        // mantenga la dirección que el usuario tiene seleccionada en el grid.
        const selected = document.querySelector('.location-card.selected');
        if (selected) selectLocation(selected, true);
    }

    function editSelectedLocation() {
        const selected = document.querySelector('.location-card.selected');
        if (!selected) {
            Toast.fire("Selecciona una dirección para editar", "info");
            return;
        }
        editLocation(
            selected.dataset.id,
            selected.querySelector('strong').innerText,
            selected.dataset.addr,
            selected.dataset.lat,
            selected.dataset.lng
        );
    }

    function selectLocation(card, silent = false) {
        if (!card) return;
        
        document.querySelectorAll('.location-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        
        document.getElementById('lat').value = card.dataset.lat;
        document.getElementById('lng').value = card.dataset.lng;
        document.getElementById('selected_address').value = card.dataset.addr;
        
        if (!silent) Toast.fire("Dirección seleccionada", "success");
        loadCheckoutItems(); // Recalcular total con delivery
    }

    async function confirmDeleteLocation(id, title) {
        const { isConfirmed } = await Swal.fire({
            title: `¿Eliminar "${title}"?`,
            text: "Se quitará de tus direcciones guardadas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, eliminar'
        });

        if (isConfirmed) {
            try {
                const response = await fetch('?route=delete_location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();
                if (result.success) {
                    Toast.fire("Dirección eliminada", "success");
                    renderLocations(result.locations);
                }
            } catch (err) {
                Toast.fire("Error al conectar con el servidor", "error");
            }
        }
    }

    /**
     * Función para editar una ubicación existente
     */
    function editLocation(id, title, address, lat, lng) {
        // Rellenar los campos del formulario
        document.getElementById('edit_loc_id').value = id;
        document.getElementById('new_loc_title').value = title;
        document.getElementById('new_loc_addr').value = address;

        // Preparar el mapa por si el usuario cambia a "Nueva" estando en el modal
        if (!map) initMap(); // Asegurarse de que el mapa esté inicializado
        map.setView([lat, lng], 15); // Centrar el mapa en la ubicación
        updateMarker(lat, lng); // Colocar el marcador

        openLocationModal(true); // Abrir el modal en modo edición
    }

    async function saveNewLocation() {
        const titleEl = document.getElementById('new_loc_title');
        const addrEl = document.getElementById('new_loc_addr');
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        const editId = document.getElementById('edit_loc_id').value;

        const data = {
            id: editId,
            title: titleEl.value,
            address: addrEl.value,
            lat: latEl.value,
            lng: lngEl.value
        };

        if(!data.title || !data.lat) { 
            Toast.fire("Título y ubicación en mapa son obligatorios", "error"); 
            return; 
        }

        try {
            const route = editId ? 'update_location' : 'save_location';
            const resp = await fetch(`?route=${route}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const textResponse = await resp.text(); // Leemos como texto primero para debug
            let res;
            try {
                res = JSON.parse(textResponse);
            } catch (err) {
                console.error("Respuesta no JSON del servidor:", textResponse);
                throw new Error("El servidor no respondió en formato JSON");
            }

            if(res && res.success) {
                // Limpiar formulario y cerrar modal
                titleEl.value = '';
                addrEl.value = '';
                document.getElementById('edit_loc_id').value = ''; // Limpiar el ID de edición
                closeLocationModal();
                
                // Re-renderizar la lista después de cerrar el modal para evitar saltos visuales
                renderLocations(res.locations, data);
            } else {
                Toast.fire(res.message || "Error al procesar la solicitud", "error");
            }
        } catch(e) {
            console.error("Error en saveNewLocation:", e);
            Toast.fire("Error al conectar con el servidor", "error");
        }
    }

    /**
     * Actualiza la lista de direcciones en el DOM y selecciona la más reciente
     */
    function renderLocations(locations, newlySaved = null) {
        const list = document.getElementById('locationsList');
        if (!locations || locations.length === 0) {
            list.innerHTML = '<p class="empty-msg">No tienes direcciones guardadas.</p>';
            return;
        }

        let html = '';
        if (locations && locations.length > 0) {
            locations.forEach(loc => {
                const safeAddr = loc.address.replace(/"/g, '&quot;');
                html += `
                    <div class="location-card" onclick="selectLocation(this)"
                         data-id="${loc.id}"
                         data-lat="${loc.lat}" data-lng="${loc.lng}" data-addr="${safeAddr}">
                        <i class="fas fa-home"></i>
                        <strong>${loc.title}</strong>
                        <small>${loc.address}</small>
                        <button type="button" class="btn-delete-checkout" 
                                onclick="event.stopPropagation(); confirmDeleteLocation(${loc.id}, '${loc.title}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });
        }
        list.innerHTML = html;

        // Auto-selección lógica: Buscamos la dirección exacta que acabamos de guardar/actualizar
        if (newlySaved && newlySaved.id) {
            const cards = list.querySelectorAll('.location-card');
            let targetCard = null;

            // Buscamos la tarjeta cuyo título coincida con el que acabamos de enviar
            cards.forEach(card => {
                if (card.querySelector('strong').innerText === newlySaved.title) {
                    targetCard = card;
                }
            });
            const targetCardById = list.querySelector(`.location-card[data-id="${newlySaved.id}"]`);
            if (targetCardById) {
                selectLocation(targetCardById, true);
            } else if (cards.length > 0) {
                // Fallback: si por alguna razón no se encuentra la tarjeta por ID, seleccionamos la última
                selectLocation(list.lastElementChild, true);
            }

            const msg = newlySaved.id ? "Ubicación actualizada y seleccionada" : "Ubicación guardada y seleccionada";
            Toast.fire(msg, "success");
        } else if (locations.length > 0) {
            // Si no hay un newlySaved específico, pero hay ubicaciones, seleccionamos la primera por defecto
            selectLocation(list.firstElementChild, true);
        }
    }

    // --- 2. Lógica del Carrito y Envío ---
    document.addEventListener('DOMContentLoaded', () => {
        loadCheckoutItems();
    });

    function loadCheckoutItems() {
        // Leer del localStorage (usamos la misma clave que tool-kit-v002.js)
        const cart = JSON.parse(localStorage.getItem('comedor_cart')) || [];
        const container = document.getElementById('checkout-items');
        const totalEl = document.getElementById('checkout-total');
        const deliveryRow = document.getElementById('delivery-cost-row');
        const deliveryPriceEl = document.getElementById('checkout-delivery-price');

        if (cart.length === 0) {
            container.innerHTML = '<p>Tu carrito está vacío.</p>';
            window.location.href = '?route=home'; // Redirigir si no hay nada
            return;
        }

        let html = '';
        let total = 0;
        let deliveryCost = 0;

        // Si es delivery y hay coordenadas seleccionadas
        const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value;
        const lat = document.getElementById('lat').value;
        const lng = document.getElementById('lng').value;

        if (deliveryType === 'delivery' && lat && lng) {
            const dist = calculateDistance(storeConfig.lat, storeConfig.lng, parseFloat(lat), parseFloat(lng));
            
            // Buscar el precio correspondiente en los rangos configurados
            let foundPrice = 0;
            if (storeConfig.rates && storeConfig.rates.length > 0) {
                for (const rate of storeConfig.rates) {
                    if (dist >= rate.start && dist <= rate.end) {
                        foundPrice = rate.price;
                        break;
                    }
                }
            }
            deliveryCost = foundPrice;
            deliveryRow.style.display = 'flex';
            deliveryPriceEl.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(deliveryCost);
        } else {
            deliveryRow.style.display = 'none';
        }

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            html += `
                <div class="summary-item">
                    <div>
                        <strong>${item.quantity}x</strong> ${item.name}
                    </div>
                    <div>Gs. ${new Intl.NumberFormat('es-PY').format(subtotal)}</div>
                </div>
            `;
        });

        container.innerHTML = html;
        totalEl.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(total + deliveryCost);
    }

    async function submitOrder() {
        // 1. Validar formulario HTML (campos required)
        const form = document.getElementById('orderForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // 2. Validar ubicación si es delivery
        const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
        if (deliveryType === 'delivery') {
            const lat = document.getElementById('lat').value;
            if (!lat) {
                alert("Por favor, toca en el mapa para indicar dónde entregar el pedido.");
                return;
            }
        }

        // 3. Preparar datos
        const formData = new FormData(form);
        const cart = JSON.parse(localStorage.getItem('comedor_cart')) || [];
        
        // Convertir FormData a objeto simple para enviar como JSON
        const data = Object.fromEntries(formData.entries());
        data.cart = cart; // Adjuntar carrito

        // 4. Enviar al servidor (AJAX)
        try {
            const btn = document.querySelector('.btn-confirm');
            btn.disabled = true;
            btn.innerText = "Procesando...";

            // Enviamos los datos al controlador
            const response = await fetch('?route=order_store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                localStorage.removeItem('comedor_cart'); // Limpiar carrito
                // Redirigir a una página de éxito o al historial
                window.location.href = '?route=order_success&id=' + result.order_id;
            } else {
                alert('Error: ' + result.message);
                btn.disabled = false;
                btn.innerText = "Confirmar Pedido";
            }

        } catch (error) {
            console.error(error);
            alert("Hubo un error al procesar el pedido.");
        }
    }
</script>