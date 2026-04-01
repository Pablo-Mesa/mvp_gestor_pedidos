<!-- Leaflet para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .delivery-section-title {
        font-size: 1rem;
        font-weight: bold;
        color: var(--delivery-subtext);
        margin: 10px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
    }
    .order-delivery-card {
        background: var(--delivery-card);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #333;
    }
    .order-delivery-card.shipped {
        border: 2px solid var(--delivery-primary);
    }
    .order-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .order-number {
        font-weight: 900;
        font-size: 1.3rem;
        color: var(--delivery-primary);
    }
    .order-time {
        font-size: 0.85rem;
        color: var(--delivery-subtext);
    }
    .client-info h3 {
        font-size: 1.4rem;
        margin-bottom: 5px;
        color: white;
    }

    /* Información de Cobro */
    .payment-summary {
        background: rgba(255, 255, 255, 0.03);
        border: 1px dashed #444;
        border-radius: 10px;
        padding: 12px;
        margin: 10px 0;
    }
    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    .payment-label { font-size: 0.8rem; color: var(--delivery-subtext); text-transform: uppercase; }
    .payment-value { font-weight: bold; color: white; }
    .amount-to-collect {
        font-size: 1.4rem;
        color: var(--delivery-primary);
        font-weight: 900;
    }
    .badge-payment {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: bold;
        text-transform: uppercase;
    }
    .bg-collect { background: #ff7675; color: #fff; }
    .bg-paid { background: #55efc4; color: #000; }

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
        grid-template-columns: 1fr 1fr;
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
        border: 1px solid #333;
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
        border-top: 1px solid #333;
    }
    .btn-logistics {
        width: 100%;
        padding: 18px;
        border-radius: 12px;
        font-weight: 900;
        font-size: 1rem;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-transform: uppercase;
    }
    .btn-start { background: var(--delivery-primary); color: #000; }
    .btn-complete { background: #2979ff; color: white; }
    .empty-state { text-align: center; padding: 5rem 2rem; color: var(--delivery-subtext); }
</style>

<div>
    <h2 class="delivery-section-title"><i class="fas fa-truck"></i> Mis Entregas Activas</h2>
    
    <?php if(empty($activeOrders) && empty($pendingOrders)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open fa-3x"></i>
            <p>No tienes pedidos asignados por ahora.</p>
        </div>
    <?php endif; ?>

    <?php foreach($orders as $order): ?>
        <?php 
            $phone = preg_replace('/[^0-9]/', '', $order['user_phone'] ?? '');
            // Lógica de cobro: Si es efectivo, el repartidor debe cobrar.
            $mustCollect = (isset($order['payment_method']) && strtolower($order['payment_method']) === 'efectivo');
        ?>
        <div class="order-delivery-card <?php echo $order['status']; ?>">
            <div class="order-header">
                <span class="order-number">#<?php echo $order['id']; ?></span>
                <span class="order-time"><?php echo date('H:i', strtotime($order['created_at'])); ?></span>
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
                    <span class="payment-label" style="color: white;">Monto Total:</span>
                    <span class="amount-to-collect">Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?></span>
                </div>
            </div>

            <div class="contact-actions">
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
                    <button class="btn-logistics btn-start">
                        <i class="fas fa-play"></i> Iniciar Entrega
                    </button>
                <?php elseif($order['status'] === 'shipped'): ?>
                    <button class="btn-logistics btn-complete">
                        <i class="fas fa-check-circle"></i> Marcar como Entregado
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach($orders as $order): ?>
        <?php if($order['delivery_lat'] && $order['delivery_lng']): ?>
            (function() {
                var lat = <?php echo $order['delivery_lat']; ?>;
                var lng = <?php echo $order['delivery_lng']; ?>;
                var mapId = 'map-<?php echo $order['id']; ?>';
                
                var map = L.map(mapId, {
                    zoomControl: false,
                    attributionControl: false
                }).setView([lat, lng], 16);
                
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);
                
                var markerIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color:#00e676; width:12px; height:12px; border-radius:50%; border:2px solid white;'></div>",
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });

                L.marker([lat, lng], {icon: markerIcon}).addTo(map);
            })();
        <?php endif; ?>
    <?php endforeach; ?>
});
</script>

<script>
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
        cancelButtonText: 'Cerrar',
        background: '#1e1e1e',
        color: '#fff'
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
</script>