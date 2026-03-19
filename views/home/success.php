<div class="card text-center shadow mt-5">
    <div class="card-header bg-success text-white">
        ¡Pedido Recibido!
    </div>
    <div class="card-body py-5">
        <h1 class="display-4 text-success mb-3">✓</h1>
        <h5 class="card-title">Gracias por tu compra</h5>
        <p class="card-text">
            Tu pedido <strong>#<?php echo htmlspecialchars($order_id); ?></strong> ha sido registrado correctamente.
            <br>Comienza a prepararse de inmediato.
        </p>
        <a href="?route=home" class="btn btn-primary mt-3">Volver al Menú</a>
    </div>
</div>