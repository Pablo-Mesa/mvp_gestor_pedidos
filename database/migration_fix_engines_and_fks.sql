-- SCRIPT DE MIGRACIÓN: NORMALIZACIÓN A INNODB Y LLAVES FORÁNEAS
-- Objetivo: Corregir inconsistencias del schema_realworld.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. CONVERSIÓN DE MOTORES (MyISAM -> InnoDB)
-- Necesario para soportar Llaves Foráneas y Transacciones
ALTER TABLE `categories` ENGINE=InnoDB;
ALTER TABLE `daily_menus` ENGINE=InnoDB;
ALTER TABLE `hero_promos` ENGINE=InnoDB;
ALTER TABLE `order_channels` ENGINE=InnoDB;
ALTER TABLE `order_items` ENGINE=InnoDB;
ALTER TABLE `orders_items` ENGINE=InnoDB;

-- 2. CORRECCIÓN DE PRECISIÓN MONETARIA
-- Evita la pérdida de céntimos en arqueos y movimientos
ALTER TABLE `cash_movements` MODIFY `amount` DECIMAL(15,2) NOT NULL;
ALTER TABLE `cash_registers` MODIFY `physical_balance` DECIMAL(15,2) DEFAULT '0.00';
ALTER TABLE `cash_registers` MODIFY `closing_balance` DECIMAL(15,2) DEFAULT '0.00';

-- 3. ELIMINACIÓN DE REGISTROS HUÉRFANOS (Limpieza previa)
-- Esto asegura que las llaves foráneas se puedan crear sin errores de integridad
DELETE FROM `products` WHERE `category_id` NOT IN (SELECT `id` FROM `categories`);
DELETE FROM `order_items` WHERE `order_id` NOT IN (SELECT `id` FROM `orders`);
DELETE FROM `order_items` WHERE `product_id` NOT IN (SELECT `id` FROM `products`);

-- 4. ESTABLECIMIENTO DE LLAVES FORÁNEAS (CONSTRAINTS)

-- Relación Productos -> Categorías
ALTER TABLE `products` 
ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT;

-- Relación Menú Diario -> Productos
ALTER TABLE `daily_menus` 
ADD CONSTRAINT `fk_daily_menu_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;

-- Relaciones en la tabla Orders
ALTER TABLE `orders` 
ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_order_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_order_channel` FOREIGN KEY (`channel_id`) REFERENCES `order_channels`(`id`) ON DELETE SET NULL;

-- Relaciones en Detalles de Pedidos (order_items)
ALTER TABLE `order_items` 
ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;

-- Relaciones en Logística (order_shipments)
-- Nota: El campo delivery_user_id debe referenciar a users(id)
ALTER TABLE `order_shipments`
ADD CONSTRAINT `fk_shipment_delivery_user` FOREIGN KEY (`delivery_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_shipment_location` FOREIGN KEY (`client_location_id`) REFERENCES `client_locations`(`id`) ON DELETE SET NULL;

-- 5. UNIFICACIÓN OPCIONAL (Recomendado: Usar solo order_items)
-- Si 'orders_items' es redundante, podrías eliminarla después de verificar que no se usa en el código.
-- Por ahora la convertimos a InnoDB por seguridad:
ALTER TABLE `orders_items` 
ADD CONSTRAINT `fk_orders_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- Mensaje de verificación
SELECT 'Migración completada con éxito. Motores InnoDB activos y llaves foráneas vinculadas.' AS Result;
COMMIT;