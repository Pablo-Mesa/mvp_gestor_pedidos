<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">🚚 Tarifas de Delivery por Distancia</h2>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php 
                if($_GET['success'] == 'active_changed') echo "La tarifa seleccionada ahora es la activa.";
                else echo "Tarifas actualizadas correctamente.";
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Historial de Versiones -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history"></i> Historial de Versiones</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($allRates as $r): ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= (isset($activeRate['id']) && $activeRate['id'] == $r['id']) ? 'bg-light border-start border-primary border-4' : '' ?>">
                                <div onclick="window.location.href='?route=settings_delivery&id=<?= $r['id'] ?>'" style="cursor:pointer; flex:1;">
                                    <div class="small fw-bold text-dark">Versión #<?= $r['id'] ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
                                    <?php if ($r['is_active']): ?>
                                        <span class="badge bg-success text-white shadow-sm" style="font-size: 0.65rem;">ACTIVA</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    <?php if (!$r['is_active']): ?>
                                        <button onclick="event.stopPropagation(); confirmActivation(<?= $r['id'] ?>)" class="btn btn-sm btn-outline-success" title="Establecer como activa">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="?route=settings_delivery&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Ver/Editar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuración de Rangos -->
        <div class="col-md-8">
            <div class="card shadow mb-4" style="border-left: 4px solid #0984e3;">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= isset($activeRate['id']) ? 'Detalles de Versión #' . $activeRate['id'] : 'Crear Nueva Versión' ?>
                    </h6>
                    <a href="?route=settings_delivery&new=1" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nueva Versión
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($activeRate): ?>
                        <div class="alert alert-info py-2 small">
                            Viendo datos creados por <strong><?= htmlspecialchars($activeRate['creator_name']) ?></strong> 
                            el <?= date('d/m/Y H:i', strtotime($activeRate['created_at'])) ?>. 
                            <?php if($activeRate['is_active'] ?? false): ?><span class="badge bg-success ms-1">Actualmente en uso</span><?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted small">
                        Define los rangos de distancia. Al guardar, se creará una nueva versión activa.
                    </p>

                    <form action="?route=settings_delivery_update" method="POST">
                        <div id="rates-container">
                            <?php 
                            $rates = $activeRate['details'] ?? [];
                            $isPlaceholder = empty($rates);
                            // Si está vacío, mostramos una fila vacía con placeholders
                            if ($isPlaceholder) $rates = [['km_from' => '', 'km_to' => '', 'price' => '']];
                            ?>

                            <?php foreach ($rates as $index => $rate): ?>
                                <div class="row mb-3 rate-row align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Desde (km)</label>
                                        <input type="number" step="0.1" name="km_start[]" class="form-control" value="<?= $rate['km_from'] ?>" placeholder="<?= $isPlaceholder ? '0' : '' ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Hasta (km)</label>
                                        <input type="number" step="0.1" name="km_end[]" class="form-control" value="<?= $rate['km_to'] ?>" placeholder="<?= $isPlaceholder ? '5' : '' ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Precio (Gs.)</label>
                                        <input type="number" name="price[]" class="form-control" value="<?= $rate['price'] ?>" placeholder="<?= $isPlaceholder ? '10000' : '' ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.rate-row').remove()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mb-4" onclick="addRateRow()">
                            <i class="fas fa-plus"></i> Agregar Rango
                        </button>

                        <div class="pt-3 border-top">
                            <button type="submit" class="btn btn-success px-5">
                                <i class="fas fa-save"></i> Guardar Tarifas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function confirmActivation(id) {
    const { isConfirmed } = await Swal.fire({
        title: '¿Activar Versión #' + id + '?',
        text: "Esto cambiará los precios de envío para todos los clientes inmediatamente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Sí, activar ahora',
        cancelButtonText: 'Cancelar'
    });

    if (isConfirmed) {
        window.location.href = '?route=settings_delivery_activate&id=' + id;
    }
}

function addRateRow() {
    const container = document.getElementById('rates-container');
    const div = document.createElement('div');
    div.className = 'row mb-3 rate-row align-items-end';
    div.innerHTML = `
        <div class="col-md-3">
            <label class="form-label small fw-bold">Desde (km)</label>
            <input type="number" step="0.1" name="km_start[]" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Hasta (km)</label>
            <input type="number" step="0.1" name="km_end[]" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Precio (Gs.)</label>
            <input type="number" name="price[]" class="form-control" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.rate-row').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>`;
    container.appendChild(div);
}
</script>