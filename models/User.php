<?php
require_once '../config/db.php';

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Login
    public function login($email, $password) {
        $query = 'SELECT id, name, email, password, role FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificamos el hash de la contraseña
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>