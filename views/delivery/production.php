<style>
    .prod-header {
        background: white;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .prod-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
        overflow-y: auto;
    }
    .prod-item {
        background: white;
        padding: 15px;
        border-radius: 12px;
        border-left: 4px solid var(--delivery-primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .prod-info h4 { margin: 0; font-size: 1rem; color: var(--delivery-text); }
    .prod-info small { color: var(--delivery-subtext); font-size: 0.8rem; }
    .prod-value { text-align: right; }
    .prod-value .cost { display: block; font-weight: 800; font-size: 1.1rem; color: var(--delivery-primary); }
    .prod-footer {
        background: #2d3436;
        color: white;
        padding: 20px;
        border-radius: 15px;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="prod-header">
    <div>
        <h2 style="margin:0; font-size: 1.2rem;">Mi Producción</h2>
        <span style="font-size: 0.85rem; color: #666;"><?php echo date('d/m/Y', strtotime($selectedDate)); ?></span>
    </div>
    <div style="text-align: right;">
        <input type="date" class="delivery-select" value="<?php echo $selectedDate; ?>" 
               onchange="location.href='?route=delivery_production&date='+this.value">
    </div>
</div>

<div class="prod-list">
    <?php if(empty($services)): ?>
        <div style="padding: 40px; text-align: center; color: #888;">
            <i class="fas fa-motorcycle fa-3x" style="margin-bottom: 15px; opacity: 0.2;"></i>
            <p>Aún no has completado servicios hoy.</p>
        </div>
    <?php else: ?>
        <?php foreach($services as $s): ?>
            <div class="prod-item">
                <div class="prod-info">
                    <h4>Pedido #<?php echo $s['id']; ?></h4>
                    <small>Rango: <?php echo $s['km_from']; ?>km - <?php echo $s['km_to']; ?>km</small>
                    <br>
                    <small><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($s['created_at'])); ?></small>
                </div>
                <div class="prod-value">
                    <span class="cost">Gs. <?php echo number_format($s['delivery_cost'] ?? 0, 0, ',', '.'); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="prod-footer">
    <span style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Suma Total</span>
    <span style="font-size: 1.4rem; font-weight: 900; color: var(--delivery-primary);">Gs. <?php echo number_format($totalProduction, 0, ',', '.'); ?></span>
</div>