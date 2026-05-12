<?php 
// Obtenemos la primera (y única) empresa si existe
$empresa = !empty($empresas) ? $empresas[0] : null; 
$isEdit = $empresa !== null;

// Sanitizar fechas "0000-00-00" de MySQL que causan errores en inputs tipo date de HTML5
if ($isEdit) {
    if (($empresa['fecha_desde_timbrado'] ?? '') === '0000-00-00') $empresa['fecha_desde_timbrado'] = '';
    if (($empresa['fecha_hasta_timbrado'] ?? '') === '0000-00-00') $empresa['fecha_hasta_timbrado'] = '';
}
?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?route=admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Datos de la Empresa</li>
        </ol>
    </nav>

    <div class="mb-3">
        <h2 class="h4 mb-0 text-gray-800">Configuración General</h2>
        <p class="text-muted small">Administra la información legal y puntos de expedición para los comprobantes de venta.</p>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-building me-2"></i>Información Fiscal y de Contacto
            </h6>
            <?php if ($isEdit): ?>
                <button type="button" id="btn-enable-edit" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-edit fa-sm me-1"></i> Modificar Datos
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="form-empresa" action="?route=<?php echo $isEdit ? 'empresa_update' : 'empresa_store'; ?>" method="POST">
                <?php if($isEdit): ?><input type="hidden" name="id" value="<?php echo $empresa['id']; ?>"><?php endif; ?>
                
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label fw-bold">Razón Social</label>
                        <input type="text" id="razon_social" name="razon_social" class="form-control" value="<?php echo $empresa['razon_social'] ?? ''; ?>" required <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">RUC</label>
                        <input type="text" id="ruc_field" name="ruc" class="form-control" value="<?php echo $empresa['ruc'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">DV</label>
                        <input type="text" name="dv" class="form-control text-center" maxlength="1" value="<?php echo $empresa['dv'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Timbrado Nº</label>
                        <input type="text" name="timbrado_vigente" class="form-control" maxlength="8" value="<?php echo $empresa['timbrado_vigente'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Vigencia Desde</label>
                        <input type="date" name="fecha_desde_timbrado" class="form-control" value="<?php echo $empresa['fecha_desde_timbrado'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Vigencia Hasta</label>
                        <input type="date" name="fecha_hasta_timbrado" class="form-control" value="<?php echo $empresa['fecha_hasta_timbrado'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Establecimiento (Suc.)</label>
                        <input type="text" name="sucursal" class="form-control text-center" maxlength="3" value="<?php echo $empresa['sucursal'] ?? '001'; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Punto Emisión</label>
                        <input type="text" name="punto_emision" class="form-control text-center" maxlength="3" value="<?php echo $empresa['punto_emision'] ?? '001'; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $empresa['email'] ?? ''; ?>" <?php echo $isEdit ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-select" <?php echo $isEdit ? 'disabled' : ''; ?>>
                            <option value="1" <?php echo (isset($empresa) && $empresa['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo (isset($empresa) && $empresa['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div id="actions-area" class="<?php echo $isEdit ? 'd-none' : ''; ?> mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                    <?php if ($isEdit): ?>
                        <button type="button" id="btn-cancel-edit" class="btn btn-outline-secondary ms-2">Cancelar</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const status = "<?php echo $_GET['success']; ?>";
        if (status === 'created') {
            Toast.fire("¡Registrado!", "Los datos de la empresa se han guardado correctamente.", "success");
        } else if (status === 'updated') {
            Toast.fire("¡Actualizado!", "Los datos de la empresa se han actualizado correctamente.", "success");
        }
        // Limpiar la URL para evitar que la alerta salga de nuevo al recargar
        window.history.replaceState({}, document.title, "?route=empresa");
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-empresa');
    const btnEnable = document.getElementById('btn-enable-edit');
    const btnCancel = document.getElementById('btn-cancel-edit');
    const actionsArea = document.getElementById('actions-area');
    const inputs = form.querySelectorAll('.form-control, .form-select');

    // Habilitar edición
    if (btnEnable) {
        btnEnable.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = false);
            actionsArea.classList.remove('d-none');
            btnEnable.classList.add('d-none');
            document.getElementById('razon_social').focus();
        });
    }

    // Cancelar (recargar para limpiar estados)
    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            window.location.reload();
        });
    }

    // Máscara RUC
    const rucInput = document.getElementById('ruc_field');
    if(rucInput) {
        rucInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar cambios?',
            text: "Se actualizará la información fiscal de la empresa.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                inputs.forEach(input => input.disabled = false);
                form.submit();
            }
        });
    });
});
</script>