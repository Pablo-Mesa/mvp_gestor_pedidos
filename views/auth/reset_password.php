<?php $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña | Solver</title>
    <meta name="theme-color" content="#f0f2f5">
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-[400px] space-y-8">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white shadow-xl shadow-blue-100/50 mb-2">
                <img src="<?php echo $baseUrl; ?>assets/icono_solver_nobg.png" alt="Solver Logo" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Solver</h1>
        </div>

        <div class="glass-card p-8 rounded-[2rem] shadow-2xl shadow-slate-200/50">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Nueva Contraseña</h2>
            
            <form action="?route=reset_password" method="POST" class="space-y-6">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-600 ml-1">Establecer Nueva Contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-green-500 transition-colors">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="password" name="password" required minlength="6" 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all"
                            placeholder="Mínimo 6 caracteres">
                    </div>
                    <p class="text-[10px] text-slate-400 ml-1 italic">* Asegúrate de que sea una clave segura.</p>
                </div>

                <button type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-100 transition-all transform active:scale-[0.98]">
                    Actualizar Contraseña
                </button>
            </form>
        </div>
        <p class="text-center text-slate-400 text-xs font-medium">
            Solver Logística &copy; <?php echo date('Y'); ?>
        </p>
    </div>
</body>
</html>