<style>
    .history-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: white;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid #eee;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .kpi-card label { display: block; font-size: 0.65rem; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
    .kpi-card span { font-size: 1.1rem; font-weight: 800; color: #2d3436; }
    .kpi-cash { border-top: 4px solid #ff7675; }
    .kpi-digital { border-top: 4px solid #55efc4; }

    .history-table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #eee;
    }
    .history-row {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f8f9fa;
        gap: 10px;
    }
    .history-row:last-child { border-bottom: none; }
    .row-id { font-weight: 900; color: var(--delivery-primary); font-size: 0.85rem; width: 45px; }
    .row-main { flex: 1; min-width: 0; }
    .row-main h4 { margin: 0; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .row-main small { color: #888; font-size: 0.75rem; }
    .row-amount { text-align: right; }
    .row-amount .price { display: block; font-weight: 800; font-size: 0.9rem; }
    .row-amount .badge { font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }

    .date-selector-container {
        background: white;
        padding: 10px 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #eee;
    }
    .date-input { border: none; font-weight: 700; color: var(--delivery-text); outline: none; flex: 1; }
</style>

<div class="date-selector-container">
    <i class="fas fa-calendar-alt" style="color: var(--delivery-primary);"></i>
    <input type="date" class="date-input" value="<?php echo $selectedDate; ?>" onchange="location.href='?route=delivery_history&date='+this.value">
</div>

<div class="history-kpi-grid">
    <div class="kpi-card">
        <label>Entregados</label>
        <span><?php echo $summary['count']; ?></span>
    </div>
    <div class="kpi-card kpi-cash">
        <label>Efectivo a Rendir</label>
        <span style="color: #d63031;">Gs. <?php echo number_format($summary['cash'], 0, ',', '.'); ?></span>
    </div>
    <div class="kpi-card kpi-digital">
        <label>Cobros Digitales</label>
        <span style="color: #00b894;">Gs. <?php echo number_format($summary['digital'], 0, ',', '.'); ?></span>
    </div>
</div>

<div class="history-table-container">
    <?php if(empty($orders)): ?>
        <div style="padding: 40px; text-align: center; color: #888;">
            <i class="fas fa-search fa-2x" style="margin-bottom: 10px; opacity: 0.5;"></i>
            <p>No hay registros para este día.</p>
        </div>
    <?php else: ?>
        <?php foreach($orders as $o): ?>
            <?php 
                $isEf = strtolower($o['payment_method']) === 'efectivo';
                $statusClass = $o['status'] === 'completed' ? 'color: #00b894;' : 'color: #ff7675;';
            ?>
            <div class="history-row">
                <div class="row-id">#<?php echo $o['id']; ?></div>
                <div class="row-main">
                    <h4><?php echo htmlspecialchars($o['user_name'] ?? 'Cliente'); ?></h4>
                    <small>
                        <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($o['created_at'])); ?> • 
                        <span style="<?php echo $statusClass; ?> font-weight: bold;"><?php echo strtoupper($o['status']); ?></span>
                    </small>
                </div>
                <div class="row-amount">
                    <span class="price">Gs. <?php echo number_format($o['total'], 0, ',', '.'); ?></span>
                    <span class="badge" style="background: <?php echo $isEf ? '#fff3cd' : '#e3f2fd'; ?>; color: <?php echo $isEf ? '#856404' : '#0c5460'; ?>;">
                        <?php echo $o['payment_method']; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div style="margin-top: 20px; padding: 15px; background: #2d3436; border-radius: 12px; color: white; display: flex; justify-content: space-between; align-items: center;">
    <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Facturado</span>
    <span style="font-size: 1.2rem; font-weight: 900;">Gs. <?php echo number_format($summary['total'], 0, ',', '.'); ?></span>
</div>