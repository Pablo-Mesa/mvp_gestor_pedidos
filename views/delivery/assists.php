<style>
    .assist-header-info {
        margin-bottom: 20px;
        flex-shrink: 0;
    }
    
    .date-selector-container {
        background: white;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #eee;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .date-input { 
        border: none; 
        font-weight: 700; 
        color: var(--delivery-text); 
        outline: none; 
        flex: 1; 
        font-size: 0.9rem;
    }

    .assists-container {
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .assist-row {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
        gap: 12px;
    }
    
    .assist-row:last-child {
        border-bottom: none;
    }

    .assist-row:nth-child(even) {
        background-color: #fafafa;
    }

    .assist-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .assist-main { flex: 1; min-width: 0; }
    .assist-main h4 { margin: 0; font-size: 0.95rem; color: var(--delivery-text); }
    .assist-main small { color: var(--delivery-subtext); font-size: 0.8rem; display: block; margin-top: 2px; }

    .assist-side { text-align: right; }
    .dist-badge {
        font-size: 0.7rem;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .dist-ok { background: #e8f5e9; color: #2e7d32; }
    .dist-warn { background: #fff3e0; color: #ef6c00; }

    .map-link {
        color: var(--delivery-primary);
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 700;
        display: block;
        margin-top: 4px;
    }
</style>

<div class="assist-header-info">
    <h2 style="margin:0; font-size: 1.3rem;">Historial de Asistencias</h2>
    <p style="color: var(--delivery-subtext); font-size: 0.85rem; margin-top: 5px;">Registros de llegada al local</p>
</div>

<!-- Selector de Fecha -->
<div class="date-selector-container">
    <i class="fas fa-calendar-day" style="color: var(--delivery-primary);"></i>
    <input type="month" class="date-input" value="<?php echo $selectedMonth ?? date('Y-m'); ?>" 
           onchange="location.href='?route=delivery_assists&month='+this.value">
</div>

<div class="assists-container">
    <?php if(empty($assists)): ?>
        <div style="padding: 60px 20px; text-align: center; color: #ccc;">
            <i class="fas fa-map-marker-alt fa-3x" style="margin-bottom: 15px; opacity: 0.3;"></i>
            <p>No hay registros de llegada para esta fecha.</p>
        </div>
    <?php else: ?>
        <?php foreach($assists as $a): ?>
            <div class="assist-row">
                <div class="assist-icon"><i class="fas fa-check-circle"></i></div>
                <div class="assist-main">
                    <h4><?php echo date('H:i:s', strtotime($a['checkin_time'])); ?> hs.</h4>
                    <small><?php echo date('d/m/Y', strtotime($a['checkin_time'])); ?></small>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $a['lat']; ?>,<?php echo $a['lng']; ?>" 
                       target="_blank" class="map-link">
                        <i class="fas fa-external-link-alt"></i> Ver ubicación GPS
                    </a>
                </div>
                <div class="assist-side">
                    <span class="dist-badge <?php echo $a['distance_meters'] <= 50 ? 'dist-ok' : 'dist-warn'; ?>">
                        <?php echo round($a['distance_meters']); ?>m del local
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>