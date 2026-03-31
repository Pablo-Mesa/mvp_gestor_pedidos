<?php
date_default_timezone_set('America/Asuncion');

require_once '../models/DailyMenu.php';
require_once '../models/Order.php';
require_once '../models/ProductReaction.php';

class HomeController {

    /*
    public function __construct() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['client_id'])) {
            header('Location: ?route=login');
            exit;
        }
    }
    */

    public function index() {
        // Obtener el menú de la fecha actual
        $date = date('Y-m-d');
        $clientId = $_SESSION['client_id'] ?? null;
        $dailyMenuModel = new DailyMenu();
        $daily_menus = $dailyMenuModel->readForDate($date, $clientId)->fetchAll(PDO::FETCH_ASSOC);

        // Cargar vistas
        $content_view = '../views/home/index.php';
        require_once '../views/layouts/client_layout.php';
    }

    public function storeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Recibir datos del formulario
            $products_input = $_POST['products'] ?? [];
            $products_half_input = $_POST['products_half'] ?? []; // Nuevo input para medias porciones
            $payment_method = $_POST['payment_method'] ?? 'cash';
            $delivery_type = $_POST['delivery_type'] ?? 'pickup';
            
            // 2. Obtener precios reales de la DB (Seguridad: no confiar en el front)
            $date = date('Y-m-d');
            $dailyMenuModel = new DailyMenu();
            $todays_menu = $dailyMenuModel->readForDate($date)->fetchAll(PDO::FETCH_ASSOC);
            
            $total = 0;
            $order_details = [];
            $items_found = false;

            // 3. Procesar items seleccionados
            foreach ($todays_menu as $menu_item) {
                $id = $menu_item['id'];
                
                // Si el usuario seleccionó cantidad > 0 para este plato
                if (isset($products_input[$id]) && $products_input[$id] > 0) {
                    $qty = (int)$products_input[$id];
                    $price = $menu_item['product_price'];
                    
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                    
                    $order_details[] = [
                        'daily_menu_id' => $id,
                        'quantity' => $qty,
                        'price' => $price
                    ];
                    $items_found = true;
                }
                
                // Lógica para MEDIO PLATO
                if (isset($products_half_input[$id]) && $products_half_input[$id] > 0 && !empty($menu_item['price_half'])) {
                    $qty = (int)$products_half_input[$id];
                    $price = $menu_item['price_half'];
                    
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                    
                    $order_details[] = [
                        'daily_menu_id' => $id,
                        'quantity' => $qty,
                        'price' => $price // Se guarda el precio reducido
                    ];
                    $items_found = true;
                }
            }

            if (!$items_found) {
                // Redirigir si no seleccionó nada (podríamos agregar un mensaje de error)
                header('Location: ?route=home');
                exit;
            }

            // 4. Crear el Objeto Orden
            $order = new Order();
            $order->client_id = $_SESSION['client_id'];
            $order->total = $total;
            $order->payment_method = $payment_method;
            $order->delivery_type = $delivery_type;
            $order->observation = $_POST['observation'] ?? ''; // Aseguramos que no sea null
            
            if ($delivery_type === 'delivery') {
                $order->delivery_address = $_POST['delivery_address'] ?? '';
                $order->delivery_lat = !empty($_POST['delivery_lat']) ? $_POST['delivery_lat'] : null;
                $order->delivery_lng = !empty($_POST['delivery_lng']) ? $_POST['delivery_lng'] : null;
            } else {
                // Cambiamos null por cadena vacía para cumplir con la restricción de la DB
                $order->delivery_address = ''; 
                $order->delivery_lat = null;
                $order->delivery_lng = null;
            }
            
            $order->details = $order_details;

            // 5. Guardar en DB
            if ($order->create()) {
                header('Location: ?route=order_success&id=' . $order->id);
            } else {
                echo "Error al guardar el pedido.";
            }
        }
    }

    public function orderSuccess() {
        $order_id = $_GET['id'] ?? 0;
        $content_view = '../views/home/success.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Muestra el historial de pedidos del cliente logueado
     */
    public function myOrders() {
        // Verificar sesión
        if (!isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        $orderModel = new Order();
        $clientId = $_SESSION['client_id'];

        // Obtener lista de meses con actividad
        $availableMonths = $orderModel->getUniqueMonthsByClient($clientId);

        $filters = ['client_id' => $clientId];
        if (isset($_GET['month']) && isset($_GET['year'])) {
            $filters['month'] = $_GET['month'];
            $filters['year'] = $_GET['year'];
        }

        $stmt = $orderModel->readAll($filters);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/home/orders.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Retorna los detalles de un pedido en JSON (AJAX)
     */
    public function orderDetailsApi() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
            exit;
        }

        $order_id = $_GET['id'] ?? null;
        $orderModel = new Order();
        $orderModel->id = $order_id;

        // Verificación de seguridad: Validar que el pedido pertenezca al cliente
        $orderData = $orderModel->readOne();
        if (!$orderData || $orderData['client_id'] != $_SESSION['client_id']) {
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }

        $details = $orderModel->readDetails();
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    }

    /**
     * Retorna solo los IDs y Estados de los pedidos del cliente para polling
     */
    public function myOrdersStatusApi() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $orderModel = new Order();
        $stmt = $orderModel->readAll(['client_id' => $_SESSION['client_id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'orders' => $orders]);
        exit;
    }

    public function productReactionApi() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
            exit;
        }

        $model = new ProductReaction();
        $result = $model->toggle($data['product_id'], $_SESSION['client_id'], $data['type']);
        
        echo json_encode(['success' => true, 'action' => $result['status']]);
        exit;
    }

    public function productReviewApi() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false]); exit;
        }

        $model = new ProductReaction();
        $success = $model->addReview($data['product_id'], $_SESSION['client_id'], $data['comment']);
        
        echo json_encode(['success' => $success]);
        exit;
    }
}
?>