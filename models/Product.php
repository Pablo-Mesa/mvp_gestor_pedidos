<?php
require_once '../config/db.php';

class Product {
    private $conn;
    private $table = 'products';

    public $id;
    public $codigobarra;
    public $name;
    public $category_id; // Cambiado de $category a $category_id
    public $description;
    public $es_vendible;
    public $price;
    public $price_half;
    public $image;
    public $is_active;
    
    // Campos adicionales para FacturaSend
    public $unidad_medida;
    public $iva_porcentaje;
    public $ncm;
    public $iva_base;
    public $lote;
    public $vencimiento;
    public $numero_serie;

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
            $query .= ' AND (p.name LIKE :search OR p.id = :id OR p.codigobarra LIKE :barcode)';
        }

        $query .= ' ORDER BY p.created_at DESC';
        
        $stmt = $this->conn->prepare($query);

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $stmt->bindValue(':category_id', $filters['category']);
        }
        if (!empty($filters['search'])) {
            $stmt->bindValue(':search', '%' . $filters['search'] . '%');
            $stmt->bindValue(':id', $filters['search']);
            $stmt->bindValue(':barcode', '%' . $filters['search'] . '%');
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
        $query = 'INSERT INTO ' . $this->table . ' (codigobarra, name, category_id, es_vendible, description, price, price_half, image, is_active, unidad_medida, iva_porcentaje, ncm, iva_base, lote, vencimiento, numero_serie) VALUES (:codigobarra, :name, :category_id, :es_vendible, :description, :price, :price_half, :image, :is_active, :unidad_medida, :iva_porcentaje, :ncm, :iva_base, :lote, :vencimiento, :numero_serie)';
        $stmt = $this->conn->prepare($query);

        // Limpieza básica
        $this->codigobarra = htmlspecialchars(strip_tags($this->codigobarra));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id)); // Cambiado a category_id
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':codigobarra', $this->codigobarra);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category_id', $this->category_id); // Cambiado a category_id
        $stmt->bindParam(':es_vendible', $this->es_vendible);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);

        // Lógica para guardar NULL si está vacío
        $price_half_val = !empty($this->price_half) ? $this->price_half : null;
        $stmt->bindParam(':price_half', $price_half_val);

        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':is_active', $this->is_active);

        // Campos FacturaSend - usar variables temporales para bindParam
        $unidad_medida_val = $this->unidad_medida ?? 77;
        $iva_porcentaje_val = $this->iva_porcentaje ?? 10;
        $ncm_val = $this->ncm ?? null;
        $iva_base_val = $this->iva_base ?? null;
        $lote_val = $this->lote ?? null;
        $vencimiento_val = $this->vencimiento ?? null;
        $numero_serie_val = $this->numero_serie ?? null;

        $stmt->bindParam(':unidad_medida', $unidad_medida_val);
        $stmt->bindParam(':iva_porcentaje', $iva_porcentaje_val);
        $stmt->bindParam(':ncm', $ncm_val);
        $stmt->bindParam(':iva_base', $iva_base_val);
        $stmt->bindParam(':lote', $lote_val);
        $stmt->bindParam(':vencimiento', $vencimiento_val);
        $stmt->bindParam(':numero_serie', $numero_serie_val);

        return $stmt->execute();
    }

    public function update() {
        $query = 'UPDATE ' . $this->table . ' SET codigobarra = :codigobarra, name = :name, category_id = :category_id, es_vendible = :es_vendible, description = :description, price = :price, price_half = :price_half, image = :image, is_active = :is_active, unidad_medida = :unidad_medida, iva_porcentaje = :iva_porcentaje, ncm = :ncm, iva_base = :iva_base, lote = :lote, vencimiento = :vencimiento, numero_serie = :numero_serie WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->codigobarra = htmlspecialchars(strip_tags($this->codigobarra));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id)); // Cambiado a category_id
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':codigobarra', $this->codigobarra);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category_id', $this->category_id); // Cambiado a category_id
        $stmt->bindParam(':es_vendible', $this->es_vendible);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        // 5. VINCULAR EL PARÁMETRO EN UPDATE TAMBIÉN
        $price_half_val = !empty($this->price_half) ? $this->price_half : null;
        $stmt->bindParam(':price_half', $price_half_val);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':is_active', $this->is_active);

        // Campos FacturaSend - usar variables temporales para bindParam
        $unidad_medida_val = $this->unidad_medida ?? 77;
        $iva_porcentaje_val = $this->iva_porcentaje ?? 10;
        $ncm_val = $this->ncm ?? null;
        $iva_base_val = $this->iva_base ?? null;
        $lote_val = $this->lote ?? null;
        $vencimiento_val = $this->vencimiento ?? null;
        $numero_serie_val = $this->numero_serie ?? null;

        $stmt->bindParam(':unidad_medida', $unidad_medida_val);
        $stmt->bindParam(':iva_porcentaje', $iva_porcentaje_val);
        $stmt->bindParam(':ncm', $ncm_val);
        $stmt->bindParam(':iva_base', $iva_base_val);
        $stmt->bindParam(':lote', $lote_val);
        $stmt->bindParam(':vencimiento', $vencimiento_val);
        $stmt->bindParam(':numero_serie', $numero_serie_val);

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