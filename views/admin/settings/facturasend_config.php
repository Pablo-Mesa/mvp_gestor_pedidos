<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        
        <!-- Card Principal -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-file-invoice text-primary fs-4"></i>
                    </div>
                    <div>
                        <h3 class="card-title mb-1 fw-bold text-dark">Configuración FacturaSend</h3>
                        <p class="text-muted mb-0 small">Integración con SIFEN - Facturación Electrónica</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success d-flex align-items-center border-0 shadow-sm">
                        <i class="fas fa-check-circle me-3 fs-5"></i>
                        <div>
                            <strong>¡Éxito!</strong> Configuración guardada correctamente.
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm">
                        <i class="fas fa-exclamation-circle me-3 fs-5"></i>
                        <div>
                            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="?route=facturasend_guardar_config">
                    <div class="mb-4">
                        <label for="tenant_id" class="form-label fw-semibold">Tenant ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                            <input type="text" class="form-control" id="tenant_id" name="tenant_id" 
                                    value="<?php echo htmlspecialchars($config['tenant_id'] ?? ''); ?>" 
                                    placeholder="Ej: tu-tenant-id" required>
                        </div>
                        <small class="text-muted">Identificador de tu cuenta en FacturaSend</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="api_key" class="form-label fw-semibold">API Key</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" class="form-control" id="api_key" name="api_key" 
                                    value="<?php echo htmlspecialchars($config['tenant_id'] ? '••••••••••••' : ''); ?>" 
                                    placeholder="Ingresa tu API Key" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKey()">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Clave de API proporcionada por FacturaSend</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="modo" class="form-label fw-semibold">Modo de Operación</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-cog text-muted"></i></span>
                            <select class="form-select" id="modo" name="modo">
                                <option value="sandbox" <?php echo ($config['modo'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>
                                    <i class="fas fa-flask me-2"></i> Sandbox (Pruebas)
                                </option>
                                <option value="production" <?php echo ($config['modo'] ?? '') === 'production' ? 'selected' : ''; ?>>
                                    <i class="fas fa-rocket me-2"></i> Producción
                                </option>
                            </select>
                        </div>
                        <small class="text-muted">Usa Sandbox para pruebas antes de ir a producción</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Estado de Configuración</label>
                        <div class="alert <?php echo $config['configured'] ?? false ? 'alert-success' : 'alert-warning'; ?> d-flex align-items-center border-0">
                            <i class="fas <?php echo $config['configured'] ?? false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-3 fs-5"></i>
                            <div>
                                <strong><?php echo $config['configured'] ?? false ? 'Configurado' : 'No configurado'; ?></strong>
                                <p class="mb-0 small text-muted">
                                    <?php echo $config['configured'] ?? false ? 'El sistema está listo para emitir facturas electrónicas.' : 'Completa los campos arriba para habilitar la facturación electrónica.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">API URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-globe text-muted"></i></span>
                            <input type="text" class="form-control bg-light" 
                                    value="<?php echo htmlspecialchars($config['api_url'] ?? ''); ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Guardar Configuración
                        </button>
                        <a href="?route=admin" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Card de Instrucciones -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h4 class="card-title fw-bold text-dark">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Instrucciones
                </h4>
            </div>
            <div class="card-body p-4">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">
                        Regístrate en <a href="https://facturasend.com.py" target="_blank" class="text-primary text-decoration-none fw-semibold">
                            FacturaSend <i class="fas fa-external-link-alt small ms-1"></i>
                        </a> para obtener tus credenciales
                    </li>
                    <li class="mb-2">Copia el <strong>Tenant ID</strong> de tu cuenta</li>
                    <li class="mb-2">Genera una <strong>API Key</strong> en el panel de FacturaSend</li>
                    <li class="mb-2">Completa los campos arriba y selecciona el modo <strong>Sandbox</strong> para pruebas</li>
                    <li>Una vez configurado, podrás emitir facturas electrónicas desde el POS</li>
                </ol>
            </div>
        </div>
        
    </div>
</div>


<script>
function toggleApiKey() {
    const apiKeyInput = document.getElementById('api_key');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (apiKeyInput.type === 'password') {
        apiKeyInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        apiKeyInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
