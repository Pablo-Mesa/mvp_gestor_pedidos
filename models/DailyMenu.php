<?php
require_once '../config/db.php';

class DailyMenu {
    private $conn;
    private $table = 'daily_menus';

    public $id;
    public $product_id;
    public $menu_date;
    public $daily_stock;
    public $is_available;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lee los menús asignados para una fecha específica.
     * Se une con la tabla de productos para obtener los detalles.
     * @param string $date 'Y-m-d'
     * @return PDOStatement
     */
    
    /*public function readForDate($date) {
        $query = 'SELECT 
                    dm.id, 
                    dm.menu_date, 
                    p.name as product_name, 
                    p.price as product_price,
                    p.price_half,
                    p.id as product_id,
                    c.name as category_name,
                    dm.daily_stock,
                    dm.is_available,
                    p.image as image
                  FROM ' . $this->table . ' dm
                  JOIN products p ON dm.product_id = p.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE dm.menu_date = :menu_date
                  ORDER BY p.name ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':menu_date', $date);
        $stmt->execute();
        return $stmt;
    }*/

    public function readForDate($date, $clientId = null) {
        $query = "SELECT 
                    dm.*, p.name as product_name, p.price as product_price, p.price_half, p.image, 
                    c.name as category_name,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'fav') as fav_count,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'like') as likes_count,
                    (SELECT COUNT(*) FROM product_reactions WHERE product_id = p.id AND type = 'share') as share_count,
                    (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as reviews_count,
                    EXISTS(SELECT 1 FROM product_reactions WHERE product_id = p.id AND client_id = :client_id AND type = 'fav') as is_favorite,
                    EXISTS(SELECT 1 FROM product_reactions WHERE product_id = p.id AND client_id = :client_id AND type = 'like') as is_liked
                FROM daily_menus dm
                JOIN products p ON dm.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE dm.menu_date = :menu_date AND dm.is_available = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':menu_date', $date);
        $stmt->bindValue(':client_id', $clientId);
        $stmt->execute();
        return $stmt;
    }


    /**
     * Asigna un producto a una fecha como menú del día.
     * @return bool
     */
    public function assign() {
        $query = 'INSERT INTO ' . $this->table . ' (product_id, menu_date, daily_stock) VALUES (:product_id, :menu_date, :daily_stock)';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':menu_date', $this->menu_date);
        // Si el stock es un string vacío, lo guardamos como NULL en la DB
        $stock_val = empty($this->daily_stock) ? null : $this->daily_stock;
        $stmt->bindParam(':daily_stock', $stock_val);

        // Usamos un try-catch para manejar el error de clave única (si ya existe)
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function unassign() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function updateAvailability() {
        $query = 'UPDATE ' . $this->table . ' SET is_available = :is_available WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_available', $this->is_available);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>