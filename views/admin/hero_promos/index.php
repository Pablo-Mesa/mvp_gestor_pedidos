<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Horarios de Atención</h2>
    <p class="text-muted">Administra los días y horarios en los que el local recibe pedidos.</p>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Orden</th>
                    <th>Título Informativo</th>
                    <th>Estado de Configuración</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($promos)): ?>
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No hay horarios configurados en el sistema.</td>
                </tr>
                <?php endif; ?>
                <?php foreach($promos as $p): ?>
                <?php if($p['type'] !== 'hours') continue; // Solo gestionamos el registro de horarios ?>
                <tr>
                    <td><span class="badge bg-secondary"><?php echo $p['order_priority']; ?></span></td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['title']); ?></strong><br>
                        <small class="text-muted"><?php echo $p['is_active'] ? '<span class="text-success">● Activo en banner</span>' : '<span class="text-danger">○ Oculto</span>'; ?></small>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt text-primary"></i> 
                            <?php 
                                $content = json_decode($p['content'], true);
                                echo is_array($content) ? 'Configuración semanal activa' : 'Pendiente de configurar';
                            ?>
                        </small>
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