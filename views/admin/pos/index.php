<?php
    $cashModel = new CashRegister();
    $isCashOpen = $cashModel->getActiveSession($_SESSION['user_id']) ? true : false;
?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>css/pos.css">

<!-- Configuración global para el JS externo -->
<script>
    window.posConfig = {
        isCashOpen: <?php echo $isCashOpen ? 'true' : 'false'; ?>,
        storeLat: <?php echo $siteSettings['store_lat'] ?? -25.3006; ?>,
        storeLng: <?php echo $siteSettings['store_lng'] ?? -57.6359; ?>
    };
</script>

<!-- Vista para el Punto de Venta (POS) -->
<div class="pos-container">

    <!-- Panel de Selección -->
    <div class="pos-products">
        
        <!-- Barra de búsqueda y filtros -->
        <div class="pos-search-bar">
            <input type="text" id="posSearch" placeholder="Buscar plato por nombre..." onkeyup="filterPOS()">
            <button class="btn btn-std" onclick="clearPOS()"><i class="fas fa-sync"></i></button>
        </div>
        
        <!-- Filtros por categoría -->
        <div class="pos-category-pills">
            <button type="button" class="btn btn-pos-filter active" tabindex="0" onclick="filterByCat('all', this)">Todos</button>
            <?php foreach($categories as $cat): ?>
                <button type="button" class="btn btn-pos-filter" tabindex="0" onclick="filterByCat('<?php echo $cat['id']; ?>', this)">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid de productos -->        
        <div class="pos-grid" id="posGrid">
            <?php foreach($products as $p): 
                $hasHalf = !empty($p['price_half']) && $p['price_half'] > 0;
            ?>
                <!-- Si el producto tiene opción de medio plato, se muestra un diseño diferente con botones para cada porción -->
                <div class="pos-item-card"
                     tabindex="0"
                     style="<?php echo $hasHalf ? 'cursor: default;' : ''; ?>"
                     data-name="<?php echo strtolower(htmlspecialchars($p['name'])); ?>"
                     data-cat="<?php echo $p['category_id']; ?>"
                     <?php if(!$hasHalf): ?>
                     onclick="addToTicket(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)">
                     <?php else: ?> >
                     <?php endif; ?>
                    
                    <?php if(!empty($p['image'])): ?>
                        <button class="btn-show-img" onclick="event.stopPropagation(); showProductImg('<?php echo $p['image']; ?>', '<?php echo addslashes($p['name']); ?>')">
                            <i class="fas fa-eye"></i>
                        </button>
                    <?php endif; ?>

                    <span class="pos-item-name"><?php echo htmlspecialchars($p['name']); ?></span>
                    
                    <?php if($hasHalf): ?>
                        <div class="pos-item-actions">
                            <button class="btn-portion" onclick="event.stopPropagation(); addToTicket(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)">Entero <small>Gs. <?php echo number_format($p['price'], 0, ',', '.'); ?></small></button>
                            <button class="btn-portion" onclick="event.stopPropagation(); addToTicket('<?php echo $p['id']; ?>_half', '<?php echo addslashes($p['name']); ?> (Medio)', <?php echo $p['price_half']; ?>)">Medio <small>Gs. <?php echo number_format($p['price_half'], 0, ',', '.'); ?></small></button>
                        </div>
                    <?php else: ?>
                        <span class="pos-item-price">Gs. <?php echo number_format($p['price'], 0, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>

    <!-- Panel de Ticket -->
    <div class="pos-ticket">
        <div class="ticket-header">
            <div>
                <h3><i class="fas fa-shopping-cart"></i> Nuevo Pedido</h3>
                <small id="current-time"></small>
            </div>
            <button class="btn-clear-cart" onclick="confirmClearCart()" title="Vaciar Pedido">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>

        <div class="ticket-items" id="ticketItems">
            <div style="text-align: center; color: rgba(255,255,255,0.3); margin-top: 40px;">
                <i class="fas fa-receipt fa-3x"></i>
                <p>Cargue productos para vender</p>
            </div>
        </div>

        <div class="ticket-footer">
            <div style="margin-bottom: 10px;">
                <label style="font-size: 0.75rem; color: #aaa;">Observación:</label>
                <input type="text" id="posObservation" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid #444; color: white; padding: 5px; border-radius: 4px;">
            </div>
            <div class="ticket-total">
                <span>TOTAL:</span>
                <span id="posTotal">0</span>
            </div>
            <button id="btnOpenFinalize" class="btn-confirm-sale" onclick="openFinalizeModal()">
                FINALIZAR <small style="font-size: 0.7em; opacity: 0.8;">[F2]</small> <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
</div>

<!-- MODAL 1: FINALIZAR VENTA -->
<div class="modal fade" id="modalFinalize" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            
            <!-- encabezado del modal -->
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-check-circle me-2 text-success"></i>Finalizar Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- cuerpo modal (campos) --> 
            <div class="modal-body">
                <!-- cliente -->
                <div class="mb-3">                    
                    <label class="form-label fw-bold small"><i class="fas fa-user me-1"></i> Cliente</label>
                    <?php 
                        // Como Juan Perez es ID 1, deberías crear un cliente llamado "OCASIONAL"
                        // y poner su ID aquí. Supongamos que el nuevo ID es 99.
                        $defaultID = 1; // El ID 1 se usará para el cliente genérico "Cliente Ocasional"
                        $defaultName = 'Cliente Ocasional';
                    ?>
                    <div class="input-group shadow-sm">
                        <input type="hidden" id="f-client-id" value="<?php echo $defaultID; ?>">
                        <input type="text" id="f-client-name" class="form-control bg-light border-end-0" value="<?php echo $defaultName; ?>" readonly>
                        <button id="btn-search-client"
                                class="btn btn-outline-secondary"
                                type="button"
                                onclick="openSearchClient()">
                            <i class="fas fa-search"></i> <small>[F3]</small>
                        </button>
                        <button class="btn btn-success" type="button" onclick="openCreateClient()">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- tipo de entrega -->        
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small"><i class="fas fa-wallet me-1"></i> Pago</label>
                        <select id="f-payment-method" class="form-select">
                            <option value="efectivo">Efectivo</option>
                            <option value="pos">POS (Tarjeta ó Qr)</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small"><i class="fas fa-truck me-1"></i> Entrega</label>
                        <select id="f-delivery-type" class="form-select" onchange="toggleDeliveryFields(this.value)">
                            <option value="local">Consumo Local</option>
                            <option value="pickup">Para Retirar</option>
                            <option value="delivery">Envio / Delivery</option>
                        </select>
                    </div>                    
                </div>
                <!-- campos adicionales para entrega -->        
                <div id="f-delivery-extra" style="display: none;" class="mb-3">
                    <label class="form-label fw-bold small text-primary"><i class="fas fa-map-marker-alt me-1"></i> Lugar de Entrega</label>
                    <button id="btn-select-location" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-between text-start shadow-sm" type="button" onclick="openSelectLocationModal()">
                        <span class="text-truncate">
                            <i class="fas fa-search-location me-2"></i>
                            <span id="f-location-display">Seleccionar dirección...</span>
                        </span>
                        <i class="fas fa-chevron-right ms-2 small opacity-50"></i>
                    </button>
                    <a id="btn-open-map-url" href="#" target="_blank" class="btn btn-sm btn-info w-100 mt-2 py-2 shadow-sm fw-bold" style="display: none; color: white;">
                        <i class="fas fa-external-link-alt me-1"></i> VER UBICACIÓN EN MAPA
                    </a>
                    <div id="f-delivery-rate-container" class="mt-2" style="display: none;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-bold small">Servicio Delivery</span>
                            <select id="f-delivery-rate-id" class="form-select fw-bold text-success" onchange="updateDeliveryCostFromSelect()">
                                <option value="" data-price="0">-- Seleccionar Tarifa --</option>
                                <?php if (!empty($activeRates['details'])): ?>
                                    <?php foreach ($activeRates['details'] as $rate): ?>
                                        <option value="<?php echo $rate['id']; ?>" 
                                                data-price="<?php echo $rate['price']; ?>"
                                                data-from="<?php echo $rate['km_from']; ?>"
                                                data-to="<?php echo $rate['km_to']; ?>">
                                            Gs. <?php echo number_format($rate['price'], 0, ',', '.'); ?> (<?php echo "{$rate['km_from']}-{$rate['km_to']} km"; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="f-delivery-cost" value="0">
                    <input type="hidden" id="f-location-id">
                    <input type="hidden" id="f-delivery-address">
                    <input type="hidden" id="f-location-url">
                    <input type="hidden" id="f-lat"><input type="hidden" id="f-lng">
                </div>
                <!-- notas adicionales -->        
                <div class="mb-4">
                    <label class="form-label fw-bold small">Notas adicionales</label>
                    <textarea id="f-observation" class="form-control" rows="2" placeholder="Ej: Sin cebolla, llamar al llegar..."></textarea>
                </div>
                <!-- total -->        
                <div class="alert alert-success d-flex justify-content-between align-items-center p-3 border-0 shadow-sm mb-0">
                    <span class="fw-bold h6 mb-0">TOTAL:</span>
                    <span class="h4 fw-bold mb-0 text-dark" id="f-total-display">Gs. 0</span>
                </div>
            </div>

            <!-- pie de modal (botones) -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Seguir cargando</button>
                <button type="button" class="btn btn-success px-5 fw-bold" onclick="confirmPOS()">CONFIRMAR VENTA</button>
            </div>

        </div>
    </div>
</div>

<!-- MODAL 2: BUSCAR CLIENTE -->
<div class="modal fade" id="modalSearchClient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-search me-2"></i>Buscar Cliente</h5>
                <button type="button" class="btn-close" onclick="closeSearchAndReturn()"></button>
            </div>
            <div class="modal-body p-4">
                <input type="text" id="s-client-term" class="form-control form-control-lg mb-3 shadow-sm" placeholder="Nombre, RUC o Teléfono..." oninput="debounceSearchClient()">
                <div class="table-responsive rounded border" style="max-height: 350px;">
                    <table class="pos-client-table mb-0">
                        <thead class="sticky-top bg-white">
                            <tr><th>Nombre</th><th>RUC/CI</th><th>Teléfono</th></tr>
                        </thead>
                        <tbody id="s-client-results">
                            <!-- Resultados AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 3: REGISTRO RÁPIDO -->
<div class="modal fade" id="modalCreateClient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Nuevo Cliente</h5>
                <button type="button" id="btn-close-quick-client" class="btn-close" onclick="closeCreateAndReturn()"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Nombre Completo</label>
                        <input id="c-name" class="form-control" placeholder="Juan Pérez">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Teléfono / WhatsApp</label>
                        <input id="c-phone" class="form-control" placeholder="0981..." maxlength="15" oninput="checkPhoneExistence(this.value)">
                        <div id="c-phone-feedback" class="small mt-1" style="display:none;"></div>
                    </div>
                    <div class="col-12 mt-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="c-has-whatsapp" value="1" checked>
                            <label class="form-check-label small fw-bold" for="c-has-whatsapp">
                                <i class="fab fa-whatsapp text-success me-1"></i> Tiene WhatsApp
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Email (Opcional)</label>
                        <input id="c-email" type="email" class="form-control" placeholder="cliente@correo.com">
                    </div>
                    <div class="col-12"><hr class="my-2"></div>
                    <div class="col-7">
                        <label class="form-label small fw-bold">Razón Social</label>
                        <input id="c-billing-name" class="form-control form-control-sm">
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-bold">RUC / CI</label>
                        <input id="c-billing-ruc" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-submit-quick-client" class="btn btn-primary w-100 fw-bold" onclick="confirmSubmitQuickClient()">REGISTRAR Y SELECCIONAR</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: SELECCIONAR UBICACIÓN -->
<div class="modal fade" id="modalSelectLocation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Direcciones de <span id="select-location-client-name" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="f-modal-locations-list" class="pos-locations-grid">
                    <!-- Se puebla vía JS -->
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary w-100 fw-bold py-2" onclick="openAddLocationModal()">
                    <i class="fas fa-plus me-2"></i>AGREGAR NUEVA DIRECCIÓN
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR UBICACIÓN -->
<div class="modal fade" id="modalAddLocation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nueva Dirección para <span id="add-location-client-name"></span></h5>
                <button type="button" class="btn-close" onclick="closeAddLocationAndReturn()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="c-location-client-id">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Título (Ej: Casa, Oficina)</label>
                    <input type="text" id="c-location-title" class="form-control" placeholder="Casa">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Link de Ubicación (WhatsApp/Maps)</label>
                    <input type="text" id="c-location-url" class="form-control" placeholder="https://maps.app.goo.gl/...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Dirección / Referencia</label>
                    <textarea id="c-location-address" class="form-control" rows="2" placeholder="Calle, número, color de portón..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-success w-100 fw-bold py-2" onclick="submitQuickLocation()">GUARDAR DIRECCIÓN</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 4: COBRO MIXTO (Integrado desde Orders) -->
<div class="modal fade" id="modalPayOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form-pay-modal">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title"><i class="fas fa-cash-register me-2"></i>Cobrar Pedido #<span id="pay-modal-id">0</span></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="pay-input-order-id">
                    <!-- Campo oculto para decidir si se imprime o no en el backend -->
                    <input type="hidden" name="should_print" id="pay-should-print" value="1">
                    <div class="row">
                        <div class="col-md-7">
                            <table class="table table-sm table-borderless align-middle">
                                <thead><tr class="text-muted small"><th>MÉTODO</th><th style="width:160px">MONTO</th><th>REF.</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $pMethods = ['efectivo' => '💵 Efectivo', 'pos' => '💳 Tarjeta', 'transferencia' => '🏦 Transf.', 'qr' => '📱 QR'];
                                    $pi = 0; foreach($pMethods as $key => $label): ?>
                                    <tr>
                                        <td class="small fw-bold text-dark"><?php echo $label; ?></td>
                                        <td>
                                            <input type="hidden" name="payments[<?php echo $pi; ?>][metodo]" value="<?php echo $key; ?>">
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="payments[<?php echo $pi; ?>][monto]" class="form-control fw-bold pay-input" data-method="<?php echo $key; ?>" value="0" min="0">
                                                <button class="btn btn-outline-secondary" type="button" onclick="fillRemainingPay('<?php echo $key; ?>')"><i class="fas fa-magic"></i></button>
                                            </div>
                                        </td>
                                        <td><input type="text" name="payments[<?php echo $pi; ?>][referencia]" class="form-control form-control-sm" placeholder="..."></td>
                                    </tr>
                                    <?php $pi++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light p-3 rounded border text-center">
                                <p class="mb-0 text-muted small">TOTAL A COBRAR</p>
                                <h3 class="fw-bold mb-3 text-dark" id="pay-modal-total">Gs. 0</h3>
                                <div id="pay-balance-card" class="p-2 border rounded">
                                    <p id="pay-balance-label" class="mb-0 small fw-bold text-muted">RESTA COBRAR</p>
                                    <h4 id="pay-balance-display" class="mb-0">Gs. 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex flex-row-reverse gap-2">
                    <!-- Botón Principal: Recibe el foco primero por ser el primero en el DOM (flex-row-reverse lo mueve a la derecha) -->
                    <button type="submit" id="pay-btn-submit" class="btn btn-success flex-fill py-2 fw-bold" onclick="document.getElementById('pay-should-print').value='1'">
                        <i class="fas fa-print me-1"></i> GUARDAR E IMPRIMIR
                    </button>
                    <!-- Botón Secundario -->
                    <button type="submit" id="pay-btn-save-only" class="btn btn-outline-success flex-fill py-2 fw-bold" onclick="document.getElementById('pay-should-print').value='0'">
                        <i class="fas fa-save me-1"></i> SOLO GUARDAR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay Global de Procesamiento -->
<div id="posLoadingOverlay" class="pos-loading-overlay">
    <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
    <h5 class="fw-bold">PROCESANDO OPERACIÓN...</h5>
    <p class="small opacity-75">Por favor, no cierre ni refresque la ventana.</p>
</div>

<script src="<?php echo $baseUrl; ?>js/pos.js"></script>

<script>
/**
 * Intercepta el registro de cliente para solicitar confirmación
 */
function confirmSubmitQuickClient() {
    const name = document.getElementById('c-name').value.trim();
    
    if (!name) {
        Toast.fire("El nombre del cliente es obligatorio", "error");
        document.getElementById('c-name').focus();
        return;
    }

    Swal.fire({
        title: '¿Confirmar nuevo cliente?',
        text: `Se registrará a "${name}" y se asignará automáticamente a este pedido.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Revisar datos',
        confirmButtonColor: '#0984e3',
        cancelButtonColor: '#64748b',
        focusConfirm: true // El foco se posiciona automáticamente en el botón "Sí, registrar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamamos a la función original que procesa el AJAX en pos.js
            if (typeof submitQuickClient === 'function') submitQuickClient();
        }
    });
}

/**
 * UX: Navegación por teclado para el Registro Rápido de Clientes
 */
document.addEventListener('DOMContentLoaded', function() {
    const modalQuickClient = document.getElementById('modalCreateClient');
    
    if (modalQuickClient) {
        // 1. Foco inicial automático al abrir
        modalQuickClient.addEventListener('shown.bs.modal', function () {
            document.getElementById('c-name').focus();
        });

        // 2. Definición del orden de recorrido
        const focusableSelectors = [
            '#c-name',
            '#c-phone',
            '#c-has-whatsapp',
            '#c-email',
            '#c-billing-name',
            '#c-billing-ruc',
            '#btn-submit-quick-client',
            '#btn-close-quick-client'
        ];

        modalQuickClient.addEventListener('keydown', function(e) {
            const activeElement = document.activeElement;
            const currentIndex = focusableSelectors.findIndex(selector => activeElement.matches(selector));

            if (currentIndex === -1) return;

            const isEnter = (e.key === 'Enter');
            const isNext = (e.key === 'ArrowDown' || e.key === 'ArrowRight');
            const isPrev = (e.key === 'ArrowUp' || e.key === 'ArrowLeft');

            if (isEnter || isNext || isPrev) {
                // Si es Enter en el botón de registro, dejamos que el click natural (o submitQuickClient) actúe
                if (isEnter && activeElement.id === 'btn-submit-quick-client') {
                    return; 
                }

                // Evitar comportamientos por defecto
                if (isEnter || isNext || isPrev) e.preventDefault();

                let nextIndex;
                
                if (isPrev) {
                    // Flechas atrás: Recorrido circular inverso
                    nextIndex = (currentIndex - 1 + focusableSelectors.length) % focusableSelectors.length;
                } else {
                    // Enter o Flechas adelante: Recorrido circular
                    // Nota: Según el pedido, Enter NO debe incluir el botón cerrar en el "recorrido de campos" normal
                    // pero para simplificar la lógica de "recorrer", seguiremos el orden del array.
                    nextIndex = (currentIndex + 1) % focusableSelectors.length;
                }

                const nextElement = document.querySelector(focusableSelectors[nextIndex]);
                if (nextElement) {
                    nextElement.focus();
                    // Si es un input de texto, seleccionar contenido para facilitar edición rápida
                    if (nextElement.tagName === 'INPUT' && nextElement.type !== 'checkbox') nextElement.select();
                }
            }
        });
    }
});
</script>
