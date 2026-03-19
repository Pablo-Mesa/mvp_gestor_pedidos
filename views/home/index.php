<h2 class="mb-4">Menú del Día: <?php echo date('d/m/Y'); ?></h2>

<?php if (empty($menus)): ?>
    <div class="alert alert-info">No hay menú disponible para hoy. Por favor intenta más tarde.</div>
<?php else: ?>

<form action="?route=order_confirm" method="POST" id="orderForm">
    <div class="row">
        <!-- Listado de Platos -->
        <div class="col-md-8">
            <div class="row">
                <?php foreach ($menus as $menu): ?>
                    <?php if($menu['is_available']): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card menu-card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($menu['product_name']); ?></h5>
                                <p class="card-text text-success fw-bold">$<?php echo number_format($menu['product_price'], 2); ?></p>
                                <?php if($menu['daily_stock'] !== null): ?>
                                    <small class="text-muted">Stock: <?php echo $menu['daily_stock']; ?></small>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <label class="form-label">Cantidad:</label>
                                    <input type="number" name="products[<?php echo $menu['id']; ?>]" class="form-control" min="0" max="10" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Panel Lateral: Datos del Pedido -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-white fw-bold">Detalles del Pedido</div>
                <div class="card-body">
                    
                    <!-- Método de Pago -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Forma de Pago</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Efectivo</option>
                            <option value="transfer">Transferencia</option>
                            <option value="debit">Tarjeta Débito</option>
                        </select>
                    </div>

                    <!-- Tipo de Entrega -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Entrega</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery_type" id="pickup" value="pickup" checked onchange="toggleDelivery(false)">
                            <label class="form-check-label" for="pickup">Retiro en local</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery_type" id="delivery" value="delivery" onchange="toggleDelivery(true)">
                            <label class="form-check-label" for="delivery">Delivery (A domicilio)</label>
                        </div>
                    </div>

                    <!-- Sección Delivery (Oculta por defecto) -->
                    <div id="deliverySection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Dirección escrita</label>
                            <textarea name="delivery_address" class="form-control" rows="2" placeholder="Calle, número, ref..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-primary"><small>📍 Marca tu ubicación en el mapa</small></label>
                            <div id="map"></div>
                            <input type="hidden" name="delivery_lat" id="lat">
                            <input type="hidden" name="delivery_lng" id="lng">
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary w-100 py-2">Confirmar Pedido</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let map, marker;

    function toggleDelivery(isDelivery) {
        const section = document.getElementById('deliverySection');
        const mapDiv = document.getElementById('map');
        
        if (isDelivery) {
            section.style.display = 'block';
            mapDiv.style.display = 'block';
            
            // Inicializar mapa si no existe (Coordenadas por defecto: Centro de ciudad ejemplo)
            if (!map) {
                map = L.map('map').setView([-34.6037, -58.3816], 13); // Cambia esto a las coords de tu ciudad
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                map.on('click', function(e) {
                    if (marker) map.removeLayer(marker);
                    marker = L.marker(e.latlng).addTo(map);
                    document.getElementById('lat').value = e.latlng.lat;
                    document.getElementById('lng').value = e.latlng.lng;
                });
            }
        } else {
            section.style.display = 'none';
        }
    }
</script>
<?php endif; ?>