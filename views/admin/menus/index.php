<style>
    .menu-manager-grid { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: flex-start; }
    .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .card h2 { margin-top: 0; margin-bottom: 1rem; font-size: 1.2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
    .btn { display: inline-block; background: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; text-align: center; }
    .btn-danger { background-color: #dc3545; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    table { width: 100%; border-collapse: collapse; }
    .badge { padding: 0.25em 0.6em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
    .badge-success { color: #155724; background-color: #d4edda; } .badge-danger { color: #721c24; background-color: #f8d7da; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f8f9fa; }
    .date-selector { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
</style>

<h1>Gestión de Menú del Día</h1>

<!-- Selector de Fecha -->
<div class="date-selector">
    <form method="GET">
        <input type="hidden" name="route" value="menus">
        <label for="date-picker" style="font-weight: 600;">Seleccionar Fecha:</label>
        <input type="date" id="date-picker" name="date" value="<?php echo htmlspecialchars($current_date); ?>" onchange="this.form.submit()">
    </form>
</div>

<div class="menu-manager-grid">
    <!-- Columna Izquierda: Menús Asignados -->
    <div class="card">
        <h2>Menú para el <?php echo date("d/m/Y", strtotime($current_date)); ?></h2>
        <?php if (empty($assigned_menus)): ?>
            <p>No hay menús asignados para esta fecha.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="width: 120px;">Stock Diario</th>
                        <th style="width: 120px;">Disponibilidad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assigned_menus as $menu): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($menu['product_name']); ?>
                                <small style="display: block; color: #6c757d;">$<?php echo number_format($menu['product_price'], 2); ?></small>
                            </td>
                            <td><?php echo $menu['daily_stock'] !== null ? htmlspecialchars($menu['daily_stock']) : 'Ilimitado'; ?></td>
                            <td>
                                <?php if ($menu['is_available']): ?>
                                    <span class="badge badge-success">Disponible</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Agotado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?route=menus_toggle_availability&id=<?php echo $menu['id']; ?>&date=<?php echo $current_date; ?>&status=<?php echo $menu['is_available']; ?>" class="btn btn-sm" style="background-color: #ffc107; color: #212529;"><?php echo $menu['is_available'] ? 'Agotar' : 'Habilitar'; ?></a>
                                <a href="?route=menus_unassign&id=<?php echo $menu['id']; ?>&date=<?php echo $current_date; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Quitar este plato del menú del día?')">Quitar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Columna Derecha: Asignar Nuevo Menú -->
    <div class="card">
        <h2>Asignar Plato</h2>
        <form action="?route=menus_assign" method="POST">
            <input type="hidden" name="menu_date" value="<?php echo htmlspecialchars($current_date); ?>">
            
            <div class="form-group">
                <label for="product_id">Seleccionar Producto</label>
                <select name="product_id" id="product_id" required>
                    <option value="">-- Elige un producto --</option>
                    <?php 
                    $assigned_product_ids = array_column($assigned_menus, 'product_id');
                    foreach($available_products as $product): 
                        if (!in_array($product['id'], $assigned_product_ids)):
                    ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="daily_stock">Stock para Hoy (Opcional)</label>
                <input type="number" name="daily_stock" id="daily_stock" min="0" placeholder="Ej: 50 (dejar vacío para ilimitado)">
            </div>

            <button type="submit" class="btn" style="width: 100%;">+ Asignar al Menú</button>
        </form>
    </div>
</div>