<?php
require_once '../models/Order.php';

class OrderController {

    public function __construct() {
        // Solo administradores
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $order = new Order();
        $stmt = $order->readAll();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }
}
?>