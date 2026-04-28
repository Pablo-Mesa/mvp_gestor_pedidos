<?php
$isEdit = isset($user);
$action = $isEdit ? '?route=users_update' : '?route=users_store';
?>

<div class="user-form-container">
    <h2 class="form-title">
        <i class="fas <?php echo $isEdit ? 'fa-user-edit' : 'fa-user-plus'; ?>"></i>
        <?php echo $isEdit ? 'Editar Miembro del Staff' : 'Registrar Nuevo Staff'; ?>
    </h2>
    
    <form action="<?php echo $action; ?>" method="POST" class="solver-form">
        <?php if($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <?php endif; ?>
            
        <div class="form-group mb-3">
            <label class="form-label">Nombre Completo</label>
            <input type="text" name="name" class="form-control" required 
                   value="<?php echo $isEdit ? htmlspecialchars($user['name']) : ''; ?>" 
                   placeholder="Ej: Carlos Repartidor">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Correo Electrónico (Acceso)</label>
            <input type="email" name="email" class="form-control" required 
                   value="<?php echo $isEdit ? htmlspecialchars($user['email']) : ''; ?>" 
                   placeholder="usuario@solver.com">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Rol en el Sistema</label>
            <select name="role" class="form-select" required>
                <option value="cajero" <?php echo ($isEdit && $user['role'] === 'cajero') ? 'selected' : ''; ?>>💰 Cajero / Operador de Caja</option>
                <option value="delivery" <?php echo ($isEdit && $user['role'] === 'delivery') ? 'selected' : ''; ?>>🛵 Repartidor / Logística</option>
                <option value="admin" <?php echo ($isEdit && $user['role'] === 'admin') ? 'selected' : ''; ?>>🛡️ Administrador (Acceso Total)</option>
            </select>
            <small class="text-muted mt-1 d-block">Los repartidores solo ven sus pedidos asignados en la App de Logística.</small>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" <?php echo $isEdit ? '' : 'required'; ?> 
                   placeholder="<?php echo $isEdit ? '•••••••• (Dejar en blanco para no cambiar)' : 'Mínimo 6 caracteres'; ?>">
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                   <?php echo (!$isEdit || $user['is_active']) ? 'checked' : ''; ?>>
            <label class="form-check-label fw-bold" for="is_active" style="color: #2d3436; cursor:pointer;">
                Cuenta habilitada para acceder al sistema
            </label>
        </div>

        <div class="form-actions d-flex align-items-center gap-3 pt-3 border-top">
            <button type="submit" class="btn-save">
                <i class="fas fa-check-circle"></i> <?php echo $isEdit ? 'Guardar Cambios' : 'Crear Usuario'; ?>
            </button>
            <a href="?route=users" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<style>
    .user-form-container {
        max-width: 550px;
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.05);
        margin: 1rem 0;
    }

    .form-title { 
        margin-bottom: 2rem; 
        font-weight: 800; 
        color: #2d3436; 
        font-size: 1.4rem;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-title i { color: #0984e3; }

    .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 0.6rem; }
    
    .form-control, .form-select {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        border: 1.5px solid #edf2f7;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus { 
        border-color: #0984e3; 
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(9, 132, 227, 0.1); 
        outline: none;
    }

    .btn-save { 
        background: #2d3436; 
        color: white; 
        border: none; 
        padding: 0.8rem 1.8rem; 
        border-radius: 10px; 
        font-weight: 700; 
        cursor: pointer; 
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover { background: #000; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.15); }
    
    .btn-cancel { color: #636e72; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
    .btn-cancel:hover { color: #2d3436; text-decoration: underline; }
    
    .text-muted { font-size: 0.8rem; line-height: 1.4; }
</style>