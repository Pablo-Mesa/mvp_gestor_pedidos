<style>
    .history-kpi-grid {
        display: grid;
        grid-template-columns: 0.7fr 1.15fr 1.15fr;
        gap: 8px;
        margin-bottom: 20px;
        flex-shrink: 0;
    }
    .kpi-card {
        background: white;
        padding: 12px 5px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid #eee;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .kpi-card label { display: block; font-size: 0.55rem; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; white-space: nowrap; }
    .kpi-card span { font-size: 0.95rem; font-weight: 800; color: #2d3436; white-space: nowrap; }
    .kpi-cash { border-top: 4px solid #ff7675; }
    .kpi-digital { border-top: 4px solid #55efc4; }

    .history-table-container {
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .history-row {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f8f9fa;
        gap: 10px;
    }
    .history-row:nth-child(even) { background-color: #fafafa; }
    .history-row:last-child { border-bottom: none; }
    .row-id { font-weight: 900; color: var(--delivery-primary); font-size: 0.85rem; width: 45px; }
    .row-main { flex: 1; min-width: 0; }
    .row-main h4 { margin: 0; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .row-main small { color: #888; font-size: 0.75rem; }
    .row-amount { text-align: right; }
    .row-amount .price { display: block; font-weight: 800; font-size: 0.9rem; }
    .row-amount .badge { font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }

    /* Fijar el resumen final al fondo */
    .history-footer-summary {
        margin-top: 20px;
        flex-shrink: 0;
        padding: 18px 20px;
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
        border-top: 4px solid var(--delivery-primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .footer-label { font-size: 0.85rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .footer-amount { font-size: 1.4rem; font-weight: 900; color: var(--delivery-text); letter-spacing: -0.5px; }

    .history-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 15px;
    }
    .btn-export {
        padding: 10px;
        border-radius: 10px;
        border: none;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-pdf { background: #636e72; color: white; }
    .btn-wa { background: #25D366; color: white; }

    /* Encabezado para PDF/Impresión */
    .print-header { display: none; }
    /* Pie de página para PDF/Impresión */
    .print-footer { display: none; }

    @media print {
        /* Forzar que el navegador permita múltiples páginas y flujo natural */
        html, body {
            height: auto !important;
            overflow: visible !important;
            display: block !important;
        }

        /* Anular el contenedor flex que bloquea los saltos de página */
        .delivery-main, .history-table-container {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }

        .print-header {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2d3436;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .print-header img { height: 60px; }
        .print-header-info { text-align: right; }
        .print-header-info h2 { margin: 0; font-size: 1.4rem; color: #2d3436; }
        .print-header-info p { margin: 2px 0; font-size: 0.9rem; }

        .print-footer {
            display: flex !important;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
            padding-top: 20px;
        }
        .print-footer-left { font-size: 0.75rem; color: #666; }
        .print-footer-right { text-align: center; min-width: 220px; }
        .sig-line { border-top: 1px solid #000; margin-bottom: 5px; }

        .delivery-header, .date-selector-container, .history-actions-grid, .menu-trigger {
            display: none !important;
        }
        body { background: white; padding: 0; }
        .history-table-container { border: none; }
        .history-footer-summary { border: 2px solid #2d3436 !important; box-shadow: none !important; color: #2d3436 !important; -webkit-print-color-adjust: exact; }
        .kpi-card { border: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
    }
</style>

<!-- Encabezado exclusivo para impresión -->
<div class="print-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png" alt="Logo">
        <div>
            <h2 style="margin:0; font-size: 1.2rem;"><?php echo htmlspecialchars($siteName ?? 'SOLVER'); ?></h2>
            <small style="color: #666;">Gestión de Logística y Entregas</small>
        </div>
    </div>
    <div class="print-header-info">
        <h2>REPORTE DE RENDICIÓN</h2>
        <p><strong>Repartidor:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <p><strong>Fecha de reporte:</strong> <?php echo date('d/m/Y', strtotime($selectedDate)); ?></p>
    </div>
</div>

<!-- botones de exportación -->
<div class="history-actions-grid">
    <button class="btn-export btn-pdf" onclick="window.print()">
        <i class="fas fa-file-pdf"></i> PDF / Imprimir
    </button>
    <button class="btn-export btn-wa" onclick="shareHistoryWA()">
        <i class="fab fa-whatsapp"></i> Compartir WA
    </button>
</div>

<!-- resumen de entregas -->
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

<!-- tabla de entregas -->
<div class="history-table-container">
    <?php if(empty($orders)): ?>
        <div style="padding: 40px; text-align: center; color: #888;">
            <i class="fas fa-search fa-2x" style="margin-bottom: 10px; opacity: 0.5;"></i>
            <p>No hay registros para este día.</p>
        </div>
    <?php else: ?>
        <?php foreach($orders as $o): ?>
            <?php 
                // TEMPORAL: Todo figura como "A Cobrar" (isEf = true) por falta de módulo de Caja
                $isEf = true; 
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
                    <span class="badge" style="background: #fff3cd; color: #856404;">
                        <?php echo $o['payment_method']; ?> (A Cobrar)
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- resumen final -->
<div class="history-footer-summary">
    <span class="footer-label">Total Facturado</span>
    <span class="footer-amount">Gs. <?php echo number_format($summary['total'], 0, ',', '.'); ?></span>
</div>

<!-- Pie de página exclusivo para impresión -->
<div class="print-footer">
    <div class="print-footer-left">
        <p style="margin:0;">Reporte generado automáticamente por <strong>Solver Logística</strong></p>
        <p style="margin:0;">Fecha y hora de emisión: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p style="margin:0;">Validez: Documento de Rendición Interna</p>
    </div>
    <div class="print-footer-right">
        <div class="sig-line"></div>
        <p style="margin:0; font-size: 0.85rem; font-weight: bold;">Firma del Repartidor</p>
        <p style="margin:0; font-size: 0.8rem;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    </div>
</div>

<script>
function shareHistoryWA() {
    const date = "<?php echo date('d/m/Y', strtotime($selectedDate)); ?>";
    const name = "<?php echo htmlspecialchars($_SESSION['user_name']); ?>";
    const count = "<?php echo $summary['count']; ?>";
    const rejected = parseInt("<?php echo $summary['rejected'] ?? 0; ?>");
    const cancelled = parseInt("<?php echo $summary['cancelled'] ?? 0; ?>");
    const cash = "<?php echo number_format($summary['cash'], 0, ',', '.'); ?>";
    const digital = "<?php echo number_format($summary['digital'], 0, ',', '.'); ?>";
    const total = "<?php echo number_format($summary['total'], 0, ',', '.'); ?>";

    let text = `🚀 *RENDICIÓN DE ENTREGAS - ${"<?php echo htmlspecialchars($siteName ?? 'SOLVER'); ?>".toUpperCase()}*\n`;
    text += `📅 *Fecha:* ${date}\n`;
    text += `👤 *Repartidor:* ${name}\n`;
    text += `--------------------------------\n`;
    text += `✅ *Entregados:* ${count}\n`;
    
    if (rejected > 0) text += `❌ *Rechazados:* ${rejected}\n`;
    if (cancelled > 0) text += `🚫 *Cancelados:* ${cancelled}\n`;

    text += `💵 *Efectivo a Rendir:* Gs. ${cash}\n`;
    text += `💳 *Cobros Digitales:* Gs. ${digital}\n`;
    text += `--------------------------------\n`;
    text += `💰 *TOTAL RENDICIÓN: Gs. ${total}*\n`;
    text += `--------------------------------\n`;
    text += `_Generado automáticamente desde el Panel de Logística_`;

    // Intentar usar Web Share API si está disponible (más nativo en móviles)
    if (navigator.share) {
        navigator.share({ text: text }).catch(console.error);
    } else {
        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
    }
}
</script>