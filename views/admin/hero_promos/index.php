<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Hero Promo</h2>
    <p class="text-muted">Administra las 5 tarjetas informativas del banner principal para clientes.</p>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Orden</th>
                    <th>Miniatura</th>
                    <th>Título / Contenido</th>
                    <th>Tipo de Tarjeta</th>
                    <th>Estilo Visual</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($promos)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No hay tarjetas configuradas. Ejecuta el script SQL inicial para ver los registros.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($promos as $p): ?>
                <tr>
                    <td><span class="badge bg-secondary"><?php echo $p['order_priority']; ?></span></td>
                    <td>
                        <?php if(!empty($p['image'])): ?>
                            <img src="uploads/<?php echo $p['image']; ?>" class="rounded" style="width: 60px; height: 40px; object-fit: cover; border: 1px solid #dee2e6;">
                        <?php else: ?>
                            <div class="bg-light rounded text-center text-muted" style="width: 60px; height: 40px; line-height: 40px; font-size: 0.6rem; border: 1px dashed #ccc;">Sin imagen</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['title']); ?></strong><br>
                        <small class="text-muted">
                            <?php if($p['type'] === 'reviews'): ?>
                                <i class="fas fa-sync-alt text-info"></i> <em>Contenido dinámico (Reseñas aleatorias)</em>
                            <?php else: ?>
                                <?php echo (strlen($p['content'] ?? '') > 50) ? substr(htmlspecialchars($p['content']), 0, 50) . '...' : htmlspecialchars($p['content'] ?? ''); ?>
                            <?php endif; ?>
                        </small>
                    </td>
                    <td>
                        <?php 
                            $icons = [
                                'offer' => 'fa-tag text-success',
                                'hours' => 'fa-clock text-primary',
                                'location' => 'fa-map-marker-alt text-danger',
                                'highlights' => 'fa-star text-warning',
                                'reviews' => 'fa-comment-dots text-info'
                            ];
                            $iconClass = $icons[$p['type']] ?? 'fa-info-circle';
                        ?>
                        <i class="fas <?php echo $iconClass; ?> me-1"></i> <?php echo ucfirst($p['type']); ?>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <?php 
                                echo ($p['css_class'] == 'ambient') ? '🖼️ Fondo Imagen' : 
                                     (($p['css_class'] == 'info-card') ? '✨ Cristal' : '🛤️ Pasos'); 
                            ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $p['is_active'] ? '<span class="text-success">● Activo</span>' : '<span class="text-danger">○ Inactivo</span>'; ?>
                    </td>
                    <td class="text-end">
                        <a href="?route=hero_promos_edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>