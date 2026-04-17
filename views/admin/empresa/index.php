<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Datos de la Empresa</h2>
        <?php if (empty($empresas)): ?>
            <a href="?route=empresa_create" class="btn btn-primary"><i class="fas fa-plus"></i> Registrar Empresa</a>
        <?php endif; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Razón Social</th>
                            <th>RUC</th>
                            <th>Establecimiento</th>
                            <th>Timbrado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $e): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($e['razon_social']); ?></strong></td>
                            <td><?php echo htmlspecialchars($e['ruc'] . '-' . $e['dv']); ?></td>
                            <td><?php echo htmlspecialchars($e['sucursal'] . '-' . $e['punto_emision']); ?></td>
                            <td><?php echo htmlspecialchars($e['timbrado_vigente']); ?></td>
                            <td>
                                <span class="badge <?php echo $e['estado'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $e['estado'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?route=empresa_edit&id=<?php echo $e['id']; ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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