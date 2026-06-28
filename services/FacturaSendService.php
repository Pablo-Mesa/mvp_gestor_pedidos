<?php
/**
 * FacturaSendService
 * Servicio para comunicación con la API de FacturaSend (SIFEN)
 */

require_once '../config/db.php';
require_once '../models/Setting.php';

class FacturaSendService {
    
    private $tenantId;
    private $apiKey;
    private $apiUrl;
    private $modo;
    private $conn;
    
    public function __construct() {
        // Cargar configuración desde settings
        $settingModel = new Setting();
        
        $this->tenantId = $settingModel->getValue('facturasend_tenant_id');
        $this->apiKey = $settingModel->getValue('facturasend_api_key');
        $this->apiUrl = $settingModel->getValue('facturasend_api_url') ?? 'https://api.facturasend.com.py';
        $this->modo = $settingModel->getValue('facturasend_modo') ?? 'sandbox';
        
        // Conexión a DB para logs
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Verifica si la configuración está completa
     */
    public function isConfigured() {
        return !empty($this->tenantId) && !empty($this->apiKey);
    }
    
    /**
     * Envía un lote de facturas a FacturaSend
     * @param array $facturas Array de facturas en formato JSON
     * @return array Respuesta de la API
     */
    public function enviarLote($facturas) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'FacturaSend no está configurado. Configure tenant_id y api_key en settings.'
            ];
        }
        
        $endpoint = "{$this->apiUrl}/{$this->tenantId}/lote/create";
        
        $jsonData = json_encode($facturas);
        
        $this->logApiCall('enviarLote', $endpoint, $jsonData);
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json; charset=utf-8'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $this->modo === 'production',
            CURLOPT_SSL_VERIFYHOST => $this->modo === 'production'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            $this->logApiError('enviarLote', $error);
            return [
                'success' => false,
                'error' => 'Error cURL: ' . $error
            ];
        }
        
        $this->logApiResponse('enviarLote', $response, $httpCode);

        // Debug temporal - ruta a carpeta public
        file_put_contents(__DIR__ . '/../public/debug_facturasend.log', 
            "HTTP Code: $httpCode\n" .
            "Raw Response: $response\n" .
            "JSON Decode: " . print_r(json_decode($response, true), true) . "\n\n", 
            FILE_APPEND
        );

        $responseData = json_decode($response, true);
        
        // Debug: log raw response
        error_log("FacturaSend Response - HTTP $httpCode: " . $response);
        error_log("FacturaSend Decoded: " . print_r($responseData, true));
        
        if ($httpCode >= 200 && $httpCode < 300) {
            if (is_array($responseData) && !empty($responseData)) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'http_code' => $httpCode
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Respuesta inválida de FacturaSend - formato incorrecto',
                    'http_code' => $httpCode,
                    'response' => $response
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => $responseData['error'] ?? $responseData['message'] ?? 'Error desconocido',
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }
    
    /**
     * Consulta el estado de una factura por CDC
     * @param string $cdc Clave de Acceso Única
     * @return array Estado de la factura
     */
    public function consultarEstado($cdc) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'FacturaSend no está configurado.'
            ];
        }
        
        $endpoint = "{$this->apiUrl}/{$this->tenantId}/factura/consulta/{$cdc}";
        
        $this->logApiCall('consultarEstado', $endpoint, null);
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $this->modo === 'production',
            CURLOPT_SSL_VERIFYHOST => $this->modo === 'production'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            $this->logApiError('consultarEstado', $error);
            return [
                'success' => false,
                'error' => 'Error cURL: ' . $error
            ];
        }
        
        $this->logApiResponse('consultarEstado', $response, $httpCode);
        
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'data' => $responseData,
                'http_code' => $httpCode
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseData['error'] ?? 'Error desconocido',
                'http_code' => $httpCode
            ];
        }
    }
    
    /**
     * Obtiene el PDF de una factura por CDC
     * @param string $cdc Clave de Acceso Única
     * @return array PDF en base64 o error
     */
    public function obtenerPDF($cdc) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'FacturaSend no está configurado.'
            ];
        }
        
        $endpoint = "{$this->apiUrl}/{$this->tenantId}/factura/pdf/{$cdc}";
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $this->modo === 'production',
            CURLOPT_SSL_VERIFYHOST => $this->modo === 'production'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'Error cURL: ' . $error
            ];
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'pdf_base64' => base64_encode($response),
                'http_code' => $httpCode
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Error al obtener PDF',
                'http_code' => $httpCode
            ];
        }
    }
    
    /**
     * Registra llamada a API en log
     */
    private function logApiCall($metodo, $endpoint, $payload) {
        try {
            $query = "INSERT INTO api_logs (metodo, endpoint, payload, created_at) 
                      VALUES (:metodo, :endpoint, :payload, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':metodo' => $metodo,
                ':endpoint' => $endpoint,
                ':payload' => $payload
            ]);
        } catch (Exception $e) {
            // Si no existe tabla api_logs, ignorar error
        }
    }
    
    /**
     * Registra respuesta de API en log
     */
    private function logApiResponse($metodo, $response, $httpCode) {
        try {
            $query = "UPDATE api_logs SET response = :response, http_code = :http_code 
                      WHERE metodo = :metodo ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':metodo' => $metodo,
                ':response' => $response,
                ':http_code' => $httpCode
            ]);
        } catch (Exception $e) {
            // Si no existe tabla api_logs, ignorar error
        }
    }
    
    /**
     * Registra error de API en log
     */
    private function logApiError($metodo, $error) {
        try {
            $query = "UPDATE api_logs SET error = :error 
                      WHERE metodo = :metodo ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':metodo' => $metodo,
                ':error' => $error
            ]);
        } catch (Exception $e) {
            // Si no existe tabla api_logs, ignorar error
        }
    }
    
    /**
     * Obtiene configuración actual
     */
    public function getConfig() {
        return [
            'tenant_id' => $this->tenantId,
            'api_url' => $this->apiUrl,
            'modo' => $this->modo,
            'configured' => $this->isConfigured()
        ];
    }
}
