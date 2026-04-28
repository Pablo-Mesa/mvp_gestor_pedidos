<?php
// Establecer zona horaria de Paraguay (UTC-3 permanente según Ley 7334/24)
date_default_timezone_set('America/Asuncion');

require_once '../models/Order.php';
require_once '../models/User.php';
require_once '../models/ClientLocation.php';
require_once '../models/Setting.php';
require_once '../models/CashRegister.php'; // Nuevo Modelo
require_once '../models/DeliveryRate.php';
require_once '../models/Client.php';
require_once '../models/Empresa.php';

class OrderController {

    // --- Métodos de ADMINISTRADOR ---

    /**
     * Valida que el usuario sea Admin. 
     * Si es repartidor, lo manda a su panel. Si no hay sesión, al login.
     */
    private function checkAdminAccess() {
        if (isset($_SESSION['client_id'])) { header('Location: ?route=home'); exit; }
        if (!isset($_SESSION['user_role'])) { header('Location: ?route=login'); exit; }

        if (!in_array($_SESSION['user_role'], ['admin', 'cajero'])) {
            if ($_SESSION['user_role'] === 'delivery') {
                header('Location: ?route=delivery');
            } else {
                header('Location: ?route=login');
            }
            exit;
        }
    }

    public function index() {
        $this->checkAdminAccess();

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

        // Obtener repartidores para asignación rápida en la tabla
        $userModel = new User();
        $deliveryUsers = $userModel->getDeliveryUsers();

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra la lista de todos los pedidos pendientes sin importar la fecha.
     */
    public function pendingOrders() {
        $this->checkAdminAccess();

        $orderModel = new Order();
        
        // Forzamos los filtros necesarios para obtener solo lo pendiente históricamente
        $_GET['status'] = 'pending';
        $_GET['date'] = ''; 

        $stmt = $orderModel->readAll($_GET);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener repartidores para asignación rápida en la tabla
        $userModel = new User();
        $deliveryUsers = $userModel->getDeliveryUsers();

        // Obtenemos los contadores de hoy para mantener la coherencia de la barra inferior
        $statusCounts = $orderModel->getStatusCountsByDate(date('Y-m-d'));

        $content_view = '../views/admin/orders/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Endpoint AJAX que devuelve los pedidos en formato JSON para auto-actualización.
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'delivery'])) {
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $orderModel = new Order();

        // Seguridad: Si es repartidor, forzamos el filtro para que solo vea sus pedidos
        if ($_SESSION['user_role'] === 'delivery') {
            $_GET['delivery_user_id'] = $_SESSION['user_id'];
        }

        // Mantener el filtro por defecto de hoy para la sincronización automática (ADMIN)
        // Solo aplicamos el filtro de fecha si NO es delivery, ya que el repartidor 
        // necesita ver sus pedidos pendientes independientemente de la fecha.
        if (!isset($_GET['date']) && $_SESSION['user_role'] !== 'delivery') {
            $_GET['date'] = date('Y-m-d');
        }

        $stmt = $orderModel->readAll($_GET);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si es repartidor, aplicamos la misma lógica de "visibilidad inteligente" 
        // que en DeliveryController::index para mantener la sincronización.
        if ($_SESSION['user_role'] === 'delivery') {
            $today = date('Y-m-d');
            $requestedDate = $_GET['date'] ?? null;

            $orders = array_filter($orders, function($o) use ($today, $requestedDate) {
                if ($requestedDate) return true; // Si pidió una fecha, no filtramos nada más

                $isFinished = in_array($o['status'], ['completed', 'rejected', 'cancelled']);
                $isFromToday = date('Y-m-d', strtotime($o['created_at'])) === $today;
                return !$isFinished || $isFromToday;
            });
            $orders = array_values($orders); // Reindexar para asegurar un array JSON []
        }

        // Formatear datos para el frontend (fechas, nombres seguros y moneda)
        foreach ($orders as &$order) {
            $order['user_name'] = htmlspecialchars($order['user_name']);
            $order['channel_name'] = htmlspecialchars($order['channel_name'] ?? 'Web');
            $order['channel_icon'] = htmlspecialchars($order['channel_icon'] ?? 'fas fa-globe');
            $order['formatted_date'] = date('d/m/Y H:i', strtotime($order['created_at']));
            $order['formatted_total'] = number_format($order['total'], 0, ',', '.');
            
            // Integridad de pago: Es pagado solo si el monto en la tabla 'pagos' cubre el total de la orden
            $totalPaid = (float)($order['total_paid'] ?? 0);
            $order['is_paid'] = ($totalPaid >= (float)$order['total']) ? 1 : 0;
            $order['has_invoice'] = (int)($order['has_invoice'] ?? 0) > 0 ? 1 : 0;
        }

        echo json_encode($orders);
        exit;
    }

    public function show() {
        $this->checkAdminAccess();

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
        $this->checkAdminAccess();

        $id = $_GET['id'] ?? null;
        $format = $_GET['format'] ?? '80mm'; // Capturamos el formato (por defecto 80mm)
        if (!$id) { header('Location: ?route=orders'); exit; }

        $orderModel = new Order();
        $orderModel->id = $id;
        $order = $orderModel->readOne();
        $details = $orderModel->readDetails();

        require_once '../views/admin/orders/ticket.php';
    }

    /**
     * Genera el Ticket de Venta (Control Interno / Cliente)
     */
    public function salesTicket() {
        $this->checkAdminAccess();

        $saleId = $_GET['id'] ?? null;
        $format = $_GET['format'] ?? '80mm';
        if (!$saleId) { header('Location: ?route=sales_history'); exit; }

        $empresaModel = new Empresa();
        $empresa = $empresaModel->readAll()->fetch(PDO::FETCH_ASSOC);

        $db = (new Database())->getConnection();
        // Traemos la cabecera de la venta
        $querySale = "SELECT v.*, c.name as client_name, c.billing_ruc as client_ruc 
                      FROM pos_ventas_cabecera v
                      LEFT JOIN clients c ON v.cliente_id = c.id
                      WHERE v.id = :id";
        $stmtSale = $db->prepare($querySale);
        $stmtSale->execute([':id' => $saleId]);
        $sale = $stmtSale->fetch(PDO::FETCH_ASSOC);

        if (!$sale) { die("Venta no encontrada."); }

        // Traemos el detalle histórico de esa venta específica
        $queryDetails = "SELECT vd.*, p.name as product_name 
                         FROM pos_ventas_detalle vd
                         JOIN products p ON vd.producto_id = p.id
                         WHERE vd.venta_id = :id";
        $stmtDetails = $db->prepare($queryDetails);
        $stmtDetails->execute([':id' => $saleId]);
        $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        require_once '../views/admin/sales/ticket.php';
    }

    /**
     * Muestra la interfaz de cobro mixto para un pedido
     */
    public function finalize() {
        $this->checkAdminAccess();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=orders'); exit; }

        // --- VALIDACIÓN DE CAJA ---
        $cashModel = new CashRegister();
        $activeSession = $cashModel->getActiveSession($_SESSION['user_id']);

        $orderModel = new Order();
        $orderModel->id = $id;

        // Cargamos los datos para la vista (Soluciona el error de variable indefinida $order y $details)
        $order = $orderModel->readOne();
        $details = $orderModel->readDetails();
        
        // Si tiene factura, verificamos si ya está pagada
        if ($orderModel->hasInvoice($id)) {
            $db = (new Database())->getConnection();
            $check = $db->prepare("SELECT p.id FROM pagos p JOIN pos_ventas_cabecera v ON p.venta_id = v.id WHERE v.order_id = :id");
            $check->execute([':id' => $id]);
            if ($check->fetch()) {
                header('Location: ?route=orders_show&id=' . $id . '&error=already_paid');
                exit;
            }
        }

        // Si se solicita facturación rápida (sin desglose de pagos manual)
        if (isset($_GET['quick']) && $_GET['quick'] == 1) {
            $orderModel->user_id = $_SESSION['user_id'];
            // Por defecto en facturación rápida, si el cliente tiene RUC intentamos Factura, sino Ticket
            $docType = (!empty($order['billing_ruc'])) ? 'factura' : 'ticket';
            $paymentsParam = $activeSession ? [] : null;
            $sessionId = $activeSession ? $activeSession['id'] : null;
            $ventaId = $orderModel->finalizeSale($paymentsParam, $sessionId, $docType); 
            if ($ventaId) {
                // Redirigimos al mismo formulario de cobro pero ya con la factura generada para completar el flujo
                header('Location: ?route=orders_finalize&id=' . $id . '&success=invoice_created&print_sale_id=' . $ventaId);
            } else {
                header('Location: ?route=sales_history&error=' . urlencode($orderModel->error));
            }
            exit;
        }

        $content_view = '../views/admin/orders/finalize.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function processFinalize() {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- VALIDACIÓN DE CAJA ---
            $cashModel = new CashRegister();
            $activeSession = $cashModel->getActiveSession($_SESSION['user_id']);

            $order = new Order();
            $order->id = $_POST['order_id'];
            $order->user_id = $_SESSION['user_id'];
            $docType = $_POST['document_type'] ?? 'ticket';
            
            $payments = $_POST['payments'] ?? [];
            $hasPayments = false;
            if (is_array($payments)) {
                foreach ($payments as $p) {
                    if (isset($p['monto']) && (float)$p['monto'] > 0) {
                        $hasPayments = true;
                        break;
                    }
                }
            }

            if ($hasPayments && !$activeSession) {
                header('Location: ?route=orders_finalize&id=' . $order->id . '&error=' . urlencode("No puedes registrar pagos sin una caja abierta."));
                exit;
            }

            // Determinar qué enviar al modelo: pagos manuales, automáticos (si hay caja) o nulo (solo factura)
            $finalPayments = $hasPayments ? $payments : ($activeSession ? [] : null);
            $sessionId = $activeSession ? $activeSession['id'] : null;

            if ($order->finalizeSale($finalPayments, $sessionId, $docType)) {
                header('Location: ?route=sales_history&success=paid');
            } else {
                header('Location: ?route=orders_finalize&id=' . $order->id . '&error=' . urlencode($order->error));
            }
        }
    }

    public function updateStatus() {
        // Permitir que tanto Admin como Delivery actualicen estados
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'delivery'])) { header('Location: ?route=login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = new Order();
            $order->id = $_POST['id'];
            $order->status = $_POST['status'];
            $order->user_id = $_SESSION['user_id']; // Pasamos el ID del staff que realiza la acción
            $success = $order->updateStatus();

            // Si el pedido se completa, formalizar la Venta y el Pago (Segunda Propuesta)
            if ($success && $_POST['status'] === 'completed') {
                $cashModel = new CashRegister();
                
                $payment_confirm = $_POST['payment_confirm'] ?? 'order_method';
                $orderData = $order->readOne();
                
                $payData = [];
                if ($payment_confirm === 'none') {
                    // Caso: Entregado sin cobrar (Autorizado). No registramos pago, queda como deuda.
                    $payData = null; 
                } elseif ($payment_confirm === 'cash') {
                    $payData = [['metodo' => 'efectivo', 'monto' => $orderData['total'], 'referencia' => 'Cobrado por Delivery']];
                } elseif ($payment_confirm === 'digital') {
                    $payData = [['metodo' => 'transferencia', 'monto' => $orderData['total'], 'referencia' => 'Verificado por Delivery']];
                } else {
                    // Por defecto: Usar el método que ya tenía el pedido
                    $payData = [[
                        'metodo' => $orderData['payment_method'],
                        'monto' => $orderData['total'],
                        'referencia' => 'Pago automático en entrega'
                    ]];
                }

                // En el cierre automático, si no hay caja abierta, finalizeSale lanzará error o se puede manejar
                $registerId = $activeSession ? $activeSession['id'] : null;
                $order->finalizeSale($payData, $registerId);
            }

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
        $success = $orderModel->assignDelivery($orderId, $deliveryId, $_SESSION['user_id']);

        // Si la asignación fue exitosa, confirmamos el pedido automáticamente si estaba pendiente
        if ($success) {
            $orderModel->id = $orderId;
            $currentOrder = $orderModel->readOne();
            if ($currentOrder && $currentOrder['status'] === 'pending') {
                $orderModel->status = 'confirmed';
                $orderModel->user_id = $_SESSION['user_id'];
                $orderModel->updateStatus();
            }
        }

        echo json_encode(['success' => $success]);
    }

    /**
     * Endpoint para el badge de la sidebar (Polling)
     */
    public function pendingCountApi() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['count' => 0]);
            exit;
        }

        $orderModel = new Order();
        $stats = $orderModel->getDashboardStats();
        echo json_encode(['count' => (int)$stats['pending_orders']]);
        exit;
    }

    // --- Métodos de CLIENTE ---

    public function checkout() {
        // 1. Verificar autenticación (Seguridad)
        if (!isset($_SESSION['client_id'])) {
            // Si no está logueado, redirigir al home forzando el modal de login
            header('Location: ?route=home&error=login_required');
            exit;
        }

        // 2. Cargar ubicaciones guardadas
        $locationModel = new ClientLocation();
        $savedLocations = $this->getLocationsWithUsage($_SESSION['client_id']);

        // 3. Cargar configuraciones y tarifas para el cálculo de delivery en el frontend
        $settingModel = new Setting();
        $siteSettings = $settingModel->getAll();

        $rateModel = new DeliveryRate();
        $activeRateData = $rateModel->getActive();
        $activeRates = $activeRateData['details'] ?? [];

        $content_view = '../views/home/checkout.php';
        require_once '../views/layouts/client_layout.php';
    }

    /**
     * Guarda una nueva ubicación vía AJAX
     */
    public function saveLocationApi() {
        ob_start(); // Capturar cualquier salida accidental (notices/warnings)
        try {
            header('Content-Type: application/json');
            if (!isset($_SESSION['client_id'])) {
                throw new Exception('Sesión expirada');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new Exception('Datos de entrada inválidos');

            $locationModel = new ClientLocation();
            $input['client_id'] = $_SESSION['client_id'];
            
            if ($locationModel->create($input)) {
                ob_clean(); // Limpiar el buffer antes de enviar el JSON real
                echo json_encode(['success' => true, 'locations' => $this->getLocationsWithUsage($_SESSION['client_id'])]);
            } else {
                throw new Exception('Error al guardar ubicación en la base de datos');
            }
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Actualiza una ubicación existente vía AJAX
     */
    public function updateLocationApi() {
        ob_start();
        try {
            header('Content-Type: application/json');
            if (!isset($_SESSION['client_id'])) {
                throw new Exception('Sesión expirada');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['id'])) throw new Exception('ID de ubicación no proporcionado');

            $locationModel = new ClientLocation();
            $input['client_id'] = $_SESSION['client_id'];
            
            if ($locationModel->update($input)) {
                ob_clean();
                echo json_encode(['success' => true, 'locations' => $this->getLocationsWithUsage($_SESSION['client_id'])]);
            } else {
                throw new Exception('Error al actualizar ubicación en la base de datos');
            }
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Elimina una ubicación existente vía AJAX
     */
    public function deleteLocationApi() {
        ob_start();
        try {
            header('Content-Type: application/json');
            if (!isset($_SESSION['client_id'])) {
                throw new Exception('Sesión expirada');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['id'])) throw new Exception('ID de ubicación no proporcionado');

            $db = (new Database())->getConnection();
            $check = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE client_location_id = :id");
            $check->execute([':id' => $input['id']]);
            if ($check->fetchColumn() > 0) {
                throw new Exception('No se puede eliminar una ubicación que tiene pedidos registrados.');
            }

            $locationModel = new ClientLocation();
            
            if ($locationModel->delete($input['id'], $_SESSION['client_id'])) {
                ob_clean();
                echo json_encode(['success' => true, 'locations' => $this->getLocationsWithUsage($_SESSION['client_id'])]);
            } else {
                throw new Exception('Error al eliminar ubicación de la base de datos');
            }
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Obtiene ubicaciones y verifica si tienen pedidos asociados para restringir eliminación
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
            
            // Flag para la vista: Solo es eliminable si no tiene pedidos
            $loc['has_orders'] = $count > 0;
        }
        return $locations;
    }

    /**
     * Calcula el costo de delivery y retorna el ID de la tarifa y el precio aplicado
     */
    private function getDeliveryPricing($clientLat, $clientLng) {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $storeLat = $settings['store_lat'] ?? -25.3006;
        $storeLng = $settings['store_lng'] ?? -57.6359;

        // Radio de la tierra en KM
        $earthRadius = 6371;

        $dLat = deg2rad($clientLat - $storeLat);
        $dLon = deg2rad($clientLng - $storeLng);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($storeLat)) * cos(deg2rad($clientLat)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c; // Distancia en KM

        $db = (new Database())->getConnection();
        $query = "SELECT d.id as delivery_rate_id, d.price FROM delivery_rate_details d
                  JOIN delivery_rates r ON d.delivery_rate_id = r.id
                  WHERE r.is_active = 1 AND :dist BETWEEN d.km_from AND d.km_to LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':dist' => $distance]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $res ?: ['delivery_rate_id' => null, 'price' => 0];
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

        // Cargar configuraciones para validar si la facturación está permitida
        $settingModel = new Setting();
        $siteSettings = $settingModel->getAll();
        $enableInvoice = $siteSettings['enable_legal_invoice'] ?? '1';

        // Crear Orden
        $order = new Order();
        $order->user_id = null; // Web no tiene usuario de staff
        $order->client_id = $_SESSION['client_id'];
        $order->channel_id = 1; // 1 = Web
        $order->status = 'pending';
        $order->payment_method = $input['payment_method'] ?? 'efectivo';
        $order->delivery_type = $input['delivery_type'] ?? 'pickup';
        $order->observation = $input['observation'] ?? ''; // Guardamos la observación
        
        // Capturar datos de facturación
        // Solo si la opción global está activa en configuraciones
        $order->billing_name = ($enableInvoice == '1') ? ($input['billing_name'] ?? null) : null;
        $order->billing_ruc = ($enableInvoice == '1') ? ($input['billing_ruc'] ?? null) : null;

        // Si se enviaron datos de facturación, actualizamos el perfil del cliente "de paso"
        if ($enableInvoice == '1' && !empty($order->billing_name) && !empty($order->billing_ruc)) {
            // Solo actualizamos si son diferentes a lo que ya tenemos en sesión para ahorrar recursos
            if ($order->billing_name !== ($_SESSION['client_billing_name'] ?? '') || 
                $order->billing_ruc !== ($_SESSION['client_billing_ruc'] ?? '')) {
                
                $clientModel = new Client();
                $clientModel->updateBilling($_SESSION['client_id'], $order->billing_name, $order->billing_ruc);
                
                // Actualizamos la sesión para que el cambio sea inmediato en la interfaz
                $_SESSION['client_billing_name'] = $order->billing_name;
                $_SESSION['client_billing_ruc'] = $order->billing_ruc;
            }
        }

        $order->client_location_id = $input['location_id'] ?? null; // Nueva relación
        
        $deliveryCost = 0;
        $order->delivery_rate_id = null;

        // Datos de delivery
        if ($order->delivery_type === 'delivery') {
            $order->delivery_address = $input['delivery_address'] ?? '';
            $order->delivery_lat = $input['delivery_lat'] ?? null;
            $order->delivery_lng = $input['delivery_lng'] ?? null;
            
            if ($order->delivery_lat && $order->delivery_lng) {
                $pricing = $this->getDeliveryPricing($order->delivery_lat, $order->delivery_lng);
                if ($pricing['delivery_rate_id'] === null) {
                    echo json_encode(['success' => false, 'message' => 'Ubicación fuera de zona de cobertura.']);
                    exit;
                }
                $deliveryCost = $pricing['price'];
                $order->delivery_rate_id = $pricing['delivery_rate_id'];
            }
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
        $order->total = $total + $deliveryCost;

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
        $order->user_id = $_SESSION['user_id']; // Quién está en el POS
        // Si no se envía client_id, usamos 0 o un ID de sistema para "Cliente Ocasional"
        $order->client_id = $input['client_id'] ?? 1; 
        $order->channel_id = 2; // 2 = Mostrador
        $order->payment_method = $input['payment_method'] ?? 'efectivo';
        $order->delivery_type = $input['delivery_type'] ?? 'local';
        $order->observation = $input['observation'] ?? '';
        $order->status = 'confirmed'; // Los pedidos de mostrador suelen estar confirmados de entrada
        
        $order->client_location_id = $input['location_id'] ?? null;
        // Usamos los nombres de propiedades correctos definidos en el modelo Order
        $order->delivery_address = $input['delivery_address'] ?? ($input['delivery_type'] === 'delivery' ? 'Ubicación vía POS' : '');
        $order->delivery_lat = $input['delivery_lat'] ?? $input['lat'] ?? null;
        $order->delivery_lng = $input['delivery_lng'] ?? $input['lng'] ?? null;

        // Si se solicita guardar una nueva ubicación permanentemente para este cliente
        if (!empty($input['save_new_location']) && $order->client_id > 1 && $order->delivery_lat && $order->delivery_lng) {
            $locationModel = new ClientLocation();
            $locationModel->create([
                'client_id' => $order->client_id,
                'title'     => !empty($input['new_location_title']) ? $input['new_location_title'] : 'Dirección POS',
                'address'   => $order->delivery_address,
                'lat'       => $order->delivery_lat,
                'lng'       => $order->delivery_lng
            ]);
        }

        $deliveryCost = 0;
        $order->delivery_rate_id = null;

        if ($order->delivery_type === 'delivery' && $order->delivery_lat && $order->delivery_lng) {
            $pricing = $this->getDeliveryPricing($order->delivery_lat, $order->delivery_lng);
            if ($pricing['delivery_rate_id'] === null) {
                echo json_encode(['success' => false, 'message' => 'La ubicación está fuera de zona. Verifique coordenadas o link.']);
                exit;
            }
            $deliveryCost = $pricing['price'];
            $order->delivery_rate_id = $pricing['delivery_rate_id'];
        }

        $total = 0;
        foreach ($input['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
            $order->details[] = [
                'product_id' => (int)$item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }
        $order->total = $total + $deliveryCost;

        if ($order->create()) {
            // El pedido queda en estado "Confirmado" por defecto para permitir flujo posterior
            $order->user_id = $_SESSION['user_id'];

            echo json_encode([
                'success' => true, 
                'order_id' => $order->id,
                'message' => 'Pedido registrado correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $order->error]);
        }
    }
}
?>