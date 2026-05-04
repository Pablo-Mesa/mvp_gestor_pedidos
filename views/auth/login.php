<?php $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Solver</title>
    <meta name="theme-color" content="#f0f2f5">
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png">  
    <!-- Tailwind para prototipado rápido o estilos de v0 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/css_cubo.css">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
    <script src="<?php echo $baseUrl; ?>js/tool-kit-v002.js"></script>
    
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-center items-center p-4">

    <div class="w-full max-w-[400px] space-y-8">
        <!-- Logo y Branding -->
        <div class="text-center space-y-4">            
            <div id="here_cube" class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white shadow-xl shadow-blue-100/50 mb-2">
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Solver</h1>
                <p class="text-slate-500 font-medium">Gestión de Logística & Menú</p>
            </div>
        </div>

        <!-- Card de Login -->
        <div class="glass-card p-8 rounded-[2rem] shadow-2xl shadow-slate-200/50">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Iniciar Sesión</h2>
            
            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 flex items-center gap-3 text-red-600 text-sm animate-pulse">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-semibold"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form action="?route=login" method="POST" class="space-y-5">
                <!-- Token de seguridad CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-slate-600 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                            placeholder="admin@comedor.com">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label for="password" class="text-sm font-semibold text-slate-600">Contraseña</label>
                        <a href="?route=forgot_password" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors">¿Olvidaste tu clave?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" 
                    class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 rounded-2xl shadow-lg shadow-slate-200 transition-all transform active:scale-[0.98] mt-4">
                    Ingresar al Panel
                </button>
            </form>
        </div>

        <!-- Footer Informativo -->
        <p class="text-center text-slate-400 text-xs font-medium">
            &copy; <?php echo date('Y'); ?> Solver Logística. Todos los derechos reservados.
        </p>
    </div>

    <script>
        // Pequeño script para feedback visual en el botón
        document.querySelector('form').addEventListener('submit', function() {
            const btn = this.querySelector('button');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Cargando...';
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        });
        drawCube("here_cube", false, "28px");
    </script>
</body>
</html>