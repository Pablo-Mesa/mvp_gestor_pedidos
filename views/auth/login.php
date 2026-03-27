<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Comedor App</title>
    <link rel="icon" type="image/png" href="assets/icono_solver_nobg.png">  
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #666; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        .error { color: red; text-align: center; margin-bottom: 1rem; font-size: 0.9rem; border: 1px solid red; padding: 0.5rem; border-radius: 4px; background-color: #fff0f0; }
        .links { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .links a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="?route=login" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="admin@comedor.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="********">
            </div>
            
            <button type="submit">Ingresar</button>
            <div style="margin-top: 10px; text-align: right;"><a href="?route=forgot_password" style="font-size: 0.8rem; color: #666; text-decoration: none;">¿Olvidaste tu contraseña?</a></div>
        </form>
        
        <!-- 
             Nota: La ruta de registro aún no existe en el enrutador, 
             pero la dejamos lista para cuando creemos el RegisterController.
        -->
        <div class="links">
            <a href="?route=register">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </div>
</body>
</html>