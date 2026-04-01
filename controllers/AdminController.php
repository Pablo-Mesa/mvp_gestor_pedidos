<?php
date_default_timezone_set('America/Asuncion');

class AdminController {

    public function __construct() {
        // Si es un cliente logueado, lo mandamos a la web pública
        if (isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        // Si no hay sesión de staff, al login
        if (!isset($_SESSION['user_role'])) {
            header('Location: ?route=login');
            exit;
        }

        // Redirección inteligente: Si es repartidor, enviarlo a logística
        if ($_SESSION['user_role'] === 'delivery') {
            header('Location: ?route=delivery');
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

        $data = ['title' => 'Punto de Venta (POS)'];
        
        $content_view = '../views/admin/pos/index.php';
        require_once '../views/layouts/admin_layout.php';
    }
}
?>