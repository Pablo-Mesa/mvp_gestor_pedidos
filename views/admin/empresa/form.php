<?php $isEdit = isset($empresa); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?route=empresa">Empresa</a></li>
            <li class="breadcrumb-item active"><?php echo $isEdit ? 'Editar' : 'Configurar'; ?> Datos</li>
        </ol>
    </nav>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'Modificar' : 'Registrar'; ?> Datos Fiscales</h6>
        </div>
        <div class="card-body">
            <form id="form-empresa" action="?route=<?php echo $isEdit ? 'empresa_update' : 'empresa_store'; ?>" method="POST">
                <?php if($isEdit): ?><input type="hidden" name="id" value="<?php echo $empresa['id']; ?>"><?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Razón Social</label>
                        <input type="text" id="razon_social" name="razon_social" class="form-control" value="<?php echo $empresa['razon_social'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">RUC</label>
                        <input type="text" id="ruc_field" name="ruc" class="form-control" value="<?php echo $empresa['ruc'] ?? ''; ?>" placeholder="Ej: 80012345">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">DV</label>
                        <input type="text" name="dv" class="form-control" maxlength="1" value="<?php echo $empresa['dv'] ?? ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Timbrado Nº</label>
                        <input type="text" name="timbrado_vigente" class="form-control" maxlength="8" value="<?php echo $empresa['timbrado_vigente'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde_timbrado" class="form-control" value="<?php echo $empresa['fecha_desde_timbrado'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta_timbrado" class="form-control" value="<?php echo $empresa['fecha_hasta_timbrado'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?php echo (isset($empresa) && $empresa['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo (isset($empresa) && $empresa['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sucursal</label>
                        <input type="text" name="sucursal" class="form-control" value="<?php echo $empresa['sucursal'] ?? '001'; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Punto Emisión</label>
                        <input type="text" name="punto_emision" class="form-control" value="<?php echo $empresa['punto_emision'] ?? '001'; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $empresa['email'] ?? ''; ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                    <a href="?route=empresa" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Foco inicial
        const razonInput = document.getElementById('razon_social');
        if(razonInput) razonInput.focus();

        // Máscara automática para RUC (Limpia espacios y caracteres especiales, permite Alfanumérico)
        const rucInput = document.getElementById('ruc_field');
        if(rucInput) {
            rucInput.addEventListener('input', function(e) {
                // Eliminar cualquier cosa que no sea letras o números y pasar a mayúsculas
                this.value = this.value.toUpperCase().replace(/[^A-Z0-0]/g, '');
            });
        }

        // Capturar errores desde la URL para mostrar Toasts
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error === 'required_fields') {
            Toast.fire("Campo Obligatorio", "La Razón Social es requerida para continuar.", "error");
        } else if (error === 'invalid_data') {
            Toast.fire("Datos Inválidos", "Por favor revisa el formato del email o la coherencia de las fechas del timbrado.", "warning");
        }

        // Validación básica antes de enviar el formulario
        const form = document.getElementById('form-empresa');
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Detener el envío para mostrar confirmación

            if (!razonInput.value.trim()) {
                Toast.fire("Atención", "La Razón Social no puede estar vacía.", "warning");
                razonInput.classList.add('is-invalid');
                return;
            }

            // Confirmación con SweetAlert2
            Swal.fire({
                title: '¿Confirmar cambios?',
                text: "Se actualizará la información fiscal de la empresa.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-save"></i> Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>