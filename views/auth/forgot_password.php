<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">
    <!-- <link rel="stylesheet" href="css/login_styles.css">
     Reutiliza estilos de login -->
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    </style>
</head>
<body>+
    <div class="login-container">
        <h2>Recuperar Acceso</h2>
        <?php if (isset($error)): ?><div style="color:red;"><?php echo $error; ?></div><?php endif; ?>
        <?php if (isset($success)): ?><div style="color:green;"><?php echo $success; ?></div><?php endif; ?>

        <?php if (isset($debug_link)): ?>
            <div style="margin-top: 15px; padding: 10px; border: 1px dashed #007bff; background: #e7f3ff; font-size: 0.8rem;">
                <strong>Modo Desarrollo (Simulación de Email):</strong><br>
                Para probar el flujo, haz clic en el enlace generado:<br>
                <a href="<?php echo $debug_link; ?>">Simular clic en el correo recibido</a>
            </div>
        <?php endif; ?>
        
        <form action="?route=send_reset_link" method="POST">
            <p style="font-size: 0.9rem; color: #666;">Ingresa tu correo administrativo y te enviaremos un enlace para restablecer tu clave.</p>
            <div style="margin-bottom: 1rem;">
                <input type="email" name="email" required placeholder="tu-correo@comedor.com" style="width:100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Enviar Enlace</button>
        </form>
        <div style="margin-top: 1rem; text-align: center;"><a href="?route=login" style="color: #007bff; text-decoration: none;">Volver al Login</a></div>
    </div>
</body>
</html>