<?php
require_once '../models/Client.php';

class ClientController {
    
    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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
        $term = $_GET['term'] ?? '';
        
        $client = new Client();
        $db = (new Database())->getConnection();
        
        $query = "SELECT id, name, phone FROM clients 
                  WHERE name LIKE :term OR phone LIKE :term 
                  LIMIT 10";
        
        $stmt = $db->prepare($query);
        $stmt->execute([':term' => "%$term%"]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($clients);
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

        if (empty($data['name']) || empty($data['phone'])) {
            echo json_encode(['success' => false, 'message' => 'Nombre y teléfono son obligatorios']);
            exit;
        }

        $client = new Client();
        $client->name = $data['name'];
        $client->phone = $data['phone'];
        $client->email = $data['email'] ?? ($data['phone'] . '@sistema.com'); // Email dummy si no se provee
        $client->password = password_hash($data['phone'], PASSWORD_DEFAULT); // Password por defecto es su cel

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
        header('Content-Type: application/json');
        $url = $_GET['url'] ?? '';
        
        if (empty($url)) {
            echo json_encode(['success' => false]);
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
            echo json_encode(['success' => false, 'message' => 'No se encontraron coordenadas en el enlace']);
        }
    }

    /**
     * Obtiene las ubicaciones guardadas de un cliente específico
     */
    public function getLocationsApi() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        if (!$id) { echo json_encode([]); exit; }

        require_once '../models/ClientLocation.php';
        $locationModel = new ClientLocation();
        $locations = $locationModel->getAllByClient($id);
        echo json_encode($locations);
    }
}