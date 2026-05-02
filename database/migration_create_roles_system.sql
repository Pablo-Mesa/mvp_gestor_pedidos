-- MIGRACIÓN: SISTEMA DE ROLES DINÁMICOS
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Crear tabla de roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insertar roles base (Incluyendo el Cajero que faltaba)
INSERT INTO `roles` (`name`, `slug`, `description`) VALUES 
('Administrador', 'admin', 'Acceso total al sistema'),
('Cajero/a', 'cajero', 'Gestión de ventas y tesorería'),
('Repartidor', 'delivery', 'Gestión de logística y entregas'),
('Cliente', 'cliente', 'Usuario final del sistema');

-- 3. Transformar columna role en users a role_id
-- Primero agregamos la columna
ALTER TABLE `users` ADD COLUMN `role_id` INT NULL AFTER `role`;

-- Vinculamos los IDs basados en los strings actuales
UPDATE `users` u JOIN `roles` r ON u.role = r.slug SET u.role_id = r.id;

SET FOREIGN_KEY_CHECKS = 1;
SELECT 'Migración de roles completada. Ahora puedes eliminar la columna role antigua.' as Result;