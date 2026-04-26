<?php
require_once '../config/db.php';

class CashRegister {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getActiveSession($userId) {
        $sql = "SELECT * FROM cash_registers WHERE user_id = :u AND status = 'open' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':u' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSessionById($id) {
        $sql = "SELECT * FROM cash_registers WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isStationOpen($stationName) {
        $sql = "SELECT id FROM cash_registers WHERE cash_station = :s AND status = 'open' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':s' => $stationName]);
        return $stmt->fetch();
    }

    public function open($userId, $amount, $stationName = 'Principal') {
        $sql = "INSERT INTO cash_registers (user_id, opening_amount, cash_station, status, opened_at) 
                VALUES (:u, :a, :s, 'open', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':u' => $userId, ':a' => $amount, ':s' => $stationName]);
    }

    public function addMovement($registerId, $amount, $type, $desc, $source = 'manual', $refId = null) {
        $sql = "INSERT INTO cash_movements (cash_register_id, amount, type, description, source, reference_id, created_at) 
                VALUES (:rid, :a, :t, :d, :s, :ref, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':rid' => $registerId,
            ':a'   => $amount,
            ':t'   => $type, // 'ingress' o 'egress'
            ':d'   => $desc,
            ':s'   => $source,
            ':ref' => $refId
        ]);
    }

    public function getSessionTotals($registerId) {
        // Simplificamos: Sumamos ingresos y egresos puros. 
        // La lógica de anulación ahora recae en crear un movimiento de 'egress' de compensación.
        $sql = "SELECT 
                    SUM(CASE WHEN type = 'ingress' THEN amount ELSE 0 END) as ingress,
                    SUM(CASE WHEN type = 'egress' THEN amount ELSE 0 END) as egress
                FROM cash_movements m
                WHERE m.cash_register_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $registerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addOrderMovement($orderId, $amount, $userId, $desc = 'Venta Online') {
        $session = $this->getActiveSession($userId);
        if ($session) {
            return $this->addMovement($session['id'], $amount, 'ingress', $desc, 'order', $orderId);
        }
        return false;
    }

    public function getMovements($registerId) {
        $sql = "SELECT m.*, u.name as user_name, o.status as order_status 
                FROM cash_movements AS m
                LEFT JOIN cash_registers AS r ON m.cash_register_id = r.id
                LEFT JOIN users AS u ON r.user_id = u.id
                LEFT JOIN orders AS o ON (m.source = 'order' AND m.reference_id = o.id)
                WHERE m.cash_register_id = :id
                ORDER BY m.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $registerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function close($id, $physicalBalance, $expectedBalance) {
        $sql = "UPDATE cash_registers 
                SET status = 'closed', closing_amount = :p, expected_amount = :c, closed_at = NOW() 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':p' => $physicalBalance, ':c' => $expectedBalance, ':id' => $id]);
    }

    public function getRecentSessions($limit = 10) {
        $sql = "SELECT r.*, u.name as user_name,
                (r.opening_amount + 
                    COALESCE((SELECT SUM(CASE WHEN m1.type = 'ingress' THEN m1.amount ELSE 0 END) 
                              FROM cash_movements m1
                              WHERE m1.cash_register_id = r.id), 0) - 
                    COALESCE((SELECT SUM(CASE WHEN m2.type = 'egress' THEN m2.amount ELSE 0 END) 
                              FROM cash_movements m2 
                              WHERE m2.cash_register_id = r.id), 0)) as current_expected
                FROM cash_registers r 
                JOIN users u ON r.user_id = u.id
                ORDER BY r.opened_at DESC LIMIT :l";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':l', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}