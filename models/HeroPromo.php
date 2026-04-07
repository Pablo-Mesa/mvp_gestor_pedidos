<?php
require_once '../config/db.php';

class HeroPromo {
    private $conn;
    private $table = 'hero_promos';

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function readActive() {
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY order_priority ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET title = :title, content = :content, image = :image, css_class = :css_class, type = :type, is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':image' => $data['image'],
            ':css_class' => $data['css_class'],
            ':type' => $data['type'],
            ':is_active' => $data['is_active'],
            ':id' => $id
        ]);
    }

    public function readAll() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY order_priority ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}