<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-shopping-cart me-2"></i>Ajustes de Checkout</h1>
        <p class="text-muted">Controla los módulos y la información requerida al finalizar un pedido.</p>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Módulos Activos</h6>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Configuración actualizada correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="?route=settings_update" method="POST">
                        <input type="hidden" name="checkout_settings" value="1">
                        
                        <div class="mb-4 p-3 border rounded bg-light">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="enable_legal_invoice" value="1" 
                                       id="flexSwitchInvoice" <?= ($settings['enable_legal_invoice'] ?? '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="flexSwitchInvoice">
                                    Habilitar solicitud de Factura Legal
                                </label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">
                                Al activar esta opción, se mostrará un módulo en el checkout donde el cliente podrá 
                                ingresar su RUC y Razón Social para recibir un comprobante legal.
                            </p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>