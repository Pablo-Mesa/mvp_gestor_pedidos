-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-04-2026 a las 16:29:44
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comedor_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_closings`
--

DROP TABLE IF EXISTS `cash_closings`;
CREATE TABLE IF NOT EXISTS `cash_closings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `opening_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expected_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closing_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `observations` text COLLATE utf8mb4_unicode_ci,
  `opened_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_movements`
--

DROP TABLE IF EXISTS `cash_movements`;
CREATE TABLE IF NOT EXISTS `cash_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cash_register_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('ingress','egress') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `reference_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_register_id` (`cash_register_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_registers`
--

DROP TABLE IF EXISTS `cash_registers`;
CREATE TABLE IF NOT EXISTS `cash_registers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `cash_station` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Principal',
  `opening_amount` decimal(15,2) DEFAULT NULL,
  `closing_amount` decimal(15,2) DEFAULT '0.00',
  `expected_amount` decimal(15,2) DEFAULT '0.00',
  `physical_balance` decimal(15,2) DEFAULT '0.00',
  `closing_balance` decimal(15,2) DEFAULT '0.00',
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `has_whatsapp` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `billing_name` varchar(255) DEFAULT NULL,
  `billing_ruc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client_locations`
--

DROP TABLE IF EXISTS `client_locations`;
CREATE TABLE IF NOT EXISTS `client_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `address` text,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `daily_menus`
--

DROP TABLE IF EXISTS `daily_menus`;
CREATE TABLE IF NOT EXISTS `daily_menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `menu_date` date NOT NULL,
  `stock` int DEFAULT '100',
  `daily_stock` int DEFAULT NULL COMMENT 'Stock para este día, NULL = ilimitado',
  `is_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = Disponible, 0 = Agotado',
  `menu_type` varchar(20) DEFAULT 'primary',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delivery_checkins`
--

DROP TABLE IF EXISTS `delivery_checkins`;
CREATE TABLE IF NOT EXISTS `delivery_checkins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `checkin_time` datetime NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `distance_meters` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_checkin` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delivery_rates`
--

DROP TABLE IF EXISTS `delivery_rates`;
CREATE TABLE IF NOT EXISTS `delivery_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_delivery_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delivery_rate_details`
--

DROP TABLE IF EXISTS `delivery_rate_details`;
CREATE TABLE IF NOT EXISTS `delivery_rate_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `delivery_rate_id` int NOT NULL,
  `km_from` decimal(10,2) NOT NULL,
  `km_to` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rate_header` (`delivery_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) NOT NULL,
  `ruc` varchar(15) NOT NULL,
  `dv` char(1) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `timbrado_vigente` varchar(8) DEFAULT NULL,
  `fecha_desde_timbrado` date DEFAULT NULL,
  `fecha_hasta_timbrado` date DEFAULT NULL,
  `punto_emision` varchar(5) DEFAULT '001',
  `sucursal` varchar(5) DEFAULT '001',
  `actividad_economica` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `actualizado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hero_promos`
--

DROP TABLE IF EXISTS `hero_promos`;
CREATE TABLE IF NOT EXISTS `hero_promos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_class` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ambient',
  `order_priority` int DEFAULT '0',
  `is_active` tinyint DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `channel_id` int DEFAULT '1',
  `status` varchar(50) DEFAULT 'pending',
  `observation` text,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `delivery_type` varchar(50) DEFAULT 'pickup',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `billing_name` varchar(255) DEFAULT NULL,
  `billing_ruc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`),
  KEY `channel_id` (`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders_items`
--

DROP TABLE IF EXISTS `orders_items`;
CREATE TABLE IF NOT EXISTS `orders_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orders_items_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_channels`
--

DROP TABLE IF EXISTS `order_channels`;
CREATE TABLE IF NOT EXISTS `order_channels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_shipments`
--

DROP TABLE IF EXISTS `order_shipments`;
CREATE TABLE IF NOT EXISTS `order_shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `client_location_id` int DEFAULT NULL,
  `delivery_user_id` int DEFAULT NULL,
  `delivery_rate_id` int DEFAULT NULL,
  `address_snapshot` text,
  `lat_snapshot` varchar(50) DEFAULT NULL,
  `lng_snapshot` varchar(50) DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipment_order` (`order_id`),
  KEY `fk_shipment_delivery_user` (`delivery_user_id`),
  KEY `fk_shipment_location` (`client_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_compras_cabecera`
--

DROP TABLE IF EXISTS `pos_compras_cabecera`;
CREATE TABLE IF NOT EXISTS `pos_compras_cabecera` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int NOT NULL,
  `timbrado` varchar(8) NOT NULL,
  `nro_comprobante` varchar(15) NOT NULL,
  `fecha_emision` date NOT NULL,
  `tipo_comprobante` int DEFAULT '1',
  `condicion` int DEFAULT '0',
  `gravada_10` decimal(12,2) DEFAULT '0.00',
  `iva_10` decimal(12,2) DEFAULT '0.00',
  `gravada_5` decimal(12,2) DEFAULT '0.00',
  `iva_5` decimal(12,2) DEFAULT '0.00',
  `exenta` decimal(12,2) DEFAULT '0.00',
  `total_factura` decimal(12,2) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_compras_detalle`
--

DROP TABLE IF EXISTS `pos_compras_detalle`;
CREATE TABLE IF NOT EXISTS `pos_compras_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario_costo` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_id` (`compra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_proveedores`
--

DROP TABLE IF EXISTS `pos_proveedores`;
CREATE TABLE IF NOT EXISTS `pos_proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(15) NOT NULL,
  `dv` char(1) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipo_contribuyente` enum('FISICA','JURIDICA') DEFAULT 'FISICA',
  `estado` tinyint(1) DEFAULT '1',
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ruc` (`ruc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_ventas_cabecera`
--

DROP TABLE IF EXISTS `pos_ventas_cabecera`;
CREATE TABLE IF NOT EXISTS `pos_ventas_cabecera` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int DEFAULT NULL,
  `nro_factura` varchar(20) NOT NULL,
  `timbrado` varchar(8) DEFAULT NULL COMMENT 'Timbrado con el que se emitió la factura (SET)',
  `punto_emision` varchar(5) DEFAULT NULL COMMENT 'Ej: 001 en formato 001-001-0000001',
  `cdc` varchar(50) DEFAULT NULL COMMENT 'Código de Control SIFEN (documento electrónico)',
  `estado_sifen` tinyint(1) DEFAULT '0' COMMENT '0=sin enviar, 1=enviado, 2=aceptado, 3=rechazado',
  `kude_path` varchar(255) DEFAULT NULL COMMENT 'Ruta al archivo KuDE si se guarda en servidor',
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gravada_10` decimal(12,2) DEFAULT '0.00',
  `iva_10` decimal(12,2) DEFAULT '0.00',
  `gravada_5` decimal(12,2) DEFAULT '0.00',
  `iva_5` decimal(12,2) DEFAULT '0.00',
  `exenta` decimal(12,2) DEFAULT '0.00',
  `total_venta` decimal(12,2) NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_ventas_detalle`
--

DROP TABLE IF EXISTS `pos_ventas_detalle`;
CREATE TABLE IF NOT EXISTS `pos_ventas_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venta_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario_venta` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigobarra` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int NOT NULL,
  `es_vendible` tinyint(1) DEFAULT '1',
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `price_half` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_reactions`
--

DROP TABLE IF EXISTS `product_reactions`;
CREATE TABLE IF NOT EXISTS `product_reactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `client_id` int NOT NULL,
  `type` enum('fav','like','share') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_reaction` (`product_id`,`client_id`,`type`),
  KEY `fk_reaction_client` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `client_id` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_review_product` (`product_id`),
  KEY `fk_review_client` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(64) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  UNIQUE KEY `setting_key_2` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `role` enum('cliente','admin','delivery') DEFAULT 'cliente',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_reset_token` (`reset_token`(250))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cash_closings`
--
ALTER TABLE `cash_closings`
  ADD CONSTRAINT `cash_closings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `cash_movements`
--
ALTER TABLE `cash_movements`
  ADD CONSTRAINT `cash_movements_ibfk_1` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD CONSTRAINT `cash_registers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `client_locations`
--
ALTER TABLE `client_locations`
  ADD CONSTRAINT `fk_client_location` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `daily_menus`
--
ALTER TABLE `daily_menus`
  ADD CONSTRAINT `fk_daily_menu_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `delivery_checkins`
--
ALTER TABLE `delivery_checkins`
  ADD CONSTRAINT `fk_user_checkin` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `delivery_rates`
--
ALTER TABLE `delivery_rates`
  ADD CONSTRAINT `fk_delivery_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `delivery_rate_details`
--
ALTER TABLE `delivery_rate_details`
  ADD CONSTRAINT `fk_rate_header` FOREIGN KEY (`delivery_rate_id`) REFERENCES `delivery_rates` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_channel` FOREIGN KEY (`channel_id`) REFERENCES `order_channels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `fk_orders_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_shipments`
--
ALTER TABLE `order_shipments`
  ADD CONSTRAINT `fk_shipment_delivery_user` FOREIGN KEY (`delivery_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_shipment_location` FOREIGN KEY (`client_location_id`) REFERENCES `client_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_shipment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pos_compras_cabecera`
--
ALTER TABLE `pos_compras_cabecera`
  ADD CONSTRAINT `pos_compras_cabecera_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `pos_proveedores` (`id`);

--
-- Filtros para la tabla `pos_compras_detalle`
--
ALTER TABLE `pos_compras_detalle`
  ADD CONSTRAINT `pos_compras_detalle_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `pos_compras_cabecera` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `product_reactions`
--
ALTER TABLE `product_reactions`
  ADD CONSTRAINT `fk_reaction_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reaction_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_review_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
