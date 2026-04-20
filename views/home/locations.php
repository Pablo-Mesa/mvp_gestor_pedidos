<!-- Leaflet CSS & JS para el Mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container" style="max-width: 800px; padding-top: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="margin:0;"><i class="fas fa-map-marked-alt"></i> Mis Direcciones</h2>
        <button type="button" class="btn-add-location" style="border: none; background: #28a745; color:white; padding: 10px 20px; border-radius: 8px; cursor: pointer;" onclick="openLocationModal()">
            <i class="fas fa-plus"></i> Agregar Nueva (en el Mapa)
        </button>
    </div>

    <div id="locationsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        <?php if(empty($savedLocations)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <i class="fas fa-map-pin" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem; display: block;"></i>
                <p>No tienes direcciones guardadas.</p>
            </div>
        <?php else: ?>
            <?php foreach($savedLocations as $loc): ?>
                <div class="section-card" style="margin-bottom: 0; position: relative;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong style="font-size: 1.1rem; color: #2d3436;"><?= htmlspecialchars($loc['title']) ?></strong>
                            <button onclick="openLocationModal(true, <?= $loc['id'] ?>, '<?= addslashes($loc['title']) ?>', '<?= addslashes($loc['address']) ?>', <?= $loc['lat'] ?>, <?= $loc['lng'] ?>)" 
                                    style="background: #ffc107; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-pen"></i>
                            </button>
                            <?php if (!$loc['has_orders']): ?>
                            <button onclick="confirmDelete(<?= $loc['id'] ?>, '<?= addslashes($loc['title']) ?>')" 
                                    style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; margin-left: 5px;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <p style="color: #636e72; font-size: 0.9rem; margin: 0; line-height: 1.4;">
                            <?= htmlspecialchars($loc['address']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Ubicación (Unificado) -->
<div id="locationModal" class="modal-overlay" style="display:none;">
    <div class="modal-card" style="max-width: 550px;">
        <div class="modal-tabs">
            <button class="tab-btn active" id="modalTitle">Guardar Ubicación</button>
        </div>
        <div class="modal-content">
            <input type="hidden" id="edit_id">
            <input type="hidden" id="lat">
            <input type="hidden" id="lng">
            
            <div class="input-group">
                <label>Título (Ej: Mi Casa, Oficina)</label>
                <input type="text" id="edit_title" class="form-control" placeholder="Casa, Trabajo..." required>
            </div>

            <div id="mapSection">
                <div class="hint-text mt-3" style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">Busca tu dirección o ubica el marcador manualmente:</div>
                <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                    <input type="text" id="mapSearchInput" class="form-control" placeholder="Ej: Calle, Ciudad..." style="flex: 1;">
                    <button type="button" class="btn-main" style="padding: 10px; margin-top: 0;" onclick="searchAddress()"><i class="fas fa-search"></i></button>
                </div>
                <div id="map" style="height: 220px; border-radius: 8px; border: 1px solid #ddd; z-index: 1;"></div>
            </div>
            
            <div class="input-group mt-3">
                <label id="addrLabel">Dirección / Referencia Detallada</label>
                <textarea id="edit_address" class="form-control" rows="3" placeholder="Nro de casa, color de reja, etc." required></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-main" style="flex:1; background:#6c757d;" onclick="closeLocationModal()">Cancelar</button>
                <button type="button" class="btn-main" style="flex:2;" onclick="saveLocation()">Guardar Información</button>
            </div>
        </div>
    </div>
</div>

<style>
    .section-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .input-group { display: flex; flex-direction: column; gap: 5px; }
    .input-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-control { padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
    .btn-main { border: none; padding: 12px; border-radius: 8px; color: white; font-weight: bold; cursor: pointer; background: #2d3436; }
    .mt-3 { margin-top: 1rem; }
</style>

<script>
let map, marker;

function initMap() {
    if (map) return;
    map = L.map('map').setView([-25.2865, -57.6470], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    map.on('click', e => updateMarker(e.latlng.lat, e.latlng.lng));
}

function updateMarker(lat, lng) {
    if (marker) marker.setLatLng([lat, lng]);
    else marker = L.marker([lat, lng]).addTo(map);
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
}

async function searchAddress() {
    const query = document.getElementById('mapSearchInput').value;
    if (!query) return;
    try {
        const resp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
        const data = await resp.json();
        if (data.length > 0) {
            const { lat, lon } = data[0];
            map.setView([lat, lon], 17);
            updateMarker(lat, lon);
        } else { Toast.fire("No se encontró la ubicación", "info"); }
    } catch (e) { console.error(e); }
}

function openLocationModal(isEdit = false, id = '', title = '', address = '', lat = '', lng = '') {
    const modal = document.getElementById('locationModal');
    const mapSection = document.getElementById('mapSection');
    const addrLabel = document.getElementById('addrLabel');
    
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_address').value = address;
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;

    if (isEdit) {
        document.getElementById('modalTitle').innerText = 'Editar Información';
        mapSection.style.display = 'none';
        addrLabel.innerText = 'Dirección / Referencia';
    } else {
        document.getElementById('modalTitle').innerText = 'Agregar Nueva Dirección';
        mapSection.style.display = 'block';
        addrLabel.innerText = 'Referencia Detallada (Opcional)';
        
        setTimeout(() => {
            initMap();
            map.invalidateSize();
            if (lat && lng) {
                map.setView([lat, lng], 15);
                updateMarker(lat, lng);
            } else if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    map.setView([pos.coords.latitude, pos.coords.longitude], 15);
                });
            }
        }, 300);
    }
    modal.style.display = 'flex';
}

function closeLocationModal() {
    document.getElementById('locationModal').style.display = 'none';
}

async function saveLocation() {
    const editId = document.getElementById('edit_id').value;
    const data = {
        id: editId,
        title: document.getElementById('edit_title').value,
        address: document.getElementById('edit_address').value,
        lat: document.getElementById('lat').value,
        lng: document.getElementById('lng').value
    };

    if(!data.title || (!editId && !data.lat)) { 
        Toast.fire("Título y ubicación en mapa son obligatorios", "error"); 
        return; 
    }

    try {
        const route = editId ? 'update_location' : 'save_location';
        const response = await fetch(`?route=${route}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            closeLocationModal();
            Toast.fire(editId ? "Ubicación actualizada" : "Ubicación guardada", "success");
            renderLocalList(result.locations);
        } else {
            Toast.fire(result.message || "Error al procesar", "error");
        }
    } catch (err) {
        Toast.fire("Error al conectar con el servidor", "error");
    }
}

async function confirmDelete(id, title) {
    const { isConfirmed } = await Swal.fire({
        title: `¿Eliminar "${title}"?`,
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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
                Toast.fire("Dirección eliminada correctamente", "success");
                renderLocalList(result.locations);
            } else {
                Toast.fire(result.message, "error");
            }
        } catch (err) {
            console.error("Error al eliminar ubicación:", err);
            Toast.fire("Error al conectar con el servidor", "error");
        }
    }
}

function renderLocalList(locations) {
    const list = document.getElementById('locationsList');
    let html = '';
    locations.forEach(loc => {
        const safeTitle = loc.title.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeAddr = loc.address.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const deleteBtn = !loc.has_orders ? `
            <button onclick="confirmDelete(${loc.id}, '${safeTitle}')" 
                    style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer; margin-left: 5px;">
                <i class="fas fa-trash"></i>
            </button>` : '';

        html += `
            <div class="section-card" style="margin-bottom: 0; position: relative;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <strong style="font-size: 1.1rem; color: #2d3436;">${loc.title}</strong>
                        <button onclick="openLocationModal(true, ${loc.id}, '${safeTitle}', '${safeAddr}', ${loc.lat}, ${loc.lng})" 
                                style="background: #ffc107; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-pen"></i>
                        </button>
                        ${deleteBtn}
                    </div>
                    <p style="color: #636e72; font-size: 0.9rem; margin: 0; line-height: 1.4;">
                        ${loc.address}
                    </p>
                </div>
            </div>
        `;
    });
    list.innerHTML = html;
}
</script>