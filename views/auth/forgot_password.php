<?php $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Acceso | Solver</title>
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
            <h2 class="text-xl font-bold text-slate-800 mb-4">Recuperar Acceso</h2>
            
            <?php if (isset($error)): ?>
                <div class="mb-4 p-4 rounded-2xl bg-red-50 text-red-600 text-sm font-semibold border border-red-100 italic">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="mb-4 p-4 rounded-2xl bg-green-50 text-green-600 text-sm font-semibold border border-green-100">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($debug_link)): ?>
                <div class="mb-6 p-4 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 text-xs">
                    <strong class="text-blue-700 block mb-1">Simulación de Email:</strong>
                    <a href="<?php echo $debug_link; ?>" class="text-blue-600 underline font-bold hover:text-blue-800">Simular clic en el correo recibido</a>
                </div>
            <?php endif; ?>
            
            <form action="?route=send_reset_link" method="POST" class="space-y-5">
                <p class="text-sm text-slate-500 leading-relaxed">
                    Ingresa tu correo administrativo y te enviaremos un enlace para restablecer tu clave.
                </p>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-600 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                            placeholder="tu-correo@comedor.com">
                    </div>
                </div>
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-100 transition-all transform active:scale-[0.98]">
                    Enviar Enlace
                </button>
            </form>
            <div class="mt-6 text-center">
                <a href="?route=login" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>