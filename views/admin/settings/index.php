<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-brush me-2"></i>Ajustes de Marca</h1>
        <p class="text-muted">Personaliza la identidad visual y las funciones legales de tu plataforma.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                    <button type="button" id="btn-enable-edit" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-edit fa-sm me-1"></i> Modificar Ajustes
                    </button>
                </div>
                <div class="card-body">
                    <form id="form-settings" action="?route=settings_update" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nombre del Establecimiento</label>
                            <input type="text" id="site_name" name="site_name" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_name'] ?? 'Solver') ?>" required disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Logotipo de la Marca</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($settings['site_logo'])): ?>
                                    <img src="uploads/<?= $settings['site_logo'] ?>" id="current-logo" alt="Logo" class="img-thumbnail" style="height: 60px;">
                                <?php endif; ?>
                                <input type="file" name="site_logo" class="form-control" accept="image/*" disabled>
                            </div>
                        </div>

                        <div id="actions-area" class="d-none pt-3 border-top">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <button type="button" id="btn-cancel-edit" class="btn btn-outline-secondary ms-2">Cancelar</button>
                            <button type="button" class="btn btn-link text-muted" onclick="confirmResetSettings()">
                                Restaurar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 text-center">
            <div class="card shadow mb-4 text-center">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vista Previa</h6>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="uploads/<?= $settings['site_logo'] ?>" style="height: 100px; object-fit: contain;" alt="Logo">
                        <?php else: ?>
                            <img src="assets/icono_solver_nobg.png" style="height: 100px; opacity: 0.3;" alt="Default Logo">
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold"><?= htmlspecialchars($settings['site_name'] ?? 'Solver') ?></h4>
                    <p class="text-muted small">Así se verá tu marca en el portal del cliente.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-settings');
    const btnEnable = document.getElementById('btn-enable-edit');
    const btnCancel = document.getElementById('btn-cancel-edit');
    const actionsArea = document.getElementById('actions-area');
    const inputs = form.querySelectorAll('input');

    if (btnEnable) {
        btnEnable.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = false);
            actionsArea.classList.remove('d-none');
            btnEnable.classList.add('d-none');
            document.getElementById('site_name').focus();
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => window.location.reload());
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar cambios?',
            text: "Se actualizará la identidad visual del portal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Revisar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

function confirmResetSettings() {
    Swal.fire({
        title: '¿Restaurar valores?',
        text: "Esta acción restablecerá el nombre y el logotipo a los valores originales del sistema.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2d3436',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar',
        focusConfirm: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?route=settings_reset';
        }
    });
}
</script>