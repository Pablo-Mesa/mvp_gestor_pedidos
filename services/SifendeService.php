<?php
/**
 * Servicio de Integración con Sifende (Facturación Electrónica Paraguay)
 * Ubicación: services/SifendeService.php
 */
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Order.php';

class SifendeService {
    private $appId;
    private $appKey;
    private $baseUrl;
    private $db;

    public function __construct() {
        $settings = new Setting();
        $this->appId = $settings->get('sifende_app_id');
        $this->appKey = $settings->get('sifende_app_key');
        $this->baseUrl = $settings->get('sifende_api_url');
        
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Mapea y envía la factura a la API de Sifende
     */
    public function enviarFactura($ventaId) {
        try {
            // 1. Obtener datos del emisor (Empresa)
            $empresaModel = new Empresa();
            $empresa = $empresaModel->readOne(1);

            // 2. Obtener datos de la venta (Cabecera y Detalle)
            $qVenta = "SELECT v.*, c.name as cliente_nombre, c.email as cliente_email, 
                              c.billing_ruc as cliente_ruc, c.tipo_documento 
                       FROM pos_ventas_cabecera v
                       LEFT JOIN clients c ON v.cliente_id = c.id
                       WHERE v.id = :id";
            $stmtV = $this->db->prepare($qVenta);
            $stmtV->execute([':id' => $ventaId]);
            $venta = $stmtV->fetch(PDO::FETCH_ASSOC);

            if (!$venta) throw new Exception("Venta no encontrada.");

            $qDetalle = "SELECT vd.*, p.name as producto_nombre, p.unidad_medida 
                         FROM pos_ventas_detalle vd
                         JOIN products p ON vd.producto_id = p.id
                         WHERE vd.venta_id = :id";
            $stmtD = $this->db->prepare($qDetalle);
            $stmtD->execute([':id' => $ventaId]);
            $detalles = $stmtD->fetchAll(PDO::FETCH_ASSOC);

            // 3. Construir el JSON según el estándar de Sifende (Estructura DNIT)
            $items = [];
            foreach ($detalles as $d) {
                $items[] = [
                    "codigo" => $d['producto_id'],
                    "descripcion" => $d['producto_nombre'],
                    "cantidad" => (float)$d['cantidad'],
                    "precioUnitario" => (float)$d['precio_unitario_venta'],
                    "ivaTipo" => (int)$d['iva_tipo'], // 10, 5 o 0
                    "unidadMedida" => (int)($d['unidad_medida'] ?? 77) // 77 = Unidad
                ];
            }

            $payload = [
                "appId" => $this->appId,
                "appKey" => $this->appKey,
                "operacion" => "emitir",
                "documento" => [
                    "tipo" => 1, // 1 = Factura Electrónica
                    "establecimiento" => $empresa['sucursal'] ?? "001",
                    "puntoEmision" => $empresa['punto_emision'] ?? "001",
                    "numero" => substr($venta['nro_factura'], -7),
                    "fecha" => date('Y-m-d\TH:i:s', strtotime($venta['fecha_hora'])),
                    "moneda" => "PYG",
                    "condicion" => 1, // 1 = Contado
                    "emisor" => [
                        "ruc" => $empresa['ruc'],
                        "dv" => $empresa['dv'],
                        "razonSocial" => $empresa['razon_social']
                    ],
                    "receptor" => [
                        "tipoDocumento" => (int)($venta['tipo_documento'] ?? 1),
                        "documento" => $venta['cliente_ruc'],
                        "razonSocial" => $venta['cliente_nombre'],
                        "email" => $venta['cliente_email']
                    ],
                    "items" => $items
                ]
            ];

            // 4. Llamar a la API
            $respuesta = $this->callAPI($payload);

            // 5. Procesar y Guardar Respuesta
            $this->procesarRespuesta($ventaId, $respuesta);

            return $respuesta;

        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    private function callAPI($data) {
        $ch = curl_init($this->baseUrl);
        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) return ["status" => "error", "message" => "CURL Error: " . $error];
        return json_decode($response, true);
    }

    private function procesarRespuesta($ventaId, $res) {
        $estado = 'rechazado';
        $cdc = null;
        $qr = null;

        if (isset($res['status']) && $res['status'] === 'success') {
            $estado = 'aprobado';
            $cdc = $res['cdc'] ?? null;
            $qr = $res['qr_url'] ?? null;
        }

        $sql = "UPDATE pos_ventas_cabecera 
                SET estado_sifen = :est, cdc = :cdc, qr_url = :qr, respuesta_sifen = :res 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':est' => $estado, ':cdc' => $cdc, ':qr' => $qr, 
            ':res' => json_encode($res), ':id' => $ventaId
        ]);
    }
}