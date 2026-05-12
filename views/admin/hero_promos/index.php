<?php 
// Buscamos el registro de tipo 'hours'
$promo = null;
foreach($promos as $p) {
    if($p['type'] === 'hours') {
        $promo = $p;
        break;
    }
}
$isEdit = ($promo !== null);
?>

<style>
    .day-row {
        display: flex; 
        align-items: center; 
        gap: 15px; 
        padding: 12px 15px; 
        border-radius: 8px; 
        transition: all 0.2s ease; 
        border: 1px solid #f1f1f1;
        background: #fff;
    }
    .day-row:hover { background-color: #fcfcfc; border-color: #dee2e6; }
    .day-label { width: 140px; font-weight: 500; color: #495057; margin: 0; }
    .time-inputs { display: flex; align-items: center; gap: 8px; flex-grow: 1; }
    .day-row.is-closed { background-color: #fff5f5; border-color: #ffe3e3; opacity: 0.7; }
    .day-row.is-closed .time-inputs { pointer-events: none; filter: grayscale(1); }
    .closed-label { min-width: 60px; font-size: 0.8rem; font-weight: 600; }
    #imgPreview { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
</style>

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?route=admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Horarios de Atención</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0 text-gray-800">Horarios de Atención</h2>
            <p class="text-muted small mb-0">Configura cuándo el local está abierto para recibir pedidos automáticos.</p>
        </div>
        <?php if ($isEdit): ?>
            <button type="button" id="btn-enable-edit" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm me-1"></i> Modificar Horarios
            </button>
        <?php endif; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="form-horarios" action="?route=hero_promos_update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $promo['id'] ?? ''; ?>">
                <input type="hidden" name="current_image" value="<?php echo $promo['image'] ?? ''; ?>">
                <input type="hidden" name="type" value="hours">
                <input type="hidden" name="css_class" value="info-card">
                <!-- Campo oculto donde se serializa el JSON -->
                <textarea name="content" id="contentTextarea" class="d-none"><?php echo htmlspecialchars($promo['content'] ?? ''); ?></textarea>

                <div class="row">
                    <!-- Columna Izquierda: Editor de Días -->
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título del Banner</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($promo['title'] ?? 'Horarios de Atención'); ?>" required <?php echo $isEdit ? 'disabled' : ''; ?>>
                        </div>

                        <div class="schedule-grid space-y-2">
                            <?php 
                            $days = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                            $currentHours = json_decode($promo['content'] ?? '', true) ?: [];
                            foreach($days as $idx => $name): 
                                $h = $currentHours[$idx] ?? ['open' => '08:00', 'close' => '22:00', 'closed' => false];
                            ?>
                            <div class="day-row mb-2 <?php echo $h['closed'] ? 'is-closed' : ''; ?>" data-day="<?php echo $idx; ?>">
                                <p class="day-label"><?php echo $name; ?></p>
                                <div class="time-inputs">
                                    <input type="time" class="form-control form-control-sm open-time" value="<?php echo $h['open']; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                                    <span class="text-muted small">a</span>
                                    <input type="time" class="form-control form-control-sm close-time" value="<?php echo $h['close']; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input closed-toggle" type="checkbox" <?php echo $h['closed'] ? 'checked' : ''; ?> <?php echo $isEdit ? 'disabled' : ''; ?>>
                                    <label class="form-check-label closed-label <?php echo $h['closed'] ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo $h['closed'] ? 'Cerrado' : 'Abierto'; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Columna Derecha: Imagen y Estado -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <label class="form-label fw-bold">Imagen de Fondo (Banner)</label>
                                <div class="mb-3">
                                    <?php $displayImg = !empty($promo['image']) ? 'uploads/' . $promo['image'] : 'https://placehold.co/400x200?text=Sin+Imagen'; ?>
                                    <img id="imgPreview" src="<?php echo $displayImg; ?>" class="mb-2 shadow-sm">
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*" <?php echo $isEdit ? 'disabled' : ''; ?> onchange="previewImage(this)">
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo ($promo['is_active'] ?? true) ? 'checked' : ''; ?> <?php echo $isEdit ? 'disabled' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="isActive">Activar Horario en Web</label>
                                </div>
                                <p class="text-muted small mt-2">Si se desactiva, el local aparecerá siempre abierto o no mostrará el aviso en el carrusel.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="actions-area" class="<?php echo $isEdit ? 'd-none' : ''; ?> mt-4 pt-3 border-top text-end">
                    <button type="button" id="btn-cancel" class="btn btn-outline-secondary px-4 me-2">Cancelar</button>
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save me-2"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-horarios');
    const btnEnable = document.getElementById('btn-enable-edit');
    const btnCancel = document.getElementById('btn-cancel');
    const actionsArea = document.getElementById('actions-area');
    const inputs = form.querySelectorAll('input, select, textarea, .closed-toggle');
    const textarea = document.getElementById('contentTextarea');

    function serialize() {
        const schedule = {};
        document.querySelectorAll('.day-row').forEach(row => {
            const day = row.dataset.day;
            schedule[day] = {
                open: row.querySelector('.open-time').value,
                close: row.querySelector('.close-time').value,
                closed: row.querySelector('.closed-toggle').checked
            };
        });
        textarea.value = JSON.stringify(schedule);
    }

    if (btnEnable) {
        btnEnable.addEventListener('click', () => {
            inputs.forEach(i => i.disabled = false);
            actionsArea.classList.remove('d-none');
            btnEnable.classList.add('d-none');
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => window.location.reload());
    }

    form.addEventListener('change', (e) => {
        if (e.target.classList.contains('closed-toggle')) {
            const row = e.target.closest('.day-row');
            const label = row.querySelector('.closed-label');
            row.classList.toggle('is-closed', e.target.checked);
            label.innerText = e.target.checked ? "Cerrado" : "Abierto";
            label.className = `form-check-label closed-label ${e.target.checked ? 'text-danger' : 'text-success'}`;
        }
        serialize();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        serialize();
        Swal.fire({
            title: '¿Guardar horarios?',
            text: "Se actualizarán las reglas de apertura automática.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Revisar'
        }).then((res) => {
            if (res.isConfirmed) form.submit();
        });
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('imgPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>