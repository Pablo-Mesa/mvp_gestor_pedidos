<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-truck-loading me-2"></i>Tarifas de Delivery por Distancia</h2>
        <div id="status-badge-container">
             <!-- Dinámico via JS -->
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if(isset($_GET['activated'])): ?>
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Versión Activada',
                            text: 'Los nuevos precios de delivery ya están en vigor.',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    });
                </script>
            <?php endif; ?>

            <?php 
                $isViewing = (isset($activeRate['id']) && !isset($_GET['new']));
                $isNew = isset($_GET['new']) || !isset($activeRate['id']);
                $isEditing = $isViewing && isset($_GET['edit']);
            ?>

            <!-- Panel Principal: Editor de Tarifas -->
            <div class="card shadow mb-5 <?= $isEditing ? 'border-left-warning' : ($isViewing ? 'border-left-info' : 'border-left-primary') ?>">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas <?= $isViewing ? 'fa-eye' : 'fa-plus-circle' ?> me-2"></i>
                        <?= $isEditing ? 'Editando Versión #' . $activeRate['id'] : ($isViewing ? 'Visualizando Versión #' . $activeRate['id'] : 'Crear Nueva Versión de Tarifas') ?>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($isViewing): ?>
                            <?php if (!$isEditing): ?>
                                <a href="?route=settings_delivery&id=<?= $activeRate['id'] ?>&edit=1" class="btn btn-sm btn-outline-info shadow-sm" title="Agregar más rangos a esta versión">
                                    <i class="fas fa-edit me-1"></i>Habilitar Edición
                                </a>
                            <?php else: ?>
                                <a href="?route=settings_delivery&id=<?= $activeRate['id'] ?>" class="btn btn-sm btn-outline-secondary shadow-sm">
                                    <i class="fas fa-times me-1"></i>Cancelar Edición
                                </a>
                            <?php endif; ?>
                            <?php if ((int)$activeRate['is_active'] !== 1): ?>
                                <button type="button" onclick="confirmActivation(<?= $activeRate['id'] ?>)" class="btn btn-sm btn-success shadow-sm me-2">
                                    <i class="fas fa-check me-1"></i>Activar esta Versión
                                </button>
                            <?php else: ?>
                                <span class="badge bg-success py-2 px-3 me-2"><i class="fas fa-check-circle me-1"></i>ACTIVA</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="?route=settings_delivery&new=1" class="btn btn-sm btn-primary shadow-sm" title="Iniciar una configuración desde cero">
                            <i class="fas fa-plus me-1"></i> Nuevo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($activeRate && !isset($_GET['new'])): ?>
                        <div class="alert <?= $isViewing ? 'alert-info' : 'alert-light' ?> border py-2 small mb-4">
                            <i class="fas fa-info-circle text-info"></i> 
                            Viendo datos creados por <strong><?= htmlspecialchars($activeRate['creator_name']) ?></strong> 
                            el <?= date('d/m/Y H:i', strtotime($activeRate['created_at'])) ?>. 
                        </div>
                    <?php endif; ?>

                    <h6 class="small font-weight-bold mb-3 text-uppercase text-muted">Configuración de Rangos</h6>

                    <form id="delivery-rates-form" action="?route=<?= $isEditing ? 'settings_delivery_add_ranges' : 'settings_delivery_update' ?>" method="POST">
                        <?php if ($isEditing): ?>
                            <input type="hidden" name="rate_id" value="<?= $activeRate['id'] ?>">
                        <?php endif; ?>
                        <div id="rates-container">
                            <?php 
                            $rates = ($isNew && !isset($_GET['id'])) ? [] : ($activeRate['details'] ?? []);
                            $isPlaceholder = empty($rates) && $isNew;
                            if ($isPlaceholder) $rates = [['km_from' => '', 'km_to' => '', 'price' => '']];
                            ?>

                            <?php foreach ($rates as $index => $rate): ?>
                                <div class="row mb-3 rate-row align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Desde (km)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="number" step="0.1" name="km_start[]" class="form-control km-start" value="<?= $rate['km_from'] ?>" placeholder="0" required <?= $isViewing ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Hasta (km)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="fas fa-flag-checkered"></i></span>
                                            <input type="number" step="0.1" name="km_end[]" class="form-control km-end" value="<?= $rate['km_to'] ?>" placeholder="5" required <?= $isViewing ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Precio (Gs.)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₲</span>
                                            <input type="number" name="price[]" class="form-control" value="<?= $rate['price'] ?>" placeholder="10000" required <?= $isViewing ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                    <?php if (!$isViewing): ?>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.rate-row').remove()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!$isViewing || $isEditing): ?>
                            <button type="button" class="btn btn-outline-primary btn-sm mb-4" onclick="addRateRow()">
                                <i class="fas fa-plus-circle"></i> Agregar Rango
                            </button>

                            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                                <?php if ($isEditing): ?>
                                    <a href="?route=settings_delivery&id=<?= $activeRate['id'] ?>" class="btn btn-outline-secondary px-4 shadow-sm">
                                        Cancelar
                                    </a>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm" id="btn-save-rates">
                                    <i class="fas fa-save me-2"></i><?= $isEditing ? 'Guardar Cambios' : 'Generar Nueva Versión' ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Sección Inferior: Historial de Versiones -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history me-1"></i> Historial de Cambios</h6>
                    <small class="text-muted">Haz clic en "Abrir" para cargar una versión anterior y usarla como base.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Versión</th>
                                    <th>Fecha de Registro</th>
                                    <th>Autor</th>
                                    <th>Estado Actual</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allRates as $r): ?>
                                    <?php 
                                        $isActive = (int)$r['is_active'] === 1;
                                        $isViewed = (isset($activeRate['id']) && $activeRate['id'] == $r['id'] && !isset($_GET['new']));
                                    ?>
                                    <tr class="<?= $isViewed ? 'table-primary' : '' ?>" style="<?= $isViewed ? '--bs-table-bg: #f0f7ff;' : '' ?>">
                                        <td class="ps-4 fw-bold">#<?= $r['id'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($r['creator_name']) ?></td>
                                        <td>
                                            <?= $isActive ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-light text-dark border">Inactiva</span>' ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="?route=settings_delivery&id=<?= $r['id'] ?>" class="btn btn-sm <?= $isViewed ? 'btn-primary' : 'btn-outline-primary' ?>">
                                                <i class="fas fa-folder-open me-1"></i>Abrir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('delivery-rates-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validación básica de lógica de rangos
    const rows = document.querySelectorAll('.rate-row');
    let valid = true;
    rows.forEach(row => {
        const start = parseFloat(row.querySelector('.km-start').value);
        const end = parseFloat(row.querySelector('.km-end').value);
        if (end <= start) {
            valid = false;
            row.querySelector('.km-end').classList.add('is-invalid');
        } else {
            row.querySelector('.km-end').classList.remove('is-invalid');
        }
    });

    if (!valid) {
        Swal.fire('Error de Rango', 'La distancia "Hasta" debe ser mayor que "Desde".', 'error');
        return;
    }

    const btn = document.getElementById('btn-save-rates');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });

        const contentType = response.headers.get("content-type");
        const rawText = await response.text();
        let result;

        if (contentType && contentType.indexOf("application/json") !== -1) {
            try {
                result = JSON.parse(rawText);
            } catch (e) {
                throw new Error("El servidor envió datos corruptos (JSON malformado).");
            }
        } else {
            if (rawText.includes('name="login"') || rawText.includes('login-form')) {
                throw new Error("Tu sesión ha expirado. Por favor, vuelve a iniciar sesión.");
            }
            throw new Error("El servidor respondió con una página web. Verifica que la ruta 'settings_delivery_update' esté registrada en index.php.");
        }

        if (response.ok && result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Tarifas Actualizadas',
                text: 'Se ha creado una nueva versión de tarifas correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
            const newId = result.id || '';
            window.location.href = `./index.php?route=settings_delivery&id=${newId}&success=1`;
        } else {
            throw new Error(result.message || "Error al guardar la nueva versión.");
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error al Guardar',
            text: error.message
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

async function confirmActivation(id) {
    const { isConfirmed } = await Swal.fire({
        title: '¿Activar Versión #' + id + '?',
        text: "Esto cambiará los precios de envío para todos los clientes inmediatamente.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Sí, activar ahora',
        cancelButtonText: 'Cancelar'
    });

    if (isConfirmed) {
        Swal.fire({
            title: 'Activando Versión...',
            didOpen: () => { Swal.showLoading(); },
            allowOutsideClick: false
        });

        try {
            const response = await fetch(`./index.php?route=settings_delivery_activate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });

            const contentType = response.headers.get("content-type");
            const rawText = await response.text();
            let result;
            
            if (contentType && contentType.indexOf("application/json") !== -1) {
                try {
                    result = JSON.parse(rawText);
                } catch (e) {
                    throw new Error("El servidor envió un JSON malformado.");
                }
            } else {
                // Si recibimos HTML, es muy probable que la sesión haya expirado o la ruta sea incorrecta
                if (rawText.includes('name="login"') || rawText.includes('login-form')) {
                    throw new Error("Tu sesión ha expirado. Por favor, vuelve a iniciar sesión en otra pestaña y reintenta.");
                }
                console.error("Contenido HTML recibido inesperadamente:", rawText);
                throw new Error("El servidor respondió con una página web en lugar de datos. Verifica que la ruta 'settings_delivery_activate' esté registrada en index.php.");
            }

            if (response.ok && result.success) {
                window.location.href = `./index.php?route=settings_delivery&id=${id}&activated=1`;
            } else {
                throw new Error(result.message || 'Error desconocido al activar.');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Activación',
                text: error.message || 'Ocurrió un problema al conectar con el sistema.'
            });
        }
    }
}

function addRateRow() {
    const container = document.getElementById('rates-container');
    const div = document.createElement('div');
    div.className = 'row mb-3 rate-row align-items-end';
    div.innerHTML = `
        <div class="col-md-3">
            <label class="form-label small text-muted">Desde (km)</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <input type="number" step="0.1" name="km_start[]" class="form-control km-start" required>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Hasta (km)</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-flag-checkered"></i></span>
                <input type="number" step="0.1" name="km_end[]" class="form-control km-end" required>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Precio (Gs.)</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">₲</span>
                <input type="number" name="price[]" class="form-control" required>
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.rate-row').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>`;
    container.appendChild(div);
}
</script>