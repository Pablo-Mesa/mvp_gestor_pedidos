<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    
    <!-- Columna Izquierda: Detalles -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="border-left: 4px solid #007bff; margin-bottom: 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <h3>Pedido #<?php echo $order['id']; ?></h3>
                <span class="badge" style="background:#eee; color:#333; font-size:0.9rem;">
                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </span>
            </div>
            
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['user_phone'] ?? 'No registrado'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
            <hr>
            <p><strong>Origen:</strong> <?php echo $order['channel_id'] == 1 ? '🌐 Web' : '🏪 Mostrador'; ?></p>
            <?php if(!empty($order['staff_name'])): ?>
                <p><strong>Registrado por:</strong> <?php echo htmlspecialchars($order['staff_name']); ?></p>
            <?php endif; ?>
            
            <?php if($order['observation']): ?>
                <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px; color: #856404;">
                    <strong>⚠️ Observación:</strong> <?php echo htmlspecialchars($order['observation']); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lista de Productos -->
        <div class="card" style="margin-bottom: 20px;">
            <h3>Items del Pedido</h3>
            <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                <tr style="background: #f8f9fa;">
                    <th style="padding: 8px; text-align: left;">Producto</th>
                    <th style="padding: 8px; text-align: center;">Cant</th>
                    <th style="padding: 8px; text-align: right;">Precio</th>
                    <th style="padding: 8px; text-align: right;">Subtotal</th>
                </tr>
                <?php foreach($details as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td style="padding: 8px; text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="padding: 8px; text-align: right;"><?php echo number_format($item['price'], 0); ?></td>
                    <td style="padding: 8px; text-align: right; font-weight: bold;"><?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="padding: 15px 8px; text-align: right; font-size: 1.2rem;"><strong>Total:</strong></td>
                    <td style="padding: 15px 8px; text-align: right; font-size: 1.2rem; color: #28a745; font-weight: bold;">
                        Gs. <?php echo number_format($order['total'], 0); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Columna Derecha: Estado y Entrega -->
    <div style="width: 350px; flex-shrink: 0;">
        
        <!-- Botones Imprimir -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <a href="?route=orders_ticket&id=<?php echo $order['id']; ?>&format=80mm" target="_blank" onclick="confirmOrderOnPrint(<?php echo $order['id']; ?>)" style="flex: 1; padding: 12px; background: #007bff; color: white; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 6px rgba(0,123,255,0.2);" title="Impresora Estándar">
                <i class="fas fa-print"></i> 80mm
            </a>
            <a href="?route=orders_ticket&id=<?php echo $order['id']; ?>&format=58mm" target="_blank" onclick="confirmOrderOnPrint(<?php echo $order['id']; ?>)" style="flex: 1; padding: 12px; background: #6c757d; color: white; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 6px rgba(108,117,125,0.2);" title="Impresora Chica">
                <i class="fas fa-print"></i> 58mm
            </a>
        </div>

        <!-- Panel de Estado -->
        <div class="card" style="margin-bottom: 20px; background: #f8f9fa;">
            <h3>Cambiar Estado</h3>
            <form action="?route=orders_update_status" method="POST" style="margin-top: 10px;">
                <?php 
                    $isErrorState = in_array($order['status'], ['rejected', 'cancelled']);
                    $isLocked = ($order['status'] == 'completed');
                ?>
                <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                <select name="status" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 10px;" <?php echo $isLocked ? 'disabled' : ''; ?>>
                    <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?> 
                        <?php echo (!$isErrorState && $order['status'] != 'pending') ? 'disabled' : ''; ?>>
                        <?php echo $isErrorState ? '🔄 Reabrir (Pendiente)' : 'Pendiente 🟡'; ?>
                    </option>
                    <option value="confirmed" <?php echo $order['status']=='confirmed'?'selected':''; ?> disabled>Confirmado (Imprimir) 🔵</option>
                    <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?> disabled>En Camino 🚚</option>
                    <option value="completed" <?php echo $order['status']=='completed'?'selected':''; ?> disabled>Entregado / Finalizado 🟢</option>
                    <option value="rejected" <?php echo $order['status']=='rejected'?'selected':''; ?>>Rechazado ⚪</option>
                    <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>Cancelado 🔴</option>
                </select>
                <button type="submit" style="width: 100%; padding: 10px; background: #343a40; color: white; border: none; border-radius: 4px; cursor: pointer;">Actualizar Estado</button>
            </form>
        </div>

        <!-- Panel de Asignación de Delivery (Logística) -->
        <?php if ($order['delivery_type'] === 'delivery' && $order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
        <div class="card" style="margin-bottom: 20px; border: 2px solid #28a745; background: #f0fff4;">
            <h3><i class="fas fa-truck"></i> Asignar Logística</h3>
            <div style="margin-top: 10px;">
                <select id="delivery_select" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 10px;">
                    <option value="">-- Seleccionar Repartidor --</option>
                    <?php foreach($deliveryUsers as $driver): ?>
                        <option value="<?php echo $driver['id']; ?>" <?php echo ($order['delivery_user_id'] == $driver['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button onclick="assignOrder(<?php echo $order['id']; ?>)" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 0.95rem;">
                    Asignar y Despachar
                </button>
            </div>
        </div>
        <script>
            async function assignOrder(orderId) {
                const deliveryId = document.getElementById('delivery_select').value;
                if (!deliveryId) return Toast.fire("Selecciona un repartidor primero", "warning");

                const formData = new FormData();
                formData.append('order_id', orderId);
                formData.append('delivery_id', deliveryId);

                try {
                    const response = await fetch('?route=orders_assign_delivery', { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) {
                        Toast.fire("Logística actualizada", "success");
                        setTimeout(() => location.reload(), 1500);
                    } else { Toast.fire("Error al asignar", "error"); }
                } catch (e) { Toast.fire("Error de conexión", "error"); }
            }
        </script>
        <?php endif; ?>

        <!-- Info de Entrega -->
        <div class="card">
            <h3>Datos de Entrega</h3>
            <p style="margin-top: 10px;">
                <strong>Tipo:</strong> 
                <?php echo ucfirst($order['delivery_type']); ?>
            </p>
            <p><strong>Pago:</strong> <?php echo ucfirst($order['payment_method']); ?></p>

            <?php if($order['delivery_type'] === 'delivery'): ?>
                <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
                <p><strong>Dirección:</strong><br> <?php echo $order['delivery_address'] ? htmlspecialchars($order['delivery_address']) : 'No especificada'; ?></p>
                
                <?php if($order['delivery_lat'] && $order['delivery_lng']): ?>
                    <div id="map-preview" style="height: 200px; width: 100%; margin-top: 10px; border-radius: 8px;"></div>
                    
                    <!-- Botón para abrir en Google Maps externo -->
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $order['delivery_lat']; ?>,<?php echo $order['delivery_lng']; ?>" 
                       target="_blank" 
                       style="display: block; text-align: center; margin-top: 10px; text-decoration: none; color: #007bff; font-size: 0.9rem;">
                       <i class="fas fa-external-link-alt"></i> Abrir en Google Maps
                    </a>

                    <!-- Leaflet JS (Reutilizado) -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var lat = <?php echo $order['delivery_lat']; ?>;
                            var lng = <?php echo $order['delivery_lng']; ?>;
                            
                            var map = L.map('map-preview').setView([lat, lng], 15);
                            
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap'
                            }).addTo(map);
                            
                            L.marker([lat, lng]).addTo(map)
                                .bindPopup("Ubicación del Cliente")
                                .openPopup();
                        });

                        function confirmOrderOnPrint(orderId) {
                            const formData = new FormData();
                            formData.append('id', orderId);
                            formData.append('status', 'confirmed');

                            fetch('?route=orders_update_status', {
                                method: 'POST',
                                body: formData,
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(() => window.location.reload());
                        }
                    </script>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="?route=orders" style="color: #666; text-decoration: none;">&larr; Volver al listado</a>
        </div>

    </div>
</div>

<style>
    .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>