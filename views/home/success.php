<div class="card" style="text-align: center; padding: 3rem;">
    <h1 style="color: #28a745;">¡Pedido Realizado!</h1>
    <p>Tu pedido #<?php echo $_GET['id'] ?? ''; ?> ha sido registrado exitosamente.</p>
    <p>Pronto lo prepararemos para ti.</p>

    <!-- Barra de progreso visual -->
    <div style="width: 100%; background: #e9ecef; height: 8px; border-radius: 10px; margin-top: 2rem; overflow: hidden;">
        <div id="progressBar" style="width: 100%; background: #28a745; height: 100%; transition: width 1s linear;"></div>
    </div>

    <!-- Contador visual -->
    <div style="margin-top: 1rem; font-size: 0.85rem; color: #636e72;">
        <i class="fas fa-sync-alt fa-spin"></i> Volviendo al menú en <span id="timer" style="font-weight: bold; color: #2d3436;">7</span> segundos...
    </div>

    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 10px; align-items: center;">
        <a href="?route=home" class="btn btn-primary" style="display: inline-block; width: fit-content;">Volver al Inicio ahora</a>
        
        <?php 
            $orderId = $_GET['id'] ?? '';
            $phone = $siteSettings['store_phone'] ?? ''; // Asumiendo que existe en la configuración
            $text = urlencode("¡Hola! Acabo de realizar el pedido #$orderId desde la web. Quedo atento a la preparación. ¡Gracias!");
            $waUrl = "https://wa.me/$phone?text=$text";
        ?>
        <a href="<?= $waUrl ?>" target="_blank" style="color: #25d366; text-decoration: none; font-size: 0.85rem; font-weight: bold;">
            <i class="fab fa-whatsapp"></i> ¿No se abrió WhatsApp? Haz clic aquí
        </a>
    </div>
</div>

<script>
    const TOTAL_TIME = 7;
    let timeLeft = TOTAL_TIME;
    const timerElement = document.getElementById('timer');
    const progressBar = document.getElementById('progressBar');

    // Intento de apertura automática de WhatsApp (Puede ser bloqueado por el navegador)
    window.onload = () => {
        setTimeout(() => {
            window.open("<?= $waUrl ?>", '_blank');
        }, 800);
    };

    const countdown = setInterval(() => {
        timeLeft--;
        timerElement.innerText = timeLeft;
        
        // Actualizar barra de progreso
        const percentage = (timeLeft / TOTAL_TIME) * 100;
        progressBar.style.width = percentage + '%';

        if (timeLeft <= 0) {
            clearInterval(countdown);
            window.location.href = '?route=home';
        }
    }, 1000);
</script>