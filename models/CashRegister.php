<?php
require_once '../config/db.php';

class CashRegister {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getActiveSession() {
        $sql = "SELECT * FROM cash_registers WHERE status = 'open' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function open($userId, $amount) {
        $sql = "INSERT INTO cash_registers (user_id, opening_balance, status, opened_at) 
                VALUES (:u, :a, 'open', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':u' => $userId, ':a' => $amount]);
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

    public function addOrderMovement($orderId, $amount, $desc = 'Venta Online') {
        $session = $this->getActiveSession();
        if ($session) {
            return $this->addMovement($session['id'], $amount, 'ingress', $desc, 'order', $orderId);
        }
        return false;
    }

    public function getMovements($registerId) {
        $sql = "SELECT m.*, u.name as user_name 
                FROM cash_movements AS m
                LEFT JOIN cash_registers AS r ON m.cash_register_id = r.id
                LEFT JOIN users AS u ON r.user_id = u.id
                WHERE m.cash_register_id = :id
                ORDER BY m.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $registerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function close($id, $physicalBalance, $closingBalance) {
        $sql = "UPDATE cash_registers 
                SET status = 'closed', physical_balance = :p, closing_balance = :c, closed_at = NOW() 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':p' => $physicalBalance, ':c' => $closingBalance, ':id' => $id]);
    }
}