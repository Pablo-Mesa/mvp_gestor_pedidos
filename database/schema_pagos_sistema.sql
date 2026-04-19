-- MODULO DE PAGOS Y FINANZAS
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Asegurar que la cabecera de venta tenga el vínculo con el pedido original
-- NOTA: Si recibes error #1060 es porque ya existe. Puedes comentar estas líneas:
-- ALTER TABLE `pos_ventas_cabecera` ADD COLUMN `order_id` INT(11) NULL AFTER `id`;

-- ALTER TABLE `pos_ventas_cabecera`
-- ADD CONSTRAINT `fk_venta_order_rel` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL;

-- 2. Tabla de Pagos (La transacción financiera global de una venta)
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `venta_id` INT(11) NOT NULL, -- Vínculo con la factura/venta
  `monto_total` DECIMAL(15,2) NOT NULL,
  `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pago_venta_rel` FOREIGN KEY (`venta_id`) REFERENCES `pos_ventas_cabecera`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Detalle de Pagos (Permite pagos mixtos: ej: Gs. 50.000 Efectivo + Gs. 20.000 QR)
CREATE TABLE IF NOT EXISTS `pagos_detalles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pago_id` INT(11) NOT NULL,
  `metodo_pago` ENUM('efectivo', 'pos', 'transferencia', 'qr') NOT NULL,
  `monto` DECIMAL(15,2) NOT NULL,
  `referencia` VARCHAR(100) DEFAULT NULL, -- Nro de comprobante, nro de tarjeta o transaccion
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pdet_pago` FOREIGN KEY (`pago_id`) REFERENCES `pagos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Normalizar precisión en caja para que coincida con los pagos
ALTER TABLE `cash_movements` MODIFY `amount` DECIMAL(15,2) NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Módulo de pagos creado correctamente.' as Result;