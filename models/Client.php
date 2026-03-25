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

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, phone, has_whatsapp) 
                  VALUES (:name, :email, :password, :phone, :has_whatsapp)";
        
        $stmt = $this->conn->prepare($query);

        // Encriptar contraseña
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':has_whatsapp', $this->has_whatsapp);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT id, name, password FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                return true;
            }
        }
        return false;
    }
}
?>