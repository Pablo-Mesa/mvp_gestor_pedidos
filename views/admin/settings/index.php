<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-brush me-2"></i>Ajustes de Marca</h1>
        <p class="text-muted">Personaliza la identidad visual y las funciones legales de tu plataforma.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                </div>
                <div class="card-body">
                    <form action="?route=settings_update" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nombre del Establecimiento</label>
                            <input type="text" name="site_name" class="form-control" 
                                   value="<?= htmlspecialchars($settings['site_name'] ?? 'Solver') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Logotipo de la Marca</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($settings['site_logo'])): ?>
                                    <img src="uploads/<?= $settings['site_logo'] ?>" alt="Logo" class="img-thumbnail" style="height: 60px;">
                                <?php endif; ?>
                                <input type="file" name="site_logo" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                            <a href="?route=settings_reset" class="btn btn-link text-muted" onclick="return confirm('¿Restaurar valores por defecto?')">Restaurar</a>
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