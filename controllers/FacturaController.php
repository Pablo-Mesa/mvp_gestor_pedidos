<?php
/**
 * FacturaController
 * Controlador para gestión de facturación electrónica via FacturaSend
 */

require_once '../services/FacturaSendService.php';
require_once '../services/FacturaSendMapper.php';
require_once '../models/Client.php';
require_once '../models/User.php';
require_once '../models/Product.php';
require_once '../models/Empresa.php';
require_once '../config/db.php';

class FacturaController {
    
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Valida acceso solo para roles autorizados
     */
    private function checkAccess() {
        if (!isset($_SESSION['user_role'])) {
            header('Location: ?route=login');
            exit;
        }
        
        $allowedRoles = ['admin', 'cajero'];
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            header('Location: ?route=login');
            exit;
        }
    }
    
    /**
     * Emite una factura electrónica para una venta existente
     */
    public function emitirFactura() {
        $this->checkAccess();
        
        $venta_id = $_POST['venta_id'] ?? null;
        
        if (!$venta_id) {
            echo json_encode(['success' => false, 'error' => 'ID de venta no proporcionado']);
            return;
        }
        
        try {
            // 1. Obtener datos de la venta
            $venta = $this->getVentaCabecera($venta_id);
            if (!$venta) {
                echo json_encode(['success' => false, 'error' => 'Venta no encontrada']);
                return;
            }
            
            // Verificar que no esté ya emitida
            if (!empty($venta['cdc'])) {
                echo json_encode(['success' => false, 'error' => 'Esta venta ya tiene CDC emitido']);
                return;
            }
            
            // 2. Obtener detalles de la venta
            $detalles = $this->getVentaDetalles($venta_id);
            if (empty($detalles)) {
                echo json_encode(['success' => false, 'error' => 'La venta no tiene detalles']);
                return;
            }
            
            // 3. Obtener datos del cliente
            $clienteModel = new Client();
            $cliente = $clienteModel->getById($venta['cliente_id']);
            
            // 4. Obtener datos del usuario emisor
            $userModel = new User();
            $usuario = $userModel->readOne($venta['user_id']);
            
            // 5. Obtener datos de la empresa (emisor)
            $empresaModel = new Empresa();
            $empresa = $empresaModel->getFirst();
            
            // 6. Obtener pagos
            $pagos = $this->getPagos($venta_id);
            
            // 7. Validar datos mínimos
            $mapper = new FacturaSendMapper();
            $errores = $mapper->validarDatosMinimos($venta, $detalles, $cliente, $empresa);
            
            if (!empty($errores)) {
                echo json_encode(['success' => false, 'error' => 'Validación fallida', 'errores' => $errores]);
                return;
            }
            
            // 8. Mapear a formato FacturaSend
            $facturaJson = $mapper->ventaToFacturaSend($venta, $detalles, $cliente, $empresa, $usuario, $pagos);
            
            // 9. Enviar a FacturaSend
            $service = new FacturaSendService();
            
            if (!$service->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'FacturaSend no está configurado. Configure tenant_id y api_key en settings.']);
                return;
            }
            
            $response = $service->enviarLote([$facturaJson]);
            
            if (!$response['success']) {
                // Guardar error en la venta
                $this->actualizarEstadoSifen($venta_id, 'rechazado', $response['error']);
                echo json_encode(['success' => false, 'error' => $response['error']]);
                return;
            }
            
            // 10. Procesar respuesta exitosa
            $data = $response['data'][0] ?? null;
            
            if ($data) {
                $cdc = $data['cdc'] ?? null;
                $qr_url = $data['qr_url'] ?? null;
                $estado = $data['estado'] ?? 'aprobado';
                
                // Actualizar venta con datos de SIFEN
                $this->actualizarVentaSifen($venta_id, $cdc, $qr_url, $estado, json_encode($response['data']));
                
                echo json_encode([
                    'success' => true,
                    'cdc' => $cdc,
                    'qr_url' => $qr_url,
                    'estado' => $estado,
                    'message' => 'Factura emitida exitosamente'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Respuesta inválida de FacturaSend']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Reemite una factura (para casos de rechazo)
     */
    public function reemitirFactura() {
        $this->checkAccess();
        
        $venta_id = $_POST['venta_id'] ?? null;
        
        if (!$venta_id) {
            echo json_encode(['success' => false, 'error' => 'ID de venta no proporcionado']);
            return;
        }
        
        try {
            // Limpiar datos SIFEN anteriores
            $this->limpiarDatosSifen($venta_id);
            
            // Volver a emitir
            $this->emitirFactura();
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Consulta el estado de una factura por CDC
     */
    public function consultarEstado() {
        $this->checkAccess();
        
        $venta_id = $_GET['venta_id'] ?? null;
        
        if (!$venta_id) {
            echo json_encode(['success' => false, 'error' => 'ID de venta no proporcionado']);
            return;
        }
        
        try {
            $venta = $this->getVentaCabecera($venta_id);
            
            if (empty($venta['cdc'])) {
                echo json_encode(['success' => false, 'error' => 'La venta no tiene CDC']);
                return;
            }
            
            $service = new FacturaSendService();
            $response = $service->consultarEstado($venta['cdc']);
            
            if ($response['success']) {
                // Actualizar estado si cambió
                $nuevoEstado = $response['data']['estado'] ?? $venta['estado_sifen'];
                if ($nuevoEstado !== $venta['estado_sifen']) {
                    $this->actualizarEstadoSifen($venta_id, $nuevoEstado);
                }
                
                echo json_encode([
                    'success' => true,
                    'estado' => $nuevoEstado,
                    'data' => $response['data']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => $response['error']]);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene el PDF de una factura
     */
    public function obtenerPDF() {
        $this->checkAccess();
        
        $venta_id = $_GET['venta_id'] ?? null;
        
        if (!$venta_id) {
            echo json_encode(['success' => false, 'error' => 'ID de venta no proporcionado']);
            return;
        }
        
        try {
            $venta = $this->getVentaCabecera($venta_id);
            
            if (empty($venta['cdc'])) {
                echo json_encode(['success' => false, 'error' => 'La venta no tiene CDC']);
                return;
            }
            
            $service = new FacturaSendService();
            $response = $service->obtenerPDF($venta['cdc']);
            
            if ($response['success']) {
                // Decodificar base64 y enviar como PDF
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="factura_' . $venta['nro_factura'] . '.pdf"');
                echo base64_decode($response['pdf_base64']);
            } else {
                echo json_encode(['success' => false, 'error' => $response['error']]);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Vista de configuración de FacturaSend
     */
    public function configuracion() {
        $this->checkAccess();
        
        $service = new FacturaSendService();
        $config = $service->getConfig();
        
        $content_view = '../views/admin/settings/facturasend_config.php';
        require_once '../views/layouts/admin_layout.php';
    }
    
    /**
     * Guarda configuración de FacturaSend
     */
    public function guardarConfiguracion() {
        $this->checkAccess();
        
        $tenant_id = $_POST['tenant_id'] ?? '';
        $api_key = $_POST['api_key'] ?? '';
        $modo = $_POST['modo'] ?? 'sandbox';
        
        try {
            $settingModel = new Setting();
            
            $settingModel->updateValue('facturasend_tenant_id', $tenant_id);
            $settingModel->updateValue('facturasend_api_key', $api_key);
            $settingModel->updateValue('facturasend_modo', $modo);
            
            header('Location: ?route=facturasend_config&success=1');
            exit;
            
        } catch (Exception $e) {
            header('Location: ?route=facturasend_config&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    
    // ==================== MÉTODOS PRIVADOS ====================
    
    private function getVentaCabecera($venta_id) {
        $query = "SELECT * FROM pos_ventas_cabecera WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $venta_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getVentaDetalles($venta_id) {
        $query = "SELECT vd.*, 
                  p.codigobarra, p.name, p.description, 
                  COALESCE(p.unidad_medida, 77) as unidad_medida,
                  COALESCE(p.iva_porcentaje, 10) as iva_porcentaje,
                  COALESCE(p.ncm, '') as ncm,
                  COALESCE(p.iva_base, 0) as iva_base,
                  COALESCE(p.lote, '') as lote,
                  COALESCE(p.vencimiento, NULL) as vencimiento,
                  COALESCE(p.numero_serie, '') as numero_serie
                  FROM pos_ventas_detalle vd
                  LEFT JOIN products p ON vd.producto_id = p.id
                  WHERE vd.venta_id = :venta_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':venta_id' => $venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getPagos($venta_id) {
        $query = "SELECT pd.*, 
                  'PYG' as moneda,
                  'Guarani' as moneda_descripcion,
                  0.0 as cambio,
                  '' as tarjeta_numero,
                  NULL as tarjeta_tipo,
                  '' as tarjeta_tipo_descripcion,
                  '' as tarjeta_titular,
                  '' as tarjeta_ruc,
                  '' as tarjeta_razon_social,
                  NULL as tarjeta_medio_pago,
                  '' as tarjeta_codigo_autorizacion,
                  '' as cheque_numero,
                  '' as cheque_banco
                  FROM pagos_detalles pd
                  LEFT JOIN pagos p ON pd.pago_id = p.id
                  WHERE p.venta_id = :venta_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':venta_id' => $venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function actualizarVentaSifen($venta_id, $cdc, $qr_url, $estado, $respuesta) {
        $query = "UPDATE pos_ventas_cabecera SET 
                  cdc = :cdc, 
                  qr_url = :qr_url, 
                  estado_sifen = :estado, 
                  respuesta_sifen = :respuesta,
                  timbrado = (SELECT timbrado_vigente FROM empresa LIMIT 1)
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':cdc' => $cdc,
            ':qr_url' => $qr_url,
            ':estado' => $estado,
            ':respuesta' => $respuesta,
            ':id' => $venta_id
        ]);
    }
    
    private function actualizarEstadoSifen($venta_id, $estado, $error = null) {
        $query = "UPDATE pos_ventas_cabecera SET estado_sifen = :estado";
        if ($error) {
            $query .= ", respuesta_sifen = :error";
        }
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $params = [
            ':estado' => $estado,
            ':id' => $venta_id
        ];
        if ($error) {
            $params[':error'] = $error;
        }
        return $stmt->execute($params);
    }
    
    private function limpiarDatosSifen($venta_id) {
        $query = "UPDATE pos_ventas_cabecera SET 
                  cdc = NULL, 
                  qr_url = NULL, 
                  estado_sifen = 'pendiente', 
                  respuesta_sifen = NULL
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $venta_id]);
    }
}
