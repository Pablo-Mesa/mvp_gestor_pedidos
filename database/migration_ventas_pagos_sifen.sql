-- AJUSTE DE ESTRUCTURA PARA COMPATIBILIDAD DNIT + PAGOS MIXTOS
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Vincular la Cabecera de Ventas con Pedidos y Staff
ALTER TABLE `pos_ventas_cabecera` 
ADD COLUMN `order_id` INT(11) NULL AFTER `id`,
ADD COLUMN `user_id` INT(11) NULL AFTER `cliente_id`,
ADD CONSTRAINT `fk_venta_order_rel` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_venta_staff_rel` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- 2. Asegurar que el detalle de venta tenga integridad con productos
ALTER TABLE `pos_ventas_detalle`
ADD CONSTRAINT `fk_vdet_prod` FOREIGN KEY (`producto_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT;

-- 3. Tabla de Pagos (La transacción financiera)
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `venta_id` INT(11) NOT NULL,
  `monto_total` DECIMAL(15,2) NOT NULL,
  `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pago_venta_rel` FOREIGN KEY (`venta_id`) REFERENCES `pos_ventas_cabecera`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Detalle de Pagos (Soporte para Efectivo + Tarjeta, etc)
CREATE TABLE IF NOT EXISTS `pagos_detalles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pago_id` INT(11) NOT NULL,
  `metodo_pago` ENUM('efectivo', 'pos', 'transferencia', 'qr') NOT NULL,
  `monto` DECIMAL(15,2) NOT NULL,
  `referencia` VARCHAR(100) DEFAULT NULL, -- Nro de boleta o transaccion
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pdet_pago` FOREIGN KEY (`pago_id`) REFERENCES `pagos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Ajustar precisión en movimientos de caja para coincidir con ventas
ALTER TABLE `cash_movements` MODIFY `amount` DECIMAL(15,2) NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;