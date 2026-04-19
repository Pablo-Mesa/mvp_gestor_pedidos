# 🍽️ Comedor App - Gestor de Pedidos (MVP)

Sistema web de gestión de pedidos para comedores y servicios de viandas. Desarrollado en **PHP nativo** implementando una arquitectura **MVC (Modelo-Vista-Controlador)** para mantener el código organizado y escalable.

Este proyecto permite a los administradores planificar el menú diario y gestionar el stock, mientras que los clientes pueden realizar pedidos para retiro o delivery.

## 🚀 Características

### 👤 Panel de Cliente
*   **Visualización del Menú:** Catálogo de platos disponibles para el día actual ("Menú del día").
*   **Filtrado:** Filtros dinámicos por categorías de comida.
*   **Carrito de Compras:** Selección de múltiples platos y cantidades.
*   **Checkout:** Opción de elegir método de entrega (**Pickup** o **Delivery** con ubicación).
*   **Historial:** Visualización de pedidos realizados.

### 🛡️ Panel de Administración
*   **Dashboard:** Resumen de pedidos pendientes, ingresos del día y platos vendidos.
*   **Gestión de Productos:** CRUD completo (Crear, Leer, Actualizar, Borrar) de platos con carga de imágenes.
*   **Planificación del Menú:** Asignación de productos a fechas específicas.
*   **Control de Stock Diario:** Definición de límites de stock por plato para el día.
*   **Gestión de Disponibilidad:** Opción rápida para marcar platos como "Agotados" o "Disponibles" en tiempo real.
*   **Gestión de Pedidos:** Visualización de detalles y actualización de estados de los pedidos.

## 🛠️ Tecnologías Utilizadas

*   **Backend:** PHP (Sin frameworks, arquitectura MVC personalizada).
*   **Base de Datos:** MySQL (Uso de PDO para seguridad y consultas preparadas).
*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla).
*   **Servidor Web:** Apache (Compatible con WAMP/XAMPP/LAMP).

## 📂 Estructura del Proyecto

El proyecto sigue una estructura MVC estricta:

```text
/comedor-app
├── config/          # Configuración de base de datos (db.php)
├── controllers/     # Lógica de negocio (MenuController, OrderController, etc.)
├── models/          # Acceso a datos y lógica de DB (Product, Order, DailyMenu)
├── public/          # Assets públicos (imágenes subidas, CSS, JS)
├── views/           # Plantillas HTML
│   ├── admin/       # Vistas protegidas del administrador
│   ├── home/        # Vistas públicas del cliente
│   └── layouts/     # Plantillas base (header/footer)
└── index.php        # Punto de entrada (Router)
```

## 📦 Instalación y Configuración

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/tu-usuario/comedor-app-mvp.git
    ```

2.  **Base de Datos:**
    *   Crea una base de datos en MySQL (ej. `comedor_db`).
    *   Importa el archivo SQL de estructura (tablas: `users`, `products`, `categories`, `daily_menus`, `orders`, `order_details`).

3.  **Configuración:**
    *   Edita el archivo `config/db.php` con tus credenciales:
    ```php
    private $host = 'localhost';
    private $db_name = 'comedor_db';
    private $username = 'root';
    private $password = '';
    ```

4.  **Ejecución:**
    *   Si usas **WAMP/XAMPP**, coloca la carpeta en `www` o `htdocs`.
    *   Accede desde el navegador: `http://localhost/mvp_gestor_pedidos/comedor-app/`.

## 📝 Notas de Desarrollo

*   **Seguridad:** El sistema implementa validación de sesiones (`$_SESSION['user_role']`) para proteger las rutas de administración.
*   **Imágenes:** Las imágenes de los productos se almacenan en `public/uploads/`.
*   **Rutas:** Se utiliza un sistema de enrutamiento simple mediante parámetros GET (ej. `?route=products`).

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo LICENSE.md para más detalles.

---
Desarrollado con ❤️ para agilizar la gestión de comedores.