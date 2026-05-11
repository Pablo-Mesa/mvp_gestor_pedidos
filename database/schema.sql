-- Estructura de Base de Datos para Sistema Solver
-- Estructura Consolidada para Sistema Solver
-- Versión: 2026.05.07 - Reflejando estructura real POS/Caja

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- 1. Seguridad y Roles
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('cliente', 'admin', 'delivery') DEFAULT 'cliente',
  `role_id` INT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reset_token` (`reset_token`(250))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Clientes (Comensales)
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `has_whatsapp` TINYINT(1) DEFAULT 0,
  `billing_name` VARCHAR(150) DEFAULT NULL,
  `billing_ruc` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Ubicaciones de Clientes (Historial y Direcciones Guardadas)
CREATE TABLE IF NOT EXISTS `client_locations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `client_id` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `lat` DECIMAL(10,8) DEFAULT NULL,
  `lng` DECIMAL(11,8) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Categorías y Productos
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `codigobarra` VARCHAR(20) DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `price_half` DECIMAL(15,2) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `es_vendible` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Planificación de Menú Diario
CREATE TABLE IF NOT EXISTS `daily_menus` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `menu_date` DATE NOT NULL,
  `daily_stock` INT DEFAULT NULL,
  `menu_type` VARCHAR(20) DEFAULT 'primary',
  `is_available` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tarifas de Delivery por Distancia
CREATE TABLE IF NOT EXISTS `delivery_rates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `is_active` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_rate_details` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `delivery_rate_id` INT NOT NULL,
  `km_from` DECIMAL(10,2) NOT NULL,
  `km_to` DECIMAL(10,2) NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`delivery_rate_id`) REFERENCES `delivery_rates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Pedidos y Detalles
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL,
  `client_id` INT DEFAULT NULL,
  `channel_id` INT DEFAULT 1,
  `status` VARCHAR(50) DEFAULT 'pending',
  `observation` TEXT DEFAULT NULL,
  `total` DECIMAL(15,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `delivery_type` VARCHAR(50) DEFAULT 'pickup',
  `billing_name` VARCHAR(150) DEFAULT NULL,
  `billing_ruc` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_shipments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `client_location_id` INT DEFAULT NULL,
  `delivery_user_id` INT DEFAULT NULL,
  `delivery_rate_id` INT DEFAULT NULL,
  `address_snapshot` TEXT,
  `lat_snapshot` VARCHAR(50) DEFAULT NULL,
  `lng_snapshot` VARCHAR(50) DEFAULT NULL,
  `shipped_at` TIMESTAMP NULL DEFAULT NULL,
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. POS y Ventas
CREATE TABLE IF NOT EXISTS `pos_ventas_cabecera` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT DEFAULT NULL,
  `cliente_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `nro_factura` VARCHAR(20) NOT NULL,
  `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total_venta` DECIMAL(15,2) NOT NULL,
  `estado` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pagos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venta_id` INT NOT NULL,
  `monto_total` DECIMAL(15,2) NOT NULL,
  `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`venta_id`) REFERENCES `pos_ventas_cabecera`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Control de Caja
CREATE TABLE IF NOT EXISTS `cash_registers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `cash_station` VARCHAR(50) DEFAULT 'Principal',
  `opening_amount` DECIMAL(15,2) DEFAULT 0,
  `closing_amount` DECIMAL(15,2) DEFAULT 0,
  `status` ENUM('open', 'closed') DEFAULT 'open',
  `opened_at` DATETIME NOT NULL,
  `closed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Configuraciones
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `razon_social` VARCHAR(255) NOT NULL,
  `ruc` VARCHAR(15) NOT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;