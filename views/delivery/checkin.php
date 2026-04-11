<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    .checkin-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        height: 100%;
    }
    #map-checkin {
        flex: 1; /* El mapa ocupa todo el espacio central disponible */
        min-height: 300px;
        width: 100%;
        border-radius: 15px;
        border: 2px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1;
    }
    .status-card {
        background: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .distance-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        margin-top: 10px;
    }
    .btn-checkin {
        padding: 18px;
        border-radius: 12px;
        border: none;
        background: #ccc;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: not-allowed;
        flex-shrink: 0; /* Evita que el botón se achique */
        transition: all 0.3s;
    }
    .btn-checkin.ready {
        background: var(--delivery-primary);
        color: #000;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,200,83,0.3);
    }
</style>

<div class="checkin-container">
    <div class="status-card">
        <h2 style="margin:0; font-size: 1.2rem;">📍 Ubicación del Local</h2>
        <p id="info-text" style="color: #666; margin-top: 5px;">Obteniendo tu ubicación GPS...</p>
        <div id="distance-info" class="distance-badge" style="background: #f1f1f1;">Calculando...</div>
    </div>

    <div id="map-checkin"></div>

    <button id="btnMarkArrival" class="btn-checkin" disabled onclick="saveArrival()">
        <i class="fas fa-check-circle"></i> MARCAR LLEGADA
    </button>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const STORE_LAT = <?= $settings['store_lat'] ?? -25.3006 ?>;
    const STORE_LNG = <?= $settings['store_lng'] ?? -57.6359 ?>;
    const VALID_RADIUS = 50; // Radio en metros permitido para marcar

    let map, userMarker, circle;
    let currentDistance = null;
    let userCoords = { lat: null, lng: null };

    function initMap() {
        map = L.map('map-checkin', { zoomControl: false }).setView([STORE_LAT, STORE_LNG], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Marcador del local
        L.marker([STORE_LAT, STORE_LNG]).addTo(map).bindPopup('<b>El Local</b>').openPopup();

        // Círculo de rango permitido
        circle = L.circle([STORE_LAT, STORE_LNG], {
            color: '#00c853',
            fillColor: '#00c853',
            fillOpacity: 0.1,
            radius: VALID_RADIUS
        }).addTo(map);

        startTracking();
    }

    function startTracking() {
        if (!navigator.geolocation) {
            alert("Tu navegador no soporta geolocalización");
            return;
        }

        navigator.geolocation.watchPosition(position => {
            const { latitude, longitude } = position.coords;
            userCoords = { lat: latitude, lng: longitude };
            
            if (!userMarker) {
                userMarker = L.circleMarker([latitude, longitude], { color: '#0984e3', radius: 8 }).addTo(map);
            } else {
                userMarker.setLatLng([latitude, longitude]);
            }

            // Calcular distancia al local (Fórmula de Haversine simplificada por Leaflet)
            const storeLatLng = L.latLng(STORE_LAT, STORE_LNG);
            const userLatLng = L.latLng(latitude, longitude);
            currentDistance = storeLatLng.distanceTo(userLatLng);

            updateUI();
        }, err => {
            document.getElementById('info-text').innerText = "Error: Por favor activa tu GPS";
            console.error(err);
        }, { enableHighAccuracy: true });
    }

    function updateUI() {
        const info = document.getElementById('distance-info');
        const btn = document.getElementById('btnMarkArrival');
        const text = document.getElementById('info-text');

        info.innerText = `Estás a ${Math.round(currentDistance)} metros`;

        if (currentDistance <= VALID_RADIUS) {
            info.style.background = "#d4edda";
            info.style.color = "#155724";
            text.innerText = "¡Estás dentro del rango! Ya puedes marcar.";
            btn.disabled = false;
            btn.classList.add('ready');
        } else {
            info.style.background = "#f8d7da";
            info.style.color = "#721c24";
            text.innerText = "Debes acercarte más al local para marcar llegada.";
            btn.disabled = true;
            btn.classList.remove('ready');
        }
    }

    async function saveArrival() {
        try {
            const res = await fetch('?route=delivery_checkin_save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...userCoords, distance: currentDistance })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire("¡Llegada marcada!", data.message, "success").then(() => {
                    location.href = '?route=delivery';
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        } catch (e) {
            console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', initMap);
</script>