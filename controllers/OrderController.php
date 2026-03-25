<?php
require_once '../models/Order.php';

class OrderController {

    // --- Métodos de ADMINISTRADOR ---

    public function index() {
        // Verificar seguridad admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }

        $orderModel = new Order();
        $stmt = $orderModel->readAll();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function show() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ?route=login'); exit; }

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=orders'); exit; }

        $orderModel = new Order();
        $orderModel->id = $id;
        $order = $orderModel->readOne();
        $details = $orderModel->readDetails();

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
            $order->updateStatus();
            header('Location: ?route=orders_show&id=' . $order->id);
        }
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
        $order->payment_method = $input['payment_method'] ?? 'efectivo';
        $order->delivery_type = $input['delivery_type'] ?? 'pickup';
        $order->observation = $input['observation'] ?? ''; // Guardamos la observación
        
        // Datos de delivery
        if ($order->delivery_type === 'delivery') {
            $order->delivery_address = $input['delivery_address'] ?? '';
            $order->delivery_lat = $input['delivery_lat'] ?? null;
            $order->delivery_lng = $input['delivery_lng'] ?? null;
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
}
?>