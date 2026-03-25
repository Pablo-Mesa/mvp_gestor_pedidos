<?php
require_once '../config/db.php';

class Order {
    private $conn;
    private $table = 'orders';

    // Propiedades de la Orden
    public $id;
    public $client_id;
    public $total;
    public $status;
    public $observation; // Nueva propiedad
    public $payment_method;
    public $delivery_type;
    public $delivery_address;
    public $delivery_lat;
    public $delivery_lng;
    public $created_at;
    public $error; // Para capturar mensajes de error SQL

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
                      (client_id, total, status, observation, payment_method, delivery_type, delivery_address, delivery_lat, delivery_lng) 
                      VALUES (:client_id, :total, 'pending', :observation, :payment_method, :delivery_type, :delivery_address, :delivery_lat, :delivery_lng)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind params
            $stmt->bindParam(':client_id', $this->client_id);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':observation', $this->observation);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->bindParam(':delivery_type', $this->delivery_type);
            $stmt->bindParam(':delivery_address', $this->delivery_address);
            // Si no hay coordenadas, se guardará NULL automáticamente si la propiedad es null
            $stmt->bindParam(':delivery_lat', $this->delivery_lat);
            $stmt->bindParam(':delivery_lng', $this->delivery_lng);

            $stmt->execute();
            $this->id = $this->conn->lastInsertId();

            // 2. Insertar Detalles
            $queryDetail = "INSERT INTO orders_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
            $stmtDetail = $this->conn->prepare($queryDetail);

            foreach ($this->details as $item) {
                $stmtDetail->bindParam(':order_id', $this->id);
                $stmtDetail->bindParam(':product_id', $item['product_id']);
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
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Obtiene todos los pedidos ordenados por fecha (más reciente primero)
     */
    public function readAll($date = null) {
        $query = "SELECT o.*, c.name as user_name 
                  FROM " . $this->table . " o
                  JOIN clients c ON o.client_id = c.id";

        if ($date) {
            $query .= " WHERE DATE(o.created_at) = :date";
        }

        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($date) {
            $stmt->bindParam(':date', $date);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtiene un solo pedido por ID
     */
    public function readOne() {
        $query = "SELECT o.*, c.name as user_name, c.email as user_email 
                  FROM " . $this->table . " o
                  JOIN clients c ON o.client_id = c.id
                  WHERE o.id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los detalles (platos) de un pedido
     */
    public function readDetails() {
        $query = "SELECT od.*, p.name as product_name 
                  FROM orders_items od
                  JOIN products p ON od.product_id = p.id
                  WHERE od.order_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        // Pasamos los valores directamente en el execute para mayor seguridad
        return $stmt->execute([
            ':status' => $this->status,
            ':id' => $this->id
        ]);
    }

    /**
     * Obtiene estadísticas reales para el dashboard
     */
    public function getDashboardStats() {
        $stats = [];
        $today = date('Y-m-d');

        // 1. Ingresos de Hoy (excluyendo cancelados)
        $query = "SELECT SUM(total) as total FROM " . $this->table . " WHERE DATE(created_at) = :today AND status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['income_today'] = $row['total'] ?? 0;

        // 2. Pedidos Pendientes (Total global)
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pending_orders'] = $row['count'] ?? 0;

        // 3. Platos Vendidos Hoy
        $query = "SELECT SUM(oi.quantity) as count FROM orders_items oi JOIN " . $this->table . " o ON oi.order_id = o.id WHERE DATE(o.created_at) = :today AND o.status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['dishes_sold'] = $row['count'] ?? 0;

        return $stats;
    }
}
?>