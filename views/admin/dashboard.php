<!-- Contenido específico del Dashboard -->
<div class="dashboard-home">
    <h1><?php echo $data['title']; ?></h1>
    <p style="color: #666; margin-bottom: 2rem;">Bienvenido al sistema de gestión. Aquí tienes un resumen de hoy.</p>

    <!-- Tarjetas de Resumen (Datos simulados desde el Controller) -->
    <div class="card-container">
        <div class="card" style="border-left-color: #ffc107;">
            <h3>Pedidos Pendientes</h3>
            <p><?php echo $data['pedidos_pendientes']; ?></p>
        </div>
        
        <div class="card" style="border-left-color: #28a745;">
            <h3>Ingresos Hoy</h3>
            <p>$<?php echo $data['ingresos_hoy']; ?></p>
        </div>
        
        <div class="card" style="border-left-color: #17a2b8;">
            <h3>Platos Vendidos</h3>
            <p><?php echo $data['platos_vendidos']; ?></p>
        </div>
    </div>
</div>