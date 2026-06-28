-- =====================================================
-- MIGRACIONES PARA INTEGRACIÓN FACTURASEND - SIFEN
-- Fecha: 22-06-2026
-- Descripción: Agregar campos necesarios para facturación electrónica
-- =====================================================

USE comedor_db;

-- =====================================================
-- 1. TABLA CLIENTS - Datos geográficos y fiscales adicionales
-- =====================================================

ALTER TABLE clients 
ADD COLUMN nombre_fantasia varchar(150) DEFAULT NULL COMMENT 'Nombre comercial del cliente',
ADD COLUMN tipo_operacion int DEFAULT 1 COMMENT '1: Exportación, 2: Operación local',
ADD COLUMN numero_casa varchar(50) DEFAULT NULL COMMENT 'Número de casa/dirección',
ADD COLUMN departamento int DEFAULT NULL COMMENT 'Código de departamento según DNIT',
ADD COLUMN departamento_descripcion varchar(100) DEFAULT NULL COMMENT 'Nombre del departamento',
ADD COLUMN distrito int DEFAULT NULL COMMENT 'Código de distrito según DNIT',
ADD COLUMN distrito_descripcion varchar(100) DEFAULT NULL COMMENT 'Nombre del distrito',
ADD COLUMN ciudad int DEFAULT NULL COMMENT 'Código de ciudad según DNIT',
ADD COLUMN ciudad_descripcion varchar(100) DEFAULT NULL COMMENT 'Nombre de la ciudad',
ADD COLUMN pais varchar(3) DEFAULT 'PRY' COMMENT 'Código ISO del país',
ADD COLUMN pais_descripcion varchar(50) DEFAULT 'Paraguay' COMMENT 'Nombre del país',
ADD COLUMN tipo_contribuyente int DEFAULT 1 COMMENT '1: Contribuyente, 2: No contribuyente',
ADD COLUMN documento_numero varchar(50) DEFAULT NULL COMMENT 'Número de documento (CI/RUC)',
ADD COLUMN celular varchar(20) DEFAULT NULL COMMENT 'Número de celular',
ADD COLUMN codigo varchar(20) DEFAULT NULL COMMENT 'Código interno del cliente',
ADD COLUMN contribuyente tinyint(1) DEFAULT 0 COMMENT '1: Es contribuyente, 0: No es contribuyente';

-- =====================================================
-- 2. TABLA USERS - Datos fiscales del emisor (usuario)
-- =====================================================

ALTER TABLE users 
ADD COLUMN documento_tipo int DEFAULT 1 COMMENT '1: CI, 2: RUC, 5: Pasaporte',
ADD COLUMN documento_numero varchar(50) DEFAULT NULL COMMENT 'Número de documento del usuario',
ADD COLUMN cargo varchar(50) DEFAULT 'Vendedor' COMMENT 'Cargo del usuario para facturación';

-- =====================================================
-- 3. TABLA PRODUCTS - Datos para SIFEN
-- =====================================================

ALTER TABLE products 
ADD COLUMN ncm varchar(10) DEFAULT NULL COMMENT 'Nomenclatura Común del Mercosur',
ADD COLUMN iva_base decimal(10,2) DEFAULT NULL COMMENT 'Base imponible para cálculo de IVA',
ADD COLUMN lote varchar(50) DEFAULT NULL COMMENT 'Número de lote del producto',
ADD COLUMN vencimiento date DEFAULT NULL COMMENT 'Fecha de vencimiento del producto',
ADD COLUMN numero_serie varchar(50) DEFAULT NULL COMMENT 'Número de serie del producto';

-- =====================================================
-- 4. TABLA PAGOS_DETALLES - Detalles de medios de pago
-- =====================================================

ALTER TABLE pagos_detalles 
ADD COLUMN moneda varchar(3) DEFAULT 'PYG' COMMENT 'Código de moneda',
ADD COLUMN moneda_descripcion varchar(50) DEFAULT 'Guarani' COMMENT 'Nombre de la moneda',
ADD COLUMN cambio decimal(10,2) DEFAULT 0.0 COMMENT 'Tipo de cambio aplicado',
ADD COLUMN tarjeta_numero varchar(20) DEFAULT NULL COMMENT 'Últimos 4 dígitos de tarjeta',
ADD COLUMN tarjeta_tipo int DEFAULT NULL COMMENT 'Tipo de tarjeta',
ADD COLUMN tarjeta_tipo_descripcion varchar(50) DEFAULT NULL COMMENT 'Descripción del tipo de tarjeta',
ADD COLUMN tarjeta_titular varchar(100) DEFAULT NULL COMMENT 'Titular de la tarjeta',
ADD COLUMN tarjeta_ruc varchar(20) DEFAULT NULL COMMENT 'RUC del titular de la tarjeta',
ADD COLUMN tarjeta_razon_social varchar(150) DEFAULT NULL COMMENT 'Razón social del titular',
ADD COLUMN tarjeta_medio_pago int DEFAULT NULL COMMENT 'Medio de pago utilizado',
ADD COLUMN tarjeta_codigo_autorizacion varchar(50) DEFAULT NULL COMMENT 'Código de autorización',
ADD COLUMN cheque_numero varchar(50) DEFAULT NULL COMMENT 'Número de cheque',
ADD COLUMN cheque_banco varchar(50) DEFAULT NULL COMMENT 'Nombre del banco del cheque';

-- =====================================================
-- 5. TABLA POS_VENTAS_CABECERA - Metadatos de factura
-- =====================================================

ALTER TABLE pos_ventas_cabecera 
ADD COLUMN tipo_documento int DEFAULT 1 COMMENT '1: Factura electrónica',
ADD COLUMN establecimiento int DEFAULT 1 COMMENT 'Número de establecimiento',
ADD COLUMN descripcion varchar(255) DEFAULT NULL COMMENT 'Descripción de la factura',
ADD COLUMN observacion text DEFAULT NULL COMMENT 'Observaciones de marketing/promoción',
ADD COLUMN tipo_emision int DEFAULT 1 COMMENT '1: Normal, 2: Contingencia',
ADD COLUMN tipo_transaccion int DEFAULT 1 COMMENT 'Tipo de transacción',
ADD COLUMN tipo_impuesto int DEFAULT 1 COMMENT 'Tipo de impuesto',
ADD COLUMN moneda varchar(3) DEFAULT 'PYG' COMMENT 'Moneda de la factura',
ADD COLUMN factura_presencia int DEFAULT 1 COMMENT '1: Presencial, 2: Electrónica',
ADD COLUMN condicion_tipo int DEFAULT 1 COMMENT '1: Contado, 2: Crédito';

-- =====================================================
-- 6. TABLAS DE CRÉDITO (opcional para ventas a crédito)
-- =====================================================

CREATE TABLE IF NOT EXISTS pos_ventas_credito (
  id int NOT NULL AUTO_INCREMENT,
  venta_id int NOT NULL,
  tipo int DEFAULT 1 COMMENT 'Tipo de crédito',
  plazo varchar(50) DEFAULT NULL COMMENT 'Plazo del crédito (ej: 30 días)',
  cuotas int DEFAULT NULL COMMENT 'Cantidad de cuotas',
  monto_entrega decimal(15,2) DEFAULT NULL COMMENT 'Monto de entrega inicial',
  PRIMARY KEY (id),
  KEY fk_credito_venta (venta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pos_ventas_credito_cuotas (
  id int NOT NULL AUTO_INCREMENT,
  credito_id int NOT NULL,
  moneda varchar(3) DEFAULT 'PYG' COMMENT 'Moneda de la cuota',
  monto decimal(15,2) NOT NULL COMMENT 'Monto de la cuota',
  vencimiento date DEFAULT NULL COMMENT 'Fecha de vencimiento de la cuota',
  PRIMARY KEY (id),
  KEY fk_cuota_credito (credito_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. CONFIGURACIÓN DE API FACTURASEND
-- =====================================================

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_tenant_id', '') ON DUPLICATE KEY UPDATE setting_value = '';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_api_key', '') ON DUPLICATE KEY UPDATE setting_value = '';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_api_url', 'https://api.facturasend.com.py') ON DUPLICATE KEY UPDATE setting_value = 'https://api.facturasend.com.py';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_modo', 'sandbox') ON DUPLICATE KEY UPDATE setting_value = 'sandbox';

-- =====================================================
-- 8. ACTUALIZAR ENUM DE USERS PARA INCLUIR CAJERO Y MOZO
-- =====================================================

ALTER TABLE users 
MODIFY COLUMN role enum('cliente','admin','delivery','cajero','mozo') DEFAULT 'cliente';

-- =====================================================
-- FIN DE MIGRACIONES
-- =====================================================
