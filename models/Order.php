<?php
date_default_timezone_set('America/Asuncion');

require_once '../config/db.php';

class Order {
    private $conn;
    private $table = 'orders';

    // Propiedades de la Orden
    public $id;
    public $client_id;
    public $channel_id;
    public $total;
    public $status;
    public $delivery_user_id; // ID del repartidor asignado
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
                      (client_id, channel_id, total, status, observation, payment_method, delivery_type, delivery_address, delivery_lat, delivery_lng) 
                      VALUES (:client_id, :channel_id, :total, :status, :observation, :payment_method, :delivery_type, :delivery_address, :delivery_lat, :delivery_lng)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind params
            $stmt->bindParam(':client_id', $this->client_id);
            $stmt->bindParam(':channel_id', $this->channel_id);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':status', $this->status);
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
    public function readAll($filters = []) {
        // Si llega como string (vieja implementación), convertirlo a array de filtros
        if (!is_array($filters)) {
            $date = $filters;
            $filters = ['date' => $date];
        }

        $query = "SELECT o.*, c.name as user_name, c.phone as user_phone, ch.name as channel_name, ch.icon as channel_icon, d.name as delivery_name
                  FROM " . $this->table . " o
                  LEFT JOIN clients c ON o.client_id = c.id 
                  LEFT JOIN order_channels ch ON o.channel_id = ch.id
                  LEFT JOIN users d ON o.delivery_user_id = d.id
                  WHERE 1=1";

        if (!empty($filters['date']) && $filters['date'] !== '') {
            $query .= " AND DATE(o.created_at) = :date";
        }
        if (!empty($filters['status']) && $filters['status'] !== '') {
            $query .= " AND o.status = :status";
        }
        if (!empty($filters['delivery_type']) && $filters['delivery_type'] !== '') {
            $query .= " AND o.delivery_type = :delivery_type";
        }
        if (!empty($filters['client_name']) && trim($filters['client_name']) !== '') {
            $query .= " AND c.name LIKE :client_name";
        }
        if (!empty($filters['client_id'])) {
            $query .= " AND o.client_id = :client_id";
        }
        if (!empty($filters['month'])) {
            $query .= " AND MONTH(o.created_at) = :month";
        }
        if (!empty($filters['year'])) {
            $query .= " AND YEAR(o.created_at) = :year";
        }
        if (!empty($filters['delivery_user_id'])) {
            $query .= " AND o.delivery_user_id = :delivery_user_id";
        }

        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);

        if (!empty($filters['date']) && $filters['date'] !== '') {
            $stmt->bindValue(':date', $filters['date']);
        }
        if (!empty($filters['status']) && $filters['status'] !== '') {
            $stmt->bindValue(':status', $filters['status']);
        }
        if (!empty($filters['delivery_type']) && $filters['delivery_type'] !== '') {
            $stmt->bindValue(':delivery_type', $filters['delivery_type']);
        }
        if (!empty($filters['client_name']) && trim($filters['client_name']) !== '') {
            $search = "%" . trim($filters['client_name']) . "%";
            $stmt->bindValue(':client_name', $search);
        }
        if (!empty($filters['client_id'])) {
            $stmt->bindValue(':client_id', $filters['client_id']);
        }
        if (!empty($filters['month'])) {
            $stmt->bindValue(':month', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $stmt->bindValue(':year', $filters['year']);
        }
        if (!empty($filters['delivery_user_id'])) {
            $stmt->bindValue(':delivery_user_id', $filters['delivery_user_id']);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Asigna un repartidor a un pedido y actualiza el estado
     */
    public function assignDelivery($orderId, $deliveryUserId) {
        $query = "UPDATE " . $this->table . " 
                  SET delivery_user_id = :delivery_id, status = 'shipped' 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':delivery_id' => $deliveryUserId,
            ':id' => $orderId
        ]);
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

    /**
     * Obtiene los meses y años únicos en los que el cliente realizó pedidos
     */
    public function getUniqueMonthsByClient($client_id) {
        $query = "SELECT DISTINCT YEAR(created_at) as year, MONTH(created_at) as month 
                  FROM " . $this->table . " 
                  WHERE client_id = :client_id 
                  ORDER BY year DESC, month DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_id', $client_id);
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
     * Obtiene los contadores de pedidos por estado para una fecha específica
     */
    public function getStatusCountsByDate($date) {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table . " WHERE DATE(created_at) = :date GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Obtiene estadísticas resumidas para el dashboard
     */
    public function getDashboardStats() {
        $today = date('Y-m-d');
        
        // Pedidos Pendientes totales (no solo de hoy)
        $q1 = "SELECT COUNT(*) FROM " . $this->table . " WHERE status = 'pending'";
        $pending = $this->conn->query($q1)->fetchColumn();

        // Ingresos de hoy (excluyendo cancelados)
        $q2 = "SELECT SUM(total) FROM " . $this->table . " WHERE DATE(created_at) = :today AND status != 'cancelled'";
        $stmt2 = $this->conn->prepare($q2);
        $stmt2->execute([':today' => $today]);
        $income = $stmt2->fetchColumn() ?: 0;

        // Platos/Items vendidos hoy
        $q3 = "SELECT SUM(quantity) FROM orders_items oi JOIN orders o ON oi.order_id = o.id WHERE DATE(o.created_at) = :today AND o.status != 'cancelled'";
        $stmt3 = $this->conn->prepare($q3);
        $stmt3->execute([':today' => $today]);
        $sold = $stmt3->fetchColumn() ?: 0;

        return [
            'pending_orders' => $pending,
            'income_today' => $income,
            'dishes_sold' => $sold
        ];
    }
}
?>