<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Historial de Asistencias</h2>
        <p class="text-muted">Registro de llegadas de los repartidores al local</p>
    </div>
    <div class="d-flex gap-2">
        <input type="date" class="form-control" value="<?php echo $date; ?>" 
               onchange="location.href='?route=admin_delivery_assists&date='+this.value">
    </div>
</div>

<?php if (isset($error_message) && $error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Repartidor</th>
                        <th>Fecha y Hora</th>
                        <th>Ubicación de Marca</th>
                        <th>Distancia al Local</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($assists)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-user-clock fa-3x mb-3 d-block opacity-25"></i>
                                No hay registros de asistencia para esta fecha.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($assists as $a): ?>
                            <?php 
                                $isFar = $a['distance_meters'] > 50;
                                $badgeClass = $isFar ? 'bg-warning text-dark' : 'bg-success';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle p-2 me-3 text-center" style="width: 35px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fw-bold"><?php echo htmlspecialchars($a['delivery_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo date('d/m/Y', strtotime($a['checkin_time'])); ?></div>
                                    <small class="text-muted"><?php echo date('H:i:s', strtotime($a['checkin_time'])); ?> hs</small>
                                </td>
                                <td>
                                    <small class="text-muted font-monospace"><?php echo $a['lat']; ?>, <?php echo $a['lng']; ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo round($a['distance_meters']); ?> metros
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $a['lat']; ?>,<?php echo $a['lng']; ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-map-marker-alt"></i> Ver Mapa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
