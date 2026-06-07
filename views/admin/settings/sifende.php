<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice me-2"></i>Facturación Electrónica (SIFEN)</h1>
        <p class="text-muted">Configura la integración con la API de Sifende para emitir comprobantes legales ante la DNIT.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Credenciales de API (Sandbox / Producción)</h6>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Credenciales actualizadas correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=settings_update" method="POST">
                        <input type="hidden" name="sifende_settings" value="1">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">App ID</label>
                                <input type="text" name="sifende_app_id" class="form-control" 
                                       value="<?= htmlspecialchars($settings['sifende_app_id'] ?? '') ?>" 
                                       placeholder="Provisto por Sifende" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">App Key (Secret Key)</label>
                                <input type="password" name="sifende_app_key" class="form-control" 
                                       value="<?= htmlspecialchars($settings['sifende_app_key'] ?? '') ?>" 
                                       placeholder="••••••••••••" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">URL del Endpoint</label>
                                <input type="url" name="sifende_api_url" class="form-control" 
                                       value="<?= htmlspecialchars($settings['sifende_api_url'] ?? 'https://api.sifende.com.py/v1/test/emitir') ?>" required>
                                <div class="form-text">Usa el endpoint de <strong>test</strong> para tus primeras pruebas. Cambia a producción solo tras la aprobación de la SET.</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex border-top pt-3">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm">
                <h6 class="fw-bold"><i class="fas fa-lightbulb me-2"></i>Soporte Técnico</h6>
                <p class="small mb-0">Recuerda que para que el timbrado sea válido, los datos de <strong>Empresa</strong> (RUC, DV y Punto de Emisión) deben coincidir con los configurados en el portal de Sifende.</p>
            </div>
        </div>
    </div>
</div>