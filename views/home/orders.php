<div class="orders-history-container">
    
    <h2 class="section-title"> <i class="fas fa-history"></i> Mi Historial de Pedidos</h2>

    <?php if(empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-utensils"></i>
            <p>Aún no has realizado ningún pedido.</p>
            <a href="?route=home" class="btn-main" style="text-decoration: none; display: inline-block; margin-top: 1rem;">Ver el Menú</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach($orders as $order): ?>
                <div class="order-card" id="order-card-<?php echo $order['id']; ?>" data-status="<?php echo $order['status']; ?>">
                    <div class="order-header">
                        <span class="order-id">Pedido #<?php echo $order['id']; ?></span>
                        <span class="order-date"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="order-body">
                        <div class="order-info">
                            <p><strong>Total:</strong> Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?></p>
                            <p><strong>Entrega:</strong> <?php echo ucfirst($order['delivery_type']); ?></p>
                            <p><strong>Pago:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                        </div>
                        <div class="order-status-badge status-<?php echo $order['status']; ?>" id="status-badge-<?php echo $order['id']; ?>">
                            <?php 
                                $statusNames = [
                                    'pending' => 'Pendiente',
                                    'preparing' => 'En Cocina',
                                    'delivered' => 'Entregado',
                                    'cancelled' => 'Cancelado'
                                ];
                                echo $statusNames[$order['status']] ?? $order['status'];
                            ?>
                        </div>
                        <button class="btn-detail" onclick="showDetails(<?php echo $order['id']; ?>)">
                            <i class="fas fa-eye"></i> Ver Detalle
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
</div>

<!-- Modal para detalles del pedido -->
<div id="detailsModal" class="modal-overlay" style="display:none; align-items:center; justify-content:center;">
    <div class="modal-card" style="max-width: 500px;">
        <div class="modal-content">
            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">
                Detalles del Pedido #<span id="detailOrderId"></span>
            </h3>
            <div id="orderDetailsList" style="margin-bottom: 1.5rem;">
                <!-- Aquí se cargarán los platos -->
            </div>

            <!-- NUEVO: Div para el Total -->
            <div id="orderTotalContainer" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background-color: #f9f9f9; border-radius: 8px; border: 1px solid #eee;">
                <span style="font-weight: bold; color: #555; text-transform: uppercase; font-size: 0.9rem;">Total a Pagar</span>
                <span id="totalDetalleMonto" style="font-size: 1.25rem; font-weight: 800; color: #333;">
                    Gs. 0
                </span>
            </div>

            <button class="btn-main" onclick="closeDetailsModal()" style="width: 100%;">Cerrar</button>
        </div>
    </div>
</div>

<script>
// Diccionario de estados para la actualización visual
const statusLabels = {
    'pending': 'Pendiente',
    'preparing': 'En Cocina',
    'delivered': 'Entregado',
    'cancelled': 'Cancelado'
};

/**
 * Verifica cambios en los estados de los pedidos cada 10 segundos
 */
async function checkStatusUpdates() {
    try {
        const response = await fetch('?route=my_orders_status');
        const result = await response.json();

        if (result.success) {
            result.orders.forEach(order => {
                const card = document.getElementById(`order-card-${order.id}`);
                const badge = document.getElementById(`status-badge-${order.id}`);
                
                if (card && badge) {
                    const oldStatus = card.getAttribute('data-status');
                    const newStatus = order.status;

                    if (oldStatus !== newStatus) {
                        // Actualizar Atributo y Clase del Badge
                        card.setAttribute('data-status', newStatus);
                        badge.className = `order-status-badge status-${newStatus}`;
                        badge.innerText = statusLabels[newStatus] || newStatus;

                        // Notificación especial si pasa a "Entregado"
                        if (newStatus === 'delivered') {
                            Toast.fire({
                                icon: 'success',
                                title: `¡Pedido #${order.id} Entregado!`,
                                text: '¡Buen provecho! Gracias por elegirnos.'
                            });
                        } else {
                            Toast.fire(`El pedido #${order.id} ahora está: ${statusLabels[newStatus]}`, 'info');
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error("Error en polling de estados:", error);
    }
}

// Iniciar el polling cada 10 segundos si hay pedidos en la lista
if (document.querySelectorAll('.order-card').length > 0) {
    setInterval(checkStatusUpdates, 10000);
}

async function showDetails(orderId) {
    const modal = document.getElementById('detailsModal');
    const listContainer = document.getElementById('orderDetailsList');
    const idSpan = document.getElementById('detailOrderId');

    idSpan.innerText = orderId;
    listContainer.innerHTML = '<p style="text-align:center;">Cargando...</p>';
    modal.style.display = 'flex';

    try {
        const response = await fetch(`?route=my_order_details&id=${orderId}`);
        const result = await response.json();

        if (result.success) {
            let html = '<table style="width:100%; border-collapse: collapse;">';
            html += '<tr style="border-bottom: 2px solid #eee; text-align:left;"><th style="padding:8px;">Plato</th><th style="padding:8px;">Cant.</th><th style="padding:8px;">Subtotal</th></tr>';
            let total_detalles = 0;

            result.data.forEach(item => {
                const subtotal = item.quantity * item.price;
                html += `<tr style="border-bottom: 1px solid #f9f9f9;">
                    <td style="padding:8px;">${item.product_name}</td>
                    <td style="padding:8px;">${item.quantity}</td>
                    <td style="padding:8px;">Gs. ${new Intl.NumberFormat('es-PY').format(subtotal)}</td>
                </tr>`;
                total_detalles = total_detalles + subtotal; 
            });
            console.log("total detalles: " + total_detalles);
            html += '</table>';
            listContainer.innerHTML = html;
            // ... después de cerrar el forEach:
            document.getElementById('totalDetalleMonto').innerText = `Gs. ${new Intl.NumberFormat('es-PY').format(total_detalles)}`;
        } else {
            listContainer.innerHTML = `<p style="color:red;">${result.message}</p>`;
        }
    } catch (error) {
        listContainer.innerHTML = '<p style="color:red;">Error al cargar los datos.</p>';
    }
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}
</script>

<style>
    .orders-history-container { max-width: 800px; margin: 0 auto; padding-bottom: 2rem;}
    .section-title { margin-bottom: 1.5rem; color: #333; display: flex; align-items: center; gap: 10px; }
    
    .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .empty-state i { font-size: 3rem; color: #ccc; margin-bottom: 1rem; display: block; }

    .orders-list { display: flex; flex-direction: column; gap: 1rem; }
    
    .order-card { background: white; border-radius: 12px; border: 1px solid #eee; padding: 1.2rem; box-shadow: 0 2px 5px rgba(0,0,0,0.03); transition: transform 0.2s; }
    .order-card:hover { transform: scale(1.01); border-color: #ddd; }
    
    .order-header { display: flex; justify-content: space-between; border-bottom: 1px dashed #eee; padding-bottom: 0.8rem; margin-bottom: 0.8rem; }
    .order-id { font-weight: bold; color: #007bff; }
    .order-date { color: #888; font-size: 0.85rem; }

    .order-body { display: flex; justify-content: space-between; align-items: center; }
    .order-info p { margin-bottom: 4px; font-size: 0.9rem; color: #555; }

    .btn-detail {
        background: #f0f2f5;
        border: 1px solid #ddd;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: 0.2s;
    }
    .btn-detail:hover { background: #e4e6e9; border-color: #ccc; }

    .order-status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Colores de estado */
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-preparing { background-color: #d1ecf1; color: #0c5460; }
    .status-delivered { background-color: #d4edda; color: #155724; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }

    @media (max-width: 600px) {
        .order-body { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .order-status-badge { align-self: flex-end; }
    }
</style>