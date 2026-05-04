# 🏗️ Lineamientos de Arquitectura y Diseño - Proyecto Solver

Este documento define los estándares técnicos para garantizar que el software sea lógico, funcional y mantenible.

## 1. Persistencia y Datos
*   **Normalización:** Evitar el uso de `ENUM` para entidades que representan lógica de negocio (Roles, Categorías, Estados complejos). Usar tablas maestras con Llaves Foráneas (FK).
*   **Integridad:** Toda relación entre tablas debe estar respaldada por un `CONSTRAINT` en la base de datos, no solo en el código PHP.
*   **Precisión:** Los valores monetarios deben usar siempre `DECIMAL(15,2)`, nunca `FLOAT` o `DOUBLE`.
*   **Auditoría Implícita:** Toda tabla transaccional debe incluir `created_at`, `updated_at` y `user_id` de auditoría.

## 2. Lógica de Negocio (MVC)
*   **Modelos "Gordos", Controladores "Delgados":** El Controller solo orquesta. La validación y el cálculo deben vivir en el Modelo.
*   **Defensa en Profundidad:** El código debe ser "desconfiado". Normalizar siempre los datos de entrada (`trim`, `strtolower`, `array_change_key_case`) antes de procesarlos.

## 3. Interfaz y Experiencia (UI/UX)
*   **Dinamismo:** Ningún componente de selección (`<select>`) debe tener opciones hardcodeadas en el HTML si esas opciones existen en la base de datos.
*   **Feedback Inmediato:** Las acciones críticas (Apertura de caja, Borrado) deben requerir confirmación visual (SweetAlert2).

## 4. Seguridad
*   **Sanitización:** Uso obligatorio de `bindValue` en PDO para prevenir Inyección SQL.
*   **RBAC (Role-Based Access Control):** El acceso a rutas debe validarse en el constructor de los controladores basándose en el ID del rol, no solo en un string.
*   **Manejo de Errores:** Nunca mostrar errores crudos de SQL (PDOException) al usuario final en producción. Usar try-catch y loguear el error internamente.

## 5. Escalabilidad (SaaS Ready)
*   **Tenant Isolation:** Diseñar las tablas pensando en una futura columna `empresa_id` para facilitar la migración a multi-tenancy sin refactorizar la lógica central.
*   **Configuración:** Separar credenciales sensibles del código fuente mediante variables de entorno o archivos de configuración no versionados.

---
*Firmado: Arquitectura de Software Solver*