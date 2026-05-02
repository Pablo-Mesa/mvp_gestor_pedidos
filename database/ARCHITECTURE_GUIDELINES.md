# 🏗️ Lineamientos de Arquitectura y Diseño - Proyecto Solver

Este documento define los estándares técnicos para garantizar que el software sea lógico, funcional y mantenible.

## 1. Persistencia y Datos
*   **Normalización:** Evitar el uso de `ENUM` para entidades que representan lógica de negocio (Roles, Categorías, Estados complejos). Usar tablas maestras con Llaves Foráneas (FK).
*   **Integridad:** Toda relación entre tablas debe estar respaldada por un `CONSTRAINT` en la base de datos, no solo en el código PHP.
*   **Precisión:** Los valores monetarios deben usar siempre `DECIMAL(15,2)`, nunca `FLOAT` o `DOUBLE`.

## 2. Lógica de Negocio (MVC)
*   **Modelos "Gordos", Controladores "Delgados":** El Controller solo orquesta. La validación y el cálculo deben vivir en el Modelo.
*   **Defensa en Profundidad:** El código debe ser "desconfiado". Normalizar siempre los datos de entrada (`trim`, `strtolower`, `array_change_key_case`) antes de procesarlos.

## 3. Interfaz y Experiencia (UI/UX)
*   **Dinamismo:** Ningún componente de selección (`<select>`) debe tener opciones hardcodeadas en el HTML si esas opciones existen en la base de datos.
*   **Feedback Inmediato:** Las acciones críticas (Apertura de caja, Borrado) deben requerir confirmación visual (SweetAlert2).

## 4. Seguridad
*   **Sanitización:** Uso obligatorio de `bindValue` en PDO para prevenir Inyección SQL.
*   **RBAC (Role-Based Access Control):** El acceso a rutas debe validarse en el constructor de los controladores basándose en el ID del rol, no solo en un string.

---
*Firmado: Arquitectura de Software Solver*