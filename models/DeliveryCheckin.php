<?php
require_once '../config/db.php';

class DeliveryCheckin {
    private $conn;
    private $table = 'delivery_checkins';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Registra una nueva llegada de repartidor en la base de datos
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, checkin_time, lat, lng, distance_meters) 
                  VALUES (:user_id, NOW(), :lat, :lng, :distance)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':user_id'  => $data['user_id'],
            ':lat'      => $data['lat'],
            ':lng'      => $data['lng'],
            ':distance' => $data['distance'] ?? 0
        ]);
    }
}