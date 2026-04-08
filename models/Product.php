<?php
require_once '../config/db.php';

class Product {
    private $conn;
    private $table = 'products';

    public $id;
    public $name;
    public $category_id; // Cambiado de $category a $category_id
    public $description;
    public $price;
    public $price_half;
    public $image;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll($filters = []) {
        $query = 'SELECT p.*, c.name as category_name 
                  FROM ' . $this->table . ' p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE 1=1';

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query .= ' AND p.category_id = :category_id';
        }

        if (!empty($filters['search'])) {
            // Buscamos coincidencia en nombre o ID (actúa como código)
            $query .= ' AND (p.name LIKE :search OR p.id = :id)';
        }

        $query .= ' ORDER BY p.created_at DESC';
        
        $stmt = $this->conn->prepare($query);

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $stmt->bindValue(':category_id', $filters['category']);
        }
        if (!empty($filters['search'])) {
            $stmt->bindValue(':search', '%' . $filters['search'] . '%');
            $stmt->bindValue(':id', $filters['search']);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Lee todos los productos activos.
     * @return PDOStatement
     */
    
    /*public function readAllActive() {
        $query = 'SELECT p.*, c.name as category_name FROM ' . $this->table . ' p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.name ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }*/

    public function readAllActive($clientId = null) {
        $query = "SELECT 
                    p.*, c.name as category_name,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'fav') as fav_count,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'like') as likes_count,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'share') as share_count,
                    (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as reviews_count,
                    EXISTS(SELECT 1 FROM product_reactions WHERE product_id = p.id AND client_id = :client_id AND type = 'fav') as is_favorite,
                    EXISTS(SELECT 1 FROM product_reactions WHERE product_id = p.id AND client_id = :client_id AND type = 'like') as is_liked
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        return $stmt;
    }


    public function readOne() {
        $query = 'SELECT p.*, c.name as category_name FROM ' . $this->table . ' p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (name, category_id, description, price, price_half, image, is_active) VALUES (:name, :category_id, :description, :price, :price_half, :image, :is_active)';
        $stmt = $this->conn->prepare($query);

        // Limpieza básica
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id)); // Cambiado a category_id
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category_id', $this->category_id); // Cambiado a category_id
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);

        // Lógica para guardar NULL si está vacío
        $price_half_val = !empty($this->price_half) ? $this->price_half : null;
        $stmt->bindParam(':price_half', $price_half_val);

        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':is_active', $this->is_active);

        return $stmt->execute();
    }

    public function update() {
        $query = 'UPDATE ' . $this->table . ' SET name = :name, category_id = :category_id, description = :description, price = :price, price_half = :price_half, image = :image, is_active = :is_active WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id)); // Cambiado a category_id
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category_id', $this->category_id); // Cambiado a category_id
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        // 5. VINCULAR EL PARÁMETRO EN UPDATE TAMBIÉN
        $price_half_val = !empty($this->price_half) ? $this->price_half : null;
        $stmt->bindParam(':price_half', $price_half_val);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>