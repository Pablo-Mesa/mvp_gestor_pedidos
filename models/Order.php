<?php
require_once '../config/db.php';

class Order {
    private $conn;
    private $table = 'orders';

    // Propiedades de la Orden
    public $id;
    public $user_id;
    public $total;
    public $status;
    public $payment_method;
    public $delivery_type;
    public $delivery_address;
    public $delivery_lat;
    public $delivery_lng;
    public $created_at;

    // Array para almacenar los detalles antes de guardar (producto, cantidad, precio)
    public $details = []; 

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crea la orden y sus detalles en una sola transacción
     */
    public function create() {
        try {
            // Iniciar transacción: Todo se guarda o nada se guarda
            $this->conn->beginTransaction();

            // 1. Insertar Cabecera (Order)
            $query = "INSERT INTO " . $this->table . " 
                      (user_id, total, status, payment_method, delivery_type, delivery_address, delivery_lat, delivery_lng) 
                      VALUES (:user_id, :total, 'pending', :payment_method, :delivery_type, :delivery_address, :delivery_lat, :delivery_lng)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind params
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->bindParam(':delivery_type', $this->delivery_type);
            $stmt->bindParam(':delivery_address', $this->delivery_address);
            // Si no hay coordenadas, se guardará NULL automáticamente si la propiedad es null
            $stmt->bindParam(':delivery_lat', $this->delivery_lat);
            $stmt->bindParam(':delivery_lng', $this->delivery_lng);

            $stmt->execute();
            $this->id = $this->conn->lastInsertId();

            // 2. Insertar Detalles
            $queryDetail = "INSERT INTO order_details (order_id, daily_menu_id, quantity, price) VALUES (:order_id, :daily_menu_id, :quantity, :price)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            foreach ($this->details as $item) {
                $stmtDetail->bindParam(':order_id', $this->id);
                $stmtDetail->bindParam(':daily_menu_id', $item['daily_menu_id']);
                $stmtDetail->bindParam(':quantity', $item['quantity']);
                $stmtDetail->bindParam(':price', $item['price']);
                $stmtDetail->execute();
            }

            // Confirmar transacción
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Si algo falla, revertir todo
            $this->conn->rollBack();
            // Opcional: registrar error con error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los pedidos ordenados por fecha (más reciente primero)
     */
    public function readAll() {
        $query = "SELECT o.*, u.name as user_name 
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>