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

        // Obtener Ajustes Globales (Para dirección, nombre, etc.)
        if (!class_exists('Setting')) require_once '../models/Setting.php';
        $settingModel = new Setting();
        $siteSettings = $settingModel->getAll();

        // Obtener Recomendados (Top 8 más gustados de todo el catálogo para el Empty State)
        $productModel = new Product();
        $allActive = $productModel->readAllActive($clientId)->fetchAll(PDO::FETCH_ASSOC);
        usort($allActive, function($a, $b) {
            return ($b['likes_count'] ?? 0) <=> ($a['likes_count'] ?? 0);
        });
        $recommended_items = $allActive; // Mostramos todos los recomendados

        // Obtener Promos activas para el Hero
        $heroModel = new HeroPromo();
        $promos = $heroModel->readActive();
        $weeklySchedule = [];
        
        $finalPromos = [];
        $db = null;

        foreach ($promos as $promo) {
            $promo['is_formatted'] = false; // Reset por seguridad
            $type = strtolower(trim($promo['type'] ?? ''));
            $rawContent = trim($promo['content'] ?? '');

            // --- PROCESAMIENTO ESTRICTO DE HORARIOS ---
            if ($type === 'hours' || $type === 'horarios') {
                // Intentamos decodificar. Si falla, $schedule será null.
                $schedule = json_decode($rawContent, true);
                
                // Si por algún error de guardado el JSON viene escapado doblemente (string de string)
                if (is_string($schedule)) {
                    $schedule = json_decode($schedule, true);
                }

                if (is_array($schedule) && !empty($schedule)) {
                    $dayNum = (int)date('w');
                    $now = date('H:i');
                    $today = $schedule[$dayNum] ?? $schedule[(string)$dayNum] ?? null;

                    $isOpen = ($today && !($today['closed'] ?? false) && $now >= ($today['open'] ?? '00:00') && $now <= ($today['close'] ?? '23:59'));
                    $promo['title'] = $isOpen ? "🟢 ¡Estamos Abiertos!" : "🕒 Horarios de Atención";

                    $daysES = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 0 => 'Dom'];
                    $groups = [];
                    $order = [1, 2, 3, 4, 5, 6, 0];
                    
                    foreach ($order as $d) {
                        $dayInfo = $schedule[$d] ?? $schedule[(string)$d] ?? ['closed' => true];
                        $timeStr = ($dayInfo['closed'] ?? false) ? "Cerrado" : ($dayInfo['open'] ?? '08:00') . " a " . ($dayInfo['close'] ?? '22:00') . " hs.";
                        
                        if (!empty($groups) && end($groups)['time'] === $timeStr) {
                            $groups[count($groups)-1]['end'] = $daysES[$d];
                        } else {
                            $groups[] = ['start' => $daysES[$d], 'end' => $daysES[$d], 'time' => $timeStr];
                        }
                    }

                    $formattedSchedule = [];
                    foreach ($groups as $g) {
                        $label = ($g['start'] === $g['end']) ? $g['start'] : $g['start'] . " a " . $g['end'];
                        $formattedSchedule[] = "<strong>$label:</strong> " . $g['time'];
                        $weeklySchedule[$label] = $g['time'];
                    }

                    $promo['content'] = implode("<br>", $formattedSchedule);
                    $promo['is_formatted'] = true;
                } else {
                    // Si los datos son basura, NO MOSTRAR EL JSON. Mostrar un fallback digno.
                    $promo['title'] = "🕒 Horarios de Atención";
                    $promo['content'] = "Atención de Lunes a Sábados.<br>Consultas al WhatsApp.";
                    $weeklySchedule = ["Lunes a Sábados" => "Consultar al local"];
                    $promo['is_formatted'] = true;
                }
                $finalPromos[] = $promo;
                continue;
            }

            // --- CASO 2: UBICACIÓN ---
            if ($type === 'location' || $type === 'ubicacion') {
                // Si el título está vacío, usamos el nombre del local o un valor por defecto
                if (empty(trim($promo['title'] ?? ''))) {
                    $promo['title'] = $siteSettings['site_name'] ?? "Nuestra Ubicación";
                }

                // Si el Hero Promo está vacío o contiene metadatos JSON (coordenadas),
                // inyectamos la dirección física configurada en Ajustes Globales y marcamos como formateado.
                $isJson = (strpos($rawContent, '{') === 0);
                if ((empty($rawContent) || $isJson) && !empty($siteSettings['store_address'])) {
                    $promo['content'] = $siteSettings['store_address'];
                    $promo['is_formatted'] = true;
                }
                $finalPromos[] = $promo;
                continue;
            }

            // --- CASO 3: RESEÑAS ---
            if ($type === 'reviews') {
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
                    $promo['is_formatted'] = false; // Las reseñas no llevan HTML de horarios
                    $finalPromos[] = $promo;
                }
            } else {
                $finalPromos[] = $promo;
            }
        }
        $promos = $finalPromos;

        // Obtener Reseñas Recientes (Top 6) para el área de testimonios en el Home
        if (!$db) $db = (new Database())->getConnection();
        $recentReviews = $db->query("SELECT r.comment, p.name as product_name, c.name as client_name, p.image as product_image, r.created_at
                                     FROM product_reviews r 
                                     JOIN products p ON r.product_id = p.id 
                                     JOIN clients c ON r.client_id = c.id
                        ORDER BY r.created_at DESC")->fetchAll(PDO::FETCH_ASSOC); // Sin límite de 6

        // Obtener Top de Reacciones (Los 4 más queridos)
        $popularProducts = $db->query("SELECT p.name, p.image, p.price, 
                                              COUNT(CASE WHEN pr.type = 'like' THEN 1 END) as total_likes,
                                              COUNT(CASE WHEN pr.type = 'fav' THEN 1 END) as total_favs
                                       FROM products p
                                       JOIN product_reactions pr ON p.id = pr.product_id
                                       GROUP BY p.id
                                       ORDER BY total_likes DESC, total_favs DESC")->fetchAll(PDO::FETCH_ASSOC); // Sin límite de 4

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
        $savedLocations = $this->getLocationsWithUsage($_SESSION['client_id']);

        $view_title = "Mis Direcciones";
        $content_view = '../views/home/locations.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Obtiene ubicaciones y verifica si tienen pedidos asociados
     */
    private function getLocationsWithUsage($clientId) {
        $locationModel = new ClientLocation();
        $locations = $locationModel->getAllByClient($clientId);
        
        if (empty($locations)) return [];

        $db = (new Database())->getConnection();
        foreach ($locations as &$loc) {
            // Verificamos si la ubicación ha sido usada en algún envío (order_shipments)
            $stmt = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE client_location_id = :id");
            $stmt->execute([':id' => $loc['id']]);
            $count = $stmt->fetchColumn();
            
            // Flag para la vista
            $loc['has_orders'] = $count > 0;
        }
        return $locations;
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