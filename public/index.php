<?php
session_start();

// Cargar DB
require_once '../config/db.php';

// Enrutador Básico
$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'start':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->start();
        break;

    case 'home':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'order_confirm':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->storeOrder();
        break;
    
    case 'order_success':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->orderSuccess();
        break;

    case 'my_orders':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->myOrders();
        break;

    case 'my_order_details':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->orderDetailsApi();
        break;

    case 'my_orders_status':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->myOrdersStatusApi();
        break;

    case 'login':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'logout':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'forgot_password':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->forgotPassword();
        break;

    case 'send_reset_link':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->sendResetLink();
        break;

    case 'reset_password':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->resetPassword();
        break;

    case 'client_login':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->clientLogin();
        break;

    case 'client_register':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->clientRegister();
        break;
        
    case 'admin':
        require_once '../controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'pos':
        require_once '../controllers/AdminController.php';
        $controller = new AdminController();
        $controller->pos();
        break;
        
    // --- Rutas de Productos ---
    case 'products':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->index();
        break;
    case 'products_create':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->create();
        break;
    case 'products_store':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->store();
        break;
    case 'products_edit':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->edit();
        break;
    case 'products_update':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->update();
        break;
    case 'products_delete':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->delete();
        break;
        
    // --- Rutas de Categorías ---
    case 'categories':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->index();
        break;
    case 'categories_create':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->create();
        break;
    case 'categories_store':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->store();
        break;
    case 'categories_edit':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->edit();
        break;
    case 'categories_update':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->update();
        break;
    case 'categories_delete':
        require_once '../controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->delete();
        break;

    // --- Rutas de Menú del Día ---
    case 'menus':
        require_once '../controllers/MenuController.php';
        $controller = new MenuController();
        $controller->index();
        break;
    case 'menus_assign':
        require_once '../controllers/MenuController.php';
        $controller = new MenuController();
        $controller->assign();
        break;
    case 'menus_unassign':
        require_once '../controllers/MenuController.php';
        $controller = new MenuController();
        $controller->unassign();
        break;
    case 'menus_toggle_availability':
        require_once '../controllers/MenuController.php';
        $controller = new MenuController();
        $controller->toggleAvailability();
        break;

    // --- Rutas de Checkout (Cliente) ---
    case 'checkout':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->checkout();
        break;
        
    case 'order_store':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->store();
        break;

    // --- Rutas de Pedidos (Admin) ---
    case 'orders':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->index();
        break;

    case 'orders_api':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->apiIndex();
        break;

    case 'orders_show':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->show();
        break;
        
    case 'orders_ticket':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->ticket();
        break;

    case 'orders_update_status':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->updateStatus();
        break;

    case 'orders_assign_delivery':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->assignDelivery();
        break;

    case 'pos_store':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->posStore();
        break;

    // --- Rutas de Reacciones (Cliente) ---
    case 'product_reaction_api':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->productReactionApi();
        break;
    case 'product_review_api':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->productReviewApi();
        break;

    case 'delivery':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->index();
        break;

    case 'save_location':
        require_once '../controllers/OrderController.php'; // Asegúrate de que esta línea ya exista
        $controller = new OrderController();
        $controller->saveLocationApi();
        break;
    
    // ... dentro de tu switch($route) o lógica de enrutamiento ...

    case 'install_delivery':
        // Script temporal para crear el Repartidor
        $db = new Database();
        $conn = $db->getConnection();
        $password = 'delivery123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO users (name, email, password, phone, address, role) 
                    VALUES ('Repartidor de Prueba', 'delivery@comedor.com', :pass, '0981000111', 'Base Central de Reparto', 'delivery')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pass', $hashed_password);
            $stmt->execute();
            echo "<h1>Usuario Delivery Creado</h1><p>Email: delivery@comedor.com<br>Pass: delivery123</p><p><a href='?route=login'>Ir al Login</a></p>";
        } catch (PDOException $e) {
            echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
        }
        break;

    case 'install':
        // Script temporal para crear el Admin
        $db = new Database();
        $conn = $db->getConnection();
        $password = 'admin123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('Administrador', 'admin@comedor.com', :pass, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pass', $hashed_password);
            $stmt->execute();
            echo "<h1>Usuario Admin Creado</h1><p>Email: admin@comedor.com<br>Pass: admin123</p><p><a href='?route=login'>Ir al Login</a></p>";
        } catch (PDOException $e) {
            echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
        }
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 - Página no encontrada</h1>';
        break;
}
?>
