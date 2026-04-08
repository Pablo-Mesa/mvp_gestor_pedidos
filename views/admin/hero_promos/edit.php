<style>
    /* Estilo para que el select deshabilitado no parezca un select */
    #typeSelect {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important; /* Quita la flecha de Bootstrap */
        padding-right: 0.75rem !important; /* Ajusta el espacio lateral interno */
    }
    *{
        box-sizing: border-box;
        padding: 0px;
        margin: 0px;
    }
</style>
<style>

    /* Contenedor para scroll interno */
    .scrollable-card-body {
        max-height: calc(100vh - 320px);
        overflow-y: auto;
        padding-right: 10px;
    }
    
    /* Estilos de fila más compactos */
    .day-row {
        display: flex; 
        flex-direction: row;
        justify-content: center;
        align-items: center; 
        gap: 8px; 
        margin-bottom: 4px; 
        padding: 4px 10px; 
        border-radius: 6px; 
        transition: background 0.3s ease; 
        border: 1px solid transparent;
    }

    .day-row:hover {
        background-color: #f8f9fa;
    }
    .day-label {
        width: 200px;
        flex-shrink: 0;
        font-weight: bold; 
        margin: 0; 
        color: #6c757d;
        font-size: 0.7rem; 
        text-transform: uppercase; 
        letter-spacing: 0.3px;
    }
    .time-inputs { display: flex; align-items: center; gap: 5px; flex-grow: 1; justify-content: center; }
    .day-row.is-closed { background-color: #fff5f5; border-color: #ffe3e3; }
    .day-row.is-closed .time-inputs { opacity: 0.1; pointer-events: none; filter: blur(1px); }
    .day-row.is-closed .closed-label { color: #dc3545; font-weight: bold; }
    .closed-label { min-width: 60px; font-size: 0.75rem; color: #28a745; text-align: right; }
    
    /* Quitar scroll de la página principal en esta vista */
    body { overflow: hidden; }
    .content-wrapper { height: 100vh; display: flex; flex-direction: column; }
</style>
<div class="container-fluid" style="max-width: 800px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?route=hero_promos">Hero Promo</a></li>
            <li class="breadcrumb-item active">Configuración Dinámica</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-body p-4">
            <form action="?route=hero_promos_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $promo['image']; ?>">

                <div class="scrollable-card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Título de la Tarjeta</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="<?php echo htmlspecialchars($promo['title']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo</label>
                        <select id="typeSelect" class="form-select form-control-lg" disabled>
                            <option value="offer" <?php echo $promo['type'] == 'offer' ? 'selected' : ''; ?>>🏷️ Oferta / General</option>
                            <option value="hours" <?php echo $promo['type'] == 'hours' ? 'selected' : ''; ?>>⏰ Horarios</option>
                            <option value="location" <?php echo $promo['type'] == 'location' ? 'selected' : ''; ?>>📍 Ubicación</option>
                            <option value="highlights" <?php echo $promo['type'] == 'highlights' ? 'selected' : ''; ?>>⭐ Destacados</option>
                            <option value="reviews" <?php echo $promo['type'] == 'reviews' ? 'selected' : ''; ?>>💬 Reseñas</option>
                        </select>
                        <input type="hidden" name="type" value="<?php echo $promo['type']; ?>">
                    </div>
                </div>

                <!-- Editor de Contenido Dinámico -->
                <div class="mb-4">
                    <label class="form-label fw-bold" id="contentLabel">Contenido / Descripción</label>
                    
                    <!-- Vista para Horarios -->
                    <div id="hoursEditor" class="border rounded p-3 bg-white d-none" style="overflow-x: hidden;">
                        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> Define los horarios para que el sistema muestre "Abierto" o "Cerrado" automáticamente.</p>
                        <?php 
                            $days = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                            $currentHours = json_decode($promo['content'], true) ?: [];
                        ?>
                        <div id="scheduleGrid">
                            <?php foreach($days as $idx => $name): 
                                $h = $currentHours[$idx] ?? ['open' => '08:00', 'close' => '22:00', 'closed' => false];
                            ?>
                            <div class="day-row <?php echo $h['closed'] ? 'is-closed' : ''; ?>" data-day="<?php echo $idx; ?>">
                                <p class="day-label"><?php echo $name; ?></p>
                                <div class="time-inputs">
                                    <input type="time" class="form-control form-control-sm open-time shadow-none" style="max-width: 110px;" value="<?php echo $h['open']; ?>">
                                    <span class="text-muted small">a</span>
                                    <input type="time" class="form-control form-control-sm close-time shadow-none" style="max-width: 110px;" value="<?php echo $h['close']; ?>">
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input closed-toggle" type="checkbox" <?php echo $h['closed'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label small closed-label">
                                        <?php echo $h['closed'] ? 'Cerrado' : 'Abierto'; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Input original oculto o visible según tipo -->
                    <textarea name="content" id="contentTextarea" class="form-control" rows="4"><?php echo htmlspecialchars($promo['content']); ?></textarea>
                    
                    <div id="reviewNotice" class="alert alert-info mt-2 d-none">
                        <i class="fas fa-magic me-2"></i> <strong>Modo Automático:</strong> Las reseñas se toman aleatoriamente de los comentarios de los clientes.
                    </div>
                    <small class="text-muted" id="contentHelp">Ingresa el texto informativo para la tarjeta.</small>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Estilo Visual (CSS Class)</label>
                        <select name="css_class" class="form-select">
                            <option value="ambient" <?php echo $promo['css_class'] == 'ambient' ? 'selected' : ''; ?>>Ambient (Imagen + Texto)</option>
                            <option value="info-card" <?php echo $promo['css_class'] == 'info-card' ? 'selected' : ''; ?>>Info Card (Cristal esmerilado)</option>
                            <option value="process" <?php echo $promo['css_class'] == 'process' ? 'selected' : ''; ?>>Process (Pasos)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Imagen de Fondo</label>
                        <input type="file" name="image" class="form-control" id="imageInput" accept="image/*" onchange="previewHeroImage(this)">
                        <div class="mt-3 text-center position-relative">
                            <?php 
                                $placeholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eeeeee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaaaaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3ESin%20Imagen%20Configurada%3C%2Ftext%3E%3C%2Fsvg%3E";
                                $displayImg = !empty($promo['image']) ? 'uploads/' . $promo['image'] : $placeholder;
                            ?>
                            <div class="d-inline-block position-relative">
                                <img id="imgPreview" src="<?php echo $displayImg; ?>" 
                                     class="rounded shadow-sm" 
                                     style="max-width: 100%; height: 160px; object-fit: cover; border: 1px solid #dee2e6;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                        onclick="resetHeroImage()" title="Quitar imagen">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="imageName" class="small text-muted mt-1"><?php echo !empty($promo['image']) ? $promo['image'] : 'Sin imagen'; ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo $promo['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold" for="isActive">Mostrar esta tarjeta en el Carrusel</label>
                </div>
                </div> <!-- Fin scrollable-card-body -->

                <hr class="mt-0">
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="?route=hero_promos" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleContentField() {
    const type = document.getElementById('typeSelect').value;
    const contentGroup = document.getElementById('contentTextarea');
    const hoursEditor = document.getElementById('hoursEditor');
    const notice = document.getElementById('reviewNotice');
    const help = document.getElementById('contentHelp');
    const label = document.getElementById('contentLabel');

    if (type === 'reviews') {
        contentGroup.readOnly = true;
        contentGroup.classList.add('bg-light');
        contentGroup.classList.remove('d-none');
        hoursEditor.classList.add('d-none');
        notice.classList.remove('d-none');
        help.classList.add('d-none');
    } else if (type === 'hours') {
        contentGroup.classList.add('d-none');
        hoursEditor.classList.remove('d-none');
        notice.classList.add('d-none');
        help.classList.add('d-none');
        label.innerText = "Configuración de Horarios Semanales";
        serializeHours(); // Asegurar que el JSON esté listo
    } else {
        contentGroup.readOnly = false;
        contentGroup.classList.remove('bg-light');
        contentGroup.classList.remove('d-none');
        hoursEditor.classList.add('d-none');
        notice.classList.add('d-none');
        help.classList.remove('d-none');
        label.innerText = "Contenido / Descripción";
    }
}

/**
 * Convierte la tabla de horarios en un JSON para el campo 'content'
 */
function serializeHours() {
    const schedule = {};
    document.querySelectorAll('.day-row').forEach(row => {
        const day = row.dataset.day;
        schedule[day] = {
            open: row.querySelector('.open-time').value,
            close: row.querySelector('.close-time').value,
            closed: row.querySelector('.closed-toggle').checked
        };
    });
    document.getElementById('contentTextarea').value = JSON.stringify(schedule);
}

// Event Listeners para el editor de horarios
document.getElementById('scheduleGrid').addEventListener('change', (e) => {
    if (e.target.classList.contains('closed-toggle')) {
        const row = e.target.closest('.day-row');
        const label = row.querySelector('.closed-label');
        
        row.classList.toggle('is-closed', e.target.checked);
        label.innerText = e.target.checked ? "Cerrado" : "Abierto";
    }
    serializeHours();
});

window.addEventListener('load', toggleContentField);

function previewHeroImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imageName').innerText = input.files[0].name;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Restablece la imagen a su estado default (vacío)
 */
function resetHeroImage() {
    document.getElementById('imageInput').value = ""; // Limpia el input file
    document.querySelector('input[name="current_image"]').value = ""; // Limpia el nombre de la imagen actual
    const placeholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23eeeeee%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23aaaaaa%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20dy%3D%22.3em%22%20text-anchor%3D%22middle%22%3ESin%20Imagen%20Configurada%3C%2Ftext%3E%3C%2Fsvg%3E";
    document.getElementById('imgPreview').src = placeholder;
    document.getElementById('imageName').innerText = 'Sin imagen';
    Toast.fire("Imagen marcada para eliminar", "info");
}
</script>