<div class="mozo-interface">
    <!-- Botón flotante para ver pedido actual -->
    <div class="fixed-bottom p-3 text-center" style="margin-bottom: 70px;">
        <button class="btn btn-primary rounded-pill shadow-lg px-4 py-2" onclick="showCurrentOrder()">
            <i class="fas fa-shopping-basket me-2"></i> Ver Comanda
        </button>
    </div>
</div>

<script>
    let currentOrder = [];

    function showCurrentOrder() {
        // Aquí abriremos un modal o drawer con el resumen, 
        // opción de quitar items y botón de "Enviar a Cocina"
        alert("Resumen de pedido: " + currentOrder.length + " items.");
    }
</script>