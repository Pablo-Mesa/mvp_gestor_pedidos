<?php
require_once '../config/db.php';

class Client {
    private $conn;
    private $table = 'clients';

    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $has_whatsapp;
    public $billing_name;
    public $billing_ruc;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, phone, has_whatsapp, billing_name, billing_ruc) 
                  VALUES (:name, :email, :password, :phone, :has_whatsapp, :billing_name, :billing_ruc)";
        
        $stmt = $this->conn->prepare($query);

        // Encriptar contraseña
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':has_whatsapp', $this->has_whatsapp);
        $stmt->bindParam(':billing_name', $this->billing_name);
        $stmt->bindParam(':billing_ruc', $this->billing_ruc);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT id, name, email, phone, has_whatsapp, billing_name, billing_ruc FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBilling($id, $name, $ruc) {
        $query = "UPDATE " . $this->table . " SET billing_name = :name, billing_ruc = :ruc WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':ruc' => $ruc,
            ':id' => $id
        ]);
    }

    public function login($email, $password) {
        $query = "SELECT id, name, password, billing_name, billing_ruc FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->billing_name = $row['billing_name'];
                $this->billing_ruc = $row['billing_ruc'];
                return true;
            }
        }
        return false;
    }
}
?>