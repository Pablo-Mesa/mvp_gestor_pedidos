<?php
require_once '../config/db.php';

class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $name;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lee todas las categorías de la base de datos.
     * @return PDOStatement
     */
    public function readAll() {
        $query = 'SELECT id, name, created_at FROM ' . $this->table . ' ORDER BY name ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lee una única categoría por su ID.
     * @return array|false
     */
    public function readOne() {
        $query = 'SELECT id, name, created_at FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva categoría.
     * @return bool
     */
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (name) VALUES (:name)';
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $stmt->bindParam(':name', $this->name);
        return $stmt->execute();
    }

    /**
     * Actualiza una categoría existente.
     * @return bool
     */
    public function update() {
        $query = 'UPDATE ' . $this->table . ' SET name = :name WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    /**
     * Elimina una categoría.
     * @return bool
     */
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>