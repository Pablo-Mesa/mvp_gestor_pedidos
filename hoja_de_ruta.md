# 🗺️ Hoja de Ruta - Proyecto Solver

Este documento establece el norte estratégico del ecosistema **Solver**, dividiendo el desarrollo en tres ámbitos principales y fases de ejecución orientadas a resolver problemas reales de gestión gastronómica.

---

## 🎯 Ámbitos del Proyecto

### 1. Ámbito: Admin (Gestión Operativa y Caja)
*   **Módulo POS (Punto de Venta):** Formulario rápido para toma de pedidos presenciales (cara a cara).
*   **Módulo de Caja:** 
    *   Registro de ingresos y egresos.
    *   Apertura y cierre de caja (Arqueo diario).
    *   Diferenciación de métodos de pago (Efectivo, Transferencia, Tarjeta).
*   **Gestión de Inventario:** Alertas de stock bajo y reportes de insumos.

### 2. Ámbito: Home (Experiencia del Cliente)
*   **Refinamiento Estético:** Mejorar transiciones, estados de carga (skeletons) y pulido de UI móvil.
*   **Seguimiento en Tiempo Real:** Interfaz para que el cliente vea el progreso de su pedido (Recibido -> En Cocina -> En Camino).
*   **Fidelización:** Motor de recomendaciones basado en los "Likes" y reseñas que ya funcionan.

### 3. Ámbito: Logística (App de Delivery)
*   **Panel de Repartidor:** Interfaz optimizada para móviles para usuarios con rol `delivery`.
*   **Gestión de Entregas:** 
    *   Mapa de ruta basado en coordenadas guardadas.
    *   Estados logísticos: `Listo para Despacho`, `En camino`, `Entregado`.
*   **Liquidación de Delivery:** Control de cobros realizados por el repartidor en efectivo.

---

## 🚀 Fases de Implementación (Objetivos)

### Fase 1: Consolidación Administrativa (Prioridad Alta)
*   [x] Gestión Dinámica de Hero Promo (Banner de Cliente).
*   [ ] Implementar el formulario de pedido local en el panel Admin.
*   [ ] Crear tablas y lógica para el control de caja y arqueo.
*   [ ] Vincular pedidos online y locales en un solo flujo de caja.

### Fase 2: Módulo de Logística y Delivery
*   [ ] Crear acceso y vistas para el rol de Repartidor.
*   [ ] Implementar el cambio de estados logísticos.
*   [ ] Integrar botón de "Abrir en Google Maps" usando `delivery_lat` y `delivery_lng`.

### Fase 3: UX y Optimización del Home
*   [ ] Refactorización estética profunda del frontend cliente.
*   [ ] Implementar WebSockets o Polling optimizado para el estatus del pedido del cliente.
*   [ ] Optimización de rendimiento y SEO básico.

---

## 🛠️ Notas Técnicas
*   Mantener la arquitectura **MVC Nativa**.
*   Asegurar que cada cambio en un ámbito no afecte la integridad de los otros (Principio de Responsabilidad Única).
*   Toda nueva funcionalidad debe ser probada tanto en escritorio como en dispositivos móviles.

*Última actualización: 31/03/2026*