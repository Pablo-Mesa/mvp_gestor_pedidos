<?php
require_once '../models/Order.php';
require_once '../models/DeliveryCheckin.php';
require_once '../models/Setting.php';

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
        
        $filters = [
            'delivery_type' => 'delivery',
            'delivery_user_id' => $_SESSION['user_id'] // Filtro crucial: solo lo asignado a MI
        ];
        
        $stmt = $orderModel->readAll($filters);
        $allOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $today = date('Y-m-d');
        $orders = array_filter($allOrders, function($o) use ($today) {
            // Vista operativa:
            // 1. Mostrar lo que no está terminado
            // 2. Mostrar lo terminado SOLO si es de hoy para que el repartidor vea sus logros recientes

            $isFinished = in_array($o['status'], ['completed', 'rejected', 'cancelled']);
            $isFromToday = date('Y-m-d', strtotime($o['created_at'])) === $today;
            
            return !$isFinished || $isFromToday;
        });

        // Agrupamos por estado para que el repartidor sepa qué tiene pendiente y qué está entregando
        $pendingOrders = array_filter($orders, fn($o) => $o['status'] === 'ready');
        $activeOrders = array_filter($orders, fn($o) => $o['status'] === 'shipped');
        $completedOrders = array_filter($orders, fn($o) => $o['status'] === 'completed');

        $view_title = "Panel de Logística";
        $content_view = '../views/delivery/index.php';
        
        // Usamos el nuevo layout independiente de logística
        require_once '../views/layouts/delivery_layout.php';
    }

    public function history() {
        $orderModel = new Order();
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        
        $filters = [
            'delivery_type' => 'delivery',
            'delivery_user_id' => $_SESSION['user_id'],
            'date' => $selectedDate
        ];

        $stmt = $orderModel->readAll($filters);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Inicializar resumen de cuentas
        $summary = [
            'cash' => 0,      // Lo cobrado en efectivo (debe rendir)
            'digital' => 0,   // Lo pagado por medios digitales
            'total' => 0,
            'count' => 0
        ];

        foreach ($orders as $o) {
            if ($o['status'] === 'completed') {
                $summary['count']++;
                $summary['total'] += $o['total'];
                if (strtolower($o['payment_method']) === 'efectivo') {
                    $summary['cash'] += $o['total'];
                } else {
                    $summary['digital'] += $o['total'];
                }
            }
        }

        $view_title = "Historial y Rendición";
        $content_view = '../views/delivery/history.php';
        require_once '../views/layouts/delivery_layout.php';
    }

    public function checkin() {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $view_title = "Marcar Llegada al Local";
        $content_view = '../views/delivery/checkin.php';
        require_once '../views/layouts/delivery_layout.php';
    }

    public function saveCheckinApi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['lat']) || empty($input['lng'])) {
            echo json_encode(['success' => false, 'message' => 'Coordenadas no recibidas']);
            exit;
        }

        try {
            $checkinModel = new DeliveryCheckin();
            $success = $checkinModel->create([
                'user_id'  => $_SESSION['user_id'],
                'lat'      => $input['lat'],
                'lng'      => $input['lng'],
                'distance' => $input['distance']
            ]);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Llegada registrada con éxito']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en base de datos']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
?>