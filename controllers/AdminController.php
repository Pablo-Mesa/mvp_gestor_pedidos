<?php
date_default_timezone_set('America/Asuncion');
require_once '../config/db.php';

class AdminController {

    public function __construct() {
        // 1. Si es un cliente logueado (tabla 'clients'), lo mandamos a la web pública
        if (isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        // 2. Si no hay sesión de staff (tabla 'users'), al login
        if (!isset($_SESSION['user_role'])) {
            header('Location: ?route=login');
            exit;
        }

        // 3. Bloqueo estricto: Solo el rol 'admin' puede permanecer en este controlador.
        // Si es repartidor, lo enviamos a su panel. Si es cualquier otro rol futuro no admin, al login.
        if ($_SESSION['user_role'] !== 'admin') {
            if ($_SESSION['user_role'] === 'delivery') {
                header('Location: ?route=delivery');
            } else {
                header('Location: ?route=login');
            }
            exit;
        }
    }

    public function dashboard() {
        require_once '../models/Order.php';
        require_once '../models/DailyMenu.php';
        
        // Obtener datos reales de la DB
        $orderModel = new Order();
        $stats = $orderModel->getDashboardStats();
        $today = date('Y-m-d');
        
        // Obtener cantidad de pedidos completados hoy para el gráfico
        // Sincronizado con el estado 'completed' que usa logística
        $stmtCompleted = $orderModel->readAll(['date' => $today, 'status' => 'completed']);
        $completed_orders = $stmtCompleted ? count($stmtCompleted->fetchAll(PDO::FETCH_ASSOC)) : 0;

        // Obtener menú de hoy para verificar stock
        $dailyMenuModel = new DailyMenu();
        $menus = $dailyMenuModel->readForDate($today)->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrar productos con stock bajo (ej. 5 o menos, si tienen stock definido)
        $low_stock_items = array_filter($menus, function($item) {
            return !is_null($item['daily_stock']) && $item['daily_stock'] <= 5;
        });

        $data = [
            'title' => 'Resumen del Día',
            'pedidos_pendientes' => $stats['pending_orders'],
            'ingresos_hoy' => $stats['income_today'],
            'platos_vendidos' => $stats['dishes_sold'],
            'low_stock_items' => $low_stock_items,
            'pedidos_completados_hoy' => $completed_orders,
            'pedidos_pendientes_hoy' => $stats['pending_orders']
        ];

        // Definimos qué vista interna queremos cargar
        $content_view = '../views/admin/dashboard.php';
        
        // Cargamos el Layout Principal (que incluirá a $content_view)
        require_once '../views/layouts/admin_layout.php';
    }

    public function pos() {
        require_once '../models/Product.php';
        require_once '../models/Category.php';
        require_once '../models/Client.php';

        $productModel = new Product();
        $products = $productModel->readAllActive()->fetchAll(PDO::FETCH_ASSOC);

        $categoryModel = new Category();
        $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        $data = ['title' => 'Recepción de Pedidos'];
        
        $content_view = '../views/admin/pos/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra el historial global de asistencias de los repartidores
     */
    public function deliveryAssists() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $assists = [];
        $error_message = null;

        try {
            $db = (new Database())->getConnection();
            if (!$db) throw new Exception("Error de conexión a la base de datos");

            $query = "SELECT a.*, u.name as delivery_name 
                      FROM delivery_checkins a 
                      JOIN users u ON a.user_id = u.id 
                      WHERE DATE(a.checkin_time) = :date 
                      ORDER BY a.checkin_time DESC";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $assists = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error_message = "Error en el sistema de logística: " . $e->getMessage();
            $assists = [];
        }

        $content_view = '../views/admin/delivery/assists.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra el historial de ventas (Facturación / Tickets)
     * Recupera datos de las tablas pos_ventas_cabecera y sus relaciones
     */
    public function salesHistory() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $sales = [];

        try {
            $db = (new Database())->getConnection();
            
            $query = "SELECT v.*, c.name as client_name, u.name as cashier_name, o.id as order_id_display
                      FROM pos_ventas_cabecera v
                      LEFT JOIN clients c ON v.cliente_id = c.id
                      LEFT JOIN users u ON v.user_id = u.id
                      LEFT JOIN orders o ON v.order_id = o.id
                      WHERE DATE(v.fecha_hora) = :date
                      ORDER BY v.fecha_hora DESC";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en salesHistory: " . $e->getMessage());
        }

        $content_view = '../views/admin/sales/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra el desglose de ingresos por método de pago (Caja)
     */
    public function paymentsReport() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $payments = [];
        $summary = [
            'efectivo' => 0,
            'pos' => 0,
            'transferencia' => 0,
            'qr' => 0,
            'total' => 0
        ];

        try {
            $db = (new Database())->getConnection();
            
            $query = "SELECT pd.*, p.fecha_pago, v.nro_factura, v.order_id as order_ref
                      FROM pagos_detalles pd
                      JOIN pagos p ON pd.pago_id = p.id
                      JOIN pos_ventas_cabecera v ON p.venta_id = v.id
                      WHERE DATE(p.fecha_pago) = :date
                      ORDER BY p.fecha_pago DESC";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($payments as $pay) {
                $summary[$pay['metodo_pago']] += $pay['monto'];
                $summary['total'] += $pay['monto'];
            }
        } catch (Exception $e) {
            error_log("Error en paymentsReport: " . $e->getMessage());
        }

        $content_view = '../views/admin/sales/payments.php';
        require_once '../views/layouts/admin_layout.php';
    }
}
?>