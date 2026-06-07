-- ====================================================================
-- 1. TABLAS MAESTRAS EXIGIDAS POR LA DNIT (PARAMÉTRICAS SIFEN)
-- ====================================================================

CREATE TABLE dnit_tipos_comprobantes (
    id_comprobante INT PRIMARY KEY, -- Ej: 1=Factura electrónica, 4=Nota de Crédito, etc.
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE dnit_tipos_operaciones (
    id_operacion INT PRIMARY KEY, -- Ej: 1=B2B, 2=B2C, 3=B2G, 4=B2F
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE dnit_tipos_documentos (
    id_tipo_doc INT PRIMARY KEY, -- Ej: 1=Cédula, 2=RUC, 3=Pasaporte, 4=Diplomático
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE dnit_formas_pago (
    id_forma_pago INT PRIMARY KEY, -- Ej: 1=Efectivo, 2=Cheque, 3=Tarjeta Crédito, 4=Débito
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE dnit_unidades_medida (
    codigo_medida VARCHAR(5) PRIMARY KEY, -- Ej: 'un', 'kg', 'm', 'gxl'
    descripcion VARCHAR(50) NOT NULL
);

-- Tablas de Ubicación Geográfica según el INE / DNIT
CREATE TABLE dnit_departamentos (
    id_departamento INT PRIMARY KEY, -- Código oficial DNIT
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE dnit_distritos (
    id_distrito INT PRIMARY KEY,
    id_departamento INT REFERENCES dnit_departamentos(id_departamento),
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE dnit_ciudades (
    id_ciudad INT PRIMARY KEY,
    id_distrito INT REFERENCES dnit_distritos(id_distrito),
    descripcion VARCHAR(100) NOT NULL
);


-- ====================================================================
-- 2. TABLAS OPERATIVAS DEL POS (ENTIDADES COMERCIALES)
-- ====================================================================

CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    id_tipo_doc INT NOT NULL REFERENCES dnit_tipos_documentos(id_tipo_doc),
    documento VARCHAR(20) NOT NULL, -- RUC o CI sin puntos ni guiones
    dv CHAR(1) NULL, -- Dígito verificador obligatorio si es RUC
    razon_social VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    id_ciudad INT NOT NULL REFERENCES dnit_ciudades(id_ciudad),
    telefono VARCHAR(30),
    email VARCHAR(100) NOT NULL -- Obligatorio para envío automático del XML/KUDE
);

CREATE TABLE productos (
    id_producto SERIAL PRIMARY KEY,
    codigo_interno VARCHAR(50) NOT NULL UNIQUE,
    codigo_barra VARCHAR(50), -- Código EAN
    descripcion VARCHAR(120) NOT NULL, -- Máximo 120 caracteres según SIFEN
    codigo_medida VARCHAR(5) NOT NULL REFERENCES dnit_unidades_medida(codigo_medida),
    precio_unitario NUMERIC(18,4) NOT NULL,
    iva_porcentaje INT NOT NULL CHECK (iva_porcentaje IN (0, 5, 10)) -- Gravámenes vigentes
);


-- ====================================================================
-- 3. TABLAS DE TRANSACCIONES Y CONTROL SIFEN
-- ====================================================================

CREATE TABLE ventas_cabecera (
    id_venta SERIAL PRIMARY KEY,
    cdc CHAR(44) UNIQUE NULL, -- Clave de Acceso Única de 44 dígitos (Obligatorio DTE)
    id_comprobante INT NOT NULL REFERENCES dnit_tipos_comprobantes(id_comprobante),
    id_operacion INT NOT NULL REFERENCES dnit_tipos_operaciones(id_operacion),
    establecimiento CHAR(3) NOT NULL, -- Ej: '001'
    punto_expedicion CHAR(3) NOT NULL, -- Ej: '002'
    numero_factura CHAR(7) NOT NULL, -- Ej: '0000045'
    fecha_emision TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_cliente INT NOT NULL REFERENCES clientes(id_cliente),
    condicion_venta INT NOT NULL CHECK (condicion_venta IN (1, 2)), -- 1=Contado, 2=Crédito
    total_gravada_10 NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_gravada_5 NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_exenta NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_iva_10 NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_iva_5 NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_general NUMERIC(18,4) NOT NULL,
    estado_sifen VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE', -- PENDIENTE, APROBADO, RECHAZADO, CONTINGENCIA
    es_contingencia BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE ventas_detalles (
    id_detalle SERIAL PRIMARY KEY,
    id_venta INT NOT NULL REFERENCES ventas_cabecera(id_venta) ON DELETE CASCADE,
    id_producto INT NOT NULL REFERENCES productos(id_producto),
    cantidad NUMERIC(12,4) NOT NULL,
    precio_unitario NUMERIC(18,4) NOT NULL,
    descuento NUMERIC(18,4) NOT NULL DEFAULT 0,
    iva_porcentaje INT NOT NULL,
    subtotal NUMERIC(18,4) NOT NULL, -- (Cantidad * Precio) - Descuento
    subtotal_iva NUMERIC(18,4) NOT NULL -- Liquidación matemática individual del IVA
);

CREATE TABLE ventas_pagos (
    id_pago SERIAL PRIMARY KEY,
    id_venta INT NOT NULL REFERENCES ventas_cabecera(id_venta) ON DELETE CASCADE,
    id_forma_pago INT NOT NULL REFERENCES dnit_formas_pago(id_forma_pago),
    monto NUMERIC(18,4) NOT NULL,
    moneda CHAR(3) NOT NULL DEFAULT 'PYG', -- ISO 4217
    tarjeta_procesadora VARCHAR(50) NULL, -- Requerido si id_forma_pago es tarjeta
    tarjeta_tipo INT NULL -- 1=Visa, 2=Mastercard, etc.
);

CREATE TABLE sifen_logs_envios (
    id_log SERIAL PRIMARY KEY,
    id_venta INT NOT NULL REFERENCES ventas_cabecera(id_venta),
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codigo_estado_sifen INT NOT NULL, -- Código numérico devuelto por el WS (Ej: 0320)
    mensaje_sifen TEXT NOT NULL, -- Descripción del error o aprobación
    xml_firmado TEXT NOT NULL -- Copia del XML enviado para resguardo legal
);
