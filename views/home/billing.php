<div class="container" style="max-width: 600px; padding-top: 20px;">
    <div style="margin-bottom: 2rem; text-align: center;">
        <i class="fas fa-file-invoice" style="font-size: 3rem; color: #0984e3; margin-bottom: 1rem;"></i>
        <h2 style="margin:0; font-weight: 800; color: #2d3436;">Datos de Facturación</h2>
        <p style="color: #636e72;">Configura tus datos predeterminados para agilizar tus pedidos.</p>
    </div>

    <div class="section-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee;">
        <form id="billingForm">
            <div class="input-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #444; margin-bottom: 8px; display: block;">Razón Social / Nombre Completo</label>
                <input type="text" name="billing_name" class="form-control" 
                       value="<?= htmlspecialchars($clientData['billing_name'] ?? $clientData['name'] ?? '') ?>" 
                       placeholder="Ej: Juan Pérez o Mi Empresa S.A." required>
                <small style="color: #888; font-size: 0.8rem; margin-top: 5px; display: block;">Nombre que aparecerá en la factura.</small>
            </div>

            <div class="input-group" style="margin-bottom: 2rem;">
                <label style="font-weight: 600; color: #444; margin-bottom: 8px; display: block;">RUC / Cédula de Identidad</label>
                <input type="text" name="billing_ruc" class="form-control" 
                       value="<?= htmlspecialchars($clientData['billing_ruc'] ?? '') ?>" 
                       placeholder="Ej: 1234567-8" required>
            </div>

            <button type="button" class="btn-main" onclick="saveBillingData()" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>

    <div style="margin-top: 1.5rem; text-align: center;">
        <a href="?route=home" style="color: #636e72; text-decoration: none; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>
</div>

<style>
    .section-card { transition: transform 0.3s ease; }
    .section-card:hover { transform: translateY(-2px); }
    .form-control { 
        width: 100%; 
        padding: 12px; 
        border: 1.5px solid #eee; 
        border-radius: 10px; 
        font-size: 1rem; 
        transition: border-color 0.3s;
    }
    .form-control:focus { 
        border-color: #0984e3; 
        outline: none; 
        background-color: #f8faff;
    }
</style>

<script>
async function saveBillingData() {
    const form = document.getElementById('billingForm');
    const btn = form.querySelector('.btn-main');
    
    // Validación básica
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = {
        billing_name: form.billing_name.value.trim(),
        billing_ruc: form.billing_ruc.value.trim()
    };

    try {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        const response = await fetch('?route=update_billing_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            Toast.fire("¡Datos actualizados correctamente!", "success");
        } else {
            Swal.fire("Error", result.message || "No se pudieron guardar los datos", "error");
        }
    } catch (error) {
        console.error(error);
        Toast.fire("Error de conexión con el servidor", "error");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
    }
}
</script>