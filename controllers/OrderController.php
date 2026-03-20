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
        // Obtener fecha de la URL o usar la actual por defecto
        $date = $_GET['date'] ?? date('Y-m-d');

        $order = new Order();
        $stmt = $order->readAll($date);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra el detalle de un pedido específico.
     */
    public function show() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $orderModel = new Order();
            $orderModel->id = $id;
            $order = $orderModel->readOne();
            $details = $orderModel->readDetails();
            
            $content_view = '../views/admin/orders/show.php';
            require_once '../views/layouts/admin_layout.php';
        }
    }

    /**
     * Actualiza el estado del pedido (ej. pendiente -> completado).
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = new Order();
            $order->id = $_POST['id'];
            $order->status = $_POST['status'];
            $order->updateStatus();
            header('Location: ?route=orders');
        }
    }
}
?>