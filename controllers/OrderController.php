<?php
// Establecer zona horaria de Paraguay (UTC-3 permanente según Ley 7334/24)
date_default_timezone_set('America/Asuncion');

require_once '../models/Order.php';
require_once '../models/User.php';

class OrderController {

    // --- Métodos de ADMINISTRADOR ---

    public function index() {
        // Verificar seguridad admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }

        $orderModel = new Order();
        
        // Por defecto, filtrar por la fecha actual si no se envió una búsqueda específica
        if (!isset($_GET['date'])) {
            $_GET['date'] = date('Y-m-d');
        }

        /** 
         * Pasamos $_GET al modelo. 
         * Este array contiene 'date', 'delivery_type' y 'client_name' capturados 
         * desde el formulario de filtros en la vista.
         */
        $stmt = $orderModel->readAll($_GET);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los contadores de estado para la fecha actual del filtro
        $statusCounts = $orderModel->getStatusCountsByDate($_GET['date']);

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Endpoint AJAX que devuelve los pedidos en formato JSON para auto-actualización.
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $orderModel = new Order();

        // Mantener el filtro por defecto de hoy para la sincronización automática
        if (!isset($_GET['date'])) {
            $_GET['date'] = date('Y-m-d');
        }

        $stmt = $orderModel->readAll($_GET);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formatear datos para el frontend (fechas, nombres seguros y moneda)
        foreach ($orders as &$order) {
            $order['user_name'] = htmlspecialchars($order['user_name']);
            $order['channel_name'] = htmlspecialchars($order['channel_name'] ?? 'Web');
            $order['channel_icon'] = htmlspecialchars($order['channel_icon'] ?? 'fas fa-globe');
            $order['formatted_date'] = date('d/m/Y H:i', strtotime($order['created_at']));
            $order['formatted_total'] = number_format($order['total'], 0, ',', '.');
        }

        echo json_encode($orders);
        exit;
    }

    public function show() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ?route=login'); exit; }

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=orders'); exit; }

        $orderModel = new Order();
        $orderModel->id = $id;
        $order = $orderModel->readOne();
        $details = $orderModel->readDetails();

        // Si el pedido es delivery, cargamos los repartidores para la asignación
        $deliveryUsers = [];
        if ($order['delivery_type'] === 'delivery') {
            $userModel = new User();
            $deliveryUsers = $userModel->getDeliveryUsers();
        }

        $content_view = '../views/admin/orders/show.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function ticket() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ?route=login'); exit; }

        $id = $_GET['id'] ?? null;
        $format = $_GET['format'] ?? '80mm'; // Capturamos el formato (por defecto 80mm)
        if (!$id) { header('Location: ?route=orders'); exit; }

        $orderModel = new Order();
        $orderModel->id = $id;
        $order = $orderModel->readOne();
        $details = $orderModel->readDetails();

        require_once '../views/admin/orders/ticket.php';
    }

    public function updateStatus() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ?route=login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = new Order();
            $order->id = $_POST['id'];
            $order->status = $_POST['status'];
            $success = $order->updateStatus();

            // Si es una petición AJAX (como al imprimir), respondemos JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit;
            }

            header('Location: ?route=orders_show&id=' . $order->id);
        }
    }

    /**
     * Procesa la asignación de un repartidor vía AJAX
     */
    public function assignDelivery() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;
        $deliveryId = $_POST['delivery_id'] ?? null;

        $orderModel = new Order();
        $success = $orderModel->assignDelivery($orderId, $deliveryId);
        echo json_encode(['success' => $success]);
    }

    // --- Métodos de CLIENTE ---

    public function checkout() {
        // 1. Verificar autenticación (Seguridad)
        if (!isset($_SESSION['client_id'])) {
            // Si no está logueado, redirigir al home forzando el modal de login
            header('Location: ?route=home&error=login_required');
            exit;
        }

        // 2. Preparar la vista
        // Aquí podríamos cargar datos del cliente si quisiéramos pre-llenar campos
        // $clientModel = new Client(); ...

        $content_view = '../views/home/checkout.php';
        require_once '../views/layouts/client_layout.php';
    }

    public function store() {
        header('Content-Type: application/json');
        
        // Verificar sesión
        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }

        // Obtener JSON del body
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || empty($input['cart'])) {
            echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
            exit;
        }

        // Crear Orden
        $order = new Order();
        $order->client_id = $_SESSION['client_id'];
        $order->channel_id = 1; // 1 = Web
        $order->status = 'pending';
        $order->payment_method = $input['payment_method'] ?? 'efectivo';
        $order->delivery_type = $input['delivery_type'] ?? 'pickup';
        $order->observation = $input['observation'] ?? ''; // Guardamos la observación
        
        // Datos de delivery
        if ($order->delivery_type === 'delivery') {
            $order->delivery_address = $input['delivery_address'] ?? '';
            $order->delivery_lat = $input['delivery_lat'] ?? null;
            $order->delivery_lng = $input['delivery_lng'] ?? null;
        } else {
            // Si no es delivery, enviamos cadena vacía para evitar el error de integridad en la DB
            $order->delivery_address = ''; 
            $order->delivery_lat = null;
            $order->delivery_lng = null;
        }

        // Procesar Detalle (Carrito)
        $total = 0;
        foreach ($input['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
            $order->details[] = [
                'product_id' => (int)$item['id'], // Forzamos a entero para limpiar sufijos como "_half"
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }
        $order->total = $total;

        // Guardar en DB
        if ($order->create()) {
            echo json_encode(['success' => true, 'order_id' => $order->id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $order->error]);
        }
    }

    /**
     * Endpoint para guardar pedidos desde el POS (Admin/Mostrador)
     */
    public function posStore() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || empty($input['cart'])) {
            echo json_encode(['success' => false, 'message' => 'El pedido está vacío']);
            exit;
        }

        $order = new Order();
        // Si no se envía client_id, usamos 0 o un ID de sistema para "Cliente Ocasional"
        $order->client_id = $input['client_id'] ?? 1; 
        $order->channel_id = 2; // 2 = Mostrador
        $order->payment_method = $input['payment_method'] ?? 'efectivo';
        $order->delivery_type = 'local';
        $order->observation = $input['observation'] ?? '';
        $order->status = 'confirmed'; // Los pedidos de mostrador suelen estar confirmados de entrada
        $order->delivery_address = ''; // Evita error de integridad SQL
        $order->delivery_lat = null;
        $order->delivery_lng = null;

        $total = 0;
        foreach ($input['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
            $order->details[] = [
                'product_id' => (int)$item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }
        $order->total = $total;

        if ($order->create()) {
            echo json_encode([
                'success' => true, 
                'order_id' => $order->id,
                'message' => 'Venta registrada correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $order->error]);
        }
    }
}
?>