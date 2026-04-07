<style>
    /* Estilo para que el select deshabilitado no parezca un select */
    #typeSelect {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important; /* Quita la flecha de Bootstrap */
        padding-right: 0.75rem !important; /* Ajusta el espacio lateral interno */
    }
</style>
<div class="container-fluid" style="max-width: 800px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?route=hero_promos">Hero Promo</a></li>
            <li class="breadcrumb-item active">Editar Tarjeta</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-body p-4">
            <form action="?route=hero_promos_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $promo['image']; ?>">

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

                <div class="mb-4" id="contentGroup">
                    <label class="form-label fw-bold">Contenido / Descripción</label>
                    <textarea name="content" id="contentTextarea" class="form-control" rows="4" required><?php echo htmlspecialchars($promo['content']); ?></textarea>
                    <div id="reviewNotice" class="alert alert-info mt-2 d-none">
                        <i class="fas fa-magic me-2"></i> <strong>Modo Automático:</strong> Las reseñas se toman aleatoriamente de los comentarios de los clientes. Solo asegúrate de que la tarjeta esté "Activa".
                    </div>
                    <small class="text-muted" id="contentHelp">Para horarios, usa saltos de línea.</small>
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
                        <div class="mt-3 text-center">
                            <?php 
                                $displayImg = !empty($promo['image']) ? 'uploads/' . $promo['image'] : 'https://via.placeholder.com/400x200?text=Sin+Imagen+Configurada';
                            ?>
                            <img id="imgPreview" src="<?php echo $displayImg; ?>" 
                                 class="rounded shadow-sm" 
                                 style="max-width: 100%; height: 160px; object-fit: cover; border: 1px solid #dee2e6;">
                            <div id="imageName" class="small text-muted mt-1"><?php echo $promo['image'] ?? ''; ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo $promo['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold" for="isActive">Mostrar esta tarjeta en el Carrusel</label>
                </div>

                <hr>
                
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
    const notice = document.getElementById('reviewNotice');
    const help = document.getElementById('contentHelp');

    if (type === 'reviews') {
        contentGroup.readOnly = true;
        contentGroup.classList.add('bg-light');
        notice.classList.remove('d-none');
        help.classList.add('d-none');
    } else {
        contentGroup.readOnly = false;
        contentGroup.classList.remove('bg-light');
        notice.classList.add('d-none');
        help.classList.remove('d-none');
    }
}

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
</script>