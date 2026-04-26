<style>
    .support-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-bottom: 30px;
        flex: 1;
        overflow-y: auto;
    }
    .info-card {
        background: white;
        padding: 25px 20px;
        border-radius: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-align: center;
        border-top: 5px solid var(--delivery-primary);
    }
    .info-card i {
        font-size: 3rem;
        color: var(--delivery-primary);
        margin-bottom: 12px;
    }
    .info-card h3 { margin: 0; font-size: 1.3rem; color: var(--delivery-text); }
    .info-card p { color: var(--delivery-subtext); margin-top: 8px; font-size: 0.95rem; line-height: 1.4; }

    .support-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .btn-support {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 20px;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.1rem;
        transition: transform 0.2s, background 0.2s;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .btn-support:active { transform: scale(0.96); }
    .btn-call-local { background: var(--delivery-text); color: white; }
    .btn-wa-local { background: #25D366; color: white; }

    .templates-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .templates-section h4 { 
        margin-bottom: 15px; 
        color: var(--delivery-text); 
        font-size: 0.9rem; 
        border-bottom: 1px solid #eee; 
        padding-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .template-item {
        display: block;
        width: 100%;
        padding: 14px 18px;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-bottom: 10px;
        text-align: left;
        color: var(--delivery-text);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
    }
    .template-item:active { background: #e9ecef; transform: translateX(8px); }
</style>

<div class="support-container">
    <div class="info-card">
        <i class="fas fa-headset"></i>
        <h3>Centro de Ayuda al Repartidor</h3>
        <p>¿Tienes algún problema con un pedido o con tu vehículo? Comunícate con la base de despacho.</p>
    </div>

    <div class="support-actions">
        <?php $localPhone = preg_replace('/\D/', '', $empresa['telefono'] ?? '0'); ?>
        <a href="tel:<?php echo $localPhone; ?>" class="btn-support btn-call-local">
            <i class="fas fa-phone-alt"></i> LLAMADA DIRECTA AL LOCAL
        </a>
        <button onclick="sendBaseWA('<?php echo $localPhone; ?>', '', this)" class="btn-support btn-wa-local">
            <i class="fab fa-whatsapp"></i> ABRIR CHAT DE WHATSAPP
        </button>
    </div>

    <div class="templates-section">
        <h4><i class="fas fa-bolt"></i> Reportes Rápidos</h4>

        <div style="margin-bottom: 15px; padding: 5px; display: flex; align-items: center; gap: 10px; background: #f0fff4; border-radius: 10px; border: 1px solid #c6f6d5;">
            <input type="checkbox" id="includeLocation" style="width: 20px; height: 20px; cursor: pointer;">
            <label for="includeLocation" style="font-size: 0.9rem; font-weight: 700; color: #276749; cursor: pointer; margin: 0;">
                Adjuntar mi ubicación GPS actual
            </label>
        </div>

        <?php
            $templates = [
                "📍 Ya llegué al local para retirar los pedidos.",
                "⏳ Tengo un poco de retraso debido al tráfico intenso.",
                "❓ No estoy logrando ubicar una dirección.",
                "📞 El cliente no atiende las llamadas en puerta.",
                "✅ Reporto que todos mis pedidos fueron entregados.",
                "⚠️ Tuve un inconveniente técnico con mi vehículo."
            ];
            foreach($templates as $msg):
        ?>
            <button class="template-item" onclick="sendBaseWA('<?php echo $localPhone; ?>', '<?php echo urlencode($msg); ?>', this)">
                <?php echo $msg; ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<script>
async function sendBaseWA(phone, text, btn = null) {
    const includeLocation = document.getElementById('includeLocation')?.checked;
    // Convertimos el texto de urlencode (PHP) a texto plano
    let messageBody = decodeURIComponent(text.replace(/\+/g, ' '));
    
    if (includeLocation && navigator.geolocation) {
        let originalContent = "";
        if (btn) {
            originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obteniendo ubicación...';
            btn.disabled = true;
        }

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, { 
                    enableHighAccuracy: true, 
                    timeout: 5000 
                });
            });
            const { latitude, longitude } = position.coords;
            const locSuffix = `\n\n📍 Mi ubicación actual:\nhttps://www.google.com/maps?q=${latitude},${longitude}`;
            messageBody += locSuffix;
        } catch (error) {
            console.warn("No se pudo obtener la ubicación GPS. Se enviará solo el mensaje.");
        } finally {
            if (btn) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }
    }

    // Asegurar formato internacional de Paraguay
    const cleanPhone = phone.startsWith('595') ? phone : '595' + (phone.startsWith('0') ? phone.substring(1) : phone);
    const finalUrl = messageBody 
        ? `https://wa.me/${cleanPhone}?text=${encodeURIComponent(messageBody)}` 
        : `https://wa.me/${cleanPhone}`;
    
    window.open(finalUrl, '_blank');
}
</script>