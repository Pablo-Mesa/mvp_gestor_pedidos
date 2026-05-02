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
     * Obtiene todos los usuarios del staff (admins y repartidores)
     */
    public function readAll() {
        $query = "SELECT u.id, u.name, u.email, r.slug as role, u.is_active 
                  FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  ORDER BY r.name ASC, u.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtiene los datos de un usuario específico
     */
    public function readOne($id) {
        $query = "SELECT u.id, u.name, u.email, r.slug as role, u.role_id, u.is_active 
                  FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE u.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo usuario de staff
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (name, email, password, role_id, is_active) VALUES (:name, :email, :password, :role_id, :is_active)";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->bindValue(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }

    /**
     * Actualiza los datos de un usuario (incluyendo password opcional)
     */
    public function update($data) {
        $passwordPart = !empty($data['password']) ? ", password = :password" : "";
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email, role_id = :role_id, is_active = :is_active " . $passwordPart . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->bindValue(':is_active', $data['is_active']);
        $stmt->bindValue(':id', $data['id']);

        if (!empty($data['password'])) {
            $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindValue(':password', $hashed);
        }

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Valida las credenciales de un administrador o usuario del sistema.
     */
    public function login($email, $password) {
        $query = "SELECT u.id, u.name, u.password, r.slug as role, u.is_active 
                  FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE u.email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            if ($row['is_active'] == 0) {
                $this->role = $row['role'];
                return 'inactive';
            }
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    /**
     * Obtiene todos los roles disponibles para el staff
     */
    public function getRoles() {
        $query = "SELECT id, name, slug FROM roles WHERE slug != 'cliente' ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "SELECT id FROM " . $this->table . " WHERE reset_token = :token AND reset_expires > NOW() AND is_active = 1 LIMIT 1";
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