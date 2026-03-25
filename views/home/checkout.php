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
                    <h3><i class="fas fa-map-marked-alt"></i> Ubicación de Entrega</h3>
                    <p class="hint-text">Toca en el mapa para marcar donde debemos llevar tu pedido.</p>
                    
                    <div id="map" class="map-container"></div>
                    
                    <!-- Inputs Ocultos para Lat/Lng -->
                    <input type="hidden" name="delivery_lat" id="lat">
                    <input type="hidden" name="delivery_lng" id="lng">

                    <div class="input-group mt-3">
                        <label>Referencia / Dirección escrita</label>
                        <input type="text" name="delivery_address" placeholder="Ej: Casa azul, reja negra, frente a la plaza..." class="form-control">
                    </div>
                </div>

                <!-- 3. Método de Pago -->
                <div class="section-card">
                    <h3><i class="fas fa-wallet"></i> Método de Pago</h3>
                    <select name="payment_method" class="form-control" required>
                        <option value="efectivo">💵 Efectivo</option>
                        <option value="qr">📱 QR / Transferencia</option>
                        <option value="tarjeta">💳 Tarjeta (POS)</option>
                    </select>
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
                <h3>Resumen</h3>
                <div id="checkout-items" class="summary-list">
                    <!-- Items inyectados por JS -->
                </div>
                
                <div class="summary-total">
                    <span>Total a Pagar</span>
                    <span id="checkout-total">Gs. 0</span>
                </div>

                <button type="button" class="btn-confirm" onclick="submitOrder()">
                    Confirmar Pedido <i class="fas fa-check-circle"></i>
                </button>
                <a href="?route=home" class="btn-back">Seguir comprando</a>
            </div>
        </div>
    </div>
</div>

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

    /* Radio Buttons Visuales */
    .delivery-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; }
    .radio-card input { display: none; } /* Ocultar radio real */
    .card-content { 
        border: 2px solid #eee; border-radius: 8px; padding: 1rem; 
        text-align: center; cursor: pointer; transition: 0.2s;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        color: #666;
    }
    .card-content i { font-size: 1.5rem; }
    /* Estado seleccionado */
    .radio-card input:checked + .card-content { border-color: #007bff; background-color: #f0f7ff; color: #007bff; font-weight: bold; }

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
    
    .btn-back { display: block; text-align: center; margin-top: 1rem; color: #666; text-decoration: none; font-size: 0.9rem; }
    .btn-back:hover { text-decoration: underline; }
</style>

<script>
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
        const addressInput = document.querySelector('input[name="delivery_address"]');
        
        if (show) {
            container.style.display = 'block';
            // Leaflet necesita recalcular su tamaño cuando se hace visible
            setTimeout(() => { 
                if(!map) initMap(); 
                else map.invalidateSize(); 
            }, 100);
            addressInput.setAttribute('required', 'required');
        } else {
            container.style.display = 'none';
            addressInput.removeAttribute('required');
            // Limpiar valores
            document.getElementById('lat').value = '';
            document.getElementById('lng').value = '';
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
        
        if (cart.length === 0) {
            container.innerHTML = '<p>Tu carrito está vacío.</p>';
            window.location.href = '?route=home'; // Redirigir si no hay nada
            return;
        }

        let html = '';
        let total = 0;

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
        totalEl.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(total);
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