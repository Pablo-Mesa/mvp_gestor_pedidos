-- =====================================================
-- MIGRACIÓN SIMPLE PARA FACTURASEND
-- Solo agrega campos faltantes en tabla users
-- El schema.sql ya tiene el resto de los campos
-- =====================================================

USE comedor_db;

-- Agregar campos faltantes en users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS documento_tipo int DEFAULT 1 COMMENT '1: CI, 2: RUC, 5: Pasaporte',
ADD COLUMN IF NOT EXISTS documento_numero varchar(50) DEFAULT NULL COMMENT 'Número de documento del usuario',
ADD COLUMN IF NOT EXISTS cargo varchar(50) DEFAULT 'Vendedor' COMMENT 'Cargo del usuario para facturación';

-- Actualizar enum de users para incluir cajero y mozo
ALTER TABLE users 
MODIFY COLUMN role enum('cliente','admin','delivery','cajero','mozo') DEFAULT 'cliente';

-- Configuración de API FacturaSend
INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_tenant_id', '') ON DUPLICATE KEY UPDATE setting_value = '';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_api_key', '') ON DUPLICATE KEY UPDATE setting_value = '';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_api_url', 'https://api.facturasend.com.py') ON DUPLICATE KEY UPDATE setting_value = 'https://api.facturasend.com.py';

INSERT INTO settings (setting_key, setting_value) VALUES 
('facturasend_modo', 'sandbox') ON DUPLICATE KEY UPDATE setting_value = 'sandbox';
