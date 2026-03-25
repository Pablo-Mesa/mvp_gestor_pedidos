const Toast = {
    container: null,

    init() {
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
        }
    },

    /**
     * Muestra una notificación Toast
     * @param {string} message Mensaje a mostrar
     * @param {string} type Tipo: 'success', 'error', 'warning', 'info'
     * @param {number} duration Duración en ms (default 3000)
     */
    fire(message, type = 'info', duration = 3000) {
        this.init();

        // Crear elemento
        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        
        // Iconos usando FontAwesome (que ya tienes en el proyecto)
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-exclamation-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };

        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <span class="toast-text">${message}</span>
                <button class="toast-close">&times;</button>
            </div>
            <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
        `;

        this.container.appendChild(toast);

        // Lógica de auto-cierre
        const removeTimeout = setTimeout(() => {
            this.dismiss(toast);
        }, duration);

        // Botón de cerrar manual
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(removeTimeout);
            this.dismiss(toast);
        });
    },

    dismiss(toast) {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        toast.addEventListener('animationend', () => {
            if (toast.parentElement) {
                toast.remove();
            }
        });
    }
};