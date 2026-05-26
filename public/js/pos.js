document.addEventListener('DOMContentLoaded', function() {
    // Recuperar carrito guardado para evitar pérdida de datos por recargas o cierres accidentales (Resiliencia)
    let posCart = [];
    try {
        const savedCart = localStorage.getItem('pos_draft_cart');
        if (savedCart) posCart = JSON.parse(savedCart);
    } catch (e) {
        console.error("Error al recuperar el carrito del localStorage:", e);
        localStorage.removeItem('pos_draft_cart');
    }

    let selectedClientId = 1; // 1 = Cliente Ocasional por defecto
    const isCashOpen = window.posConfig ? window.posConfig.isCashOpen : false;

    // Instancias de Modales
    let bsModalFinalize, bsModalSearch, bsModalCreate, payModal, bsModalSelectLocation, bsModalAddLocation;

    const totalEl = document.getElementById('posTotal');
    const itemsEl = document.getElementById('ticketItems');

    function updateTime() {
        document.getElementById('current-time').innerText = new Date().toLocaleString();
    }
    setInterval(updateTime, 1000);

    function showProductImg(img, name) {
        Swal.fire({
            title: name,
            imageUrl: 'uploads/' + img,
            imageWidth: 400,
            imageAlt: name,
            confirmButtonText: 'Cerrar'
        });
    }

    /**
     * Controla la visibilidad del overlay global de carga
     */
    function toggleLoadingOverlay(show) {
        const overlay = document.getElementById('posLoadingOverlay');
        if (overlay) overlay.style.display = show ? 'flex' : 'none';
    }

    /**
     * Vacía el carrito actual con confirmación de SweetAlert
     */
    async function confirmClearCart() {
        if(posCart.length === 0) return;

        const result = await Swal.fire({
            title: '¿Vaciar pedido?',
            text: "Se eliminarán todos los productos cargados en el ticket actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff4757',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            posCart = [];
            document.getElementById('posObservation').value = "";
            renderTicket();
            Toast.fire("Pedido vaciado", "info");
        }
    }

    function clearPOS() {
        document.getElementById('posSearch').value = '';
        filterByCat('all', document.querySelector('.pos-category-pills button'));
    }

    function filterPOS() {
        const val = document.getElementById('posSearch').value.toLowerCase();
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = card.dataset.name.includes(val) ? 'block' : 'none';
        });
    }

    function filterByCat(catId, btn) {
        document.querySelectorAll('.btn-pos-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.pos-item-card').forEach(card => {
            card.style.display = (catId === 'all' || card.dataset.cat === catId) ? 'block' : 'none';
        });
    }

    function addToTicket(id, name, price) {
        const exists = posCart.find(i => i.id === id);
        if(exists) {
            exists.quantity++;
        } else {
            posCart.push({ id, name, price, quantity: 1 });
        }
        renderTicket();
    }

    function removeFromTicket(id) {
        posCart = posCart.filter(i => i.id !== id);
        renderTicket();
    }

    function renderTicket() {
        // Persistencia resiliente: Guardar en localStorage si hay items, borrar si está vacío
        if (posCart.length > 0) {
            localStorage.setItem('pos_draft_cart', JSON.stringify(posCart));
        } else {
            localStorage.removeItem('pos_draft_cart');
        }

        if(posCart.length === 0) {
            itemsEl.innerHTML = '<div style="text-align: center; color: rgba(255,255,255,0.3); margin-top: 40px;"><i class="fas fa-receipt fa-3x"></i><p>Cargue productos</p></div>';
            totalEl.innerText = "0";
            return;
        }

        let html = '';
        let total = 0;
        posCart.forEach(item => {
            total += item.price * item.quantity;
            html += `
                <div class="ticket-item">
                    <div class="ticket-item-info">
                        <span class="ticket-item-qty">${item.quantity}</span> ${item.name}
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span>${new Intl.NumberFormat('es-PY').format(item.price * item.quantity)}</span>
                        <button onclick="removeFromTicket('${item.id}')" style="background:none; border:none; color:#ff4757; cursor:pointer;"><i class="fas fa-times"></i></button>
                    </div>
                </div>`;
        });
        itemsEl.innerHTML = html;
        totalEl.innerText = new Intl.NumberFormat('es-PY').format(total);
    }

    /**
     * Resetea el estado completo del POS
     */
    function resetPOS() {
        posCart = [];
        localStorage.removeItem('pos_draft_cart'); // Limpiar persistencia del borrador inmediatamente
        document.getElementById('posObservation').value = "";
        selectedClientId = 1;

        // Re-habilitar botón de confirmación en caso de que haya quedado bloqueado
        const confirmBtn = document.querySelector('#modalFinalize .btn-success');
        if(confirmBtn) confirmBtn.disabled = false;
        
        // Reset campos modal finalización
        const fields = {
            'f-client-id': 1,
            'f-client-name': 'Cliente Ocasional',
            'f-delivery-type': 'local',
            'f-payment-method': 'efectivo',
            'f-observation': '',
            'f-delivery-cost': 0
        };
        
        Object.keys(fields).forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fields[id];
        });
        
        toggleDeliveryFields('local');
        clearPOS(); // Restablece búsqueda y filtros de categorías
        renderTicket();

        // Preparar el foco para la siguiente venta
        setTimeout(() => document.getElementById('posSearch').focus(), 50);
    }

    /**
     * Abre el modal principal y sincroniza los datos del ticket
     */
    window.openFinalizeModal = function() {
        if(posCart.length === 0) return Toast.fire("Agrega productos al ticket", "warning");

        const delType = document.getElementById('f-delivery-type').value;
        if (delType !== 'delivery') {
            document.getElementById('f-delivery-cost').value = 0;
            document.getElementById('f-delivery-rate-id').value = "";
            document.getElementById('f-delivery-rate-id').disabled = false;
        }

        updateFinalizeTotal();

        if(!document.getElementById('f-observation').value) {
             document.getElementById('f-observation').value = document.getElementById('posObservation').value;
        }

        bsModalFinalize.show();
    }

    /**
     * Recalcula el total del modal de finalización sumando el ticket + envío
     */
    window.updateFinalizeTotal = function() {
        let cartTotal = 0;
        posCart.forEach(item => cartTotal += item.price * item.quantity);

        const deliveryCost = parseFloat(document.getElementById('f-delivery-cost').value) || 0;
        const finalTotal = cartTotal + deliveryCost;

        document.getElementById('f-total-display').innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(finalTotal);
    }

    window.openSearchClient = function() {
        // Evita el error de aria-hidden quitando el foco del botón antes de ocultar el modal
        if (document.activeElement instanceof HTMLElement) document.activeElement.blur();
        
        bsModalFinalize.hide();
        bsModalSearch.show();
        window.searchClientListApi();
    }

    window.closeSearchAndReturn = function() {
        bsModalSearch.hide();
        bsModalFinalize.show();
    }

    window.openCreateClient = function() {
        // Evita el error de aria-hidden quitando el foco del botón antes de ocultar el modal
        if (document.activeElement instanceof HTMLElement) document.activeElement.blur();

        bsModalFinalize.hide();
        bsModalCreate.show();
    }

    window.closeCreateAndReturn = function() {
        bsModalCreate.hide();
        bsModalFinalize.show();
    }

    /**
     * Registro rápido de cliente vía AJAX (Bootstrap Modal)
     */
    window.submitQuickClient = async function() {
        const data = {
            name: document.getElementById('c-name').value,
            phone: document.getElementById('c-phone').value,
            email: document.getElementById('c-email').value,
            billing_name: document.getElementById('c-billing-name').value,
            billing_ruc: document.getElementById('c-billing-ruc').value
        };

        if(!data.name || !data.phone) return Toast.fire("Nombre y Teléfono requeridos", "error");

        try {
            const resp = await fetch('?route=admin_clients_store_api', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await resp.json();
            if(res.success) {
                selectClientFromList(res.id, data.name, data.phone);
                Toast.fire("Cliente registrado", "success");
                // Limpiar campos para la próxima
                ['c-name', 'c-phone', 'c-email', 'c-billing-name', 'c-billing-ruc'].forEach(id => document.getElementById(id).value = '');
            }
        } catch(e) { console.error(e); }
    }

    /**
     * Funcionalidad de Cobro Mixto (Adaptada de Orders)
     */
    function openPaymentModal(order) {
        document.getElementById('pay-modal-id').innerText = order.id;
        document.getElementById('pay-input-order-id').value = order.id;
        document.getElementById('pay-modal-total').innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(order.total);
        document.getElementById('pay-modal-total').dataset.total = order.total;

        document.querySelectorAll('.pay-input').forEach(inp => inp.value = 0);
        const firstInput = document.querySelector(`.pay-input[data-method="${order.payment_method}"]`);
        if (firstInput) firstInput.value = order.total;

        calculatePayBalance();
        payModal.show();
    }

    function calculatePayBalance() {
        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let paid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => paid += parseFloat(inp.value) || 0);
        const remaining = total - paid;
        const display = document.getElementById('pay-balance-display');
        const card = document.getElementById('pay-balance-card');
        const label = document.getElementById('pay-balance-label');
        const submitBtn = document.getElementById('pay-btn-submit');

        card.style.backgroundColor = '#fff';
        if (remaining > 0) {
            label.innerText = 'RESTA COBRAR';
            display.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(remaining);
            display.className = 'mb-0 fw-bold text-danger';
            if (submitBtn) submitBtn.disabled = true;
        } else if (remaining < 0) {
            label.innerText = 'VUELTO';
            display.innerText = 'Gs. ' + new Intl.NumberFormat('es-PY').format(Math.abs(remaining));
            display.className = 'mb-0 fw-bold text-primary';
            if (submitBtn) submitBtn.disabled = false;
        } else {
            label.innerText = 'ESTADO';
            display.innerText = 'MONTO EXACTO ✅';
            display.className = 'mb-0 fw-bold text-success';
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    window.fillRemainingPay = function(method) {
        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let otherPaid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => {
            if (inp.dataset.method !== method) otherPaid += parseFloat(inp.value) || 0;
        });
        document.querySelector(`.pay-input[data-method="${method}"]`).value = Math.max(0, total - otherPaid);
        calculatePayBalance();
    };

    async function submitPayment(e) {
        e.preventDefault();

        const total = parseFloat(document.getElementById('pay-modal-total').dataset.total);
        let paid = 0;
        document.querySelectorAll('.pay-input').forEach(inp => paid += parseFloat(inp.value) || 0);

        if (paid < total) {
            Toast.fire("El monto cobrado es insuficiente", "warning");
            return;
        }

        const formData = new FormData(document.getElementById('form-pay-modal'));
        const shouldPrint = document.getElementById('pay-should-print').value;
        const btn = e.submitter || document.getElementById('pay-btn-submit');
        const originalHTML = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESANDO...';
        toggleLoadingOverlay(true);

        try {
            const resp = await fetch('?route=orders_process_finalize', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const res = await resp.json();
            if (res.success) {
                if (typeof Toast !== 'undefined') Toast.fire("Venta registrada con éxito", "success");
                
                payModal.hide();
                toggleLoadingOverlay(false); // Liberar la interfaz inmediatamente tras ocultar el modal
                
                if (shouldPrint === '1' && res.print_sale_id) {
                    printSaleTicket(res.print_sale_id);
                }

                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    resetPOS();
                }, shouldPrint === '1' ? 1200 : 100);
            } else {
                Toast.fire(res.message || "Error al procesar el pago", "error");
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                toggleLoadingOverlay(false);
            }
        } catch(e) { 
            Toast.fire("Error de red", "error"); 
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            toggleLoadingOverlay(false);
        }
    }

    /**
     * Manejo de interfaz dinámica
     */
    // Initial DOMContentLoaded listener
    // This is already present in the original file, so we'll just move its content here.
    // The `DOMContentLoaded` event listener should be placed at the top level of the JS file.

    // Initializations
    bsModalFinalize = new bootstrap.Modal(document.getElementById('modalFinalize'));
    bsModalSearch = new bootstrap.Modal(document.getElementById('modalSearchClient'));
    bsModalCreate = new bootstrap.Modal(document.getElementById('modalCreateClient'));
    payModal = new bootstrap.Modal(document.getElementById('modalPayOrder'));
    bsModalSelectLocation = new bootstrap.Modal(document.getElementById('modalSelectLocation'));
    bsModalAddLocation = new bootstrap.Modal(document.getElementById('modalAddLocation'));

    const payInputs = Array.from(document.querySelectorAll('.pay-input'));
    const paySubmitBtn = document.getElementById('pay-btn-submit');

    document.getElementById('form-pay-modal').onsubmit = submitPayment;
    payInputs.forEach((inp, index) => {
        inp.addEventListener('input', calculatePayBalance);
        inp.addEventListener('focus', function() { if(this.value == "0") this.value = ""; });
        inp.addEventListener('blur', function() { if(this.value == "") this.value = "0"; });

        // La navegación específica por input se ha movido al Motor Global de Teclado
    });

    /**
     * Motor de Navegación Global por Teclado
     */
    document.addEventListener('keydown', function(e) {
        const active = document.activeElement;

        // 1. Buscador -> Categorías
        if (active.id === 'posSearch' && e.key === 'ArrowDown') {
            e.preventDefault();
            const firstCat = document.querySelector('.btn-pos-filter');
            if (firstCat) firstCat.focus();
        }

        // 2. Navegación en Categorías
        if (active.classList.contains('btn-pos-filter')) {
            const pills = Array.from(document.querySelectorAll('.btn-pos-filter'));
            const idx = pills.indexOf(active);
            if (e.key === 'ArrowRight') { e.preventDefault(); if (pills[idx + 1]) pills[idx + 1].focus(); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); if (pills[idx - 1]) pills[idx - 1].focus(); else document.getElementById('posSearch').focus(); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); const firstItem = document.querySelector('.pos-item-card:not([style*="display: none"])'); if (firstItem) firstItem.focus(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); document.getElementById('posSearch').focus(); }
        }

        // 3. Grid de Productos
        const card = active.closest('.pos-item-card');
        if (card && !active.classList.contains('btn-portion')) {
            const items = Array.from(document.querySelectorAll('.pos-item-card:not([style*="display: none"])'));
            const idx = items.indexOf(card);
            const grid = document.getElementById('posGrid');
            const cols = grid ? getComputedStyle(grid).gridTemplateColumns.split(' ').length : 4;

            if (e.key === 'ArrowRight') { e.preventDefault(); if (items[idx + 1]) items[idx + 1].focus(); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); if (items[idx - 1]) items[idx - 1].focus(); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); if (items[idx + cols]) items[idx + cols].focus(); }
            else if (e.key === 'ArrowUp') { 
                e.preventDefault(); 
                if (items[idx - cols]) items[idx - cols].focus(); 
                else { const activeCat = document.querySelector('.btn-pos-filter.active'); if (activeCat) activeCat.focus(); }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const portions = card.querySelectorAll('.btn-portion');
                if (portions.length > 0) portions[0].focus();
                else { card.click(); card.style.transform = 'scale(0.95)'; setTimeout(() => card.style.transform = '', 100); }
            }
        }

        // 4. Botones de Porción
        if (active.classList.contains('btn-portion')) {
            const cardParent = active.closest('.pos-item-card');
            const portions = Array.from(cardParent.querySelectorAll('.btn-portion'));
            const pIdx = portions.indexOf(active);
            if (e.key === 'ArrowRight' && portions[pIdx + 1]) { e.preventDefault(); portions[pIdx + 1].focus(); }
            else if (e.key === 'ArrowLeft' && portions[pIdx - 1]) { e.preventDefault(); portions[pIdx - 1].focus(); }
            else if (e.key === 'ArrowUp' || e.key === 'Escape') { e.preventDefault(); cardParent.focus(); }
        }

        // 5. Modal de Pago: Recorrido Efectivo -> Tarjeta -> Transf -> QR -> Botones
        const payModalEl = active.closest('#modalPayOrder');
        if (payModalEl) {
            // Selector que garantiza el orden natural del DOM (montos, referencias y botones finales)
            const focusable = Array.from(payModalEl.querySelectorAll('input:not([type="hidden"]), .modal-footer button[type="submit"]'));
            const fIdx = focusable.indexOf(active);

            if (e.key === 'ArrowDown' || (e.key === 'Enter' && active.tagName === 'INPUT')) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (focusable[fIdx + 1]) {
                    focusable[fIdx + 1].focus();
                    if (focusable[fIdx + 1].tagName === 'INPUT') focusable[fIdx + 1].select();
                } else {
                    document.getElementById('pay-btn-submit').focus();
                }
            } else if (e.key === 'ArrowUp' && focusable[fIdx - 1]) {
                e.preventDefault();
                focusable[fIdx - 1].focus();
                if (focusable[fIdx - 1].tagName === 'INPUT') focusable[fIdx - 1].select();
            }
        }
    });

    // Foco automático al abrir el modal de cobro
    document.getElementById('modalPayOrder').addEventListener('shown.bs.modal', function () {
        const firstInput = this.querySelector('.pay-input');
        if (firstInput) { firstInput.focus(); firstInput.select(); }
    });

    document.getElementById('modalFinalize').addEventListener('shown.bs.modal', function () {
        const modalEl = document.getElementById('modalFinalize');
        const searchBtn = modalEl.querySelector('[onclick="openSearchClient()"]');
        const createBtn = modalEl.querySelector('[onclick="openCreateClient()"]');

        const delivery = document.getElementById('f-delivery-type');
        const selectLocationBtn = document.getElementById('btn-select-location');
        const payment = document.getElementById('f-payment-method');
        const observation = document.getElementById('f-observation');
        const confirmBtn = modalEl.querySelector('[onclick="confirmPOS()"]');

        // Colocar el foco explícitamente cuando el modal ya es visible
        if (searchBtn) {
            searchBtn.focus();
        }

        const focusPath = [searchBtn, createBtn, payment, delivery, selectLocationBtn, observation, confirmBtn];

        focusPath.forEach((el, index) => {
            if (el) {
                el.onkeydown = (e) => {
                    if (e.key === 'Enter') { /*&& e.target.tagName !== 'TEXTAREA'*/
                        e.preventDefault();
                        let nextFound = false;
                        for (let i = index + 1; i < focusPath.length; i++) {
                            // offsetParent es null si el elemento o sus padres tienen display:none
                            if (focusPath[i] && focusPath[i].offsetParent !== null) {
                                focusPath[i].focus();
                                nextFound = true;
                                break;
                            }
                        }
                        if (!nextFound) {
                            if (el === confirmBtn) {
                                confirmBtn.click();
                            } else {
                                confirmBtn.focus();
                            }
                        }
                    }
                };
            }
        });
    });

    // Foco automático para el buscador de clientes cuando termina la animación
    document.getElementById('modalSearchClient').addEventListener('shown.bs.modal', function () {
        document.getElementById('s-client-term').focus();
    });

    // Lógica de navegación por teclado para el buscador de clientes
    const clientSearchInput = document.getElementById('s-client-term');
    const clientResultsBody = document.getElementById('s-client-results');

    clientSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            const firstRow = clientResultsBody.querySelector('.selectable-client');
            if (firstRow) {
                e.preventDefault();
                firstRow.focus();
            }
        }
    });

    clientResultsBody.addEventListener('keydown', function(e) {
        const currentFocused = document.activeElement;
        if (!currentFocused || !currentFocused.classList.contains('selectable-client')) return;

        if (e.key === 'ArrowDown') {
            const nextRow = currentFocused.nextElementSibling;
            if (nextRow && nextRow.classList.contains('selectable-client')) {
                e.preventDefault();
                nextRow.focus();
            }
        } else if (e.key === 'ArrowUp') {
            const prevRow = currentFocused.previousElementSibling;
            if (prevRow && prevRow.classList.contains('selectable-client')) {
                e.preventDefault();
                prevRow.focus();
            } else {
                e.preventDefault();
                clientSearchInput.focus();
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            currentFocused.click();
        }
    });

    // Foco automático para el registro de nuevo cliente
    document.getElementById('modalCreateClient').addEventListener('shown.bs.modal', function () {
        document.getElementById('c-name').focus();
    });

    // Foco automático en el botón de agregar dirección al abrir el selector de ubicaciones
    document.getElementById('modalSelectLocation').addEventListener('shown.bs.modal', function () {
        const addBtn = this.querySelector('.modal-footer .btn-primary');
        if (addBtn) addBtn.focus();
    });

    // Foco automático al abrir el formulario de nueva dirección
    document.getElementById('modalAddLocation').addEventListener('shown.bs.modal', function () {
        document.getElementById('c-location-title').focus();
    });

    window.toggleDeliveryFields = function(val) {
        const container = document.getElementById('f-delivery-extra');
            const isDelivery = (val === 'delivery');
            container.style.display = isDelivery ? 'block' : 'none';

            // Ocultar el selector de tarifas por defecto hasta que se elija una ubicación
            document.getElementById('f-delivery-rate-container').style.display = 'none';
            document.getElementById('btn-open-map-url').style.display = 'none';

            if(isDelivery) {
                loadClientLocations(document.getElementById('f-client-id').value);
            } else {
                document.getElementById('f-delivery-cost').value = 0;
                document.getElementById('f-delivery-rate-id').value = "";
                updateFinalizeTotal();
            }

            // Re-enfocar el modal en pantalla para evitar recortes por cambio de altura dinámica
            if (bsModalFinalize) bsModalFinalize.handleUpdate();
        }

    /**
     * Recolección de datos y envío final
     */
    window.confirmPOS = function() {
        const formValues = {
            clientId: document.getElementById('f-client-id').value,
            locationId: document.getElementById('f-location-id').value,
            deliveryAddress: document.getElementById('f-delivery-address').value,
            deliveryType: document.getElementById('f-delivery-type').value,
            paymentMethod: document.getElementById('f-payment-method').value,
            observation: document.getElementById('f-observation').value,
            deliveryCost: document.getElementById('f-delivery-cost').value,
            deliveryRateId: document.getElementById('f-delivery-rate-id').value,
            lat: document.getElementById('f-lat').value,
            lng: document.getElementById('f-lng').value
        };

        submitPOS(formValues);
    }

    window.openSelectLocationModal = function() {
        const clientId = document.getElementById('f-client-id').value;
        const clientName = document.getElementById('f-client-name').value;
        if (document.activeElement instanceof HTMLElement) document.activeElement.blur();
        
        document.getElementById('select-location-client-name').innerText = clientName;
        bsModalSelectLocation.show();
    }

    window.openAddLocationModal = function() {
        const clientId = document.getElementById('f-client-id').value;
        const clientName = document.getElementById('f-client-name').value;
        document.getElementById('add-location-client-name').innerText = clientName;
        document.getElementById('c-location-client-id').value = clientId;

        // Esperar a que el modal de selección se oculte para evitar errores de foco (aria-hidden)
        const selectModalEl = document.getElementById('modalSelectLocation');
        const onHidden = () => {
            bsModalAddLocation.show();
            selectModalEl.removeEventListener('hidden.bs.modal', onHidden);
        };
        selectModalEl.addEventListener('hidden.bs.modal', onHidden);
        bsModalSelectLocation.hide();
    }

    window.closeAddLocationAndReturn = function() {
        const addModalEl = document.getElementById('modalAddLocation');
        const onHidden = () => {
            bsModalSelectLocation.show();
            addModalEl.removeEventListener('hidden.bs.modal', onHidden);
        };
        addModalEl.addEventListener('hidden.bs.modal', onHidden);
        bsModalAddLocation.hide();
    }
    /**
     * Carga las ubicaciones del cliente seleccionado en el POS
     */
    window.loadClientLocations = async function(clientId) {
        const btnSelect = document.getElementById('btn-select-location');
        const listEl = document.getElementById('f-modal-locations-list');

        if(!listEl) return;
        listEl.innerHTML = '<div class="col-12 text-center py-3"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';

        try {
            const resp = await fetch(`?route=admin_client_locations&id=${clientId}`);
            const locations = await resp.json();
            listEl.innerHTML = '';

            if(Array.isArray(locations) && locations.length > 0) {
                if(btnSelect) btnSelect.classList.replace('btn-outline-primary', 'btn-primary');

                locations.forEach(loc => {
                    const card = document.createElement('div');
                    card.className = 'pos-location-card';
                    card.innerHTML = `<i class="fas fa-map-marker-alt"></i><strong>${loc.title}</strong><small>${loc.address}</small>`;
                    card.onclick = () => {
                        window.selectClientLocation(loc);
                    };
                    listEl.appendChild(card);
                });
            } else {
                if(btnSelect) btnSelect.classList.replace('btn-primary', 'btn-outline-primary');
                listEl.innerHTML = '<div class="col-12 text-center text-muted py-3">No hay direcciones registradas.</div>';
            }

            // Ajustar posición del modal tras cargar contenido dinámico (direcciones)
            if (bsModalSelectLocation) bsModalSelectLocation.handleUpdate();
        } catch (e) { console.error(e); }
    }

    window.selectClientLocation = function(loc) {
        document.getElementById('f-location-id').value = loc.id;
        document.getElementById('f-delivery-address').value = loc.address;
        document.getElementById('f-location-url').value = loc.location_url;
        document.getElementById('f-lat').value = loc.lat;
        document.getElementById('f-lng').value = loc.lng;

        const display = document.getElementById('f-location-display');
        if(display) display.innerText = loc.title + ': ' + loc.address;

        // Mostrar botón de mapa si la ubicación seleccionada tiene un link
        const btnOpenMap = document.getElementById('btn-open-map-url');
        if (loc.location_url && loc.location_url.trim() !== '') {
            btnOpenMap.href = loc.location_url;
            btnOpenMap.style.display = 'block';
        } else {
            btnOpenMap.style.display = 'none';
        }

        // Mostrar el selector de tarifas ahora que se ha seleccionado una ubicación
        document.getElementById('f-delivery-rate-container').style.display = 'block';

        const rateSelect = document.getElementById('f-delivery-rate-id');

        if (loc.lat && loc.lng) {
            const storeLat = window.posConfig ? window.posConfig.storeLat : -25.3006;
            const storeLng = window.posConfig ? window.posConfig.storeLng : -57.6359;

            const distance = calculateDistance(storeLat, storeLng, parseFloat(loc.lat), parseFloat(loc.lng));

            let found = false;
            for (let i = 0; i < rateSelect.options.length; i++) {
                const opt = rateSelect.options[i];
                const from = parseFloat(opt.dataset.from);
                const to = parseFloat(opt.dataset.to);
                if (distance >= from && distance <= to) {
                    rateSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }

            if (!found) rateSelect.value = "";
            rateSelect.disabled = true; // Bloqueado porque fue automático
        } else {
            rateSelect.value = "";
            rateSelect.disabled = false; // Habilitado para selección manual
            setTimeout(() => { rateSelect.focus(); }, 500);
        }

        // Re-enfocar el modal principal ya que su contenido (contenedor de tarifas) cambió de visibilidad
        if (bsModalFinalize) bsModalFinalize.handleUpdate();

        updateDeliveryCostFromSelect();

        Toast.fire("Dirección cargada", "success");
       bsModalSelectLocation.hide();
    }

    window.updateDeliveryCostFromSelect = function() {
        const select = document.getElementById('f-delivery-rate-id');
        const selectedOption = select.options[select.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price) || 0;
        document.getElementById('f-delivery-cost').value = price;
        updateFinalizeTotal();
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radio de la Tierra en km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c) * 1.3; // Factor de circuidad (ruta real aprox)
    }

    window.processAddLocationUrl = async function(url) {
        if (!url || url.trim().length < 10) {
            return Toast.fire("Pegue un link de ubicación primero", "warning");
        }

        // Regex robusto para extraer coordenadas de links de Maps o WhatsApp (@lat,lng / q=lat,lng / !3dlat!4dlng)
        const regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/; // Simplified for direct lat,lng after @
        const match = url.match(regex);

        if (match) {
            window.updateAddLocationMarker(parseFloat(match[1]), parseFloat(match[2]));
            Toast.fire("Ubicación extraída correctamente", "success");
        } else if (url.includes('goo.gl') || url.includes('maps.app.goo.gl')) {
            try {
                const resp = await fetch(`?route=admin_resolve_map_url&url=${encodeURIComponent(url.trim())}`);
                const res = await resp.json();
                if (res.success) {
                    window.updateAddLocationMarker(parseFloat(res.lat), parseFloat(res.lng));
                    Toast.fire("Ubicación resuelta con éxito", "success");
                } else {
                    Toast.fire(res.message || "No se detectaron coordenadas en el link", "error");
                }
            } catch (e) { console.error(e); }
        } else {
            Toast.fire("No se encontraron coordenadas en el texto/enlace proporcionado", "warning");
        }
    }

    window.updateAddLocationMarker = function(lat, lng) {
        const latInp = document.getElementById('c-location-lat');
        const lngInp = document.getElementById('c-location-lng');
        if (latInp) latInp.value = lat;
        if (lngInp) lngInp.value = lng;

        // Assuming 'addLocationMap' and 'addLocationMarker' are defined elsewhere if a map is used
        // if (addLocationMap) {
        //     const newPos = [lat, lng];
        //     addLocationMarker.setLatLng(newPos);
        //     addLocationMap.setView(newPos, 16);
        //     // Pequeño delay para asegurar que Leaflet procese el cambio de vista
        //     setTimeout(() => addLocationMap.invalidateSize(), 200);
        // }
    }

    window.ProcesarPedidoAddLocation = function() {
        const fullTextInput = document.getElementById('c-location-url').value;
        if (!fullTextInput || fullTextInput.trim() === '') {
            Toast.fire("Pegue el texto o link de ubicación primero", "warning");
            return;
        }

        const mapRegex = /https?:\/\/(maps\..+?|goo\.gl\/maps\/|maps\.app\.goo\.gl\/)\/\S+/g;
        const linkMatch = fullTextInput.match(mapRegex);

        if (linkMatch && linkMatch.length > 0) {
            const extractedUrl = linkMatch[0];
            window.processAddLocationUrl(extractedUrl);
        } else {
            Toast.fire("No se detectó un enlace de ubicación válido en el texto. Asegúrate de que el mensaje lo incluya.", "error");
        }
    }

    // This function seems to be a remnant or example, not directly used in the current POS flow.
    // Keeping it for now but noting its potential irrelevance.
    // function procesarPedido(){
    //     const text = document.getElementById('rawText').value;
    //     if (!text) return alert("Pega el texto primero");

    //     // 1. Buscar URL de Google Maps
    //     const mapRegex = /https?:\/\/(maps\..+?|goo\.gl\/maps\/|maps\.app\.goo\.gl\/)\/\S+/g;
    //     const linkEncontrado = text.match(mapRegex);

    //     // 2. Intentar sacar un nombre (opcional: asume que la primera línea es el nombre)
    //     const lineas = text.split('\n');
    //     const nombreCliente = lineas[0].substring(0, 20); // Toma los primeros 20 caracteres

    //     if (linkEncontrado) {
    //         const url = linkEncontrado[0];
    //         crearTarjeta(nombreCliente, url);
    //         document.getElementById('rawText').value = ""; // Limpiar input
    //     } else {
    //         alert("No se detectó un enlace de ubicación. Asegúrate de que el mensaje lo incluya.");
    //     }
    // }

    window.submitQuickLocation = async function() {
        const data = {
            client_id: document.getElementById('c-location-client-id').value,
            title: document.getElementById('c-location-title').value,
            address: document.getElementById('c-location-address').value,
            location_url: document.getElementById('c-location-url').value
        };

        if(!data.title || !data.address || !data.location_url) return Toast.fire("Complete los datos", "warning");

        const resp = await fetch('?route=pos_save_client_location', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
        });
        const res = await resp.json();
        if(res.success) {
            Toast.fire(res.message, "success");
            bsModalAddLocation.hide();
            window.loadClientLocations(data.client_id);
            bsModalSelectLocation.show();
        } else { Toast.fire(res.message, "error"); }
    }

    /**
     * Buscador de Clientes AJAX
     */
    let searchTimeout;
    window.debounceSearchClient = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => searchClientListApi(), 350);
    }

    window.searchClientListApi = async function() {
        const term = document.getElementById('s-client-term').value.trim();
        const tbody = document.getElementById('s-client-results');

        // Si el término tiene un solo caracter, no buscamos (esperamos al segundo)
        // Si está vacío, traemos los primeros 10 por defecto
        if (term.length === 1) return;

        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Buscando...</td></tr>';

        try {
            const resp = await fetch(`?route=admin_clients_search&term=${encodeURIComponent(term)}&limit=10&order=name_asc`);
            const clients = await resp.json();

            // El "Cliente Ocasional" ahora es simplemente una opción rápida que no sobreescribe
            // la identidad si el ID 1 ya tiene un nombre real en la tabla.
            let html = '';

            if (clients.length === 0) {
                html += '<tr><td colspan="3" class="text-center py-4">Sin resultados</td></tr>';
            } else {
                clients.forEach(c => {
                    // Escapar nombres para evitar que comillas rompan el atributo HTML
                    const safeName = c.name.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    html += `
                        <tr class="selectable-client" tabindex="0" onclick="window.selectClientFromList(${c.id}, '${safeName}', '${c.phone || ''}')">
                            <td><strong>${c.name}</strong></td>
                            <td>${c.billing_ruc || '---'}</td>
                            <td>${c.phone || '---'}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = html;

            // Re-enfocar el modal tras actualizar la lista de resultados de búsqueda
            if (bsModalSearch) bsModalSearch.handleUpdate();
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error en búsqueda</td></tr>';
        }
    }

    window.selectClientFromList = function(id, name, phone) {
        const currentId = document.getElementById('f-client-id').value;

        // Si el cliente seleccionado es distinto al actual, reiniciamos los datos de logística
        // para evitar cargar un pedido con información (dirección, coordenadas, tarifas) del cliente anterior.
        if (currentId != id) {
            // Desenfocar elemento actual (ej: fila de la tabla) para evitar conflictos de aria-hidden
            if (document.activeElement instanceof HTMLElement) document.activeElement.blur();

            document.getElementById('f-location-id').value = '';
            document.getElementById('f-delivery-address').value = '';
            document.getElementById('f-location-url').value = '';
            document.getElementById('f-lat').value = '';
            document.getElementById('f-lng').value = '';
            document.getElementById('f-delivery-cost').value = 0;
            document.getElementById('f-observation').value = ''; // Limpiamos también observaciones por seguridad

            const rateSelect = document.getElementById('f-delivery-rate-id');
            if (rateSelect) {
                rateSelect.value = "";
                rateSelect.disabled = false;
            }

            const display = document.getElementById('f-location-display');
            if (display) display.innerText = 'Seleccionar dirección...';

            document.getElementById('btn-open-map-url').style.display = 'none';
            document.getElementById('f-delivery-rate-container').style.display = 'none';
            updateFinalizeTotal();
        }

        // Forzamos el nombre de sistema para el ID 1, para los demás usamos el formato estándar
        const displayName = (id == 1) ? 'Cliente Ocasional' : (phone ? `${name} (${phone})` : name);

        // Actualizar los campos del modal y el estado interno
        document.getElementById('f-client-id').value = id; // Actualiza el campo oculto
        document.getElementById('f-client-name').value = displayName; // Actualiza el campo visible
        // Sincronizar el estado interno para mantener consistencia si se cierra y abre el modal

        // Ocultar modales de búsqueda/creación
        bsModalSearch.hide();
        bsModalCreate.hide();

        setTimeout(() => {
            bsModalFinalize.show();
            document.getElementById('btn-open-map-url').style.display = 'none';
            if(document.getElementById('f-delivery-type').value === 'delivery') loadClientLocations(id);
        }, 150);
    }

    /**
     * Valida el formato del teléfono y verifica su existencia en la DB
     */
    window.checkPhoneExistence = async (phone) => {
        const feedback = document.getElementById('c-phone-feedback');
        if(!phone || phone.trim().length < 6) { feedback.style.display = 'none'; return; }

        // Normalizar: Solo números para validación
        const cleanPhone = phone.replace(/\D/g, '');

        /**
         * Validación para Paraguay (Mobile):
         * 09xx xxxxxx
         */
        const pyMobileRegex = /^(?:595)?(?:0)?(9[6-9][1-6])(\d{6})$/;
        const isValid = pyMobileRegex.test(cleanPhone);

        feedback.style.display = 'block';
        if (!isValid) {
            feedback.innerText = '❌ Formato inválido (ej: 0981 123456)';
            feedback.style.color = '#dc3545';
            return;
        }

        try {
            const resp = await fetch(`?route=admin_clients_check_phone&phone=${cleanPhone}`);
            const res = await resp.json();
            feedback.innerText = res.exists ? '⚠️ Teléfono ya registrado' : '✅ Formato válido y disponible';
            feedback.style.color = res.exists ? '#dc3545' : '#198754';
        } catch (e) {
            console.error("Error validando teléfono", e);
        }
    }

    /**
     * Envío final del pedido (POS)
     */
    async function submitPOS(data) {
        if(posCart.length === 0) return;
        
        // Bloquear botón para evitar doble envío
        const confirmBtn = document.querySelector('#modalFinalize .btn-success');
        if(confirmBtn) confirmBtn.disabled = true;

        bsModalFinalize.hide();
        toggleLoadingOverlay(true);

        try {
            const response = await fetch('?route=pos_store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    cart: posCart, 
                    client_id: data.clientId,
                    location_id: data.locationId,
                    delivery_address: data.deliveryAddress,
                    delivery_type: data.deliveryType,
                    payment_method: data.paymentMethod,
                    observation: data.observation,
                    location_url: document.getElementById('f-location-url').value || null,
                    lat: data.lat || null,
                    lng: data.lng || null,
                    delivery_cost: data.deliveryCost,
                    delivery_rate_id: data.deliveryRateId
                })
            });

            const res = await response.json();
            if(res.success) {
                Toast.fire(res.message, "success");
                
                // Impresión automática si la función existe (comanda)
                if (typeof printOrderDirectly === 'function') {
                    printOrderDirectly(res.order_id, '80mm');
                }

                const createdOrder = {
                    id: res.order_id,
                    total: res.total, 
                    payment_method: data.paymentMethod
                };

                // Limpiar y preparar la vista para la siguiente venta de forma centralizada
                resetPOS();

                // Preguntar por el cobro inmediato
                setTimeout(() => {
                    Swal.fire({
                        title: '¿Registrar pago?',
                        text: "Pedido guardado. ¿Desea proceder al cobro ahora?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#00b894',
                        confirmButtonText: 'Sí, cobrar ahora',
                        cancelButtonText: 'No, cerrar',
                        didOpen: () => { Swal.getConfirmButton().focus(); }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (!isCashOpen) {
                                Toast.fire("No puede cobrar sin caja abierta", "error");
                            } else {
                                openPaymentModal(createdOrder);
                            }
                        }
                    });
                }, 800);
                toggleLoadingOverlay(false);
            } else {
                Toast.fire(res.message, "error");
                if(confirmBtn) confirmBtn.disabled = false;
                toggleLoadingOverlay(false);
            }
        } catch (e) {
            Toast.fire("Error crítico al procesar venta", "error");
            if(confirmBtn) confirmBtn.disabled = false;
            toggleLoadingOverlay(false);
        }
    }

    /**
     * Atajos de teclado globales para el POS
     */
    document.addEventListener('keydown', function(e) {
        // F2: Finalizar Venta
        if (e.key === 'F2') {
            e.preventDefault();
            const finalizeBtn = document.getElementById('btnOpenFinalize');
            if (finalizeBtn && !finalizeBtn.disabled) finalizeBtn.click();
        }
        
        // F3: Buscar Cliente (si el modal de finalizar está abierto)
        if (e.key === 'F3') {
            e.preventDefault();
            if (document.getElementById('modalFinalize').classList.contains('show')) {
                openSearchClient();
            }
        }

        // F4: Crear Cliente
        if (e.key === 'F4') {
            e.preventDefault();
            if (document.getElementById('modalFinalize').classList.contains('show')) {
                openCreateClient();
            }
        }

        // Esc: Cerrar modales abiertos en orden inverso (Bootstrap lo maneja por defecto, 
        // pero aquí podemos añadir lógica personalizada si es necesario)
    });

    document.getElementById('posSearch').focus();

    // Sincronizar visualmente el carrito recuperado al cargar la página
    if (posCart.length > 0) renderTicket();

    // Exportar funciones necesarias al objeto window para los onclick del HTML
    window.addToTicket = addToTicket;
    window.removeFromTicket = removeFromTicket;
    window.confirmClearCart = confirmClearCart;
    window.filterPOS = filterPOS;
    window.clearPOS = clearPOS;
    window.filterByCat = filterByCat;
    window.showProductImg = showProductImg;
    window.resetPOS = resetPOS;
});