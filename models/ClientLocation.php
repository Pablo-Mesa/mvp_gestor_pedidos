<?php
require_once '../config/db.php';

class ClientLocation {
    private $conn;
    private $table = 'client_locations';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllByClient($client_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE client_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (client_id, title, address, lat, lng) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['client_id'],
            $data['title'],
            $data['address'],
            $data['lat'],
            $data['lng']
        ]);
    }
}