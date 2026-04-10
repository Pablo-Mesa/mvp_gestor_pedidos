<style>
    /* Integración de estilos Solver (basados en client_layout.css y consistencia Admin) */
    .header-actions {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
        background-color: #fff;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .page-title { margin: 0; font-size: 1.6rem; font-weight: 700; color: #2d3436; letter-spacing: -0.5px; }

    .btn-add-product { 
        background: #2d3436; /* Color primario Solver */
        color: white; 
        padding: 0.75rem 1.25rem; 
        text-decoration: none; 
        border-radius: 10px; 
        font-weight: 600; 
        font-size: 0.9rem;
        white-space: nowrap; 
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add-product:hover { 
        background: #000; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
    }

    /* Tabla con estética Solver */
    .contenedor-tabla {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        border-radius: 12px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        background: white;
    }

    table { width: 100%; border-collapse: collapse; }
    thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        text-align: left;
    }

    td { padding: 1rem; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }

    /* Efecto de enfoque Solver: desenfoca la lista pero resalta la fila activa */
    table tbody:hover tr { filter: blur(1px); opacity: 0.6; transition: all 0.3s; }
    table tbody tr:hover { filter: blur(0); opacity: 1; background-color: #f8f9fa; }

    /* Badges de Rol */
    .role-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .role-admin { background-color: #e3f2fd; color: #0984e3; }
    .role-delivery { background-color: #e8f5e9; color: #28a745; }

    /* Status Badges */
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .dot-active { background-color: #2ecc71; box-shadow: 0 0 8px #2ecc71; }
    .dot-inactive { background-color: #95a5a6; }
    .text-active { color: #27ae60; font-weight: 700; font-size: 0.8rem; }
    .text-inactive { color: #7f8c8d; font-weight: 700; font-size: 0.8rem; }

    .user-avatar-list {
        width: 35px;
        height: 35px;
        background: #f1f2f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #2d3436;
        font-size: 0.9rem;
        border: 1px solid #eee;
    }

    .btn-action {
        padding: 8px;
        border-radius: 8px;
        color: #636e72;
        transition: 0.2s;
        border: 1px solid #eee;
        background: white;
    }
    .btn-action:hover { background: #f8f9fa; color: #2d3436; border-color: #ddd; }
    .btn-delete-staff:hover { color: #dc3545; border-color: #f8d7da; }
</style>

<div class="header-actions">
    <div class="header-main">
        <h1 class="page-title">Gestión de Staff</h1>
        <a href="?route=users_create" class="btn-add-product">
            <i class="fas fa-user-plus"></i> <span class="d-none d-sm-inline">Nuevo Usuario</span>
        </a>
    </div>
</div>

<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;"></th>
                <th>Nombre Completo</th>
                <th>Email Acceso</th>
                <th>Estado</th>
                <th>Rol / Permisos</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <div class="user-avatar-list">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    </td>
                    <td>
                        <strong style="color: #2d3436;"><?php echo htmlspecialchars($user['name']); ?></strong>
                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-light text-dark ms-1" style="font-size: 0.6rem;">TÚ</span>
                        <?php endif; ?>
                    </td>
                    <td><code style="color: #0984e3;"><?php echo htmlspecialchars($user['email']); ?></code></td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?route=users_toggle_status&id=<?php echo $user['id']; ?>&status=<?php echo $user['is_active']; ?>" 
                               style="text-decoration: none;" title="Cambiar Estado">
                                <span class="status-dot <?php echo $user['is_active'] ? 'dot-active' : 'dot-inactive'; ?>"></span>
                                <span class="<?php echo $user['is_active'] ? 'text-active' : 'text-inactive'; ?>">
                                    <?php echo $user['is_active'] ? 'ACTIVO' : 'SUSPENDIDO'; ?>
                                </span>
                            </a>
                        <?php else: ?>
                            <span class="status-dot dot-active"></span><span class="text-active">SIEMPRE ACTIVO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="role-badge <?php echo $user['role'] === 'admin' ? 'role-admin' : 'role-delivery'; ?>">
                            <i class="fas <?php echo $user['role'] === 'admin' ? 'fa-user-shield' : 'fa-motorcycle'; ?>"></i>
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="?route=users_edit&id=<?php echo $user['id']; ?>" class="btn-action" title="Editar Perfil">
                                <i class="fas fa-pen"></i>
                            </a>
                            
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn-action btn-delete-staff" 
                                        onclick="confirmAction('?route=users_delete&id=<?php echo $user['id']; ?>', {
                                            title: '¿Revocar acceso?',
                                            message: 'El usuario <?php echo addslashes(htmlspecialchars($user['name'])); ?> ya no podrá entrar al sistema.',
                                            btnText: 'Eliminar del Staff'
                                        })" title="Eliminar Staff">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success === 'created') {
        Toast.fire('Personal registrado con éxito', 'success');
    } else if (success === 'updated') {
        Toast.fire('Información actualizada', 'success');
    } else if (success === 'deleted') {
        Toast.fire('Usuario eliminado del sistema', 'info');
    }

    if (error === 'self_delete') {
        Swal.fire('Operación no permitida', 'No puedes eliminar tu propia cuenta de administrador.', 'warning');
    } else if (error === 'email_exists') {
        Swal.fire('Email duplicado', 'Este correo ya está asignado a otro miembro del staff.', 'error');
    }
});
</script>