<?php
require_once '../models/Order.php';

class DeliveryController {

    public function __construct() {
        // Si es un cliente, mandarlo a su home
        if (isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        // Seguridad: Verificar si hay sesión iniciada
        if (!isset($_SESSION['user_role'])) {
            header('Location: ?route=login');
            exit;
        }

        // Redirección inteligente: Si es Admin, enviarlo a su Dashboard en lugar de login
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: ?route=admin');
            exit;
        }

        // Si por alguna razón tiene otro rol que no sea delivery, fuera de aquí
        if ($_SESSION['user_role'] !== 'delivery') {
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
        $completedOrders = array_filter($orders, fn($o) => $o['status'] === 'completed');

        $view_title = "Panel de Logística";
        $content_view = '../views/delivery/index.php';
        
        // Usamos el nuevo layout independiente de logística
        require_once '../views/layouts/delivery_layout.php';
    }
}
?>