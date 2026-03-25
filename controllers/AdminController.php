<?php
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
        
        // Obtener datos reales de la DB
        $orderModel = new Order();
        $stats = $orderModel->getDashboardStats();

        $data = [
            'title' => 'Resumen del Día',
            'pedidos_pendientes' => $stats['pending_orders'],
            'ingresos_hoy' => $stats['income_today'],
            'platos_vendidos' => $stats['dishes_sold']
        ];

        // Definimos qué vista interna queremos cargar
        $content_view = '../views/admin/dashboard.php';
        
        // Cargamos el Layout Principal (que incluirá a $content_view)
        require_once '../views/layouts/admin_layout.php';
    }
}
?>