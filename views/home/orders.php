<style>
    .orders-history-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 0 1rem;
        /* Altura dinámica: 100vh menos el header y márgenes de seguridad */
        height: calc(100vh - var(--compact-header-height) - 20px); 
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch; /* Scroll suave en iOS */
        scrollbar-width: thin;
    }

    /* Estilos para el encabezado y los filtros de mes que deben ser sticky */
    /* Este contenedor agrupa el título y los filtros de mes */
    .history-controls-fixed {
        position: sticky;
        top: 0; /* Se pegará a la parte superior del contenedor scrollable */
        background-color: #fbfbfb; /* Fondo para que el contenido no se vea a través */
        z-index: 10; /* Asegura que esté por encima de las tarjetas */
        margin: 0 -1rem; /* Compensar el padding lateral del contenedor principal */
        padding-left: 1rem;
        padding-right: 1rem;
        padding: 1rem 1rem 1rem 1rem; /* Padding interno para el sticky wrapper */
    }

    .history-header {
        display: flex; 
        flex-direction: row; 
        justify-content: space-between; 
        align-items: center; 
        width: 100%;
        padding: 0.5rem 0;
    }

    .list-months {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 12px 0;
        scrollbar-width: none; /* Firefox */
    }
    .list-months::-webkit-scrollbar { display: none; }

    .month-pill {
        padding: 8px 16px;
        background: rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        text-decoration: none;
        color: #555;
        font-weight: 500;
        font-size: 0.8rem;
        white-space: nowrap;
        transition: 0.3s;
    }
    .month-pill.active, .month-pill:hover {
        background: #000;
        color: #fff;
        border-color: #000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .section-title { font-size: 1.4rem; font-weight: 800; color: #2d3436; letter-spacing: -0.5px; margin: 0; }
    
    .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px; border: 1px solid #eee; }
    .empty-state i { font-size: 3rem; color: #ccc; margin-bottom: 1rem; display: block; }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1rem; 
        padding-bottom: 2rem;
        width: 100% ;
        /* No necesita overflow-y: auto aquí, ya lo tiene el contenedor padre */
    }
    .order-card { 
        width: 100%;
        background: white;
        border-radius: 16px; 
        border: 1px solid #eee; 
        padding: 1.2rem; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.02); 
        transition: all 0.3s ease; 
    }
    .order-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); border-color: #ddd; }
    
    .order-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8f9fa; padding-bottom: 1rem; margin-bottom: 1rem; }
    .order-id { font-weight: 800; color: #2d3436; font-size: 1.05rem; }
    .order-date { color: #a4b0be; font-size: 0.8rem; font-weight: 500; }

    .order-body { display: flex; justify-content: space-between; align-items: center; }
    .order-info p { margin-bottom: 5px; font-size: 0.9rem; color: #57606f; }
    .order-info strong { color: #2d3436; }

    .btn-detail {
        background: #fff;
        border: 1px solid #eee;
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 700;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #2d3436;
    }
    .btn-detail:hover { background: #f8f9fa; border-color: #2d3436; }

    .order-status-badge {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Colores de estado */
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-preparing { background-color: #d1ecf1; color: #0c5460; }
    .status-shipped { background-color: #e3f2fd; color: #1976d2; }
    .status-completed { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f5f5f5; color: #616161; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }

    @media (max-width: 768px) {
        .orders-history-container {
            height: calc(100vh - var(--compact-header-height) - 10px);
            padding: 0 12px; /* Reducimos el padding lateral para ganar espacio */
        }
        .history-controls-fixed {
            margin: 0 -12px 1rem -12px; /* Sincronizado con el nuevo padding del contenedor */
            padding: 1rem 12px 0.5rem 12px;
        }
        .history-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
            padding: 0;
        }
    }
</style>

<div class="orders-history-container">

    <div class="history-controls-fixed">
        <div class="history-header">
            <h2 class="section-title"> <i class="fas fa-history"></i> Mi Historial de Pedidos</h2>
        </div>
        <div class="list-months">
            <?php 
            $monthsES = [1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'];
            
            // Botón "Todos"
            $allActive = (!isset($_GET['month'])) ? 'active' : '';
            echo "<a href='?route=my_orders' class='month-pill $allActive'>Todos</a>";

            if (!empty($availableMonths)) {
                foreach ($availableMonths as $m) {
                    $isCurrent = (isset($_GET['month']) && $_GET['month'] == $m['month'] && $_GET['year'] == $m['year']) ? 'active' : '';
                    $label = $monthsES[$m['month']] . ' ' . $m['year'];
                    echo "<a href='?route=my_orders&month={$m['month']}&year={$m['year']}' class='month-pill $isCurrent'>$label</a>";
                }
            }
            ?>
        </div>
    </div>

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
                            <p style="font-size: 1.1rem; margin-bottom: 8px;">
                                <strong>Total:</strong> <span style="color: #28a745; font-weight: 800;">Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?></span>
                            </p>
                            
                            <?php if ($order['delivery_type'] === 'delivery'): ?>
                                <p><i class="fas fa-motorcycle" style="width: 18px; color: #a4b0be;"></i> <strong>Envío:</strong> Gs. <?php echo number_format($order['delivery_cost'] ?? 0, 0, ',', '.'); ?></p>
                            <?php endif; ?>

                            <p><i class="fas fa-wallet" style="width: 18px; color: #a4b0be;"></i> <strong>Pago:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                            <p><i class="fas fa-shopping-bag" style="width: 18px; color: #a4b0be;"></i> <strong>Tipo:</strong> <?php echo ($order['delivery_type'] === 'delivery') ? 'Delivery' : (($order['delivery_type'] === 'pickup') ? 'Retiro' : 'En Local'); ?></p>
                        </div>

                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px; min-width: 150px;">
                            <div class="order-status-badge status-<?php echo $order['status']; ?>" id="status-badge-<?php echo $order['id']; ?>" style="width: 100%; text-align: center;">
                                <?php 
                                    $statusNames = [
                                        'pending' => 'Pendiente',
                                        'preparing' => 'En Cocina',
                                        'shipped' => 'En Camino 🛵',
                                        'rejected' => 'Rechazado',
                                        'completed' => 'Entregado ✅',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    
                                    $label = $statusNames[$order['status']] ?? $order['status'];
                                    if ($order['status'] === 'confirmed' && !empty($order['delivery_user_id'])) {
                                        $label = "Asignado 🛵";
                                    }
                                    echo strtoupper($label);
                                ?>
                            </div>
                            <button class="btn-detail" onclick="showDetails(<?php echo $order['id']; ?>, <?php echo $order['delivery_cost'] ?? 0; ?>)" style="width: 100%; justify-content: center;">
                                <i class="fas fa-eye"></i> Detalle
                            </button>
                        </div>
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
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem; background-color: #f0f2f5; padding: 1rem; border-radius: 8px; border: 1px solid #ddd;">
                <i class="fas fa-receipt" style="font-size: 1.5rem; color: #007bff;"></i>
                <h3 style="margin:0">Detalles del Pedido #<span id="detailOrderId"></span></h3>
            </div>
            <div id="orderDetailsList" style="margin-bottom: 1.5rem;">
                <!-- Aquí se cargarán los platos -->
            </div>

            <!-- Desglose de Totales estilo Ticket -->
            <div id="orderTotalContainer" style="padding: 1.2rem; background-color: #fcfcfc; border-radius: 12px; border: 1px dashed #ced4da; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #636e72; font-size: 0.9rem;">
                    <span>Subtotal Productos:</span>
                    <span id="totalItemsMonto">Gs. 0</span>
                </div>
                <div id="deliveryDetailRow" style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee; color: #636e72; font-size: 0.9rem;">
                    <span>Costo de Envío:</span>
                    <span id="deliveryMonto">Gs. 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 800; color: #2d3436;">
                    <span style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;">Total Final</span>
                    <span id="totalDetalleMonto" style="font-size: 1.4rem; color: #28a745;">Gs. 0</span>
                </div>
            </div>

            <button class="btn-main" onclick="closeDetailsModal()" style="width: 100%;">Cerrar</button>
        </div>
    </div>
</div>

<script>
// Diccionario de estados para la actualización visual
const statusLabels = {
    'pending': 'PENDIENTE',
    'confirmed': 'CONFIRMADO',
    'preparing': 'EN COCINA',
    'shipped': 'EN CAMINO 🛵',
    'completed': '¡ENTREGADO! ✅',
    'rejected': 'RECHAZADO ❌',
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
                        
                        let text = statusLabels[newStatus] || newStatus;
                        if (newStatus === 'confirmed' && order.delivery_user_id) {
                            text = "CONFIRMADO + DELIVERY ASIGNADO";
                        }
                        badge.innerText = text;

                        // Notificación especial si pasa a "Entregado"
                        if (newStatus === 'completed') {
                            Toast.fire(`¡Pedido #${order.id} ENTREGADO! ¡Buen provecho!`, "success");
                        } else if (newStatus === 'rejected') {
                             Toast.fire(`Pedido #${order.id} RECHAZADO. Lo sentimos, no pudo ser entregado.`, "error");
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

async function showDetails(orderId, deliveryCost = 0) {
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
            let html = '<table style="width:100%; border-collapse: collapse; font-size: 0.95rem;">';
            html += '<tr style="border-bottom: 2px solid #f1f2f6; text-align:left; color: #a4b0be; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;"><th style="padding:8px;">Plato</th><th style="padding:8px; text-align:center;">Cant.</th><th style="padding:8px; text-align:right;">Subtotal</th></tr>';
            let total_detalles = 0;

            result.data.forEach(item => {
                const subtotal = item.quantity * item.price;
                html += `<tr style="border-bottom: 1px solid #f1f2f6;">
                    <td style="padding:12px 8px; font-weight: 600; color: #2d3436;">${item.product_name}</td>
                    <td style="padding:12px 8px; text-align:center; color: #636e72;">${item.quantity}</td>
                    <td style="padding:12px 8px; text-align:right; font-weight: 700; color: #2d3436;">Gs. ${new Intl.NumberFormat('es-PY').format(subtotal)}</td>
                </tr>`;
                total_detalles = total_detalles + subtotal; 
            });
            html += '</table>';
            listContainer.innerHTML = html;
            
            // Actualizar Totales en el Modal
            const formatter = new Intl.NumberFormat('es-PY');
            document.getElementById('totalItemsMonto').innerText = `Gs. ${formatter.format(total_detalles)}`;
            
            // Lógica de visibilidad del Delivery
            const deliveryRow = document.getElementById('deliveryDetailRow');
            const deliveryVal = parseInt(deliveryCost) || 0;
            
            if (deliveryVal > 0) {
                deliveryRow.style.display = 'flex';
                document.getElementById('deliveryMonto').innerText = `Gs. ${formatter.format(deliveryVal)}`;
            } else {
                deliveryRow.style.display = 'none';
            }
            
            const finalTotal = total_detalles + deliveryVal;
            document.getElementById('totalDetalleMonto').innerText = `Gs. ${formatter.format(finalTotal)}`;
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
