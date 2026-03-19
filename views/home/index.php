<div class="card">
    <h2>📅 Menú de Hoy (<?php echo date('d/m/Y'); ?>)</h2>
    <p>Selecciona los platos que deseas ordenar.</p>
</div>

<form action="?route=order_confirm" method="POST">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <?php if(empty($menus)): ?>
            <p>No hay menús disponibles para hoy.</p>
        <?php else: ?>
            <?php foreach($menus as $menu): ?>
                <div class="card" style="border: 1px solid #eee;">
                    <h3><?php echo htmlspecialchars($menu['product_name']); ?></h3>
                    <p style="color: #666; font-size: 0.9rem;">Precio: $<?php echo number_format($menu['product_price'], 2); ?></p>
                    
                    <div style="margin-top: 1rem;">
                        <label>Cantidad:</label>
                        <input type="number" name="products[<?php echo $menu['id']; ?>]" min="0" value="0" style="width: 60px; padding: 0.25rem;">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if(!empty($menus)): ?>
    <div class="card">
        <h3>Datos de Entrega</h3>
        
        <div style="margin-bottom: 1rem;">
            <label>Método de Pago:</label>
            <select name="payment_method" style="padding: 0.5rem;">
                <option value="cash">Efectivo</option>
                <option value="card">Tarjeta</option>
            </select>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Tipo de Entrega:</label>
            <select name="delivery_type" id="delivery_type" onchange="toggleAddress()" style="padding: 0.5rem;">
                <option value="pickup">Retiro en Comedor</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>

        <div id="address_field" style="display: none; margin-bottom: 1rem;">
            <label>Dirección de Entrega:</label>
            <textarea name="delivery_address" placeholder="Calle, Número, Referencia" style="width: 100%; padding: 0.5rem;"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem;">Confirmar Pedido</button>
    </div>
    <?php endif; ?>
</form>

<script>
    function toggleAddress() {
        var type = document.getElementById('delivery_type').value;
        var field = document.getElementById('address_field');
        field.style.display = (type === 'delivery') ? 'block' : 'none';
    }
</script>