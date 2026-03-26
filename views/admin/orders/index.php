<div class="header-actions">
    <h1 class="page-title">Gestión de Pedidos</h1>
    
    <form action="index.php" method="GET" class="filter-form">
        <input type="hidden" name="route" value="orders">
        
        <div class="filter-group">
            <input type="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>" title="Filtrar por Fecha">
            
            <select name="delivery_type">
                <option value="">Todas las entregas</option>
                <option value="delivery" <?php echo ($_GET['delivery_type'] ?? '') == 'delivery' ? 'selected' : ''; ?>>🛵 Delivery</option>
                <option value="pickup" <?php echo ($_GET['delivery_type'] ?? '') == 'pickup' ? 'selected' : ''; ?>>🛍️ Retiro</option>
                <option value="table" <?php echo ($_GET['delivery_type'] ?? '') == 'table' ? 'selected' : ''; ?>>🍽️ Mesa</option>
            </select>

            <input type="text" name="client_name" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($_GET['client_name'] ?? ''); ?>">
            
            <button type="submit" class="btn-filter-submit"><i class="fas fa-search"></i></button>
            
            <?php if(!empty($_GET['date']) || !empty($_GET['delivery_type']) || !empty($_GET['client_name'])): ?>
                <a href="?route=orders" class="btn-clear" title="Limpiar Filtros"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<style>
    /* Reutilizamos estilos de products/index.php para consistencia */
    .header-actions {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem; background-color: #fff; padding: 1rem;
        border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .filter-form { display: flex; align-items: center; }
    .filter-group { display: flex; gap: 8px; align-items: center; }
    
    .filter-group input, .filter-group select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        outline: none;
    }
    .filter-group input:focus, .filter-group select:focus { border-color: #007bff; }

    .btn-filter-submit { background: #343a40; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: 0.2s; }
    .btn-filter-submit:hover { background: #212529; }

    .btn-clear { color: #dc3545; background: #fff1f2; padding: 8px 12px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; justify-content: center; }

    .page-title { margin: 0; font-size: 1.5rem; color: #333; }
    
    .contenedor-tabla {
        max-height: 600px; overflow-y: auto; border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); background: white;
    }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; font-weight: 600; color: #495057; position: sticky; top: 0; }
    
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
    /* Colores de estado */
    .status-pending { background: #ffc107; color: #333; } /* Amarillo */
    .status-confirmed { background: #17a2b8; color: #fff; } /* Azul Info */
    .status-completed { background: #28a745; color: #fff; } /* Verde */
    .status-cancelled { background: #dc3545; color: #fff; } /* Rojo */

    .btn-view { padding: 5px 10px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem; }
    .btn-view:hover { background: #0056b3; }

    /* Animación para nuevos pedidos */
    @keyframes highlightNew {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
    .row-new {
        animation: highlightNew 5s ease-out;
    }
</style>

<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Fecha</th>
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
                                    case 'completed': $statusClass = 'status-completed'; break;
                                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                                }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php 
                                    $statusMap = ['pending'=>'Pendiente', 'confirmed'=>'Confirmado', 'completed'=>'Entregado', 'cancelled'=>'Cancelado'];
                                    echo $statusMap[$order['status']] ?? $order['status']; 
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="?route=orders_show&id=<?php echo $order['id']; ?>" class="btn-view">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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
    const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

    /**
     * Función para actualizar la tabla de pedidos vía AJAX
     */
    function refreshOrders() {
        const isFirstLoad = (lastMaxId === 0);
        const urlParams = new URLSearchParams(window.location.search);
        // Cambiamos el parámetro route para apuntar al endpoint JSON
        urlParams.set('route', 'orders_api');
        
        console.log("Sincronizando pedidos...");

        fetch('index.php?' + urlParams.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la red: ' + response.status);
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);
            const tbody = document.getElementById('orders-tbody');
            if (!tbody || data.error) return;

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 2rem;">No hay pedidos registrados.</td></tr>';
                return;
            }
            
            let hasNewOrders = false;
            let currentMaxIdInResponse = lastMaxId;

            tbody.innerHTML = data.map(order => {
                const isNew = order.id > lastMaxId;
                if (isNew) {
                    hasNewOrders = true;
                    if (order.id > currentMaxIdInResponse) currentMaxIdInResponse = order.id;
                }

                let statusClass = 'status-pending';
                let statusText = 'Pendiente';
                
                // Lógica de clases y estados
                if (order.status === 'confirmed') { statusClass = 'status-confirmed'; statusText = 'Confirmado'; }
                else if (order.status === 'completed') { statusClass = 'status-completed'; statusText = 'Entregado'; }
                else if (order.status === 'cancelled') { statusClass = 'status-cancelled'; statusText = 'Cancelado'; }

                let deliveryHtml = order.delivery_type === 'delivery' 
                    ? '<span style="color:#d63384;"><i class="fas fa-motorcycle"></i> Delivery</span>'
                    : (order.delivery_type === 'pickup' 
                        ? '<span style="color:#0d6efd;"><i class="fas fa-walking"></i> Retiro</span>'
                        : '<span style="color:#fd7e14;"><i class="fas fa-utensils"></i> Local</span>');

                return `
                    <tr class="${isNew ? 'row-new' : ''}">
                        <td>#${order.id}</td>
                        <td>${order.formatted_date}</td>
                        <td><i class="fas fa-user"></i> ${order.user_name}</td>
                        <td>${deliveryHtml}</td>
                        <td style="font-weight: bold;">Gs. ${order.formatted_total}</td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td><a href="?route=orders_show&id=${order.id}" class="btn-view">Ver Detalle</a></td>
                    </tr>`;
            }).join('');

            // Si hay pedidos nuevos, disparamos alertas
            if (hasNewOrders) {
                lastMaxId = currentMaxIdInResponse;
                notificationSound.play().catch(e => console.log("Audio bloqueado por navegador hasta interacción"));
                document.title = "(!ID) ¡Nuevo Pedido! - Comedor App".replace("!ID", lastMaxId);
                
                // Restaurar título después de 5 segundos
                setTimeout(() => { document.title = "Admin - Comedor App"; }, 5000);
            }
        })
        .catch(err => console.error('Error en la auto-actualización:', err));
    }

    // Configurar intervalo de 10 segundos para pruebas más rápidas
    setInterval(() => {
        if (!document.hidden) refreshOrders();
    }, 10000);
</script>