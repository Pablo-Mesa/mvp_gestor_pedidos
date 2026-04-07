<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">⚙️ Ajustes de Identidad (Cliente)</h2>
    </div>

    <?php if(isset($_GET['success'])): 
        $msg = ($_GET['success'] == 'reset') ? 'Identidad restablecida a los valores por defecto.' : 'Configuración actualizada correctamente.';
    ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): 
        $error_msg = [
            'file_too_large' => 'La imagen es muy pesada (máx 1MB).',
            'invalid_image' => 'El archivo no es una imagen válida.',
            'dimensions_too_large' => 'La imagen es muy grande (máx 1200x1200px).'
        ];
        $msg = $error_msg[$_GET['error']] ?? 'Ocurrió un error inesperado.';
    ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4" style="border-left: 4px solid #0984e3;">
                <div class="card-body">
                    <form action="?route=settings_update" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nombre del Negocio</label>
                            <input type="text" name="site_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" 
                                   placeholder="Ej: Mi Comedor Gourmet">
                            <small class="text-muted">Este nombre aparecerá en la cabecera y el título de la web del cliente.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Logo de la Marca</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if(!empty($settings['site_logo'])): ?>
                                    <img src="uploads/<?php echo $settings['site_logo']; ?>" alt="Logo actual" 
                                         style="height: 60px; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; border-radius: 8px; border: 1px dashed #ccc;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="site_logo" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted">Se recomienda una imagen PNG transparente de 200x200px.</small>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="confirmReset()">
                                <i class="fas fa-undo"></i> Restablecer por defecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmReset() {
    confirmAction('?route=settings_reset', {
        title: '¿Restablecer Identidad?',
        message: 'Se borrará el logo cargado y el nombre volverá a "Solver". ¿Continuar?',
        btnText: 'Sí, restablecer',
        btnClass: 'btn-warning'
    });
}
</script>