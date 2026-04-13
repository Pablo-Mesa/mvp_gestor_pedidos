<style>
    /* Ajuste del contenedor principal para permitir crecimiento dinámico */
    .content-wrapper {
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important; /* Evita el scroll doble en la página */
    }

    /* Reutilizamos estilos de products/index.php para consistencia */
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        background-color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .filter-form { display: flex; align-items: center; }
    .filter-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    
    .filter-group input, .filter-group select {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 0.9rem;
        outline: none;
    }
    .filter-group input:focus, .filter-group select:focus { border-color: #007bff; }

    .btn-filter-submit { background: #343a40; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: 0.2s; }
    .btn-filter-submit:hover { background: #212529; }

    .btn-clear { color: #dc3545; background: #fff1f2; padding: 8px 12px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; justify-content: center; }

    .page-title { margin: 0; font-size: 1.5rem; color: #333; }
    
    /* Optimizaciones Responsive para el Header de Acciones */
    @media (max-width: 992px) {
        .header-actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
        }
        .filter-form {
            width: 100%;
        }
        .filter-group {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr; /* Dos columnas en tablets */
            gap: 10px;
        }
        .filter-group input[name="client_name"] {
            grid-column: span 2; /* El buscador ocupa todo el ancho */
        }
        .btn-filter-submit, .btn-clear {
            justify-content: center;
            height: 40px;
        }

        .status-counters {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 1rem!important;
            padding: 0.4rem 0.6rem!important;
            border-radius: 12px!important;
        }

    }

    @media (max-width: 576px) {
        .filter-group {
            grid-template-columns: 1fr; /* Una sola columna en móviles pequeños */
        }
        .filter-group input[name="client_name"] {
            grid-column: span 1;
        }
    }
    
    /**/
    
    /* Tabla con Scroll */
    .contenedor-tabla {
        flex: 1;
        min-height: 0; /* Permite que el contenedor se reduzca y active el scroll interno */
        overflow-y: auto;
        border-radius: 8px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        background: white;
        margin-bottom: 1rem;
    }
    table {
        width: 100%;
        border-collapse: collapse; 
        background: white;
    }
    thead th {
        position: sticky;
        top: 0;           /* Se queda pegado arriba */
        z-index: 10;      /* Asegura que quede por encima del contenido del tbody */
        background-color: #f8f9fa; /* Usamos el mismo gris claro de tus th originales */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Opcional: añade una sombrita para dar profundidad */
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    /* 1. Cuando pases el mouse sobre la TABLA, desenfoca TODAS las filas */
    table tbody:hover tr {
        filter: blur(1.5px);
        opacity: 0.5; /* Opcional: ayuda a que el efecto se vea más limpio */
        transition: filter 0.3s, opacity 0.3s;
    }

    /* 2. Pero, la fila que tiene el mouse encima se mantiene clara y con tu color */
    table tbody tr:hover {
        filter: blur(0);
        opacity: 1;
        background-color: #f1f1f1;
    }

    /* Estilo para el select de estado en la tabla */
    .status-select {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        outline: none;
    }

    /* Corrección estética: Restablecer el fondo de las opciones para que no hereden el color del select */
    .status-select option {
        background-color: #fff !important;
        color: #333 !important;
    }

    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
    /* Colores de estado */
    .status-pending { background: #ffc107; color: #333; } /* Amarillo */
    .status-confirmed { background: #17a2b8; color: #fff; } /* Azul Info */
    .status-shipped { background: #1976d2; color: #fff; } /* Azul oscuro */
    .status-rejected { background: #6c757d; color: #fff; } /* Gris */
    .status-completed { background: #28a745; color: #fff; } /* Verde */
    .status-cancelled { background: #dc3545; color: #fff; } /* Rojo */

    .btn-view { padding: 5px 10px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem; }
    .btn-view:hover { background: #0056b3; }

    .btn-print-table { padding: 5px 8px; color: white; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: bold; margin-left: 3px; }
    .btn-print-80 { background: #17a2b8; }
    .btn-print-58 { background: #6c757d; }

    /* Animación para nuevos pedidos */
    @keyframes highlightNew {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
    .row-new {
        animation: highlightNew 5s ease-out;
    }

    .status-counters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0.8rem 1.5rem;
        background: #ffffff;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        z-index: 100;
        border: 1px solid #dee2e6;
        width: 100%;
        flex-shrink: 0; /* Evita que la barra de estados se aplaste */
    }

    .stat-card {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        padding: 0.2rem;
        border-radius: 10px;
        text-align: center;
        transition: transform 0.2s;
        width: 150px;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card i { font-size: 1.2rem; margin-bottom: 5px; display: block; }
    .stat-card span { font-size: 1.1rem; font-weight: bold; display: block; }
    .stat-card label { font-size: 0.75rem; text-transform: uppercase; color: #666; font-weight: 600; }
    
    /* Animación de Pulso para los números */
    @keyframes pulseUpdate {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); color: #007bff; }
        100% { transform: scale(1); }
    }

    .pulse-animation {
        animation: pulseUpdate 0.6s ease-out;
        display: inline-block;
    }

    .stat-all{ background: #e2e3e5; border-color: #d6d8db; color: #383d41; }
    .stat-pending { background: #fff9db; border-color: #ffc107; color: #856404; }
    .stat-confirmed { background: #e3f2fd; border-color: #17a2b8; color: #0c5460; }
    .stat-completed { background: #e8f5e9; border-color: #28a745; color: #155724; }
    .stat-cancelled { background: #fce8e8; border-color: #dc3545; color: #721c24; }
</style>

<!-- titulo -->
<div class="header-actions">
    <h1 class="page-title">Gestión de Pedidos</h1>
    <!-- Filtros -->
    <form action="index.php" method="GET" class="filter-form">
        <input type="hidden" name="route" value="orders">
        
        <div class="filter-group">
            <input type="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>" title="Filtrar por Fecha">
            
            <select name="delivery_type">
                <option value="">Todas las entregas</option>
                <option value="delivery" <?php echo ($_GET['delivery_type'] ?? '') == 'delivery' ? 'selected' : ''; ?>>🛵 Delivery</option>
                <option value="pickup" <?php echo ($_GET['delivery_type'] ?? '') == 'pickup' ? 'selected' : ''; ?>>🛍️ Retiro</option>
                <option value="local" <?php echo ($_GET['delivery_type'] ?? '') == 'local' ? 'selected' : ''; ?>>🍽️ Mesa Local</option>
            </select>

            <select name="status">
                <option value="">Todos los estados</option>
                <option value="pending" <?php echo ($_GET['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>🟡 Pendientes</option>
                <option value="confirmed" <?php echo ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : ''; ?>>🔵 Confirmados</option>
                <option value="completed" <?php echo ($_GET['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>🟢 Entregados</option>
                <option value="cancelled" <?php echo ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>🔴 Cancelados</option>
                <option value="rejected" <?php echo ($_GET['status'] ?? '') == 'rejected' ? 'selected' : ''; ?>>⚪ Rechazados</option>
            </select>

            <input type="text" name="client_name" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($_GET['client_name'] ?? ''); ?>">
            
            <button type="submit" class="btn-filter-submit"><i class="fas fa-search"></i></button>
            
            <?php if(!empty($_GET['date']) || !empty($_GET['delivery_type']) || !empty($_GET['client_name'])): ?>
                <a href="?route=orders" class="btn-clear" title="Limpiar Filtros"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- contenido de la tabla -->
<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Fecha</th>
                <th>Origen</th>
                <th>Cliente</th>
                <th>Entrega</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="orders-tbody">
            <?php if(empty($orders)): ?>
                <tr><td colspan="7" style="text-align:center; padding: 2rem;">No hay pedidos registrados.</td></tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td title="<?php echo $order['channel_name']; ?>">
                            <i class="<?php echo $order['channel_icon'] ?? 'fas fa-globe'; ?>"></i>
                            <span style="font-size: 0.75rem; color: #666;"><?php echo $order['channel_name']; ?></span>
                        </td>
                        <td>
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['user_name']); ?>
                        </td>
                        <td>
                            <?php if($order['delivery_type'] == 'delivery'): ?>
                                <span style="color:#d63384;"><i class="fas fa-motorcycle"></i> Delivery</span>
                            <?php elseif($order['delivery_type'] == 'pickup'): ?>
                                <span style="color:#0d6efd;"><i class="fas fa-walking"></i> Retiro</span>
                            <?php else: ?>
                                <span style="color:#fd7e14;"><i class="fas fa-utensils"></i> Local</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: bold;">
                            Gs. <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </td>
                        <td>
                            <?php 
                                $statusClass = 'status-pending';
                                switch($order['status']) {
                                    case 'confirmed': $statusClass = 'status-confirmed'; break;
                                    case 'shipped': $statusClass = 'status-shipped'; break;
                                    case 'completed': $statusClass = 'status-completed'; break;
                                    case 'rejected': $statusClass = 'status-rejected'; break;
                                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                                }

                                $isLocked = ($order['status'] == 'completed');
                                $isErrorState = in_array($order['status'], ['rejected', 'cancelled']);
                            ?>
                            <select class="status-select <?php echo $statusClass; ?>"
                                    onchange="updateOrderStatus(this, <?php echo $order['id']; ?>)"
                                    data-current-status="<?php echo $order['status']; ?>"
                                    <?php echo $isLocked ? 'disabled' : ''; ?>
                            >
                                <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?> <?php echo (!$isErrorState && $order['status'] != 'pending') ? 'disabled' : ''; ?>>
                                    <?php echo $isErrorState ? '🔄 Reabrir (Pendiente)' : 'Pendiente'; ?>
                                </option>
                                <option value="confirmed" <?php echo $order['status']=='confirmed'?'selected':''; ?> disabled>Confirmado (Imprimir)</option>
                                <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?> disabled>En Camino</option>
                                <option value="completed" <?php echo $order['status']=='completed'?'selected':''; ?> disabled>Entregado</option>
                                <option value="rejected" <?php echo $order['status']=='rejected'?'selected':''; ?>>Rechazado</option>
                                <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>Cancelado</option>
                            </select>
                            <?php if ($order['status'] === 'confirmed' && $order['delivery_user_id']): ?>
                                <div style="font-size: 0.7rem; color: #28a745; font-weight: bold; margin-top: 4px;">
                                    <i class="fas fa-user-check"></i> DELIVERY ASIGNADO
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn-view" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                                <a href="?route=orders_ticket&id=<?php echo $order['id']; ?>&format=80mm" target="_blank" class="btn-print-table btn-print-80" title="Imprimir 80mm" onclick="confirmOrderOnPrint(<?php echo $order['id']; ?>)"><i class="fas fa-print"></i> 80</a>
                                <a href="?route=orders_ticket&id=<?php echo $order['id']; ?>&format=58mm" target="_blank" class="btn-print-table btn-print-58" title="Imprimir 58mm" onclick="confirmOrderOnPrint(<?php echo $order['id']; ?>)"><i class="fas fa-print"></i> 58</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- contadores de estados -->
<div class="status-counters">

    <div class="stat-card stat-all">
        <i class="fas fa-list"></i>
        <span id="count-all-orders"><?php echo $statusCounts['all'] ?? 0; ?></span>
        <label>Todos</label>
    </div>                            

    <div class="stat-card stat-pending">
        <i class="fas fa-clock"></i>
        <span id="count-pending"><?php echo $statusCounts['pending'] ?? 0; ?></span>
        <label>Pendientes</label>
    </div>

    <div class="stat-card stat-confirmed">
        <i class="fas fa-fire"></i>
        <span id="count-confirmed"><?php echo $statusCounts['confirmed'] ?? 0; ?></span>
        <label>En Cocina</label>
    </div>

    <div class="stat-card stat-confirmed" style="background: #e3f2fd; border-color: #1976d2;">
        <i class="fas fa-truck"></i>
        <span id="count-shipped"><?php echo $statusCounts['shipped'] ?? 0; ?></span>
        <label>En Camino</label>
    </div>

    <div class="stat-card stat-completed">
        <i class="fas fa-check-circle"></i>
        <span id="count-completed"><?php echo $statusCounts['completed'] ?? 0; ?></span>
        <label>Entregados</label>
    </div>

    <div class="stat-card stat-cancelled">
        <i class="fas fa-times-circle"></i>
        <span id="count-cancelled"><?php echo $statusCounts['cancelled'] ?? 0; ?></span>
        <label>Cancelados</label>
    </div>
    
</div>

<?php 
// Verificamos si la lista está vacía Y si el usuario intentó filtrar algo
$hasFilter = !empty($_GET['date']) || !empty($_GET['delivery_type']) || !empty($_GET['client_name']);
if (empty($orders) && $hasFilter): 
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Toast !== 'undefined') {
                Toast.fire("No se encontraron pedidos con los filtros seleccionados.", "info");
            }
        });
    </script>
<?php endif; ?>

<script>
    // Almacenamos el ID más alto actual para saber cuáles son nuevos
    let lastMaxId = <?php echo !empty($orders) ? max(array_column($orders, 'id')) : 0; ?>;
    // Mapa para rastrear cambios de estado de pedidos visibles
    let orderStatusMap = {};
    // Huella digital de los datos para evitar re-renderizados (parpadeo) innecesarios
    let lastOrdersFingerprint = '';
    const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

    /**
     * Actualiza el estado de un pedido directamente desde la tabla
     */
    async function updateOrderStatus(selectElement, orderId) {
        const newStatus = selectElement.value;
        const currentStatus = selectElement.getAttribute('data-current-status');

        // Si intenta marcar como rechazado o cancelado, pedimos confirmación
        if (newStatus === 'rejected' || newStatus === 'cancelled') {
            const actionName = newStatus === 'rejected' ? 'RECHAZAR' : 'CANCELAR';
            const result = await Swal.fire({
                title: `¿Confirmar ${actionName}?`,
                text: `¿Estás seguro de que deseas marcar el pedido #${orderId} como ${newStatus}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'No, volver'
            });

            if (!result.isConfirmed) {
                selectElement.value = currentStatus; // Revertimos el select visualmente
                return;
            }
        }

        // Cambiar color visualmente mientras procesa
        selectElement.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('id', orderId);
        formData.append('status', newStatus);

        fetch('?route=orders_update_status', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error al actualizar');
            Toast.fire("Estado actualizado", "success");
            refreshOrders(); // Esto recargará la tabla y actualizará contadores y colores
        })
        .catch(err => {
            Toast.fire("Error al cambiar estado", "error");
            selectElement.style.opacity = '1';
        });
    }

    /**
     * Actualiza el estado a 'confirmed' automáticamente al imprimir
     */
    function confirmOrderOnPrint(orderId) {
        const formData = new FormData();
        formData.append('id', orderId);
        formData.append('status', 'confirmed');

        fetch('?route=orders_update_status', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(() => refreshOrders()); // Refrescar la tabla inmediatamente
    }

    /**
     * Función para actualizar la tabla de pedidos vía AJAX
     */
    function refreshOrders() {
        const isFirstLoad = (lastMaxId === 0);
        const urlParams = new URLSearchParams(window.location.search);
        // Cambiamos el parámetro route para apuntar al endpoint JSON
        urlParams.set('route', 'orders_api');
        
        //console.log("**Sincronizando pedidos...");

        fetch('index.php?' + urlParams.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la red: ' + response.status);
            return response.json();
        })
        .then(data => {
            //console.log("**Datos recibidos:", data);
            const tbody = document.getElementById('orders-tbody');
            if (!tbody || data.error) return;

            // Crear huella combinando ID y Estado de todos los pedidos
            const newFingerprint = data.map(o => `${o.id}-${o.status}`).join('|');
            // Si la huella es idéntica a la anterior, no hacemos nada (evita el parpadeo)
            if (newFingerprint === lastOrdersFingerprint) return;
            lastOrdersFingerprint = newFingerprint;

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 2rem;">No hay pedidos registrados.</td></tr>';
                return;
            }
            
            let hasNewOrders = false;
            let deliveryUpdateInfo = null;
            let currentMaxIdInResponse = lastMaxId;

            // Objeto para recalcular contadores localmente
            const newCounts = { all: data.length, pending: 0, confirmed: 0, shipped: 0, completed: 0, rejected: 0, cancelled: 0 };

            tbody.innerHTML = data.map(order => {
                const isNew = order.id > lastMaxId;
                if (isNew) {
                    hasNewOrders = true;
                    if (order.id > currentMaxIdInResponse) currentMaxIdInResponse = order.id;
                }

                // Detectar si un repartidor cambió el estado a 'completed' (Entregado)
                if (orderStatusMap[order.id] && orderStatusMap[order.id] !== order.status) {
                    if (['completed', 'rejected', 'cancelled'].includes(order.status)) {
                        deliveryUpdateInfo = {
                            id: order.id,
                            status: order.status,
                            label: order.status === 'completed' ? 'ENTREGADO' : (order.status === 'rejected' ? 'RECHAZADO' : 'CANCELADO')
                        };
                    }
                }
                orderStatusMap[order.id] = order.status;

                // Sumar al contador correspondiente
                if (!newCounts[order.status]) newCounts[order.status] = 0;
                newCounts[order.status]++;

                let deliveryHtml = order.delivery_type === 'delivery' 
                    ? '<span style="color:#d63384;"><i class="fas fa-motorcycle"></i> Delivery</span>'
                    : (order.delivery_type === 'pickup' 
                        ? '<span style="color:#0d6efd;"><i class="fas fa-walking"></i> Retiro</span>'
                        : '<span style="color:#fd7e14;"><i class="fas fa-utensils"></i> Local</span>');

                // Variables de control de estado para JS
                const isErrorState = (order.status === 'rejected' || order.status === 'cancelled');
                const isLocked = (order.status === 'completed');
                const deliveryAssignedBadge = (order.status === 'confirmed' && order.delivery_user_id) 
                    ? `<div style="font-size: 0.7rem; color: #28a745; font-weight: bold; margin-top: 4px;"><i class="fas fa-user-check"></i> DELIVERY ASIGNADO</div>` 
                    : '';

                let statusClass = 'status-pending';
                if (order.status === 'confirmed') statusClass = 'status-confirmed';
                else if (order.status === 'shipped') statusClass = 'status-shipped';
                else if (order.status === 'completed') statusClass = 'status-completed';
                else if (order.status === 'rejected') statusClass = 'status-rejected';
                else if (order.status === 'cancelled') statusClass = 'status-cancelled';

                return `
                    <tr class="${isNew ? 'row-new' : ''}">
                        <td>#${order.id}</td>
                        <td>${order.formatted_date}</td>
                        <td title="${order.channel_name}"><i class="${order.channel_icon}"></i> <span style="font-size: 0.75rem; color: #666;">${order.channel_name}</span></td>
                        <td><i class="fas fa-user"></i> ${order.user_name}</td>
                        <td>${deliveryHtml}</td>
                        <td style="font-weight: bold;">Gs. ${order.formatted_total}</td>
                        <td>
                            <select class="status-select ${statusClass}" onchange="updateOrderStatus(this, ${order.id})" data-current-status="${order.status}" ${isLocked ? 'disabled' : ''}>
                                <option value="pending" ${order.status === 'pending' ? 'selected' : ''} ${(!isErrorState && order.status !== 'pending') ? 'disabled' : ''}>${isErrorState ? '🔄 Reabrir (Pendiente)' : 'Pendiente'}</option>
                                <option value="confirmed" ${order.status === 'confirmed' ? 'selected' : ''} disabled>Confirmado (Imprimir)</option>
                                <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''} disabled>En Camino</option>
                                <option value="completed" ${order.status === 'completed' ? 'selected' : ''} disabled>Entregado</option>
                                <option value="rejected" ${order.status === 'rejected' ? 'selected' : ''}>Rechazado</option>
                                <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelado</option>
                            </select>
                            ${deliveryAssignedBadge}
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <a href="?route=orders_show&id=${order.id}" class="btn-view" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                                <a href="?route=orders_ticket&id=${order.id}&format=80mm" target="_blank" class="btn-print-table btn-print-80" title="Imprimir 80mm" onclick="confirmOrderOnPrint(${order.id})"><i class="fas fa-print"></i> 80</a>
                                <a href="?route=orders_ticket&id=${order.id}&format=58mm" target="_blank" class="btn-print-table btn-print-58" title="Imprimir 58mm" onclick="confirmOrderOnPrint(${order.id})"><i class="fas fa-print"></i> 58</a>
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            /**
             * Función auxiliar para actualizar con animación
             */
            const updateCountWithAnimation = (id, newValue) => {
                const el = document.getElementById(id);
                if (el && el.innerText != newValue) {
                    el.innerText = newValue;
                    el.classList.remove('pulse-animation');
                    void el.offsetWidth; // Trigger reflow para reiniciar animación
                    el.classList.add('pulse-animation');
                }
            };

            updateCountWithAnimation('count-all-orders', newCounts.all);
            updateCountWithAnimation('count-pending', newCounts.pending);
            updateCountWithAnimation('count-confirmed', newCounts.confirmed);
            updateCountWithAnimation('count-shipped', newCounts.shipped || 0);
            updateCountWithAnimation('count-completed', newCounts.completed);
            updateCountWithAnimation('count-cancelled', newCounts.cancelled);

            // Si hay pedidos nuevos, disparamos alertas
            if (hasNewOrders) {
                lastMaxId = currentMaxIdInResponse;
                notificationSound.play().catch(e => console.log("Audio bloqueado por navegador hasta interacción"));
                document.title = "(!ID) ¡Nuevo Pedido! - Comedor App".replace("!ID", lastMaxId);
                
                // Restaurar título después de 5 segundos
                setTimeout(() => { document.title = "Admin - Comedor App"; }, 5000);
            }

            // Alerta si un repartidor actualizó un pedido desde la calle
            if (deliveryUpdateInfo) {
                notificationSound.play().catch(e => {});
                const icon = deliveryUpdateInfo.status === 'completed' ? 'success' : 'warning';
                Toast.fire(`Pedido #${deliveryUpdateInfo.id}: ${deliveryUpdateInfo.label}`, icon);
            }
        })
        .catch(err => console.error('Error en la auto-actualización:', err));
    }

    // Configurar intervalo de 10 segundos para pruebas más rápidas
    setInterval(() => {
        if (!document.hidden) refreshOrders();
    }, 10000);
</script>