<?php
require_once '../config/db.php';

class ProductReaction {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Alterna una reacción (Like, Fav, Share) para un producto y cliente.
     * @param int $productId ID del producto
     * @param int $clientId ID del cliente en sesión
     * @param string $type Tipo de reacción ('fav', 'like', 'share')
     * @return array Estado de la acción
     */
    public function toggle($productId, $clientId, $type) {
        // 1. Verificar si ya existe el registro
        $query = "SELECT id FROM product_reactions WHERE product_id = ? AND client_id = ? AND type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$productId, $clientId, $type]);
        
        if ($stmt->fetch()) {
            // Si existe, lo eliminamos (Toggle Off)
            $delete = "DELETE FROM product_reactions WHERE product_id = ? AND client_id = ? AND type = ?";
            $this->conn->prepare($delete)->execute([$productId, $clientId, $type]);
            return ['status' => 'removed'];
        } else {
            // Si no existe, lo insertamos (Toggle On)
            $insert = "INSERT INTO product_reactions (product_id, client_id, type) VALUES (?, ?, ?)";
            $this->conn->prepare($insert)->execute([$productId, $clientId, $type]);
            return ['status' => 'added'];
        }
    }

    /**
     * Agrega una reseña o comentario a un producto.
     * @param int $productId
     * @param int $clientId
     * @param string $comment
     * @return bool
     */
    public function addReview($productId, $clientId, $comment) {
        $query = "INSERT INTO product_reviews (product_id, client_id, comment) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$productId, $clientId, $comment]);
    }
}
?>