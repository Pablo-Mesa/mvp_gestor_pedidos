# 🍽️ Comedor App - Gestor de Pedidos (MVP)

Comedor App es una aplicación web desarrollada en **PHP nativo** bajo una arquitectura **MVC (Modelo-Vista-Controlador)**. Está diseñada como un MVP funcional para centralizar y automatizar la operación diaria de comedores, servicios de viandas o restaurantes con modalidades de venta presencial (POS), retiro (Pickup) y Delivery.

El sistema prioriza la seguridad, la usabilidad operativa y el control administrativo del negocio en tiempo real.

## 🎯 Objetivo del Sistema

Centralizar la operación integral del negocio en una sola plataforma, permitiendo al personal administrativo y de servicio gestionar de manera eficiente el flujo completo desde la materia prima hasta la entrega al cliente y el cuadre de caja.

## ✨ Funcionalidades Principales

### 👤 Panel de Clientes (Web Pública)
*   **Menú del Día:** Visualización dinámica del catálogo de platos asignados a la fecha actual.
*   **Filtrado Inteligente:** Buscador y filtros rápidos por categorías de comida (ej: Minutas, Bebidas, Postres).
*   **Carrito de Compras:** Selección fluida de múltiples platos, gestión de cantidades y cálculo de subtotales.
*   **Checkout flexible:** Selección de método de entrega (**Pickup** o **Delivery** con registro de ubicación).
*   **Historial:** Seguimiento y consulta de pedidos realizados por el usuario.

### 🛡️ Panel Administrativo & Operativo
*   **Dashboard de Control:** Métricas clave en tiempo real (pedidos pendientes, ingresos del día y platos más vendidos).
*   **Gestión de Productos (CRUD):** Control total de platos, precios, categorías y carga automatizada de imágenes.
*   **Planificación del Menú:** Herramienta para asignar qué productos del catálogo global estarán disponibles cada día.
*   **Control de Stock y Disponibilidad:** Definición de límites diarios por plato y conmutador rápido para marcar productos como "Agotados" o "Disponibles".
*   **Gestión de Pedidos:** Monitor de órdenes entrantes con actualización de estados en tiempo real (Pendiente, Preparando, Enviado, Completado).
*   **Módulo POS (Punto de Venta):** Interfaz optimizada para registrar ventas presenciales directamente en el mostrador.
*   **Gestión de Caja y Movimientos:** Control estricto de apertura, ingresos, egresos, fondo de sencillo y cierre de caja.
*   **Logística de Delivery:** Asignación de repartidores, control de asistencias y cálculo de costos de entrega por distancia/mapa.
*   **Módulo de Reportes:** Auditoría de costos, reportes de ingresos, métodos de pago y analítica operativa.

## 🛠️ Tecnologías Utilizadas

*   **Backend:** PHP Nativo (Sin frameworks externos, arquitectura limpia).
*   **Base de Datos:** MySQL (Conexión mediante **PDO** con consultas preparadas para mitigar SQL Injection).
*   **Frontend:** HTML5, CSS3 y JavaScript (Vanilla / Vanilla JS).
*   **Servidor Web:** Apache (Totalmente compatible con entornos WAMP, XAMPP o LAMP).

## 🧱 Arquitectura del Proyecto

El código está estrictamente desacoplado en capas para facilitar el mantenimiento y la escalabilidad:

```text
/comedor-app
├── config/         # Archivos de configuración global y conexión (db.php)
├── controllers/    # Lógica de control y enrutamiento (MenuController, OrderController, etc.)
├── database/       # Scripts de estructura, migraciones y respaldos SQL
├── docs/           # Documentación técnica, diagramas y arquitectura
├── models/         # Acceso a datos, consultas SQL y reglas de negocio
├── public/         # Directorio público (Assets: CSS, JS, imágenes e interfaz de uploads)
├── services/       # Clases de servicio para integraciones, pagos y lógica compleja
└── views/          # Plantillas HTML/PHP (vistas de administración, cliente y layouts compartidos)
```

## ⚙️ Instalación y Configuración

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/tu-usuario/comedor-app-mvp.git
    cd comedor-app-mvp
    ```

2.  **Preparar la Base de Datos:**
    *   Crea una base de datos en tu servidor MySQL (ej: `comedor_db`).
    *   Importa en orden los scripts ubicados en la carpeta `/database/`:
        1. `schema.sql` (Estructura base del sistema).
        2. `tablas_dnit_pos_04062026.sql` (Módulos extendidos de facturación y POS).

3.  **Configurar Credenciales:**
    *   Ve a la carpeta `/config/`.
    *   Renombra el archivo `db.php.example` a `db.php`.
    *   Edita `config/db.php` con los datos de tu servidor local:
    ```php
    private \$host = 'localhost';
    private \$db_name = 'comedor_db';
    private \$username = 'root';
    private \$password = '';
    ```

4.  **Despliegue Local:**
    *   Mueve la carpeta del proyecto a la ruta raíz de tu servidor local (`www` en WAMP o `htdocs` en XAMPP).
    *   Accede desde tu navegador preferido: `http://localhost/mvp_gestor_pedidos/comedor-app/`

## 🏁 Guía de Primer Uso (Onboarding)

Para inicializar el sistema operativamente por primera vez, el Administrador debe seguir este flujo:

1.  **Configuración del Negocio:** Ingresar al módulo de configuraciones, definir los datos del local y geolocalizar el negocio en el mapa (esencial para el cálculo automático de delivery).
2.  **Estructura del Catálogo:** Crear las categorías principales e importar/crear los productos con sus precios y fotos correspondientes.
3.  **Planificación Operativa:** Configurar el "Menú del Día" seleccionando los platos del catálogo que se venderán en la fecha.
4.  **Apertura de Caja:** Registrar el monto inicial en el módulo de caja. *Nota: El sistema bloquea las funciones del POS y del Checkout si la caja del día no ha sido abierta.*
5.  **Flujo Comercial:** El sistema queda listo para recibir pedidos web o procesar ventas físicas.

## 🔐 Notas de Desarrollo y Seguridad

*   **Seguridad de Acceso:** Control estricto de rutas protegidas mediante validación de roles en variables de sesión (`$_SESSION['user_role']`).
*   **Persistencia:** Las imágenes de los platos cargadas desde el panel de administración se almacenan físicamente en el directorio `public/uploads/`.
*   **Enrutamiento:** Sistema de enrutamiento limpio gestionado por el controlador frontal mediante parámetros GET (ej: `?route=products`).

---
Desarrollado con ❤️ para agilizar la gestión de comedores y optimizar servicios de viandas.
