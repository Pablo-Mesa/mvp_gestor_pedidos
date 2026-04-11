<!-- Leaflet CSS para el Mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Vista para configurar la ubicación del local en el mapa. 
Se utiliza Leaflet para mostrar un mapa interactivo donde el usuario puede
colocar un marcador. -->
<div class="container-fluid pb-5"> <!-- Añadido padding inferior para accesibilidad -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">📍 Ajustes de Ubicación del Local</h2>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">La ubicación del local ha sido actualizada correctamente.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4" style="border-left: 4px solid #00b894;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Establecer coordenadas en el Mapa</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle"></i> Haz clic en el mapa para posicionar el marcador o arrástralo hasta la ubicación exacta de tu tienda. 
                        Esta información se utilizará para que los clientes te encuentren y para el cálculo de rutas de delivery.
                    </p>
                    
                    <!-- Contenedor del Mapa -->
                    <div id="map" style="height: 400px; border-radius: 12px; border: 1px solid #ddd; z-index: 1;"></div>

                    <form action="?route=settings_location_update" method="POST" class="mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Latitud</label>
                                <input type="text" name="store_lat" id="lat" class="form-control bg-light" 
                                       value="<?php echo htmlspecialchars($settings['store_lat'] ?? '-25.3006'); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Longitud</label>
                                <input type="text" name="store_lng" id="lng" class="form-control bg-light" 
                                       value="<?php echo htmlspecialchars($settings['store_lng'] ?? '-57.6359'); ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Dirección Referencial</label>
                            <input type="text" name="store_address" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['store_address'] ?? ''); ?>" 
                                   placeholder="Ej: Calle 14 de Mayo casi Avda. Principal">
                        </div>

                        <div class="pt-3 border-top">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save"></i> Guardar Ubicación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Coordenadas por defecto (Paraguay si no hay nada guardado)
        const savedLat = <?php echo !empty($settings['store_lat']) ? $settings['store_lat'] : '-25.3006'; ?>;
        const savedLng = <?php echo !empty($settings['store_lng']) ? $settings['store_lng'] : '-57.6359'; ?>;
        const zoomLevel = <?php echo !empty($settings['store_lat']) ? '17' : '13'; ?>;

        const map = L.map('map').setView([savedLat, savedLng], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marcador arrastrable
        let marker = L.marker([savedLat, savedLng], { draggable: true }).addTo(map);

        // Actualizar inputs al mover marcador o hacer clic
        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(7);
            document.getElementById('lng').value = lng.toFixed(7);
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(event) {
            const position = marker.getLatLng();
            updateCoords(position.lat, position.lng);
        });
    });
</script>