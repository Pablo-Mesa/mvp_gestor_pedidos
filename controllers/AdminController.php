<?php
date_default_timezone_set('America/Asuncion');

class AdminController {

    public function __construct() {
        // 1. Verificación de Seguridad: Solo Admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
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
        $stmtCompleted = $orderModel->readAll(['date' => $today, 'status' => 'completado']);
        $completed_orders = $stmtCompleted ? count($stmtCompleted->fetchAll(PDO::FETCH_ASSOC)) : 0;

        // Obtener cantidad de pedidos pendientes hoy para el gráfico
        $stmtPendingToday = $orderModel->readAll(['date' => $today, 'status' => 'pending']);
        $pending_orders_today = $stmtPendingToday ? count($stmtPendingToday->fetchAll(PDO::FETCH_ASSOC)) : 0;

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
            'pedidos_pendientes_hoy' => $pending_orders_today
        ];

        // Definimos qué vista interna queremos cargar
        $content_view = '../views/admin/dashboard.php';
        
        // Cargamos el Layout Principal (que incluirá a $content_view)
        require_once '../views/layouts/admin_layout.php';
    }
}
?>