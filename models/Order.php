<?php
date_default_timezone_set('America/Asuncion');

require_once '../config/db.php';

class Order {
    private $conn;
    private $table = 'orders';

    // Propiedades de la Orden
    public $id;
    public $user_id;     // Quien registra el pedido (Staff)
    public $client_id;
    public $channel_id;
    public $total;
    public $status;
    public $delivery_user_id; // ID del repartidor asignado
    public $observation; // Nueva propiedad
    public $billing_name;
    public $billing_ruc;
    public $payment_method;
    public $delivery_type;
    public $client_location_id; // Relación con envíos
    public $delivery_rate_id;   // Relación con envíos
    public $delivery_address;   // Propiedad para snapshot (no persistente en orders)
    public $delivery_lat;       // Propiedad para snapshot (no persistente en orders)
    public $delivery_lng;       // Propiedad para snapshot (no persistente en orders)
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
            // 0. Validación de integridad para envíos
            if ($this->delivery_type === 'delivery') {
                if (empty($this->delivery_address) || is_null($this->delivery_lat) || is_null($this->delivery_lng)) {
                    throw new Exception("Error de integridad: El pedido es delivery pero faltan datos de ubicación (Dirección/Coordenadas).");
                }
            }

            // Iniciar transacción: Todo se guarda o nada se guarda
            $this->conn->beginTransaction();

            // 1. Insertar Cabecera (Order)
            $query = "INSERT INTO " . $this->table . " 
                      (user_id, client_id, channel_id, total, status, observation, billing_name, billing_ruc, payment_method, delivery_type) 
                      VALUES (:user_id, :client_id, :channel_id, :total, :status, :observation, :billing_name, :billing_ruc, :payment_method, :delivery_type)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind params
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':client_id', $this->client_id);
            $stmt->bindParam(':channel_id', $this->channel_id);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':observation', $this->observation);
            $stmt->bindParam(':billing_name', $this->billing_name);
            $stmt->bindParam(':billing_ruc', $this->billing_ruc);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->bindParam(':delivery_type', $this->delivery_type);

            $stmt->execute();
            $this->id = $this->conn->lastInsertId();

            // 2. Si es delivery, insertar en order_shipments
            if ($this->delivery_type === 'delivery') {
                $queryShip = "INSERT INTO order_shipments 
                              (order_id, client_location_id, delivery_rate_id, address_snapshot, lat_snapshot, lng_snapshot, delivery_user_id) 
                              VALUES (:order_id, :location_id, :rate_id, :address, :lat, :lng, :delivery_user_id)";
                $stmtShip = $this->conn->prepare($queryShip);
                
                $stmtShip->bindValue(':order_id', $this->id);
                $stmtShip->bindValue(':location_id', $this->client_location_id);
                $stmtShip->bindValue(':rate_id', $this->delivery_rate_id);
                $stmtShip->bindValue(':address', $this->delivery_address); // Propiedad temporal para snapshot
                $stmtShip->bindValue(':lat', $this->delivery_lat);
                $stmtShip->bindValue(':lng', $this->delivery_lng);
                $stmtShip->bindValue(':delivery_user_id', $this->delivery_user_id);
                $stmtShip->execute();
            }

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

        $query = "SELECT o.*, c.name as user_name, c.phone as user_phone, ch.name as channel_name, ch.icon as channel_icon, 
                         s.address_snapshot as delivery_address, s.lat_snapshot as delivery_lat, s.lng_snapshot as delivery_lng, 
                         s.delivery_user_id, d.name as delivery_name, drd.price as delivery_cost, drd.km_from, drd.km_to,
                         (SELECT COALESCE(SUM(p.monto_total), 0) FROM pagos p JOIN pos_ventas_cabecera v ON p.venta_id = v.id WHERE v.order_id = o.id AND v.estado = 1) as total_paid,
                         (SELECT COUNT(*) FROM pos_ventas_cabecera WHERE order_id = o.id) as has_invoice
                  FROM " . $this->table . " o
                  LEFT JOIN clients c ON o.client_id = c.id 
                  LEFT JOIN order_channels ch ON o.channel_id = ch.id
                  LEFT JOIN order_shipments s ON o.id = s.order_id
                  LEFT JOIN delivery_rate_details drd ON s.delivery_rate_id = drd.id
                  LEFT JOIN users d ON s.delivery_user_id = d.id
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
            $query .= " AND s.delivery_user_id = :delivery_user_id";
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
    public function assignDelivery($orderId, $deliveryUserId, $staffId = null) {
        // Actualizamos el repartidor en la tabla de envíos
        $query = "UPDATE order_shipments
                  SET delivery_user_id = :delivery_id
                  WHERE order_id = :id";
        $stmt = $this->conn->prepare($query);
        $res = $stmt->execute([
            ':delivery_id' => $deliveryUserId,
            ':id' => $orderId
        ]);

        // Si el pedido no tiene un usuario asignado (vía web), lo vinculamos al staff que asigna el delivery
        if ($res && $staffId) {
            $queryOrder = "UPDATE " . $this->table . " SET user_id = IFNULL(user_id, :staff_id) WHERE id = :id";
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([':staff_id' => $staffId, ':id' => $orderId]);
        }

        return $res;
    }

    /**
     * Obtiene un solo pedido por ID
     */
    public function readOne() {
        $query = "SELECT o.*, c.name as user_name, c.email as user_email, c.phone as user_phone,
                         s.address_snapshot as delivery_address, s.lat_snapshot as delivery_lat, s.lng_snapshot as delivery_lng,
                         s.delivery_user_id, st.name as staff_name, drd.price as delivery_cost,
                         (SELECT COALESCE(SUM(p.monto_total), 0) FROM pagos p JOIN pos_ventas_cabecera v ON p.venta_id = v.id WHERE v.order_id = o.id AND v.estado = 1) as total_paid,
                         (SELECT COUNT(*) FROM pos_ventas_cabecera WHERE order_id = o.id) as has_invoice
                  FROM " . $this->table . " o
                  JOIN clients c ON o.client_id = c.id
                  LEFT JOIN order_shipments s ON o.id = s.order_id
                  LEFT JOIN delivery_rate_details drd ON s.delivery_rate_id = drd.id
                  LEFT JOIN users st ON o.user_id = st.id
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
        try {
            // Solo iniciamos transacción si no hay una activa
            $isNested = $this->conn->inTransaction();
            if (!$isNested) $this->conn->beginTransaction();

            // 1. Actualizar el estado principal del pedido
            // Usamos IFNULL para que, si el user_id está vacío (pedido web), se asigne al staff que opera ahora
            $query = "UPDATE " . $this->table . " 
                      SET status = :status, user_id = IFNULL(user_id, :staff_id) 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':status' => $this->status,
                ':staff_id' => $this->user_id,
                ':id' => $this->id
            ]);

            // 2. Lógica de Tiempos Logísticos en order_shipments
            if ($this->status === 'shipped') {
                // Marcamos la salida del repartidor
                $queryShip = "UPDATE order_shipments SET shipped_at = CURRENT_TIMESTAMP WHERE order_id = :id AND shipped_at IS NULL";
                $stmtShip = $this->conn->prepare($queryShip);
                $stmtShip->execute([':id' => $this->id]);
            } elseif ($this->status === 'rejected' || $this->status === 'cancelled') {
                // --- LÓGICA DE COMPENSACIÓN FINANCIERA ---
                
                // 1. Anular Factura/Ticket si existe
                $qAnular = "UPDATE pos_ventas_cabecera SET estado = 0 WHERE order_id = :id";
                $stAnular = $this->conn->prepare($qAnular);
                $stAnular->execute([':id' => $this->id]);

                // 2. Registrar Egreso en Caja de compensación
                $orderData = $this->readOne();
                $cashModel = new CashRegister();
                $activeSession = $cashModel->getActiveSession($this->user_id);
                
                if ($activeSession) {
                    // Verificamos si realmente hubo un ingreso previo en cash_movements para este pedido
                    $qCheckMov = "SELECT SUM(amount) FROM cash_movements WHERE reference_id = :oid AND source = 'order' AND type = 'ingress' AND cash_register_id = :rid";
                    $stCheckMov = $this->conn->prepare($qCheckMov);
                    $stCheckMov->execute([':oid' => $this->id, ':rid' => $activeSession['id']]);
                    $montoIngresado = $stCheckMov->fetchColumn() ?: 0;

                    if ($montoIngresado > 0) {
                        $qRev = "INSERT INTO cash_movements 
                                 (cash_register_id, amount, type, description, source, reference_id, created_at) 
                                 VALUES (:rid, :amt, 'egress', :desc, 'order', :ref, CURRENT_TIMESTAMP)";
                        $stRev = $this->conn->prepare($qRev);
                        $stRev->execute([
                            ':rid'  => $activeSession['id'],
                            ':amt'  => $montoIngresado,
                            ':desc' => "ANULACIÓN AUTOMÁTICA: Pedido #{$this->id} " . ($this->status === 'rejected' ? 'Rechazado' : 'Cancelado'),
                            ':ref'  => $this->id
                        ]);
                    }
                }
            } elseif ($this->status === 'completed') {
                // Marcamos la entrega efectiva al cliente
                $queryShip = "UPDATE order_shipments SET delivered_at = CURRENT_TIMESTAMP WHERE order_id = :id AND delivered_at IS NULL";
                $stmtShip = $this->conn->prepare($queryShip);
                $stmtShip->execute([':id' => $this->id]);
            }

            if (!$isNested) $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if (!$isNested && $this->conn->inTransaction()) $this->conn->rollBack();
            $this->error = $e->getMessage();
            if ($isNested) throw $e; // Re-lanzar para que el padre la capture
            return false;
        }
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
     * Obtiene pedidos que están listos para ser facturados pero aún no tienen factura.
     * Se consideran pedidos confirmados, en cocina o enviados.
     */
    public function getOrdersAwaitingInvoice() {
        $query = "SELECT o.id, o.created_at, o.total, o.delivery_type, IFNULL(c.name, 'Cliente Ocasional') as client_name 
                  FROM " . $this->table . " o
                  LEFT JOIN clients c ON o.client_id = c.id
                  LEFT JOIN pos_ventas_cabecera v ON o.id = v.order_id
                  WHERE v.id IS NULL 
                  AND o.status IN ('pending', 'confirmed', 'shipped', 'completed')
                  ORDER BY o.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un pedido ya tiene un registro de venta (Factura/Ticket)
     */
    public function hasInvoice($orderId) {
        $query = "SELECT id FROM pos_ventas_cabecera WHERE order_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * Transforma un pedido en una Venta Legal y registra el pago.
     * Soporta pagos mixtos (Efectivo, Tarjeta, etc.)
     * @param array|null $payments Detalles de los pagos recibidos. Si es null, no registra pago.
     * @param int|null $cash_register_id ID de la sesión de caja activa
     */
    public function finalizeSale($payments = [], $cash_register_id = null) {
        try {
            // Solo iniciamos transacción si no hay una activa
            $isNested = $this->conn->inTransaction();
            if (!$isNested) $this->conn->beginTransaction();

            // 1. Verificar si ya existe la cabecera (evitar duplicar factura al cobrar después)
            $qCheck = "SELECT id FROM pos_ventas_cabecera WHERE order_id = :oid LIMIT 1";
            $stCheck = $this->conn->prepare($qCheck);
            $stCheck->execute([':oid' => $this->id]);
            $existingVenta = $stCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingVenta) {
                $ventaId = $existingVenta['id'];
                
                // Validar si ya existe un pago para esta venta para evitar duplicidad
                $qCheckPago = "SELECT id FROM pagos WHERE venta_id = :vid LIMIT 1";
                $stCheckPago = $this->conn->prepare($qCheckPago);
                $stCheckPago->execute([':vid' => $ventaId]);
                if ($stCheckPago->fetch()) {
                    throw new Exception("Integridad: Esta venta ya tiene un pago registrado. No se puede duplicar.");
                }
                $order = $this->readOne();
            } else {
                // Validar que no se intente crear una factura sin intención de pago
                if ($payments === null) {
                    throw new Exception("Integridad: No se permite crear facturas sin un registro de pago asociado.");
                }

                $order = $this->readOne();
                $details = $this->readDetails();

                // 1. Calcular Impuestos (IVA 10% para Gastronomía en Py)
                $total = $order['total'];
                $iva10 = round($total / 11, 2);
                $grav10 = $total - $iva10;

                // 2. Insertar Cabecera de Venta
                $qVenta = "INSERT INTO pos_ventas_cabecera 
                           (order_id, cliente_id, user_id, nro_factura, fecha_hora, gravada_10, iva_10, total_venta, estado) 
                           VALUES (:oid, :cid, :uid, :nro, CURRENT_TIMESTAMP, :g10, :i10, :total, 1)";
                $stVenta = $this->conn->prepare($qVenta);
                $stVenta->execute([
                    ':oid'   => $order['id'],
                    ':cid'   => $order['client_id'],
                    ':uid'   => $this->user_id,
                    ':nro'   => 'PROV-' . str_pad($order['id'], 7, '0', STR_PAD_LEFT),
                    ':g10'   => $grav10,
                    ':i10'   => $iva10,
                    ':total' => $total
                ]);
                $ventaId = $this->conn->lastInsertId();

                // 3. Insertar Detalles de Venta
                $qDet = "INSERT INTO pos_ventas_detalle (venta_id, producto_id, cantidad, precio_unitario_venta, subtotal) 
                         VALUES (:vid, :pid, :cant, :pre, :sub)";
                $stDet = $this->conn->prepare($qDet);
                foreach ($details as $item) {
                    $stDet->execute([
                        ':vid'  => $ventaId,
                        ':pid'  => $item['product_id'],
                        ':cant' => $item['quantity'],
                        ':pre'  => $item['price'],
                        ':sub'  => $item['price'] * $item['quantity']
                    ]);
                }
            }

            // --- PROCESAMIENTO DE PAGO (OPCIONAL) ---
            if ($payments !== null) { // Ahora este bloque es prácticamente obligatorio para nuevas facturas
                // Si se pasan vacíos [], usamos el método del pedido
                if (empty($payments)) {
                    $payments[] = [
                        'metodo' => $order['payment_method'],
                        'monto' => $order['total'],
                        'referencia' => 'Pago automático'
                    ];
                }

                $total = $order['total'];
                
                // 4. Registrar Pago
                $qPago = "INSERT INTO pagos (venta_id, monto_total) VALUES (:vid, :tot)";
                $stPago = $this->conn->prepare($qPago);
                $stPago->execute([':vid' => $ventaId, ':tot' => $total]);
                $pagoId = $this->conn->lastInsertId();

                // 5. Detalle de los pagos mixtos
                $qPDet = "INSERT INTO pagos_detalles (pago_id, metodo_pago, monto, referencia) VALUES (:pid, :met, :mon, :ref)";
                $stPDet = $this->conn->prepare($qPDet);
                
                foreach ($payments as $pay) {
                    if ($pay['monto'] <= 0) continue;
                    $stPDet->execute([
                        ':pid' => $pagoId,
                        ':met' => $pay['metodo'],
                        ':mon' => $pay['monto'],
                        ':ref' => $pay['referencia'] ?? null
                    ]);
                }

                // 6. Registrar movimiento en la Caja si se proporcionó una sesión
                if ($cash_register_id) {
                    $qMov = "INSERT INTO cash_movements 
                             (cash_register_id, amount, type, description, source, reference_id, created_at) 
                             VALUES (:rid, :amt, 'ingress', :desc, 'order', :ref, CURRENT_TIMESTAMP)";
                    $stMov = $this->conn->prepare($qMov);
                    $stMov->execute([
                        ':rid'  => $cash_register_id,
                        ':amt'  => $total,
                        ':desc' => "Venta Pedido #" . $order['id'] . " (Factura " . 'PROV-' . str_pad($order['id'], 7, '0', STR_PAD_LEFT) . ")",
                        ':ref'  => $order['id']
                    ]);
                }
            }

            // 7. Actualizar el estado: Si es delivery y no se ha entregado aún, queda como "paid"
            // para que logística sepa que debe despacharlo. Si es local/retiro, se completa.
            $orderData = $this->readOne();
            if ($orderData['delivery_type'] === 'delivery' && !in_array($orderData['status'], ['completed', 'shipped'])) {
                $this->status = 'paid';
            } else {
                $this->status = 'completed';
            }
            $this->updateStatus();

            if (!$isNested) $this->conn->commit();
            return $ventaId;
        } catch (Exception $e) {
            if (!$isNested && $this->conn->inTransaction()) $this->conn->rollBack();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Obtiene estadísticas resumidas para el dashboard
     */
    public function getDashboardStats($date = null) {
        $target_date = $date ?: date('Y-m-d');

        // Pedidos Pendientes totales (no solo de hoy)
        $q1 = "SELECT COUNT(*) FROM " . $this->table . " WHERE status = 'pending' AND DATE(created_at) = :target_date";
        $stmt1 = $this->conn->prepare($q1);
        $stmt1->execute([':target_date' => $target_date]);
        $pending = $stmt1->fetchColumn();

        // Ingresos de hoy (excluyendo cancelados y rechazados)
        $q2 = "SELECT SUM(total) FROM " . $this->table . " WHERE DATE(created_at) = :target_date AND status NOT IN ('cancelled', 'rejected')";
        $stmt2 = $this->conn->prepare($q2);
        $stmt2->execute([':target_date' => $target_date]);
        $income = $stmt2->fetchColumn() ?: 0;

        // Platos/Items vendidos hoy (excluyendo anulados)
        $q3 = "SELECT SUM(quantity) FROM orders_items oi JOIN orders o ON oi.order_id = o.id WHERE DATE(o.created_at) = :target_date AND o.status NOT IN ('cancelled', 'rejected')";
        $stmt3 = $this->conn->prepare($q3);
        $stmt3->execute([':target_date' => $target_date]);
        $sold = $stmt3->fetchColumn() ?: 0;

        return [
            'pending_orders' => $pending,
            'income_today' => $income,
            'dishes_sold' => $sold
        ];
    }

    /**
     * Obtiene estadísticas agregadas y desglose diario para un mes específico
     */
    public function getMonthlyStats($year, $month) {
        // Totales del mes
        $q = "SELECT 
                COUNT(id) as total_orders,
                SUM(total) as total_income,
                (SELECT SUM(quantity) FROM orders_items oi JOIN orders o2 ON oi.order_id = o2.id 
                 WHERE o2.status NOT IN ('cancelled', 'rejected') AND YEAR(o2.created_at) = :y AND MONTH(o2.created_at) = :m) as dishes_sold
              FROM " . $this->table . " 
              WHERE status NOT IN ('cancelled', 'rejected') AND YEAR(created_at) = :y AND MONTH(created_at) = :m";
        
        $stmt = $this->conn->prepare($q);
        $stmt->execute([':y' => $year, ':m' => $month]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        // Desglose diario para el gráfico de barras
        $qChart = "SELECT DAY(created_at) as day, SUM(total) as income 
                   FROM " . $this->table . " 
                   WHERE status NOT IN ('cancelled', 'rejected') AND YEAR(created_at) = :y AND MONTH(created_at) = :m
                   GROUP BY DAY(created_at)
                   ORDER BY DAY(created_at) ASC";
        $stmtChart = $this->conn->prepare($qChart);
        $stmtChart->execute([':y' => $year, ':m' => $month]);
        $dailyData = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

        return [
            'income' => $totals['total_income'] ?: 0,
            'orders' => $totals['total_orders'] ?: 0,
            'dishes' => $totals['dishes_sold'] ?: 0,
            'chart'  => $dailyData
        ];
    }
}
?>