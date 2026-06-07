-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 04-06-2026 a las 22:27:02
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
  `cdc` char(44) UNIQUE DEFAULT NULL COMMENT 'Clave de Acceso Única (44 dígitos)',
  `qr_url` text DEFAULT NULL COMMENT 'URL para escaneo del KUDE',
  `estado_sifen` enum('pendiente','aprobado','rechazado','contingencia') DEFAULT 'pendiente',
  `respuesta_sifen` text DEFAULT NULL COMMENT 'Respuesta técnica de la API',
  `xml_path` varchar(255) DEFAULT NULL COMMENT 'Ruta al XML firmado',
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
  `iva_tipo` int DEFAULT '10' COMMENT '10, 5 o 0',
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Restricciones para tablas volcadas
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
