<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Establecer Nueva Contraseña</h2>
        <form action="?route=reset_password" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            <div style="margin-bottom: 1rem;">
                <label>Nueva Contraseña</label>
                <input type="password" name="password" required minlength="6" style="width:100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>