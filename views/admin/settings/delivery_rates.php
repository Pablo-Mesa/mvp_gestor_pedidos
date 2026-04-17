<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">🚚 Tarifas de Delivery por Distancia</h2>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Tarifas actualizadas correctamente.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-10">
            <div class="card shadow mb-4" style="border-left: 4px solid #0984e3;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Configurar Rangos y Precios</h6>
                </div>
                <div class="card-body">
                    <?php if ($activeRate): ?>
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-history"></i> 
                            Versión actual creada por <strong><?= htmlspecialchars($activeRate['creator_name']) ?></strong> 
                            el <?= date('d/m/Y H:i', strtotime($activeRate['created_at'])) ?>.
                        </div>
                    <?php endif; ?>

                    <p class="text-muted small">
                        Define los rangos de distancia. Al guardar, se creará una nueva versión activa.
                    </p>

                    <form action="?route=settings_delivery_update" method="POST">
                        <div id="rates-container">
                            <?php 
                            $rates = $activeRate['details'] ?? [];
                            if (empty($rates)): ?>
                                <?php 
                                $rates = [
                                    ['km_from' => 0, 'km_to' => 5, 'price' => 10000]
                                ];
                                ?>
                            <?php endif; ?>

                            <?php foreach ($rates as $index => $rate): ?>
                                <div class="row mb-3 rate-row align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Desde (km)</label>
                                        <input type="number" step="0.1" name="km_start[]" class="form-control" value="<?= $rate['km_from'] ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Hasta (km)</label>
                                        <input type="number" step="0.1" name="km_end[]" class="form-control" value="<?= $rate['km_to'] ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Precio (Gs.)</label>
                                        <input type="number" name="price[]" class="form-control" value="<?= $rate['price'] ?>" required>
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