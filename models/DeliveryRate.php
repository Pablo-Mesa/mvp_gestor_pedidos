<?php
require_once '../config/db.php';

class DeliveryRate {
    private $conn;
    private $table = 'delivery_rates';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtiene la tarifa activa con sus detalles
     */
    public function getActive() {
        $query = "SELECT r.id, r.created_at, u.name as creator_name 
                  FROM " . $this->table . " r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.is_active = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $header = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($header) {
            $header['details'] = $this->getDetails($header['id']);
        }
        return $header;
    }

    public function getDetails($rateId) {
        $query = "SELECT km_from, km_to, price FROM delivery_rate_details WHERE delivery_rate_id = ? ORDER BY km_from ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$rateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva versión de tarifas y la activa automáticamente
     */
    public function createVersion($userId, $rows) {
        try {
            $this->conn->beginTransaction();

            // 1. Desactivar todas las anteriores
            $this->conn->query("UPDATE " . $this->table . " SET is_active = 0");

            // 2. Insertar Cabecera
            $query = "INSERT INTO " . $this->table . " (user_id, is_active) VALUES (?, 1)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            $rateId = $this->conn->lastInsertId();

            // 3. Insertar Detalles
            $queryDetail = "INSERT INTO delivery_rate_details (delivery_rate_id, km_from, km_to, price) VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            foreach ($rows as $row) {
                $stmtDetail->execute([$rateId, $row['start'], $row['end'], $row['price']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getPriceForDistance($distance) {
        $query = "SELECT d.price FROM delivery_rate_details d
                  JOIN delivery_rates r ON d.delivery_rate_id = r.id
                  WHERE r.is_active = 1 AND ? BETWEEN d.km_from AND d.km_to LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$distance]);
        return $stmt->fetchColumn() ?: 0;
    }
}