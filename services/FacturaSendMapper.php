<?php
/**
 * FacturaSendMapper
 * Transforma datos de la base de datos al formato JSON de FacturaSend
 */

require_once '../models/Client.php';
require_once '../models/User.php';
require_once '../models/Product.php';
require_once '../models/Empresa.php';

class FacturaSendMapper {
    
    /**
     * Transforma una venta completa al formato JSON de FacturaSend
     * @param array $venta Datos de la cabecera de venta
     * @param array $detalles Array de detalles de la venta
     * @param array $cliente Datos del cliente
     * @param array $empresa Datos de la empresa (emisor)
     * @param array $usuario Datos del usuario que emite
     * @param array $pagos Datos de los pagos
     * @return array JSON formato FacturaSend
     */
    public function ventaToFacturaSend($venta, $detalles, $cliente, $empresa, $usuario, $pagos) {
        
        // Debug forzar error si no se carga este archivo
        file_put_contents(__DIR__ . '/../public/debug_facturasend.log', 
            "MAPPER LOADED - Nueva fecha: " . date('Y-m-d\TH:i:s', strtotime($venta['fecha_hora'])) . "\n", 
            FILE_APPEND
        );

        $factura = [
            'tipoDocumento' => $venta['tipo_documento'] ?? 1,
            'establecimiento' => $empresa['sucursal'] ?? '001',
            'punto' => $empresa['punto_emision'] ?? '001',
            'numero' => preg_replace('/[^0-9]/', '', $venta['nro_factura']),
            'descripcion' => $venta['descripcion'] ?? '',
            'observacion' => $venta['observacion'] ?? '',
            'fecha' => date('Y-m-d\TH:i:s', strtotime($venta['fecha_hora'])),
            'tipoEmision' => $venta['tipo_emision'] ?? 1,
            'tipoTransaccion' => $venta['tipo_transaccion'] ?? 1,
            'tipoImpuesto' => $venta['tipo_impuesto'] ?? 1,
            'moneda' => $venta['moneda'] ?? 'PYG',
            
            // Datos del cliente (receptor)
            'cliente' => $this->mapearCliente($cliente),
            
            // Datos del usuario emisor
            'usuario' => $this->mapearUsuario($usuario),
            
            // Datos de la factura
            'factura' => [
                'presencia' => $venta['factura_presencia'] ?? 1
            ],
            
            // Condición de pago
            'condicion' => $this->mapearCondicion($venta, $pagos),
            
            // Items de la factura
            'items' => $this->mapearItems($detalles)
        ];
        
        // Debug temporal
        file_put_contents(__DIR__ . '/../public/debug_facturasend.log', 
            "Fecha enviada: " . date('Y-m-d', strtotime($venta['fecha_hora'])) . "\n" .
            "Fecha original: " . $venta['fecha_hora'] . "\n\n", 
            FILE_APPEND
        );

        return $factura;
    }
    
    /**
     * Mapea datos del cliente al formato FacturaSend
     */
    private function mapearCliente($cliente) {
        return [
            'contribuyente' => (bool)($cliente['contribuyente'] ?? 0),
            'ruc' => $cliente['billing_ruc'] ?? '',
            'razonSocial' => $cliente['billing_name'] ?? $cliente['name'] ?? '',
            'nombreFantasia' => $cliente['nombre_fantasia'] ?? $cliente['billing_name'] ?? $cliente['name'] ?? '',
            'tipoOperacion' => $cliente['tipo_operacion'] ?? 1,
            'direccion' => $cliente['billing_address'] ?? $cliente['address'] ?? '',
            'numeroCasa' => $cliente['numero_casa'] ?? '',
            'departamento' => $cliente['departamento'] ?? null,
            'departamentoDescripcion' => $cliente['departamento_descripcion'] ?? '',
            'distrito' => $cliente['distrito'] ?? null,
            'distritoDescripcion' => $cliente['distrito_descripcion'] ?? '',
            'ciudad' => $cliente['ciudad'] ?? null,
            'ciudadDescripcion' => $cliente['ciudad_descripcion'] ?? '',
            'pais' => $cliente['pais'] ?? 'PRY',
            'paisDescripcion' => $cliente['pais_descripcion'] ?? 'Paraguay',
            'tipoContribuyente' => $cliente['tipo_contribuyente'] ?? 1,
            'documentoTipo' => $cliente['tipo_documento'] ?? 1,
            'documentoNumero' => $cliente['documento_numero'] ?? $cliente['billing_ruc'] ?? '',
            'telefono' => $cliente['phone'] ?? '',
            'celular' => $cliente['celular'] ?? '',
            'email' => $cliente['email'] ?? '',
            'codigo' => $cliente['codigo'] ?? ''
        ];
    }
    
    /**
     * Mapea datos del usuario emisor
     */
    private function mapearUsuario($usuario) {
        return [
            'documentoTipo' => $usuario['documento_tipo'] ?? 1,
            'documentoNumero' => $usuario['documento_numero'] ?? '',
            'nombre' => $usuario['name'] ?? '',
            'cargo' => $usuario['cargo'] ?? 'Vendedor'
        ];
    }
    
    /**
     * Mapea condición de pago y entregas
     */
    private function mapearCondicion($venta, $pagos) {
        $condicion = [
            'tipo' => $venta['condicion_tipo'] ?? 1
        ];
        
        // Mapear entregas (pagos)
        $entregas = [];
        foreach ($pagos as $pago) {
            $entrega = [
                'tipo' => $this->getTipoPago($pago['metodo_pago']),
                'monto' => (float)$pago['monto'],
                'moneda' => $pago['moneda'] ?? 'PYG',
                'monedaDescripcion' => $pago['moneda_descripcion'] ?? 'Guarani',
                'cambio' => (float)($pago['cambio'] ?? 0.0)
            ];
            
            // Agregar detalles específicos según tipo de pago
            if ($pago['metodo_pago'] === 'pos' && !empty($pago['tarjeta_numero'])) {
                $entrega['infoTarjeta'] = [
                    'numero' => $pago['tarjeta_numero'],
                    'tipo' => $pago['tarjeta_tipo'] ?? 1,
                    'tipoDescripcion' => $pago['tarjeta_tipo_descripcion'] ?? '',
                    'titular' => $pago['tarjeta_titular'] ?? '',
                    'ruc' => $pago['tarjeta_ruc'] ?? '',
                    'razonSocial' => $pago['tarjeta_razon_social'] ?? '',
                    'medioPago' => $pago['tarjeta_medio_pago'] ?? 1,
                    'codigoAutorizacion' => $pago['tarjeta_codigo_autorizacion'] ?? ''
                ];
            }
            
            if ($pago['metodo_pago'] === 'cheque' && !empty($pago['cheque_numero'])) {
                $entrega['infoCheque'] = [
                    'numeroCheque' => $pago['cheque_numero'],
                    'banco' => $pago['cheque_banco'] ?? ''
                ];
            }
            
            $entregas[] = $entrega;
        }
        
        $condicion['entregas'] = $entregas;
        
        // Si es crédito, agregar información de cuotas (pendiente implementar)
        if ($venta['condicion_tipo'] == 2) {
            $condicion['credito'] = [
                'tipo' => 1,
                'plazo' => '30 días',
                'cuotas' => 1,
                'montoEntrega' => (float)$venta['total_venta'],
                'infoCuotas' => []
            ];
        }
        
        return $condicion;
    }
    
    /**
     * Mapea items de la factura
     */
    private function mapearItems($detalles) {
        $items = [];
        
        foreach ($detalles as $detalle) {
            $item = [
                'codigo' => $detalle['codigobarra'] ?? '',
                'descripcion' => $detalle['name'] ?? '',
                'observacion' => $detalle['description'] ?? '',
                'ncm' => $detalle['ncm'] ?? '',
                'unidadMedida' => $detalle['unidad_medida'] ?? 77,
                'cantidad' => (float)$detalle['cantidad'],
                'precioUnitario' => (float)$detalle['precio_unitario_venta'],
                'cambio' => 0.0,
                'ivaTipo' => $this->getIvaTipo($detalle['iva_tipo']),
                'ivaBase' => round((float)$detalle['precio_unitario_venta'] * (float)$detalle['cantidad'], 2),
                'iva' => (float)$detalle['iva_tipo'],
                'ivaProporcion' => $this->getIvaProporcion($detalle['iva_tipo']),
                'lote' => $detalle['lote'] ?? '',
                'vencimiento' => !empty($detalle['vencimiento']) ? date('Y-m-d', strtotime($detalle['vencimiento'])) : '',
                'numeroSerie' => $detalle['numero_serie'] ?? '',
                'numeroPedido' => '',
                'numeroSeguimiento' => ''
            ];
            
            $items[] = $item;
        }
        
        return $items;
    }
    
    /**
     * Convierte método de pago interno a tipo FacturaSend
     */
    private function getTipoPago($metodoPago) {
        $mapeo = [
            'efectivo' => 1,
            'cheque' => 2,
            'pos' => 3,
            'transferencia' => 4,
            'qr' => 5
        ];
        
        return $mapeo[$metodoPago] ?? 1;
    }
    
    /**
     * Convierte porcentaje IVA a tipo FacturaSend
     */
    private function getIvaTipo($ivaPorcentaje) {
        $mapeo = [
            10 => 1,
            5 => 2,
            0 => 3
        ];
        
        return $mapeo[$ivaPorcentaje] ?? 1;
    }
    
    /**
     * Convierte porcentaje IVA a ivaProporcion FacturaSend
     * Según FacturaSend: ivaTipo=1 (10%) -> ivaProporcion=100
     */
    private function getIvaProporcion($ivaPorcentaje) {
        $mapeo = [
            10 => 100,
            5 => 50,
            0 => 0
        ];
        
        return $mapeo[$ivaPorcentaje] ?? 100;
    }
    
    /**
     * Valida que los datos mínimos estén presentes
     */
    public function validarDatosMinimos($venta, $detalles, $cliente, $empresa) {
        $errores = [];
        
        // Validar venta
        if (empty($venta['nro_factura'])) {
            $errores[] = 'Falta número de factura';
        }
        
        // Validar cliente
        if (empty($cliente['billing_ruc']) && empty($cliente['billing_name'])) {
            $errores[] = 'Cliente debe tener RUC o nombre de facturación';
        }
        
        // Validar empresa
        if (empty($empresa['ruc'])) {
            $errores[] = 'Empresa debe tener RUC configurado';
        }
        if (empty($empresa['timbrado_vigente'])) {
            $errores[] = 'Empresa debe tener timbrado vigente';
        }
        if (empty($empresa['punto_emision'])) {
            $errores[] = 'Empresa debe tener punto de emisión configurado';
        }
        if (empty($empresa['sucursal'])) {
            $errores[] = 'Empresa debe tener sucursal configurada';
        }
        
        // Validar detalles
        if (empty($detalles)) {
            $errores[] = 'La factura debe tener al menos un item';
        }
        
        return $errores;
    }
}
