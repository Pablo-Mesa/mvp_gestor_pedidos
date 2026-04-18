<?php
date_default_timezone_set('America/Asuncion');

require_once '../models/DailyMenu.php';
require_once '../models/Order.php';
require_once '../models/ProductReaction.php';
require_once '../models/HeroPromo.php';
require_once '../models/ClientLocation.php';
require_once '../models/Product.php';
require_once '../models/Client.php';

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

    /**
     * Punto de entrada inteligente para la PWA
     */
    public function start() {
        // 1. Si hay una sesión activa de Staff (Tabla 'users')
        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['user_role'] ?? '';
            if ($role === 'delivery') {
                header('Location: ?route=delivery');
            } elseif ($role === 'admin') {
                header('Location: ?route=admin');
            } else {
                header('Location: ?route=login');
            }
            exit;
        }

        // 2. Si es Cliente (Tabla 'clients') o no está logueado
        // Enviamos al home donde podrá ver el menú o loguearse como cliente
        header('Location: ?route=home');
        exit;
    }

    public function index() {
        // Obtener el menú de la fecha actual
        $date = date('Y-m-d');
        $clientId = $_SESSION['client_id'] ?? null;
        $dailyMenuModel = new DailyMenu();
        $daily_menus = $dailyMenuModel->readForDate($date, $clientId, true)->fetchAll(PDO::FETCH_ASSOC);

        // Obtener Recomendados (Top 8 más gustados de todo el catálogo para el Empty State)
        $productModel = new Product();
        $allActive = $productModel->readAllActive($clientId)->fetchAll(PDO::FETCH_ASSOC);
        usort($allActive, function($a, $b) {
            return ($b['likes_count'] ?? 0) <=> ($a['likes_count'] ?? 0);
        });
        $recommended_items = array_slice($allActive, 0, 8);

        // Obtener Promos activas para el Hero
        $heroModel = new HeroPromo();
        $promos = $heroModel->readActive();
        
        $finalPromos = [];
        $db = null;

        foreach ($promos as $promo) {
            if ($promo['type'] === 'reviews') {
                if (!$db) $db = (new Database())->getConnection();
                
                // Buscamos una reseña aleatoria unida al nombre del producto
                $sql = "SELECT r.comment, p.name as product_name 
                        FROM product_reviews r 
                        JOIN products p ON r.product_id = p.id 
                        ORDER BY RAND() LIMIT 1";
                $res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($res) {
                    $promo['content'] = $res['comment'];
                    $promo['title'] = "Opinión: " . $res['product_name'];
                    $finalPromos[] = $promo;
                }
                // Si no hay reseñas en la DB, no la agregamos al array final
            } elseif ($promo['type'] === 'hours') {
                // Lógica de Horarios Dinámicos
                $schedule = json_decode($promo['content'], true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($schedule)) {
                    $dayNum = date('w'); // 0 (Dom) a 6 (Sab)
                    $now = date('H:i');
                    $today = $schedule[$dayNum] ?? null;

                    if ($today && !$today['closed']) {
                        $isOpen = ($now >= $today['open'] && $now <= $today['close']);
                        if ($isOpen) {
                            $promo['title'] = "🟢 ¡Estamos Abiertos!";
                            $promo['content'] = "Atendiendo hoy hasta las " . $today['close'] . ". ¡Haz tu pedido ahora!";
                        } else {
                            $promo['title'] = "🔴 Cerrado temporalmente";
                            $nextOpen = ($now < $today['open']) ? "Abrimos hoy a las " . $today['open'] : "Abrimos mañana";
                            $promo['content'] = $nextOpen . ". ¡Vuelve pronto!";
                        }
                    } else {
                        $promo['title'] = "🗓️ Hoy Cerrado";
                        $promo['content'] = "Hoy no abrimos al público. ¡Te esperamos en nuestro próximo horario!";
                    }
                }
                // Si no es JSON (texto viejo), se muestra tal cual
                $finalPromos[] = $promo;
            } else {
                $finalPromos[] = $promo;
            }
        }
        $promos = $finalPromos;

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
     * Muestra la gestión de direcciones del cliente
     */
    public function myLocations() {
        if (!isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        $locationModel = new ClientLocation();
        $savedLocations = $locationModel->getAllByClient($_SESSION['client_id']);

        $view_title = "Mis Direcciones";
        $content_view = '../views/home/locations.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Muestra la gestión de datos de facturación
     */
    public function myBilling() {
        if (!isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        $clientModel = new Client();
        $clientData = $clientModel->getById($_SESSION['client_id']);

        $view_title = "Datos de Facturación";
        $content_view = '../views/home/billing.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Procesa la actualización de datos de facturación vía AJAX
     */
    public function updateBillingApi() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['client_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['billing_name']) || empty($data['billing_ruc'])) {
            echo json_encode(['success' => false, 'message' => 'El nombre y RUC son obligatorios']);
            exit;
        }

        $clientModel = new Client();
        if ($clientModel->updateBilling($_SESSION['client_id'], $data['billing_name'], $data['billing_ruc'])) {
            // Actualizar sesión para reflejar cambios en checkout
            $_SESSION['client_billing_name'] = $data['billing_name'];
            $_SESSION['client_billing_ruc'] = $data['billing_ruc'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos']);
        }
        exit;
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