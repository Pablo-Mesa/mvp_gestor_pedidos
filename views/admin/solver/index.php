<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-percentage me-2 text-primary"></i> Costos de Plataforma Solver</h1>
            <p class="text-muted">Resumen mensual de comisiones y cargos generados.</p>
        </div>
        <form action="" method="GET" class="d-flex align-items-center gap-2 bg-white p-2 rounded shadow-sm">
            <input type="hidden" name="route" value="solver_costs">
            <label class="small fw-bold text-muted mb-0">Periodo:</label>
            <input type="month" name="month" class="form-control form-control-sm" value="<?php echo $data['selected_month']; ?>" onchange="this.form.submit()">
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Detalle de Comisiones: <?php echo $data['month_name'] . ' ' . $data['year']; ?></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th class="text-end">Web (10%)</th>
                            <th class="text-end">Mostrador (0%)</th>
                            <th class="text-end">Mozo (0%)</th>
                            <th class="text-end">Logística (G. 1.000)</th>
                            <th class="text-end pe-4">Total Diario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total = 0;
                        $total_web = 0;
                        $total_logistics = 0;
                        
                        if (empty($data['costs'])): 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay registros de ventas para este periodo.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['costs'] as $row): 
                                $daily_total = $row['web_cost'] + $row['logistics_cost'];
                                $grand_total += $daily_total;
                                $total_web += $row['web_cost'];
                                $total_logistics += $row['logistics_cost'];
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                    <td class="text-end">
                                        <span class="text-dark">Gs. <?php echo number_format($row['web_cost'], 0, ',', '.'); ?></span>
                                        <div class="text-muted small" style="font-size: 0.7rem;">Venta: Gs. <?php echo number_format($row['web_income'], 0, ',', '.'); ?></div>
                                    </td>
                                    <td class="text-end text-muted">Gs. 0</td>
                                    <td class="text-end text-muted">Gs. 0</td>
                                    <td class="text-end">
                                        <span class="text-dark">Gs. <?php echo number_format($row['logistics_cost'], 0, ',', '.'); ?></span>
                                        <div class="text-muted small" style="font-size: 0.7rem;"><?php echo $row['delivery_qty']; ?> envíos</div>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-primary">Gs. <?php echo number_format($daily_total, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="ps-4">TOTAL MENSUAL</td>
                            <td class="text-end">Gs. <?php echo number_format($total_web, 0, ',', '.'); ?></td>
                            <td class="text-end text-muted">Gs. 0</td>
                            <td class="text-end text-muted">Gs. 0</td>
                            <td class="text-end">Gs. <?php echo number_format($total_logistics, 0, ',', '.'); ?></td>
                            <td class="text-end pe-4 text-primary" style="font-size: 1.1rem;">Gs. <?php echo number_format($grand_total, 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light rounded border">
        <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i> Criterios de Cálculo</h6>
        <ul class="small mb-0 text-muted">
            <li><strong>Canal Web:</strong> 10% de comisión sobre el subtotal de productos (no incluye cargos de delivery cobrados al cliente).</li>
            <li><strong>Canal Mostrador / Mozos:</strong> 0% de comisión. Uso gratuito de la plataforma para ventas internas.</li>
            <li><strong>Logística:</strong> Cargo fijo de Gs. 1.000 por cada pedido finalizado con tipo de entrega "Delivery".</li>
        </ul>
    </div>

    <!-- Gráfico de Evolución de Costos -->
    <div class="card shadow-sm border-0 mt-4 overflow-hidden">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-area me-2"></i> Evolución Mensual de Costos</h6>
        </div>
        <div class="card-body">
            <div style="height: 350px; position: relative;">
                <canvas id="solverCostsTrendChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Script para el gráfico -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('solverCostsTrendChart').getContext('2d');
    
    // Mapeo de datos desde PHP
    const rawData = <?php echo json_encode($data['costs']); ?>;
    
    const labels = rawData.map(item => {
        const date = new Date(item.date + 'T00:00:00');
        return date.toLocaleDateString('es-PY', { day: '2-digit', month: 'short' });
    });

    const webCosts = rawData.map(item => parseFloat(item.web_cost));
    const logisticsCosts = rawData.map(item => parseFloat(item.logistics_cost));
    const totalCosts = rawData.map(item => parseFloat(item.web_cost) + parseFloat(item.logistics_cost));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Costo Total Solver',
                    data: totalCosts,
                    borderColor: '#0984e3',
                    backgroundColor: 'rgba(9, 132, 227, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#0984e3'
                },
                {
                    label: 'Comisión Web (10%)',
                    data: webCosts,
                    borderColor: '#7950f2',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3,
                    pointRadius: 0
                },
                {
                    label: 'Cargos Logística',
                    data: logisticsCosts,
                    borderColor: '#901009',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6, font: { size: 11, weight: '600' } } },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.dataset.label}: Gs. ${new Intl.NumberFormat('es-PY').format(context.parsed.y)}`
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f3f5' }, ticks: { font: { size: 10 }, callback: (v) => 'Gs. ' + v.toLocaleString() } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });
});
</script>