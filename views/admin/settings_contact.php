<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-phone-volume me-2 text-primary"></i> Canales de Contacto
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addContactRow()">
                        <i class="fas fa-plus me-1"></i> Agregar Número
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Configura los números de teléfono disponibles y qué tipo de comunicación permiten cada uno.</p>
                    
                    <form id="contactForm" action="?route=save_contact_settings" method="POST">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Etiqueta (Ej: Pedidos)</th>
                                        <th style="width: 25%;">Número de Teléfono</th>
                                        <th class="text-center">Llamadas</th>
                                        <th class="text-center">Mensajes (SMS)</th>
                                        <th class="text-center">WhatsApp</th>
                                        <th class="text-center">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="contact-list">
                                    <?php if (empty($contacts)): ?>
                                        <tr class="empty-row"><td colspan="6" class="text-center text-muted py-4">No hay contactos configurados. Haz clic en "Agregar Número".</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($contacts as $index => $c): ?>
                                            <tr class="contact-row">
                                                <td><input type="text" name="contacts[<?php echo $index; ?>][label]" class="form-control" value="<?php echo htmlspecialchars($c['label']); ?>" required></td>
                                                <td><input type="tel" name="contacts[<?php echo $index; ?>][phone]" class="form-control" value="<?php echo htmlspecialchars($c['phone']); ?>" required></td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="contacts[<?php echo $index; ?>][calls]" <?php echo $c['calls'] ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="contacts[<?php echo $index; ?>][sms]" <?php echo $c['sms'] ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" name="contacts[<?php echo $index; ?>][whatsapp]" <?php echo $c['whatsapp'] ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let contactIndex = <?php echo count($contacts); ?>;

function addContactRow() {
    const tbody = document.getElementById('contact-list');
    const emptyRow = tbody.querySelector('.empty-row');
    if (emptyRow) emptyRow.remove();

    const row = document.createElement('tr');
    row.className = 'contact-row';
    row.innerHTML = `
        <td><input type="text" name="contacts[${contactIndex}][label]" class="form-control" placeholder="Nombre del canal" required></td>
        <td><input type="tel" name="contacts[${contactIndex}][phone]" class="form-control" placeholder="Número" required></td>
        <td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input" type="checkbox" name="contacts[${contactIndex}][calls]" checked></div></td>
        <td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input" type="checkbox" name="contacts[${contactIndex}][sms]"></div></td>
        <td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input" type="checkbox" name="contacts[${contactIndex}][whatsapp]" checked></div></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>`;
    tbody.appendChild(row);
    contactIndex++;
}
</script>