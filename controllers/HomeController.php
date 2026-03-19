<?php
require_once '../models/DailyMenu.php';
require_once '../models/Order.php';

class HomeController {

    public function __construct() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        // Obtener el menú de la fecha actual
        $date = date('Y-m-d');
        $dailyMenuModel = new DailyMenu();
        $menus = $dailyMenuModel->readForDate($date)->fetchAll(PDO::FETCH_ASSOC);

        // Cargar vistas
        $content_view = '../views/home/index.php';
        require_once '../views/layouts/client_layout.php';
    }

    public function storeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Recibir datos del formulario
            $products_input = $_POST['products'] ?? [];
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
            }

            if (!$items_found) {
                // Redirigir si no seleccionó nada (podríamos agregar un mensaje de error)
                header('Location: ?route=home');
                exit;
            }

            // 4. Crear el Objeto Orden
            $order = new Order();
            $order->user_id = $_SESSION['user_id'];
            $order->total = $total;
            $order->payment_method = $payment_method;
            $order->delivery_type = $delivery_type;
            
            if ($delivery_type === 'delivery') {
                $order->delivery_address = $_POST['delivery_address'] ?? '';
                $order->delivery_lat = !empty($_POST['delivery_lat']) ? $_POST['delivery_lat'] : null;
                $order->delivery_lng = !empty($_POST['delivery_lng']) ? $_POST['delivery_lng'] : null;
            } else {
                $order->delivery_address = null;
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
}
?>