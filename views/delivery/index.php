<!-- Leaflet para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    
    .order-card {
        background: var(--delivery-card);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.2rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .order-card:active { transform: scale(0.98); }
    
    .order-card.shipped {
        border-left: 5px solid var(--delivery-primary);
    }

    /* Estilo para Pedidos Entregados */
    .order-card.completed {
        border: 1px solid #c8e6c9;
        background-color: #f1f8e9;
        box-shadow: none;
        padding-bottom: 0.5rem; /* Para reducir el tamaño de la tarjeta completada */
        position: relative;
    }
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
    }
    .header-left, .header-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .order-number {
        font-weight: 900;
        font-size: 1.2rem;
        color: var(--delivery-primary);
        letter-spacing: -0.5px;
    }
    .toggle-section-btn {
        background: none;
        border: none;
        color: var(--delivery-subtext);
        cursor: pointer;
        padding: 8px 4px;
        display: flex;
        align-items: center;
    }
    /* Rotación del icono de la flecha */
    .toggle-section-btn .fas.fa-chevron-down {
        transition: transform 0.3s ease;
    }
    .order-card.collapsed .toggle-section-btn .fas.fa-chevron-down {
        transform: rotate(-90deg);
    }
    .order-time {
        font-size: 0.85rem;
        color: var(--delivery-subtext);
        font-weight: 600;
    }
    /* Badge de Estado en Header */
    .status-badge-header {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1);
    }
    .status-badge-header.completed { background: #c8e6c9; color: #2e7d32; }
    .status-badge-header.shipped { background: #e3f2fd; color: #1976d2; }
    .status-badge-header.rejected { background: #e0e0e0; color: #616161; }
    .status-badge-header.cancelled { background: #ffcdd2; color: #c62828; }

    .client-info h3 {
        font-size: 1.4rem;
        margin-bottom: 5px;
        color: var(--delivery-text);
    }

    /* Estilos para la sección operativa colapsable con animación */
    .card-operative-section {
        max-height: 500px; /* Valor suficientemente grande para el contenido expandido */
        overflow: hidden;
        transition: max-height 0.5s ease-in-out, opacity 0.5s ease-in-out;
        opacity: 1;
    }
    .order-card.collapsed .card-operative-section {
        max-height: 0;
        opacity: 0;
        padding-top: 0;    /* Eliminar padding para un colapso más limpio */
        padding-bottom: 0; /* Eliminar padding para un colapso más limpio */
        margin-top: 0;     /* Eliminar margen para un colapso más limpio */
        margin-bottom: 0;  /* Eliminar margen para un colapso más limpio */
    }

    /* Información de Cobro */
    .payment-summary {
        background: #fcfcfc;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        padding: 12px;
        margin: 10px 0;
    }
    
    .amount-to-collect { font-size: 1.4rem; color: #2d3436; font-weight: 900; letter-spacing: -1px; }
    
    .badge-payment {
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
    }
    .bg-collect { background: #fff5f5; color: #e03131; border: 1px solid #ffa8a8; }
    .bg-paid { background: #ebfbee; color: #099268; border: 1px solid #b2f2bb; }

    .address-box {
        padding: 10px 0;
        font-size: 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        color: var(--delivery-subtext);
    }
    .address-box i { color: #ff5252; margin-top: 4px; }

    /* Botones de Contacto */
    .contact-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 15px 0;
    }
    .btn-contact {
        padding: 12px;
        border-radius: 10px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .btn-call { background: #2d3436; color: white; border: 1px solid #444; }
    .btn-whatsapp { background: #25D366; color: white; }

    /* Mapa */
    .map-wrapper {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        margin: 15px 0;
        border: 1px solid #eee;
    }
    .map-preview { height: 160px; width: 100%; z-index: 1; }
    .map-overlay-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        z-index: 10;
        text-decoration: none;
        border: 1px solid var(--delivery-primary);
    }

    .delivery-actions {
        margin-top: 10px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .btn-logistics {
        width: 100%;
        padding: 20px;
        border-radius: 16px;
        font-weight: 900;
        font-size: 1.1rem;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    .btn-logistics:active { transform: translateY(2px); box-shadow: none; }
    .btn-start { background: var(--delivery-primary); color: #000; }
    .btn-complete { background: #007bff; color: white; }
    .empty-state { text-align: center; padding: 5rem 2rem; color: var(--delivery-subtext); }

    #delivery-orders-container {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>

<div id="delivery-orders-container">
        
    <?php if(empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open fa-3x"></i>
            <p>No tienes pedidos asignados por ahora.</p>
        </div>
    <?php endif; ?>

    <?php foreach($orders as $order): ?>
        <?php echo renderOrderCardHTML($order); ?>
    <?php endforeach; ?>

</div>

<?php
/**
 * Función auxiliar para renderizar el HTML de una tarjeta (PHP)
 * Esto asegura que el renderizado inicial y el de JS sean consistentes.
 */
function renderOrderCardHTML($order) {
    $phone = preg_replace('/[^0-9]/', '', $order['user_phone'] ?? '');
    $mustCollect = (isset($order['payment_method']) && strtolower($order['payment_method']) === 'efectivo');
    $isCollapsed = ($order['status'] === 'completed' || $order['status'] === 'rejected' || $order['status'] === 'cancelled') ? 'collapsed' : '';
    
    ob_start(); ?>
    <div class="order-card <?php echo $order['status'] . ' ' . $isCollapsed; ?>" data-status="<?php echo $order['status']; ?>" id="card-<?php echo $order['id']; ?>">
            <!-- Área 1: Información de Cliente y Pago -->
            <div class="card-info-section">
                <div class="order-header">
                    <div class="header-left">
                        <button class="toggle-section-btn" onclick="toggleCardSection(this.closest('.order-card'))" title="Expandir/Colapsar">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <span class="order-number">#<?php echo $order['id']; ?></span>
                    </div>
                    <div class="header-right">
                        <?php if($order['status'] === 'completed'): ?>
                            <span class="status-badge-header completed"><i class="fas fa-check-circle"></i> Entregado</span>
                        <?php elseif($order['status'] === 'rejected'): ?>
                            <span class="status-badge-header rejected"><i class="fas fa-undo"></i> Rechazado</span>
                        <?php elseif($order['status'] === 'cancelled'): ?>
                            <span class="status-badge-header cancelled"><i class="fas fa-times-circle"></i> Cancelado</span>
                        <?php elseif($order['status'] === 'confirmed'): ?>
                            <span class="status-badge-header shipped" style="background:#fff3cd; color:#856404;"><i class="fas fa-clock"></i> Asignado</span>
                        <?php elseif($order['status'] === 'shipped'): ?>
                            <span class="status-badge-header shipped"><i class="fas fa-truck"></i> En Camino</span>
                        <?php endif; ?>
                        <span class="order-time"><?php echo date('H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                </div>
                <div class="client-info">
                    <h3><?php echo htmlspecialchars($order['user_name'] ?? 'Cliente'); ?></h3>
                    <div class="address-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($order['delivery_address'] ?: 'Sin dirección especificada'); ?></span>
                    </div>
                </div>

                <div class="payment-summary">
                    <div class="payment-row">
                        <span class="payment-label">Método: <?php echo htmlspecialchars($order['payment_method'] ?? 'No especificado'); ?></span>
                        <span class="badge-payment <?php echo $mustCollect ? 'bg-collect' : 'bg-paid'; ?>">
                            <?php echo $mustCollect ? 'A Cobrar' : 'Ya Pagado'; ?>
                        </span>
                    </div>
                    <div class="payment-row" style="margin-top: 5px;">
                        <span class="payment-label">Monto Total:</span>
                        <span class="amount-to-collect">Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Área 2: Operaciones y Logística (Se oculta al completar) -->
            <div class="card-operative-section">
                <div class="contact-actions">
                    <button onclick="showPhoneModal('<?php echo $phone; ?>')" class="btn-contact btn-call" style="background: #0984e3; border:none; cursor:pointer;" title="Ver número">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="tel:<?php echo $phone; ?>" class="btn-contact btn-call">
                        <i class="fas fa-phone-alt"></i> Llamar
                    </a>
                    <button onclick="openWhatsAppMenu('<?php echo $phone; ?>', '<?php echo $order['id']; ?>')" class="btn-contact btn-whatsapp" style="border:none; cursor:pointer;">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </button>
                </div>
                <?php if($order['delivery_lat'] && $order['delivery_lng']): ?>
                    <div class="map-wrapper">
                        <div id="map-<?php echo $order['id']; ?>" class="map-preview"></div>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $order['delivery_lat']; ?>,<?php echo $order['delivery_lng']; ?>" 
                           target="_blank" class="map-overlay-btn">
                            <i class="fas fa-directions"></i> GPS
                        </a>
                    </div>
                <?php endif; ?>

                <div class="delivery-actions">
                    <?php if($order['status'] === 'confirmed'): ?>
                        <!-- El pedido está asignado y listo para que el repartidor inicie el viaje -->
                        <button class="btn-logistics btn-start" style="background: #ffc107; color: #000;" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'shipped')">
                            <i class="fas fa-play"></i> Iniciar Entrega
                        </button>
                    <?php elseif($order['status'] === 'shipped'): ?>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <button class="btn-logistics btn-complete" style="cursor:pointer;" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                <i class="fas fa-check-circle"></i> CONFIRMAR ENTREGA
                            </button>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <button class="btn-logistics" style="background:#636e72; color:white; padding:12px; font-size:0.8rem;" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'rejected')">
                                    <i class="fas fa-undo"></i> RECHAZADO
                                </button>
                                <button class="btn-logistics" style="background:#ff7675; color:white; padding:12px; font-size:0.8rem;" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">
                                    <i class="fas fa-times"></i> CANCELADO
                                </button>
                            </div>
                        </div>
                    <?php elseif($order['status'] === 'completed'): ?>
                        <div style="text-align: center; color: var(--delivery-primary); font-weight: bold; padding: 10px;">
                            <i class="fas fa-check-double"></i> PEDIDO ENTREGADO
                        </div>
                    <?php elseif($order['status'] === 'rejected'): ?>
                        <div style="text-align: center; color: #636e72; font-weight: bold; padding: 10px;">
                            <i class="fas fa-undo"></i> ENTREGA RECHAZADA
                        </div>
                    <?php elseif($order['status'] === 'cancelled'): ?>
                        <div style="text-align: center; color: #ff7675; font-weight: bold; padding: 10px;">
                            <i class="fas fa-times-circle"></i> PEDIDO CANCELADO
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php return ob_get_clean();
} ?>

<script>
/**
 * Estado global del repartidor para evitar recargas innecesarias
 */
let lastInteractedOrderId = null;
let currentOrdersData = <?php echo json_encode($orders); ?>;
const currentUserId = <?php echo $_SESSION['user_id']; ?>;

// Sonido de claxon para nuevos pedidos
const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/1343/1343-preview.mp3');

/**
 * Sincroniza pedidos sin recargar la página completa
 */
async function refreshDeliveryOrders(forceUpdate = false) {
    try {
        const cacheBuster = Date.now();
        const dateParam = document.getElementById('dateFilter')?.value || '';
        
        // Corregida la URL que causaba el error de ejecución
        const url = `index.php?route=orders_api&delivery_user_id=${currentUserId}&date=${dateParam}&_=${cacheBuster}`;
        
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const newData = await response.json();

        // 1. Verificar si hay cambios reales (ID o Status)
        if (!Array.isArray(newData)) return;
        const currentFingerprint = currentOrdersData.map(o => `${o.id}-${o.status}`).join('|');
        const newFingerprint = newData.map(o => `${o.id}-${o.status}`).join('|');

        if (currentFingerprint !== newFingerprint || forceUpdate) {
            console.log("Detectados cambios en pedidos. Actualizando vista...");
            
            // Detectar si hay IDs nuevos (pedidos recién asignados)
            const currentIds = currentOrdersData.map(o => o.id);
            const hasNewOrders = newData.some(o => !currentIds.includes(o.id));

            if (hasNewOrders) {
                notificationSound.play().catch(e => console.log("Audio bloqueado: requiere interacción previa"));
                Swal.fire("¡Nuevo pedido asignado! 🛵", "success");
            }

            currentOrdersData = newData;
            updateOrdersUI(newData);
        }
    } catch (err) {
        console.error("Error sincronizando pedidos:", err);
    }
}

/**
 * Reconstruye el listado de pedidos en el DOM
 */
function updateOrdersUI(orders) {
    const container = document.getElementById('delivery-orders-container');
    if (!container) return;

    if (orders.length === 0) {
        container.innerHTML = `<div class="empty-state"><i class="fas fa-box-open fa-3x"></i><p>No tienes pedidos asignados por ahora.</p></div>`;
        return;
    }

    container.innerHTML = orders.map(order => renderOrderCardJS(order)).join('');
    
    // Lazy Loading: Reinicializar mapas solo para tarjetas que se muestran expandidas inicialmente
    setTimeout(() => {
        orders.forEach(order => {
            const card = document.getElementById(`card-${order.id}`);
            // Solo cargamos si la tarjeta NO está colapsada y tiene coordenadas
            if (card && !card.classList.contains('collapsed') && order.delivery_lat) {
                initMapForOrder(order);
            }
        });

        // Centrar la vista en el pedido que acaba de ser actualizado
        if (lastInteractedOrderId) {
            const target = document.getElementById(`card-${lastInteractedOrderId}`);
            if (target && target.style.display !== 'none') {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            lastInteractedOrderId = null; // Limpiar para que no salte en el próximo polling automático
        }
    }, 100);

    // Actualizar contadores en el select del layout
    if (typeof updateFilterCounts === 'function') {
        updateFilterCounts(orders);
    }

    // Re-aplicar filtros si el usuario tiene alguno seleccionado
    const activeFilter = document.getElementById('statusFilter').value;
    if (activeFilter !== 'all') {
        document.getElementById('statusFilter').dispatchEvent(new Event('change'));
    }
}

/**
 * Template Literal para renderizar la tarjeta en JS (espejo del PHP)
 */
function renderOrderCardJS(order) {
    const phone = (order.user_phone || '').replace(/\D/g, '');
    const mustCollect = (order.payment_method || '').toLowerCase() === 'efectivo';
    const isCollapsed = ['completed', 'rejected', 'cancelled'].includes(order.status) ? 'collapsed' : '';
    const formattedTotal = new Intl.NumberFormat('es-PY').format(order.total);
    const time = (order.created_at.split(' ')[1] || '').substring(0, 5);
    
    let statusBadge = '';
    if (order.status === 'completed') statusBadge = '<span class="status-badge-header completed"><i class="fas fa-check-circle"></i> Entregado</span>';
    else if (order.status === 'rejected') statusBadge = '<span class="status-badge-header rejected"><i class="fas fa-undo"></i> Rechazado</span>';
    else if (order.status === 'cancelled') statusBadge = '<span class="status-badge-header cancelled"><i class="fas fa-times-circle"></i> Cancelado</span>';
    else if (order.status === 'confirmed') statusBadge = '<span class="status-badge-header shipped" style="background:#fff3cd; color:#856404;"><i class="fas fa-clock"></i> Asignado</span>';
    else if (order.status === 'shipped') statusBadge = '<span class="status-badge-header shipped"><i class="fas fa-truck"></i> En Camino</span>';

    return `
        <div class="order-card ${order.status} ${isCollapsed}" data-status="${order.status}" id="card-${order.id}">
            <div class="card-info-section">
                <div class="order-header">
                    <div class="header-left">
                        <button class="toggle-section-btn" onclick="toggleCardSection(this.closest('.order-card'))">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <span class="order-number">#${order.id}</span>
                    </div>
                    <div class="header-right">
                        ${statusBadge}
                        <span class="order-time">${time}</span>
                    </div>
                </div>
                <div class="client-info">
                    <h3>${order.user_name || 'Cliente'}</h3>
                    <div class="address-box"><i class="fas fa-map-marker-alt"></i><span>${order.delivery_address || 'Sin dirección'}</span></div>
                </div>
                <div class="payment-summary">
                    <div class="payment-row">
                        <span class="payment-label">Método: ${order.payment_method}</span>
                        <span class="badge-payment ${mustCollect ? 'bg-collect' : 'bg-paid'}">${mustCollect ? 'A Cobrar' : 'Ya Pagado'}</span>
                    </div>
                    <div class="payment-row" style="margin-top: 5px;">
                        <span class="payment-label">Monto Total:</span>
                        <span class="amount-to-collect">Gs. ${formattedTotal}</span>
                    </div>
                </div>
            </div>
            <div class="card-operative-section">
                <div class="contact-actions">
                    <a href="tel:${phone}" class="btn-contact btn-call"><i class="fas fa-phone-alt"></i> Llamar</a>
                    <button onclick="showPhoneModal('${phone}')" class="btn-contact btn-call" style="background: #0984e3; border:none; cursor:pointer;" title="Ver número">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="openWhatsAppMenu('${phone}', '${order.id}')" class="btn-contact btn-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                </div>
                ${order.delivery_lat ? `<div class="map-wrapper"><div id="map-${order.id}" class="map-preview"></div><a href="https://www.google.com/maps/search/?api=1&query=${order.delivery_lat},${order.delivery_lng}" target="_blank" class="map-overlay-btn"><i class="fas fa-directions"></i> GPS</a></div>` : ''}
                <div class="delivery-actions">
                    ${order.status === 'confirmed' ? `<button class="btn-logistics btn-start" style="background: #ffc107; color: #000; cursor: pointer;" onclick="updateOrderStatus(${order.id}, 'shipped')"><i class="fas fa-play"></i> Iniciar Entrega</button>` : ''}
                    ${order.status === 'shipped' ? `
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <button class="btn-logistics btn-complete" style="cursor: pointer;" onclick="updateOrderStatus(${order.id}, 'completed')"><i class="fas fa-check-circle"></i> CONFIRMAR ENTREGA</button>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <button class="btn-logistics" style="background:#636e72; color:white; padding:12px; font-size:0.8rem; cursor: pointer;" onclick="updateOrderStatus(${order.id}, 'rejected')"><i class="fas fa-undo"></i> RECHAZADO</button>
                                <button class="btn-logistics" style="background:#ff7675; color:white; padding:12px; font-size:0.8rem; cursor: pointer;" onclick="updateOrderStatus(${order.id}, 'cancelled')"><i class="fas fa-times"></i> CANCELADO</button>
                            </div>
                        </div>` : ''}
                    ${order.status === 'completed' ? `
                        <div style="text-align: center; color: var(--delivery-primary); font-weight: bold; padding: 10px;">
                            <i class="fas fa-check-double"></i> PEDIDO ENTREGADO
                        </div>` : ''}
                    ${order.status === 'rejected' ? `
                        <div style="text-align: center; color: #636e72; font-weight: bold; padding: 10px;">
                            <i class="fas fa-undo"></i> ENTREGA RECHAZADA
                        </div>` : ''}
                    ${order.status === 'cancelled' ? `
                        <div style="text-align: center; color: #ff7675; font-weight: bold; padding: 10px;">
                            <i class="fas fa-times-circle"></i> PEDIDO CANCELADO
                        </div>` : ''}
                </div>
            </div>
        </div>`;
}

/**
 * Inicializa un mapa de Leaflet para un pedido específico
 */
function initMapForOrder(order) {
    const mapId = `map-${order.id}`;
    const container = document.getElementById(mapId);
    if (!container || container._leaflet_id) return; // Evitar reinicializar si ya existe

    // Ajuste de zoom y centrado
    const map = L.map(mapId, { zoomControl: false, dragging: true, touchZoom: true, scrollWheelZoom: false }).setView([order.delivery_lat, order.delivery_lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    const markerIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='background-color:#00e676; width:12px; height:12px; border-radius:50%; border:2px solid white;'></div>",
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });
    L.marker([order.delivery_lat, order.delivery_lng], {icon: markerIcon}).addTo(map);
    
    // Asegurar que el mapa calcule bien su tamaño al aparecer
    setTimeout(() => map.invalidateSize(), 400);
}

// Iniciar polling inteligente cada 15 segundos
setInterval(refreshDeliveryOrders, 15000);

// Carga inicial de mapas (solo para los que arrancan expandidos)
document.addEventListener('DOMContentLoaded', () => {
    currentOrdersData.forEach(order => {
        const card = document.getElementById(`card-${order.id}`);
        if (card && !card.classList.contains('collapsed') && order.delivery_lat) {
            initMapForOrder(order);
        }
    });
});

/**
 * Expande o colapsa la sección operativa de la tarjeta
 */
function toggleCardSection(cardElement) {
    cardElement.classList.toggle('collapsed');

    // Lazy Loading: Inicializar mapa al expandir si existe la data y no ha sido inicializado aún
    if (!cardElement.classList.contains('collapsed')) {
        const orderId = cardElement.id.replace('card-', '');
        const order = currentOrdersData.find(o => String(o.id) === String(orderId));
        if (order && order.delivery_lat && order.delivery_lng) {
            initMapForOrder(order);
        }
    }
}

/**
 * Muestra el número de teléfono en un modal para chequeo rápido
 */
function showPhoneModal(phone) {
    if (!phone) {
        Toast.fire("No hay número registrado", "error");
        return;
    }
    Swal.fire({
        title: 'Número de Contacto',
        html: `<h2 style="letter-spacing: 2px; color: #2d3436;">${phone}</h2>`,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#2d3436'
    });
}

/**
 * Abre un menú táctil para seleccionar mensajes predeterminados de WhatsApp
 */
function openWhatsAppMenu(phone, orderId) {
    const messages = [
        { title: "💬 Solo abrir chat", text: "" },
        { title: " En camino", text: `¡Hola! Soy el repartidor de *Solver*. Estoy en camino con tu pedido *#${orderId}*. Llego en 5-10 min.` },
        { title: "📍 En la puerta", text: `¡Hola! Ya llegué con tu pedido *#${orderId}*. Estoy afuera.` },
        { title: "⏳ Retraso", text: `¡Hola! Te pido disculpas, hay un poco de tráfico. Tu pedido *#${orderId}* llegará con unos minutos de retraso.` },
        { title: "❓ No encuentro la ubicación", text: `¡Hola! No estoy logrando ubicar tu dirección para el pedido *#${orderId}*. ¿Podrías darme una referencia?` }
    ];

    const options = messages.map((m, index) => 
        `<button onclick="sendWA('${phone}', '${m.text ? encodeURIComponent(m.text) : ''}')" 
                 class="swal2-confirm swal2-styled" style="display:block; width:100%; margin: 10px 0; background:${m.text ? '#25D366' : '#2d3436'}; border:none; border-radius:8px; padding:12px; color:white; font-weight:bold; cursor:pointer;">${m.title}</button>`
    ).join('');

    Swal.fire({
        title: 'Enviar Mensaje',
        html: `<div style="text-align:left;">${options}</div>`,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Cerrar'
    });
}

function sendWA(phone, text) {
    // Formato internacional para Paraguay (595)
    const url = text 
        ? `https://wa.me/595${phone}?text=${text}` 
        : `https://wa.me/595${phone}`;
    window.open(url, '_blank');
    Swal.close();
}

/**
 * Actualiza el estado del pedido vía AJAX
 */
function updateOrderStatus(orderId, newStatus) {
    let confirmText = '¿Confirmas esta acción?';
    if (newStatus === 'completed') confirmText = '¿Confirmas que el pedido fue ENTREGADO con éxito?';
    if (newStatus === 'rejected') confirmText = '¿El cliente rechazó el pedido?';
    if (newStatus === 'cancelled') confirmText = '¿Deseas CANCELAR esta entrega por algún inconveniente?';
    
    Swal.fire({
        title: 'Gestión de Pedido',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus === 'completed' ? '#00e676' : '#636e72',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, proceder'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', orderId);
            formData.append('status', newStatus);

            fetch('?route=orders_update_status', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Haptic Feedback: Vibración doble corta al completar con éxito
                    if (newStatus === 'completed' && "vibrate" in navigator) {
                        navigator.vibrate([100, 50, 100]);
                    }

                // Guardamos el ID para re-centrar la vista tras el renderizado
                lastInteractedOrderId = orderId;
                
                // Solo refrescamos y centramos después de que el usuario cierre el SweetAlert
                Swal.fire("¡Actualizado!", "El estado del pedido ha sido actualizado.", "success").then(() => {
                    refreshDeliveryOrders(true);
                });
                }
            });
        }
    });
}
</script>