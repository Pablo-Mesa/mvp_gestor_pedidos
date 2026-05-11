-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 07-05-2026 a las 19:30:52
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Almuerzos', '2026-03-18 11:46:41'),
(2, 'Minutas', '2026-03-18 11:46:55'),
(3, 'Desayunos', '2026-03-18 11:47:07'),
(4, 'Bebidas', '2026-03-18 11:47:17'),
(6, 'Postres en Gral.', '2026-03-19 11:27:28');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password`, `phone`, `has_whatsapp`, `created_at`, `billing_name`, `billing_ruc`) VALUES
(1, 'Juan Perez', 'juanperez@gmail.com', '$2y$10$YoUHOSfG3AzNVm5BUd4r6eL4zcHPccYIHpD8bsBVbbFEomeK9IEjq', '0987123456', 1, '2026-03-24 20:09:52', 'Empresa JP', '800012345-9'),
(2, 'Jorge Alfonso', 'jorgealfonso@gmail.com', '$2y$10$x17AKyc09F2JgAhW3ySmb.3Jd7xz/Ueqj0sl/.sk3Xc5wQ0AZeAaq', '0987456789', 1, '2026-03-25 01:59:52', NULL, NULL),
(3, 'Gustavo Gimenez', 'gustavogimenez@gmail.com', '$2y$10$WvqqYm3xrr1vawhlp7O05utun6pCnznZimIvYZgl9RJe1KXLwGMDK', '0987987654', 1, '2026-03-25 12:31:45', NULL, NULL),
(4, 'Alberto Coglio', 'albertocoglio@comedor.com', '$2y$10$Am0HHLMtfF3Qvtn06TlrNeqDdW2U60yd0idi0xzC4wPIjbXKL6sB2', '0981321123', NULL, '2026-04-14 02:24:39', NULL, NULL),
(5, 'Carlos Buenaventura', 'carlosbuenaventura', '$2y$10$PsatIt2cN5i1vRUwj4DSLe9Mk2ipJtcRuOeyN24i7vBInJPYrgg1u', '0987234234', NULL, '2026-04-14 02:27:26', NULL, NULL),
(6, 'Julio Cabello', 'juliocabello@comedor.com', '$2y$10$qh3RBSFK6.IRMk3Ap4GTU.FufsvquKzI9S.ga821HCEIIdpAKOgza', '0987881992', NULL, '2026-04-14 02:37:03', NULL, NULL),
(7, 'Roberto Carlos', 'robertocarlos@comedor.com', '$2y$10$w814MXvT4JaGeo6u4cOp0OaSEP5azzf0C/M3v62ctURlWSSrSjEca', '0987101010', NULL, '2026-04-14 03:06:06', NULL, NULL),
(8, 'Victor Benitez', 'victorbenitez@comedor.com', '$2y$10$WTp5yQJBVelx.iS.Ydl17O340p9YL0wfQupL3wuk3CTRcXKCsrSBS', '0987898332', NULL, '2026-04-14 03:49:26', NULL, NULL),
(9, 'Raul Ortega', 'raulortega@comedor.com', '$2y$10$1i0vcVSBDNq2Jjnu6Zlmve.LgwhUF76471BPMK/TCapJEXrYv6qf6', '0981778291', NULL, '2026-04-14 12:50:23', NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `client_locations`
--

INSERT INTO `client_locations` (`id`, `client_id`, `title`, `address`, `lat`, `lng`, `created_at`) VALUES
(1, 1, 'Mi casa', '581', -25.26694881, -57.57627869, '2026-04-05 20:21:26'),
(2, 1, 'Trabajo', '5231', -25.29137053, -57.60749817, '2026-04-05 20:34:25'),
(3, 1, 'Casa de tio y tia Lucas y Gloria', '7133', -25.29183615, -57.62603760, '2026-04-05 20:35:29'),
(4, 1, 'Casa de abuelo y abuela', '7109', -25.28117817, -57.63813972, '2026-04-06 06:00:05');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `delivery_rates`
--

INSERT INTO `delivery_rates` (`id`, `user_id`, `is_active`, `created_at`) VALUES
(8, 1, 0, '2026-04-17 05:22:19'),
(9, 1, 0, '2026-04-17 05:41:31'),
(10, 1, 1, '2026-04-18 12:30:06');

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `delivery_rate_details`
--

INSERT INTO `delivery_rate_details` (`id`, `delivery_rate_id`, `km_from`, `km_to`, `price`) VALUES
(16, 8, 0.00, 2.00, 10000.00),
(17, 8, 2.10, 5.00, 15000.00),
(18, 9, 0.00, 5.00, 15000.00),
(19, 9, 5.10, 8.00, 20000.00),
(20, 9, 8.10, 11.00, 26000.00),
(21, 10, 0.00, 5.00, 15000.00),
(22, 10, 5.10, 8.00, 20000.00),
(23, 10, 8.10, 11.00, 26000.00),
(24, 10, 11.10, 12.00, 32000.00),
(25, 10, 12.10, 16.00, 34000.00);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `order_channels`
--

INSERT INTO `order_channels` (`id`, `name`, `icon`) VALUES
(1, 'Web', 'fas fa-globe'),
(2, 'Mostrador', 'fas fa-cash-register'),
(3, 'App Mozo', 'fas fa-mobile-alt');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `monto_total` decimal(15,2) NOT NULL,
  `fecha_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pago_venta_rel` (`venta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_detalles`
--

DROP TABLE IF EXISTS `pagos_detalles`;
CREATE TABLE IF NOT EXISTS `pagos_detalles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pago_id` int NOT NULL,
  `metodo_pago` enum('efectivo','pos','transferencia','qr') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `referencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pdet_pago` (`pago_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pos_ventas_cabecera`
--

DROP TABLE IF EXISTS `pos_ventas_cabecera`;
CREATE TABLE IF NOT EXISTS `pos_ventas_cabecera` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `cliente_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  KEY `fk_venta_order_rel` (`order_id`),
  KEY `fk_venta_staff_rel` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `codigobarra`, `name`, `category_id`, `es_vendible`, `description`, `price`, `price_half`, `image`, `is_active`, `created_at`) VALUES
(1, NULL, 'Guiso de arroz con pollo', 1, 1, 'Guiso de arroz con pollo (papas, zanahorias, arvejas y zapallo)', 20000.00, 13500.00, '1773802008_guisoDeArroz.jfif', 1, '2026-03-18 02:46:48'),
(3, NULL, 'Grille de Pollo con fideo al pesto', 1, 1, '', 23000.00, NULL, '1773837404_GrilleDePolloConFideoAlPesto.jfif', 1, '2026-03-18 12:19:14'),
(4, NULL, 'Grille de Pollo con arroz', 1, 1, '', 23000.00, NULL, '1773837281_grilleDePolloConArroz.jfif', 1, '2026-03-18 12:34:41'),
(5, NULL, 'Arroz con leche', 6, 1, '', 9000.00, NULL, '1773919735_arrozConLeche.jfif', 1, '2026-03-19 11:28:55'),
(6, NULL, 'Milanesa de pollo con ensalada rusa', 1, 1, '', 28000.00, NULL, '1774092142_milanesaPolloConRusa.jfif', 1, '2026-03-21 11:22:22'),
(7, '', 'Coca cola 250ml', 4, 1, '', 3500.00, NULL, '1777553718_cocacola250.jpg', 1, '2026-03-24 21:16:32'),
(8, '', 'Coca cola 500ml', 4, 1, '', 7000.00, NULL, '1777553705_cocacola500.jpg', 1, '2026-03-24 21:16:55'),
(9, '', 'Coca cola 1.5L', 4, 1, '', 13500.00, NULL, '1777553686_cocacola1.5descartable.jpg', 1, '2026-03-24 21:17:26'),
(10, '', 'Coca cola 1L', 4, 1, '', 10000.00, NULL, '1777553627_cocacola1l.jpg', 1, '2026-03-25 03:10:37'),
(11, NULL, 'Muslo al horno con arroz', 1, 1, '', 30000.00, NULL, '1774439910_musloConArroz.jfif', 1, '2026-03-25 11:58:30'),
(12, NULL, 'Empanada de carne', 2, 1, '', 4000.00, NULL, '1774525165_empanadaCarne.jfif', 1, '2026-03-26 11:39:25'),
(13, NULL, 'Cafe con leche', 3, 1, '', 6000.00, NULL, '1774525187_cafeCup.jfif', 1, '2026-03-26 11:39:47'),
(14, NULL, 'Empanada de pollo', 2, 1, '', 4000.00, NULL, '1774800748_empanadaPollo.avif', 1, '2026-03-29 16:12:28'),
(15, NULL, 'Strogonoff de pollo con arroz', 1, 1, '', 20000.00, 14000.00, '1775454627_strogonffDePolloConArroz.jpg', 1, '2026-04-06 05:50:10'),
(16, NULL, 'Guiso de fideo spaggeti con pollo', 1, 1, '', 20000.00, 14000.00, '1775521688_guisoDeFideosSpageti.jpg', 1, '2026-04-07 00:27:42'),
(17, '2001', 'Guiso de arroz con carne de cerdo', 1, 1, '', 20000.00, 14000.00, '1775522123_guisoDeArrozConChancho.jpg', 1, '2026-04-07 00:35:23');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `product_reactions`
--

INSERT INTO `product_reactions` (`id`, `product_id`, `client_id`, `type`, `created_at`) VALUES
(1, 1, 1, 'like', '2026-04-30 11:19:34'),
(2, 3, 1, 'fav', '2026-04-30 11:19:36'),
(3, 4, 1, 'like', '2026-04-30 11:19:37'),
(4, 15, 1, 'like', '2026-04-30 11:19:40'),
(5, 11, 1, 'fav', '2026-04-30 11:19:41'),
(6, 6, 1, 'fav', '2026-04-30 11:19:43'),
(7, 3, 1, 'like', '2026-04-30 18:25:59'),
(8, 13, 1, 'fav', '2026-04-30 18:47:33'),
(9, 1, 1, 'fav', '2026-05-01 22:34:48'),
(10, 17, 1, 'like', '2026-05-02 11:29:33'),
(11, 16, 1, 'like', '2026-05-02 11:29:35');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `client_id`, `comment`, `created_at`) VALUES
(1, 6, 1, 'Excelente la preparacion de la milanesa, muy rica!!', '2026-04-30 11:20:22'),
(2, 15, 1, 'Como estira cuando hace frio y ademas es super liviano', '2026-04-30 11:20:48'),
(3, 4, 1, 'Que rico el grille con pure', '2026-04-30 11:21:28'),
(4, 17, 1, 'Muy rico y como estira con el frio', '2026-05-02 11:30:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Administrador', 'admin', 'Acceso total al sistema'),
(2, 'Cajero/a', 'cajero', 'Gestión de ventas y tesorería'),
(3, 'Repartidor', 'delivery', 'Gestión de logística y entregas'),
(4, 'Cliente', 'cliente', 'Usuario final del sistema');

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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'site_name', 'Comedor'),
(2, 'site_logo', 'logo_brand_1776771007.jpg'),
(14, 'store_lat', '-25.2619682'),
(15, 'store_lng', '-57.5895167'),
(16, 'store_address', 'Esta es la direccion que debe aparecer al publico'),
(25, 'enable_legal_invoice', '1'),
(59, 'contact_channels', '[{\"label\":\"Atencion al publico\",\"phone\":\"0987123456\",\"calls\":0,\"sms\":0,\"whatsapp\":1}]');

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
  `role_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_reset_token` (`reset_token`(250))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `role_id`, `is_active`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'Administrador', 'admin@comedor.com', '$2y$10$3ifo8Ck4a83DWkkL8KNsCui8QbgKMgGWChmzOkG43kMLlnsEEiUDe', NULL, NULL, 'admin', 1, 1, '2026-03-18 01:59:37', 'e4012f21dd6500a75de316aa5cc4de114f8e49c1660aac01ce258123e0eb2639', '2026-03-27 13:00:40'),
(2, 'Repartidor de Prueba', 'delivery@comedor.com', '$2y$10$YftP8ctgiYk.F00ajHAgde2SKvhURR4Q6tJXwrPCkNeS/V8GzcOT2', '0981000111', 'Base Central de Reparto', 'delivery', 3, 1, '2026-04-01 04:30:56', NULL, NULL),
(4, 'Roberto Cuellar', 'robertocuellar@gmail.com', '$2y$10$v2Le7IBRPcFYLbcB0uE5gugm1jUUOTgzSnug1T3cOflGYC5PG/YB6', NULL, NULL, 'delivery', 3, 1, '2026-04-10 13:00:59', NULL, NULL),
(7, 'Marcela Esteche', 'mesteche@comedor.com', '$2y$10$i1Nsr4vCwiYpQvOhYC0y8.hIJfue4nuM3vf3XbtgCdQb31bri4/ri', NULL, NULL, 'cliente', 2, 1, '2026-05-02 01:18:22', NULL, NULL);

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
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pago_venta_rel` FOREIGN KEY (`venta_id`) REFERENCES `pos_ventas_cabecera` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos_detalles`
--
ALTER TABLE `pagos_detalles`
  ADD CONSTRAINT `fk_pdet_pago` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `pos_ventas_cabecera`
--
ALTER TABLE `pos_ventas_cabecera`
  ADD CONSTRAINT `fk_venta_order_rel` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_venta_staff_rel` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pos_ventas_detalle`
--
ALTER TABLE `pos_ventas_detalle`
  ADD CONSTRAINT `fk_vdet_prod` FOREIGN KEY (`producto_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

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
