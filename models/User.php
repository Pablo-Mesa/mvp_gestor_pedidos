<?php
require_once '../config/db.php';

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $role;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Valida las credenciales de un administrador o usuario del sistema.
     */
    public function login($email, $password) {
        $query = "SELECT id, name, password, role FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function findByEmail($email) {
        $query = "SELECT id, name, email FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda el token de recuperación y su fecha de expiración.
     */
    public function saveResetToken($token, $expires) {
        $query = "UPDATE " . $this->table . " SET reset_token = :token, reset_expires = :expires WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    /**
     * Valida el token, verifica que no haya expirado y actualiza la contraseña.
     */
    public function resetPasswordWithToken($token, $newPassword) {
        // Buscamos un usuario que tenga ese token y que no haya expirado (fecha mayor a "ahora")
        $query = "SELECT id FROM " . $this->table . " WHERE reset_token = :token AND reset_expires > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE " . $this->table . " SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':password', $hashedPassword);
            $updateStmt->bindParam(':id', $user['id']);
            return $updateStmt->execute();
        }
        return false;
    }

    /**
     * Obtiene todos los usuarios con rol de repartidor
     */
    public function getDeliveryUsers() {
        $query = "SELECT id, name FROM " . $this->table . " WHERE role = 'delivery'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}