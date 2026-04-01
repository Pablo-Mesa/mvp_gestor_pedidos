<?php
require_once '../models/Order.php';

class DeliveryController {

    public function __construct() {
        // Seguridad: Solo usuarios con rol 'delivery' o 'admin' pueden entrar
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['delivery', 'admin'])) {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $orderModel = new Order();
        
        // Filtramos pedidos que sean para delivery y que estén 'listos' o 'en camino'
        // Nota: Asumimos que el modelo Order soporta estos filtros en su método readAll
        $filters = [
            'delivery_type' => 'delivery',
            'date' => date('Y-m-d'),
            'delivery_user_id' => $_SESSION['user_id'] // Filtro crucial: solo lo asignado a MI
        ];
        
        $stmt = $orderModel->readAll($filters);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupamos por estado para que el repartidor sepa qué tiene pendiente y qué está entregando
        $pendingOrders = array_filter($orders, fn($o) => $o['status'] === 'ready');
        $activeOrders = array_filter($orders, fn($o) => $o['status'] === 'shipped');

        $view_title = "Panel de Logística";
        $content_view = '../views/delivery/index.php';
        
        // Usamos el nuevo layout independiente de logística
        require_once '../views/layouts/delivery_layout.php';
    }
}
?>