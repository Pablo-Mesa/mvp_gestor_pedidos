<?php
    $cashModel = new CashRegister();
    $isCashOpen = $cashModel->getActiveSession($_SESSION['user_id']) ? true : false;
?>
<style>
    .pos-container {
        display: flex;
        gap: 20px;
        height: calc(100vh - 145px);
    }

    /* Panel Izquierdo: Productos */
    .pos-products {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .pos-search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .pos-search-bar input {
        flex: 1;
        padding: 12px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }
    .pos-search-bar input:focus { border-color: #0984e3; background-color: #fff; outline: none; box-shadow: 0 0 0 3px rgba(9, 132, 227, 0.1); }

    .pos-category-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        flex-shrink: 0;
        padding-bottom: 12px;
    }
    .pos-category-pills::-webkit-scrollbar { display: none; }
    
    .btn-pos-filter {
        padding: 8px 18px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #fff;
        white-space: nowrap;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        line-height: 1;
        flex-shrink: 0;
    }

    .btn-pos-filter:focus, .btn-pos-filter.active {
        box-shadow: none !important;
        outline: none !important;
    }

    .btn-pos-filter.active {
        background: #2d3436 !important;
        color: white !important;
        border-color: #2d3436 !important;
    }

    .pos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 15px;
        overflow-y: auto;
        padding-right: 8px;
        flex: 1;
        min-height: 0;
        align-items: start; /* Evita que las tarjetas se estiren verticalmente */
    }
    .pos-grid::-webkit-scrollbar { width: 5px; }
    .pos-grid::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    .pos-item-card {
        border: 1px solid #f1f5f9;
        padding: 15px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: #fff;
        display: flex; /* Convierte la tarjeta en un contenedor flex */
        flex-direction: column; /* Apila los elementos internos verticalmente */
        min-height: 120px; /* Altura mínima para mantener consistencia */
    }
    .pos-item-card:hover { border-color: #0984e3; transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    
    .pos-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; display: block; }
    .pos-item-price { color: #00b894; font-weight: 700; font-size: 0.9rem; display: block; } /* Asegura que el precio ocupe su propia línea */

    /* Acciones de Porción */
    .pos-item-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }
    .btn-portion {
        flex: 1;
        padding: 6px;
        font-size: 0.65rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
        line-height: 1.2;
        transition: all 0.2s;
    }
    .btn-portion:hover { border-color: #0984e3; color: #0984e3; background: #f0f7ff; }
    .btn-portion small { color: #00b894; font-weight: 800; display: block; margin-top: 2px; }

    .btn-show-img {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #f1f5f9;
        border: none;
        border-radius: 4px;
        padding: 5px 7px;
        color: #64748b;
        cursor: pointer;
        font-size: 0.75rem;
    }
    .btn-show-img:hover { background: #e2e8f0; color: #0984e3; }

    /* Panel Derecho: Ticket */
    .pos-ticket {
        width: 400px;
        background: #2d3436;
        color: white;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .ticket-header { 
        border-bottom: 2px dashed rgba(255,255,255,0.1); 
        padding-bottom: 15px; 
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .ticket-header h3 { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.5px; }

    .btn-clear-cart {
        background: none;
        border: 1px solid rgba(255,255,255,0.15);
        color: #ff4757;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-clear-cart:hover { background: #ff4757; color: white; border-color: #ff4757; }

    .ticket-items { flex: 1; overflow-y: auto; margin-bottom: 15px; padding-right: 5px; }
    .ticket-items::-webkit-scrollbar { width: 4px; }
    .ticket-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .ticket-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 0.9rem;
        background: rgba(255,255,255,0.03);
        padding: 8px 12px;
        border-radius: 8px;
    }

    .ticket-item-info { flex: 1; }
    .ticket-item-qty { background: #0984e3; color: white; padding: 2px 8px; border-radius: 4px; margin-right: 10px; font-weight: 700; font-size: 0.8rem; }

    .ticket-footer { border-top: 2px dashed rgba(255,255,255,0.1); padding-top: 20px; }
    .ticket-total { display: flex; justify-content: space-between; font-size: 1.6rem; font-weight: 800; margin-bottom: 20px; color: #00b894; letter-spacing: -1px; }

    .btn-confirm-sale {
        width: 100%;
        padding: 18px;
        background: #00b894;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.1rem;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.2s;
        box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3);
    }
    .btn-confirm-sale:hover { background: #00a887; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 184, 148, 0.4); }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pos-container { flex-direction: column; height: auto; }
        .pos-ticket { width: 100%; height: auto; }
        .pos-grid { grid-template-columns: 1fr 1fr; }
    }

    /* Estilos para cuadrícula de ubicaciones (Igual al Checkout) */
    .pos-locations-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 1rem; }
    .pos-location-card { 
        border: 1px solid #eee; padding: 12px; border-radius: 10px; cursor: pointer; 
        display: flex; flex-direction: column; gap: 4px; transition: 0.2s; font-size: 0.8rem; position: relative; background: #fff;
    }
    .pos-location-card i { font-size: 1.1rem; color: #aaa; margin-bottom: 3px; }
    .pos-location-card.selected { border-color: #00b894; background: #f0fdf4; border-width: 2px; }
    .pos-location-card strong { color: #2d3436; display: block; }
    .pos-location-card small { color: #636e72; font-size: 0.7rem; line-height: 1.2; }

    /* Tabla de Clientes en Modal */
    .pos-client-table { width: 100%; font-size: 0.85rem; border-collapse: collapse; }
    .pos-client-table th { background: #f8fafc; padding: 10px; border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b; }
    .pos-client-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
    .pos-client-table tr:hover td { background: #f0f7ff; color: #0984e3; }
    .pos-client-table tr:focus { background: #e0f2fe; outline: 2px solid #0984e3; outline-offset: -2px; }

    /* Igualar altura visual de modales secundarios con modalFinalize */
    #modalAddLocation .modal-content,
    #modalSelectLocation .modal-content {
        min-height: 620px;
    }

    /* Quitar flechas de los input de número (Spinner) en el cobro */
    .pay-input::-webkit-outer-spin-button,
    .pay-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .pay-input[type=number] {
        -moz-appearance: textfield;
    }
</style>

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
            <button type="button" class="btn btn-pos-filter active" onclick="filterByCat('all', this)">Todos</button>
            <?php foreach($categories as $cat): ?>
                <button type="button" class="btn btn-pos-filter" onclick="filterByCat('<?php echo $cat['id']; ?>', this)">
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
                <label style="font-size: 0.75rem; color: #aaa;">Yes:</label>
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
    <div class="modal-dialog modal-dialog-centered">
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
                            <span id="f-location-display">Seleccionar o agregar dirección...</span>
                        </span>
                        <i class="fas fa-chevron-right ms-2 small opacity-50"></i>
                    </button>
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
                <button type="button" class="btn-close" onclick="closeCreateAndReturn()"></button>
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
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="submitQuickClient()">REGISTRAR Y SELECCIONAR</button>
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
                <div class="mb-3"><label class="form-label small fw-bold">Título (Ej: Casa, Oficina)</label><input type="text" id="c-location-title" class="form-control" placeholder="Casa"></div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Link de Ubicación (WhatsApp/Maps)</label>
                    <input type="text" id="c-location-url" class="form-control" placeholder="https://maps.app.goo.gl/...">
                </div>
                <div class="mb-3"><label class="form-label small fw-bold">Dirección / Referencia</label><textarea id="c-location-address" class="form-control" rows="2" placeholder="Calle, número, color de portón..."></textarea></div>
            </div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-success w-100 fw-bold py-2" onclick="submitQuickLocation()">GUARDAR DIRECCIÓN</button></div>
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
                <div class="modal-footer py-2">
                    <button type="submit" id="pay-btn-submit" class="btn btn-success w-100 py-2 fw-bold">FINALIZAR Y REGISTRAR VENTA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let posCart = [];
    let selectedClientId = 1; // 1 = Cliente Ocasional por defecto
    const isCashOpen = <?php echo ($isCashOpen ?? false) ? 'true' : 'false'; ?>;
    
    // Instancias de Modales
    let bsModalFinalize, bsModalSearch, bsModalCreate, payModal, bsModalSelectLocation, bsModalAddLocation;
    
    const totalEl = document.getElementById('posTotal');
    const itemsEl = document.getElementById('ticketItems');

    function updateTime() {
        document.getElementById('current-time').innerText = new Date().toLocaleString();
    }
    setInterval(updateTime, 1000);

    function showProductImg(img, name) {
        Swal.fire({
            title: name,
            imageUrl: 'uploads/' + img,
            imageWidth: 400,
            imageAlt: name,
            confirmButtonText: 'Cerrar'
        });
    }

    /**
     * Vacía el carrito actual con confirmación de SweetAlert
     */
    async function confirmClearCart() {
        if(posCart.length === 0) return;

        const result = await Swal.fire({
            title: '¿Vaciar pedido?',
            text: "Se eliminarán todos los productos cargados en el ticket actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff4757',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
            Toast.fire("Pedido vaciado", "info");
        }
    }

    function clearPOS() {
        document.getElementById('posSearch').value = '';
        filterByCat('all', document.querySelector('.pos-category-pills button'));
    }

    function filterPOS() {
        const val = document.getElementById('posSearch').value.toLowerCase();
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = card.dataset.name.includes(val) ? 'block' : 'none';
        });
    }

    function filterByCat(catId, btn) {
        document.querySelectorAll('.btn-pos-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = (catId === 'all' || card.dataset.cat === catId) ? 'block' : 'none';
        });
    }

    function addToTicket(id, name, price) {
        const exists = posCart.find(i => i.id === id);
        if(exists) {
            exists.quantity++;
        } else {
            posCart.push({ id, name, price, quantity: 1 });
        }
        renderTicket();
    }

    function removeFromTicket(id) {
        posCart = posCart.filter(i => i.id !== id);
        renderTicket();
    }

    function renderTicket() {
        if(posCart.length === 0) {
            itemsEl.innerHTML = '<div style="text-align: center; color: rgba(255,255,255,0.3); margin-top: 40px;"><i class="fas fa-receipt fa-3x"></i><p>Cargue productos</p></div>';
            totalEl.innerText = "0";
            return;
        }

        let html = '';
        let total = 0;
        posCart.forEach(item => {
            total += item.price * item.quantity;
            html += `
                <div class="ticket-item">
                    <div class="ticket-item-info">
                        <span class="ticket-item-qty">${item.quantity}</span> ${item.name}
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>${new Intl.NumberFormat('es-PY').format(item.price * item.quantity)}</span>
                        <button onclick="removeFromTicket(${item.id})" style="background:none; border:none; color:#ff4757; cursor:pointer;"><i class="fas fa-times"></i></button>
                    </div>
                </div>`;
        });
        itemsEl.innerHTML = html;
        totalEl.innerText = new Intl.NumberFormat('es-PY').format(total);
    }

    /**
     * Abre el modal principal y sincroniza los datos del ticket
     */
    window.openFinalizeModal = function() {
        if(posCart.length === 0) return Toast.fire("Agrega productos al ticket", "warning");
        
        document.getElementById('f-total-display').innerText = 'Gs. ' + totalEl.innerText;

        if(!document.getElementById('f-observation').value) {
             document.getElementById('f-observation').value = document.getElementById('posObservation').value;
        }
                        
        bsModalFinalize.show();
    }

    window.openSearchClient = function() {
        bsModalFinalize.hide();
        bsModalSearch.show();
        window.searchClientListApi();
    }

    window.closeSearchAndReturn = function() {
        bsModalSearch.hide();
        bsModalFinalize.show();
    }

    window.openCreateClient = function() {
        bsModalFinalize.hide();
        bsModalCreate.show();
    }

    window.closeCreateAndReturn = function() {
        bsModalCreate.hide();
        bsModalFinalize.show();
    }

    /**
     * Registro rápido de cliente vía AJAX (Bootstrap Modal)
     */
    window.submitQuickClient = async function() {
        const data = {
            name: document.getElementById('c-name').value,
            phone: document.getElementById('c-phone').value,
            email: document.getElementById('c-email').value,
            billing_name: document.getElementById('c-billing-name').value,
            billing_ruc: document.getElementById('c-billing-ruc').value
        };

        if(!data.name || !data.phone) return Toast.fire("Nombre y Teléfono requeridos", "error");

        try {
            const resp = await fetch('?route=admin_clients_store_api', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await resp.json();
            if(res.success) {
                selectClientFromList(res.id, data.name, data.phone);
                Toast.fire("Cliente registrado", "success");
                // Limpiar campos para la próxima
                ['c-name', 'c-phone', 'c-email', 'c-billing-name', 'c-billing-ruc'].forEach(id => document.getElementById(id).value = '');
            }
        } catch(e) { console.error(e); }
    }

    /**
     * Funcionalidad de Cobro Mixto (Adaptada de Orders)
     */
    function openPaymentModal(order) {
        document.getElementById('pay-modal-id').innerText = order.id;
        document.getElementById('pay-input-order-id').value = order.id;
        document.getElementById('pay-modal-total').innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(order.total);
        document.getElementById('pay-modal-total').dataset.total = order.total;
        
        document.querySelectorAll('.pay-input').forEach(inp => inp.value = 0);
        const firstInput = document.querySelector(`.pay-input[data-method="${order.payment_method}"]`);
        if (firstInput) firstInput.value = order.total;
        
        calculatePayBalance();
        payModal.show();
    }

    function calculatePayBalance() {
        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let paid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => paid += parseFloat(inp.value) || 0);
        const remaining = total - paid;
        const display = document.getElementById('pay-balance-display');
        const card = document.getElementById('pay-balance-card');
        const label = document.getElementById('pay-balance-label');
        const submitBtn = document.getElementById('pay-btn-submit');
        
        card.style.backgroundColor = '#fff';
        if (remaining > 0) {
            label.innerText = 'RESTA COBRAR';
            display.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(remaining);
            display.className = 'mb-0 fw-bold text-danger';
            if (submitBtn) submitBtn.disabled = true;
        } else if (remaining < 0) {
            label.innerText = 'VUELTO';
            display.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(Math.abs(remaining));
            display.className = 'mb-0 fw-bold text-primary';
            if (submitBtn) submitBtn.disabled = false;
        } else {
            label.innerText = 'ESTADO';
            display.innerText = 'MONTO EXACTO ✅';
            display.className = 'mb-0 fw-bold text-success';
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    window.fillRemainingPay = function(method) {
        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let otherPaid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => {
            if (inp.dataset.method !== method) otherPaid += parseFloat(inp.value) || 0;
        });
        document.querySelector(`.pay-input[data-method="${method}"]`).value = Math.max(0, total - otherPaid);
        calculatePayBalance();
    };

    async function submitPayment(e) {
        e.preventDefault();
        
        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let paid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => paid += parseFloat(inp.value) || 0);

        if (paid < total) {
            Toast.fire("El monto cobrado es insuficiente", "warning");
            return;
        }

        const result = await Swal.fire({
            title: '¿Confirmar cobro?',
            text: `Se registrará el pago para el Pedido #${document.getElementById('pay-input-order-id').value}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Sí, registrar pago',
            cancelButtonText: 'Revisar',
            didOpen: () => { Swal.getConfirmButton().focus(); }
        });

        if (!result.isConfirmed) return;

        const formData = new FormData(document.getElementById('form-pay-modal'));
        const btn = document.getElementById('pay-btn-submit');
        btn.disabled = true;
        
        try {
            const resp = await fetch('?route=orders_process_finalize', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const res = await resp.json();
            if (res.success) {
                payModal.hide();
                setTimeout(() => {
                    Swal.fire({ 
                        title: "¡Éxito!", 
                        text: "Venta registrada correctamente.", 
                        icon: "success",
                        didOpen: () => { Swal.getConfirmButton().focus(); }
                    });
                }, 400);
            } else {
                Toast.fire(res.message || "Error", "error");
                btn.disabled = false;
            }
        } catch(e) { Toast.fire("Error de red", "error"); btn.disabled = false; }
    }

    /**
     * Manejo de interfaz dinámica
     */
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('modalFinalize');

        // Inicialización de modales ahora que el DOM y Bootstrap están listos
        bsModalFinalize = new bootstrap.Modal(document.getElementById('modalFinalize'));
        bsModalSearch = new bootstrap.Modal(document.getElementById('modalSearchClient'));
        bsModalCreate = new bootstrap.Modal(document.getElementById('modalCreateClient'));
        payModal = new bootstrap.Modal(document.getElementById('modalPayOrder'));
        bsModalSelectLocation = new bootstrap.Modal(document.getElementById('modalSelectLocation'));
        bsModalAddLocation = new bootstrap.Modal(document.getElementById('modalAddLocation'));

        const payInputs = Array.from(document.querySelectorAll('.pay-input'));
        const paySubmitBtn = document.getElementById('pay-btn-submit');

        document.getElementById('form-pay-modal').onsubmit = submitPayment;
        payInputs.forEach((inp, index) => {
            inp.addEventListener('input', calculatePayBalance);
            inp.addEventListener('focus', function() { if(this.value == "0") this.value = ""; });
            inp.addEventListener('blur', function() { if(this.value == "") this.value = "0"; });

            // Navegación por teclado en campos de pago
            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === 'ArrowDown') {
                    e.preventDefault(); // Evita que la flecha modifique el valor numérico
                    const nextInput = payInputs[index + 1];
                    if (nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    } else {
                        // Es el último campo: si el botón está habilitado (monto cubierto), darle foco
                        if (!paySubmitBtn.disabled) {
                            paySubmitBtn.focus();
                            // Si presionó Enter, ejecutar directamente la acción
                            if (e.key === 'Enter') paySubmitBtn.click();
                        }
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevInput = payInputs[index - 1];
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.select();
                    }
                }
            });
        });

        // Foco automático al abrir el modal de cobro
        document.getElementById('modalPayOrder').addEventListener('shown.bs.modal', function () {
            const firstInp = payInputs.find(i => parseFloat(i.value) > 0) || payInputs[0];
            if (firstInp) { firstInp.focus(); firstInp.select(); }
        });

        modalEl.addEventListener('shown.bs.modal', function () {
            const searchBtn = modalEl.querySelector('[onclick="openSearchClient()"]');
            const createBtn = modalEl.querySelector('[onclick="openCreateClient()"]');
            
            const delivery = document.getElementById('f-delivery-type');
            const selectLocationBtn = document.getElementById('btn-select-location');
            const payment = document.getElementById('f-payment-method');
            const observation = document.getElementById('f-observation');
            const confirmBtn = modalEl.querySelector('[onclick="confirmPOS()"]');

            // Colocar el foco explícitamente cuando el modal ya es visible
            if (searchBtn) {
                searchBtn.focus();
            }

            const focusPath = [searchBtn, createBtn, payment, delivery, selectLocationBtn, observation, confirmBtn];
            
            focusPath.forEach((el, index) => {
                if (el) {
                    el.onkeydown = (e) => {
                        if (e.key === 'Enter') { /*&& e.target.tagName !== 'TEXTAREA'*/
                            e.preventDefault();
                            let nextFound = false;
                            for (let i = index + 1; i < focusPath.length; i++) {
                                // offsetParent es null si el elemento o sus padres tienen display:none
                                if (focusPath[i] && focusPath[i].offsetParent !== null) {
                                    focusPath[i].focus();
                                    nextFound = true;
                                    break;
                                }
                            }
                            if (!nextFound) {
                                if (el === confirmBtn) {
                                    confirmBtn.click();
                                } else {
                                    confirmBtn.focus();
                                }
                            }
                        }
                    };
                }
            });
        });

        // Foco automático para el buscador de clientes cuando termina la animación
        document.getElementById('modalSearchClient').addEventListener('shown.bs.modal', function () {
            document.getElementById('s-client-term').focus();
        });

        // Lógica de navegación por teclado para el buscador de clientes
        const clientSearchInput = document.getElementById('s-client-term');
        const clientResultsBody = document.getElementById('s-client-results');

        clientSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') {
                const firstRow = clientResultsBody.querySelector('.selectable-client');
                if (firstRow) {
                    e.preventDefault();
                    firstRow.focus();
                }
            }
        });

        clientResultsBody.addEventListener('keydown', function(e) {
            const currentFocused = document.activeElement;
            if (!currentFocused || !currentFocused.classList.contains('selectable-client')) return;

            if (e.key === 'ArrowDown') {
                const nextRow = currentFocused.nextElementSibling;
                if (nextRow && nextRow.classList.contains('selectable-client')) {
                    e.preventDefault();
                    nextRow.focus();
                }
            } else if (e.key === 'ArrowUp') {
                const prevRow = currentFocused.previousElementSibling;
                if (prevRow && prevRow.classList.contains('selectable-client')) {
                    e.preventDefault();
                    prevRow.focus();
                } else {
                    e.preventDefault();
                    clientSearchInput.focus();
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                currentFocused.click();
            }
        });

        // Foco automático para el registro de nuevo cliente
        document.getElementById('modalCreateClient').addEventListener('shown.bs.modal', function () {
            document.getElementById('c-name').focus();
        });

    });

    window.toggleDeliveryFields = function(val) {
        const container = document.getElementById('f-delivery-extra');
            const isDelivery = (val === 'delivery');
            container.style.display = isDelivery ? 'block' : 'none';
            if(isDelivery) loadClientLocations(document.getElementById('f-client-id').value);
        }

    /**
     * Recolección de datos y envío final
     */
    window.confirmPOS = function() {
        const formValues = {
            clientId: document.getElementById('f-client-id').value,
            deliveryType: document.getElementById('f-delivery-type').value,
            paymentMethod: document.getElementById('f-payment-method').value,
            observation: document.getElementById('f-observation').value
        };

        submitPOS(formValues);
    }

    window.openSelectLocationModal = function() {
        const clientId = document.getElementById('f-client-id').value;
        const clientName = document.getElementById('f-client-name').value;
        document.getElementById('select-location-client-name').innerText = clientName;
        bsModalSelectLocation.show();
    }

    window.openAddLocationModal = function() {
        const clientId = document.getElementById('f-client-id').value;
        const clientName = document.getElementById('f-client-name').value;
        document.getElementById('add-location-client-name').innerText = clientName;
        document.getElementById('c-location-client-id').value = clientId;
        
        // Esperar a que el modal de selección se oculte para evitar errores de foco (aria-hidden)
        const selectModalEl = document.getElementById('modalSelectLocation');
        const onHidden = () => {
            bsModalAddLocation.show();
            selectModalEl.removeEventListener('hidden.bs.modal', onHidden);
        };
        selectModalEl.addEventListener('hidden.bs.modal', onHidden);
        bsModalSelectLocation.hide();
    }

    window.closeAddLocationAndReturn = function() {
        const addModalEl = document.getElementById('modalAddLocation');
        const onHidden = () => {
            bsModalSelectLocation.show();
            addModalEl.removeEventListener('hidden.bs.modal', onHidden);
        };
        addModalEl.addEventListener('hidden.bs.modal', onHidden);
        bsModalAddLocation.hide();
    }
    /**
     * Carga las ubicaciones del cliente seleccionado en el POS
     */
    window.loadClientLocations = async function(clientId) {
        const btnSelect = document.getElementById('btn-select-location');
        const listEl = document.getElementById('f-modal-locations-list');
        
        if(!listEl) return;
        listEl.innerHTML = '<div class="col-12 text-center py-3"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';

        try {
            const resp = await fetch(`?route=admin_client_locations&id=${clientId}`);
            const locations = await resp.json();
            listEl.innerHTML = '';
            
            if(Array.isArray(locations) && locations.length > 0) {
                if(btnSelect) btnSelect.classList.replace('btn-outline-primary', 'btn-primary');

                locations.forEach(loc => {
                    const card = document.createElement('div');
                    card.className = 'pos-location-card';
                    card.innerHTML = `<i class="fas fa-map-marker-alt"></i><strong>${loc.title}</strong><small>${loc.address}</small>`;
                    card.onclick = () => window.selectClientLocation(loc);
                    listEl.appendChild(card);
                });
            } else {
                if(btnSelect) btnSelect.classList.replace('btn-primary', 'btn-outline-primary');
                listEl.innerHTML = '<div class="col-12 text-center text-muted py-3">No hay direcciones registradas.</div>';
            }
        } catch (e) { console.error(e); }
    }

    window.selectClientLocation = function(loc) {
        document.getElementById('f-location-url').value = loc.location_url;
        document.getElementById('f-lat').value = loc.lat;
        document.getElementById('f-lng').value = loc.lng;
        
        const display = document.getElementById('f-location-display');
        if(display) display.innerText = loc.title + ': ' + loc.address;

        Toast.fire("Dirección cargada", "success");
        bsModalSelectLocation.hide();
    }

    window.processAddLocationUrl = async function(url) {
        if (!url || url.trim().length < 10) {
            return Toast.fire("Pegue un link de ubicación primero", "warning");
        }
        
        // Regex robusto para extraer coordenadas de links de Maps o WhatsApp (@lat,lng / q=lat,lng / !3dlat!4dlng)
        const regex = /https?:\/\/(maps\..+?|goo\.gl\/maps\/|maps\.app\.goo\.gl\/)\/\S+/g; ///(?:@|query=|q=|!3d|search\/)(-?\d+\.\d+)[,%2C\s!4d]+([-+]?\d+\.\d+)/
        const match = url.match(regex);

        if (match) {
            console.log("Coordenadas extraídas: " + match[1] + ", " + match[2] + ", " + match);
        }
            
        if (match) {
            window.updateAddLocationMarker(parseFloat(match[1]), parseFloat(match[2]));
            Toast.fire("Ubicación extraída correctamente", "success");
        } else if (url.includes('goo.gl') || url.includes('maps.app.goo.gl')) {
            try {                
                const resp = await fetch(`?route=admin_resolve_map_url&url=${encodeURIComponent(url.trim())}`);
                const res = await resp.json();
                if (res.success) {
                    window.updateAddLocationMarker(parseFloat(res.lat), parseFloat(res.lng));
                    Toast.fire("Ubicación resuelta con éxito", "success");
                } else {
                    Toast.fire(res.message || "No se detectaron coordenadas en el link", "error");
                }
            } catch (e) { console.error(e); }
        } else {
            Toast.fire("No se encontraron coordenadas en el texto/enlace proporcionado", "warning");
        }
    }

    window.updateAddLocationMarker = function(lat, lng) {
        const latInp = document.getElementById('c-location-lat');
        const lngInp = document.getElementById('c-location-lng');
        if (latInp) latInp.value = lat;
        if (lngInp) lngInp.value = lng;

        if (addLocationMap) {
            const newPos = [lat, lng];
            addLocationMarker.setLatLng(newPos);
            addLocationMap.setView(newPos, 16);
            // Pequeño delay para asegurar que Leaflet procese el cambio de vista
            setTimeout(() => addLocationMap.invalidateSize(), 200);
        }
    }

    window.ProcesarPedidoAddLocation = function() {
        const fullTextInput = document.getElementById('c-location-url').value;
        if (!fullTextInput || fullTextInput.trim() === '') {
            Toast.fire("Pegue el texto o link de ubicación primero", "warning");
            return;
        }

        const mapRegex = /https?:\/\/(maps\..+?|goo\.gl\/maps\/|maps\.app\.goo\.gl\/)\/\S+/g;
        const linkMatch = fullTextInput.match(mapRegex);

        if (linkMatch && linkMatch.length > 0) {
            const extractedUrl = linkMatch[0];
            window.processAddLocationUrl(extractedUrl);
        } else {
            Toast.fire("No se detectó un enlace de ubicación válido en el texto. Asegúrate de que el mensaje lo incluya.", "error");
        }
    }

    function procesarPedido(){
        const text = document.getElementById('rawText').value;
        if (!text) return alert("Pega el texto primero");

        // 1. Buscar URL de Google Maps
        const mapRegex = /https?:\/\/(maps\..+?|goo\.gl\/maps\/|maps\.app\.goo\.gl\/)\/\S+/g;
        const linkEncontrado = text.match(mapRegex);

        // 2. Intentar sacar un nombre (opcional: asume que la primera línea es el nombre)
        const lineas = text.split('\n');
        const nombreCliente = lineas[0].substring(0, 20); // Toma los primeros 20 caracteres

        if (linkEncontrado) {
            const url = linkEncontrado[0];
            crearTarjeta(nombreCliente, url);
            document.getElementById('rawText').value = ""; // Limpiar input
        } else {
            alert("No se detectó un enlace de ubicación. Asegúrate de que el mensaje lo incluya.");
        }
    }

    window.submitQuickLocation = async function() {
        const data = {
            client_id: document.getElementById('c-location-client-id').value,
            title: document.getElementById('c-location-title').value,
            address: document.getElementById('c-location-address').value,
            location_url: document.getElementById('c-location-url').value
        };

        if(!data.title || !data.address || !data.location_url) return Toast.fire("Complete los datos", "warning");

        const resp = await fetch('?route=pos_save_client_location', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
        });
        const res = await resp.json();
        if(res.success) {
            Toast.fire(res.message, "success");
            bsModalAddLocation.hide();
            window.loadClientLocations(data.client_id);
            bsModalSelectLocation.show();
        } else { Toast.fire(res.message, "error"); }
    }

    /**
     * Buscador de Clientes AJAX
     */
    let searchTimeout;
    window.debounceSearchClient = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => searchClientListApi(), 350);
    }

    window.searchClientListApi = async function() {
        const term = document.getElementById('s-client-term').value.trim();
        const tbody = document.getElementById('s-client-results');

        // Si el término tiene un solo caracter, no buscamos (esperamos al segundo)
        // Si está vacío, traemos los primeros 10 por defecto
        if (term.length === 1) return;
        
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Buscando...</td></tr>';

        try {
            const resp = await fetch(`?route=admin_clients_search&term=${encodeURIComponent(term)}&limit=10&order=name_asc`);
            const clients = await resp.json();
            
            // El "Cliente Ocasional" ahora es simplemente una opción rápida que no sobreescribe 
            // la identidad si el ID 1 ya tiene un nombre real en la tabla.
            let html = '';
            
            if (clients.length === 0) {
                html += '<tr><td colspan="3" class="text-center py-4">Sin resultados</td></tr>';
            } else {
                clients.forEach(c => {
                    // Escapar nombres para evitar que comillas rompan el atributo HTML
                    const safeName = c.name.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    html += `
                        <tr class="selectable-client" tabindex="0" onclick="window.selectClientFromList(${c.id}, '${safeName}', '${c.phone || ''}')">
                            <td><strong>${c.name}</strong></td>
                            <td>${c.billing_ruc || '---'}</td>
                            <td>${c.phone || '---'}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = html;
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error en búsqueda</td></tr>';
        }
    }

    window.selectClientFromList = function(id, name, phone) {
        // Forzamos el nombre de sistema para el ID 1, para los demás usamos el formato estándar
        const displayName = (id == 1) ? 'Cliente Ocasional' : (phone ? `${name} (${phone})` : name);

        // Actualizar los campos del modal y el estado interno
        document.getElementById('f-client-id').value = id; // Actualiza el campo oculto
        document.getElementById('f-client-name').value = displayName; // Actualiza el campo visible
        // Sincronizar el estado interno para mantener consistencia si se cierra y abre el modal

        // Ocultar modales de búsqueda/creación
        bsModalSearch.hide();
        bsModalCreate.hide();

        setTimeout(() => {
            bsModalFinalize.show();
            if(document.getElementById('f-delivery-type').value === 'delivery') loadClientLocations(id);
        }, 150);
    }

    /**
     * Valida el formato del teléfono y verifica su existencia en la DB
     */
    window.checkPhoneExistence = async (phone) => {
        const feedback = document.getElementById('c-phone-feedback');
        if(!phone || phone.trim().length < 6) { feedback.style.display = 'none'; return; }

        // Normalizar: Solo números para validación y envío
        const cleanPhone = phone.replace(/\D/g, '');

        /**
         * Validación para Paraguay (Mobile):
         * ^(?:595)?      -> Prefijo país opcional
         * (?:0)?         -> Cero inicial opcional
         * (9[6-9][1-6])  -> Operadora (971-976, 981-986, 991-995, 961-962)
         * (\d{6})$       -> Número de 6 dígitos
         */
        const pyMobileRegex = /^(?:595)?(?:0)?(9[6-9][1-6])(\d{6})$/;
        const isValid = pyMobileRegex.test(cleanPhone);

        feedback.style.display = 'block';
        if (!isValid) {
            feedback.innerText = '❌ Formato inválido (ej: 0981 123456)';
            feedback.style.color = '#dc3545';
            return;
        }

        const resp = await fetch(`?route=admin_clients_check_phone&phone=${cleanPhone}`);
        const res = await resp.json();
        feedback.innerText = res.exists ? '⚠️ Teléfono ya registrado' : '✅ Formato válido y disponible';
        feedback.style.color = res.exists ? '#dc3545' : '#198754';
    }

    /**
     * Envío final del pedido (POS)
     */
    async function submitPOS(data) {
        // Cerramos el modal inmediatamente para evitar doble click
        bsModalFinalize.hide();

        const response = await fetch('?route=pos_store', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                cart: posCart, 
                client_id: data.clientId,
                delivery_type: data.deliveryType,
                payment_method: data.paymentMethod,
                observation: data.observation,
                location_url: document.getElementById('f-location-url').value,
                lat: document.getElementById('f-lat').value,
                lng: document.getElementById('f-lng').value
            })
        });

        const res = await response.json();
        if(res.success) {
            Toast.fire(res.message, "success");
            // Impresión automática de la comanda
            printOrderDirectly(res.order_id, '80mm');

            const createdOrder = {
                id: res.order_id,
                total: res.total, // El servidor ahora retorna el total calculado
                payment_method: data.paymentMethod
            };

            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
            
            // Resetear inputs del modal
            document.getElementById('f-client-id').value = 1;
            document.getElementById('f-client-name').value = 'Cliente Ocasional';
            document.getElementById('f-delivery-type').value = 'local';
            document.getElementById('f-payment-method').value = 'efectivo';
            document.getElementById('f-observation').value = '';
            document.getElementById('f-location-url').value = '';
            document.getElementById('f-lat').value = '';
            document.getElementById('f-lng').value = '';
            
            const display = document.getElementById('f-location-display');
            if(display) display.innerText = 'Seleccionar o agregar dirección...';

            // Ocultar campos de delivery y resetear indicadores visuales
            toggleDeliveryFields('local');

            // Resetear el estado del modal de finalización para el próximo pedido
            const posFinalizeState = {
                clientId: 1,
                clientName: 'Cliente Ocasional',
                observation: '',
                deliveryType: 'local',
                paymentMethod: 'efectivo'
            };
            // Flujo de cobranza: Preguntar si desea registrar el pago ahora (con retraso para esperar impresión)
            setTimeout(() => {
                Swal.fire({
                    title: '¿Registrar pago?',
                    text: "El pedido se guardó. ¿Desea proceder al cobro ahora?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#00b894',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cobrar ahora',
                    cancelButtonText: 'No, cerrar',
                    didOpen: () => { Swal.getConfirmButton().focus(); }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!isCashOpen) {
                            Toast.fire("No puede cobrar sin caja abierta", "error");
                        } else {
                            openPaymentModal(createdOrder);
                        }
                    }
                });
            }, 1200); // Pausa de 1.2s para asegurar que el foco regrese tras abrir el diálogo de impresión
        } else {
            Toast.fire(res.message, "error");
        }
    }

    /**
     * Atajo de teclado F2 para abrir el modal de finalización rápidamente
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            openFinalizeModal();            
        }
        // F3 para Buscar Cliente en lista
        if (e.key === 'F3' || e.code === 'F3') {
            e.preventDefault();
            openSearchClient();
        }
        // F4 para Registrar Nuevo Cliente
        if (e.key === 'F4' || e.code === 'F4') {
            e.preventDefault();
            openCreateClient();
        }
    }, true); // Usamos capture para asegurar que el evento se detecte antes de que el modal lo bloquee

    // Autofocus en el buscador al cargar la vista (descomentar si se desea)
    // document.getElementById('posSearch').focus();

</script>
