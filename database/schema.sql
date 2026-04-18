-- Estructura de Base de Datos para Sistema Solver
-- Generado para arquitectura MVC Nativa PHP

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Usuarios del Staff (Admin, Repartidores)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'delivery', 'staff') DEFAULT 'staff',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Clientes (Comensales)
CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `has_whatsapp` TINYINT(1) DEFAULT 0,
  `billing_name` VARCHAR(150) DEFAULT NULL,
  `billing_ruc` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ubicaciones de Clientes (Historial y Direcciones Guardadas)
CREATE TABLE IF NOT EXISTS `client_locations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `client_id` INT(11) NOT NULL,
  `title` VARCHAR(50) DEFAULT 'Mi Casa',
  `address` TEXT NOT NULL,
  `lat` DECIMAL(10,8) DEFAULT NULL,
  `lng` DECIMAL(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Categorías y Productos
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigobarra` VARCHAR(50) DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `price_half` DECIMAL(12,2) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `es_vendible` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Planificación de Menú Diario
CREATE TABLE IF NOT EXISTS `daily_menus` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `menu_date` DATE NOT NULL,
  `daily_stock` INT(11) DEFAULT NULL,
  `menu_type` ENUM('primary', 'secondary') DEFAULT 'primary',
  `is_available` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tarifas de Delivery por Distancia
CREATE TABLE IF NOT EXISTS `delivery_rates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 0,
  `version` INT(11) DEFAULT 1,
  `created_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `delivery_rate_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `delivery_rate_id` INT(11) NOT NULL,
  `km_from` DECIMAL(5,2) NOT NULL,
  `km_to` DECIMAL(5,2) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`delivery_rate_id`) REFERENCES `delivery_rates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Pedidos y Detalles
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL, -- Staff que tomó el pedido (POS)
  `client_id` INT(11) NOT NULL DEFAULT 1, -- 1 es Cliente Ocasional
  `channel_id` INT(11) DEFAULT 1, -- 1: Web, 2: Mostrador
  `total` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'preparing', 'ready', 'shipped', 'completed', 'rejected', 'cancelled') DEFAULT 'pending',
  `payment_method` ENUM('efectivo', 'pos', 'transferencia') DEFAULT 'efectivo',
  `delivery_type` ENUM('local', 'pickup', 'delivery') DEFAULT 'local',
  `delivery_cost` DECIMAL(12,2) DEFAULT 0,
  `delivery_address` TEXT DEFAULT NULL,
  `delivery_lat` DECIMAL(10,8) DEFAULT NULL,
  `delivery_lng` DECIMAL(11,8) DEFAULT NULL,
  `client_location_id` INT(11) DEFAULT NULL,
  `delivery_user_id` INT(11) DEFAULT NULL, -- Repartidor asignado
  `observation` TEXT DEFAULT NULL,
  `billing_name` VARCHAR(150) DEFAULT NULL,
  `billing_ruc` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`),
  FOREIGN KEY (`delivery_user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Control de Caja y Arqueos
CREATE TABLE IF NOT EXISTS `cash_sessions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `opening_amount` DECIMAL(12,2) NOT NULL,
  `closing_amount` DECIMAL(12,2) DEFAULT NULL,
  `physical_balance` DECIMAL(12,2) DEFAULT NULL,
  `expected_balance` DECIMAL(12,2) DEFAULT NULL,
  `status` ENUM('open', 'closed') DEFAULT 'open',
  `station` VARCHAR(50) DEFAULT 'Principal',
  `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cash_movements` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `session_id` INT(11) NOT NULL,
  `order_id` INT(11) DEFAULT NULL,
  `user_id` INT(11) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `type` ENUM('ingress', 'egress') NOT NULL,
  `description` TEXT DEFAULT NULL,
  `method` ENUM('efectivo', 'manual') DEFAULT 'efectivo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`session_id`) REFERENCES `cash_sessions`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Configuraciones y Otros
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `empresas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `razon_social` VARCHAR(255) NOT NULL,
  `ruc` VARCHAR(15) DEFAULT NULL,
  `dv` CHAR(1) DEFAULT NULL,
  `direccion` TEXT DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;