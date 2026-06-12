<?php
require_once '../config/db.php';
require_once '../models/Client.php';

class ClientController {
    
    public function __construct() {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'cajero'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
    }

    /**
     * Busca clientes por nombre o teléfono para el POS
     */
    public function search() {
        header('Content-Type: application/json');
        $term = trim($_GET['term'] ?? '');
        
        $client = new Client();
        $db = (new Database())->getConnection();
        
        try {
            $limit = 15;
            $params = [];

            // SQL base para clientes locales: Normalizamos con CAST para evitar conflictos de Collation en la UNION
            $sqlLocalSelect = "SELECT id, CAST(name AS CHAR) AS name, CAST(phone AS CHAR) AS phone, 
                                      CAST(billing_ruc AS CHAR) AS billing_ruc, 0 AS is_taxpayer 
                               FROM clients";
            
            $sqlLocalWhere = " WHERE (LOWER(CAST(name AS CHAR)) LIKE LOWER(:t1) OR phone LIKE :t2 OR billing_ruc LIKE :t3)";

            $params[':t1'] = "%$term%";
            $params[':t2'] = "%$term%";
            $params[':t3'] = "%$term%";

            // Solo consultamos la tabla masiva si el término es lo suficientemente largo
            if (strlen($term) >= 3) {
                // Verificamos si la tabla de contribuyentes existe
                try {
                    $tableCheck = $db->query("SHOW TABLES LIKE 'contribuyentes'");
                    $tableExists = $tableCheck && $tableCheck->rowCount() > 0;
                } catch (Exception $e) {
                    $tableExists = false;
                }

                if ($tableExists) {
                    $query = "SELECT * FROM (
                                ($sqlLocalSelect $sqlLocalWhere)
                                UNION ALL
                                (SELECT NULL AS id, 
                                        CAST(razon_social AS CHAR) AS name, 
                                        NULL AS phone, 
                                        CAST(CONCAT(ruc, '-', IFNULL(dv, '')) AS CHAR) AS billing_ruc, 
                                        1 AS is_taxpayer 
                                 FROM contribuyentes c
                                 WHERE (LOWER(CAST(razon_social AS CHAR)) LIKE LOWER(:t4) OR CAST(ruc AS CHAR) LIKE :t5)
                                 AND NOT EXISTS (
                                     SELECT 1 FROM clients cl 
                                     WHERE cl.billing_ruc = CAST(c.ruc AS CHAR) 
                                        OR cl.billing_ruc = CAST(CONCAT(c.ruc, '-', IFNULL(c.dv, '')) AS CHAR)
                                 ))
                              ) as combined
                              ORDER BY (CASE WHEN name LIKE :t_order THEN 1 ELSE 2 END), name ASC
                              LIMIT $limit";
                    
                    $params[':t4'] = "%$term%";
                    $params[':t5'] = "$term%";
                    $params[':t_order'] = "$term%";
                } else {
                    // Si no existe la tabla masiva, buscamos solo en locales pero con el orden inteligente
                    $query = $sqlLocalSelect . $sqlLocalWhere . " ORDER BY (CASE WHEN name LIKE :t_order THEN 1 ELSE 2 END), name ASC LIMIT $limit";
                    $params[':t_order'] = "$term%";
                }
            } else {
                $query = $sqlLocalSelect . $sqlLocalWhere . " ORDER BY name ASC LIMIT $limit";
            }

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($results);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Verifica si un teléfono ya existe (AJAX)
     */
    public function checkPhoneApi() {
        header('Content-Type: application/json');
        $phone = $_GET['phone'] ?? '';
        
        if (empty($phone)) {
            echo json_encode(['exists' => false]);
            exit;
        }

        $db = (new Database())->getConnection();
        $query = "SELECT id FROM clients WHERE phone = :phone LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':phone' => $phone]);
        
        echo json_encode(['exists' => (bool)$stmt->fetch()]);
    }

    /**
     * Registra un cliente de forma rápida desde el POS
     */
    public function storeApi() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['phone'])) {
            echo json_encode(['success' => false, 'message' => 'El teléfono es obligatorio']);
            exit;
        }

        $client = new Client();
        // Si el nombre viene vacío, generamos uno automático basado en el teléfono
        $client->name = !empty($data['name']) ? $data['name'] : 'Cliente ' . $data['phone'];
        $client->phone = $data['phone'];
        // Si viene vacío o no existe, enviamos null para que el modelo lo maneje
        $client->email = !empty($data['email']) ? $data['email'] : null;
        $client->password = null; // Dejamos nulo para que el modelo use el teléfono como clave por defecto
        $client->billing_name = $data['billing_name'] ?? null;
        $client->billing_ruc = $data['billing_ruc'] ?? null;
        $client->has_whatsapp = $data['has_whatsapp'] ?? 1;

        if ($client->register()) {
            echo json_encode(['success' => true, 'id' => $client->id, 'name' => $client->name]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar cliente. El teléfono podría estar duplicado.']);
        }
    }

    /**
     * Resuelve links cortos de Google Maps para extraer coordenadas
     */
    public function resolveMapUrl() {
        if (ob_get_length()) ob_clean(); // Limpiar cualquier salida previa accidental
        header('Content-Type: application/json');
        $url = $_GET['url'] ?? '';
        
        if (empty($url)) {
            echo json_encode(['success' => false]);
            exit;
        }

        if (!function_exists('curl_init')) {
            echo json_encode(['success' => false, 'message' => 'La extensión CURL no está habilitada en el servidor.']);
            exit;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $response = curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        // Regex simplificada: busca cualquier par de coordenadas decimales separadas por coma o códigos de Google
        // Ejemplo: @-25.123,-57.123 o /place/-25.123,-57.123
        $regex = '/([-+]?\d+\.\d+)[,%2C\s!4d]+([-+]?\d+\.\d+)/';
        if (preg_match($regex, $finalUrl, $matches)) {
            echo json_encode(['success' => true, 'lat' => $matches[1], 'lng' => $matches[2]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se detectaron coordenadas en el enlace: ' . $finalUrl]);
        }
        exit;
    }

    /**
     * Obtiene las ubicaciones guardadas de un cliente específico
     */
    public function getLocationsApi() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        if (!$id) { echo json_encode([]); exit; }

        require_once '../models/ClientLocation.php';
        $locationModel = new ClientLocation();
        $locations = $locationModel->getAllByClient($id);
        echo json_encode($locations);
        exit;
    }
}