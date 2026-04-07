<?php
session_start();

// 1. Capturar la ruta solicitada
$route = $_GET['route'] ?? 'start';

// 2. Sistema de Enrutamiento
switch ($route) {
    // --- Rutas Públicas / Cliente ---
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

    // --- Autenticación ---
    case 'login':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
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

    case 'logout':
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // --- Checkout y Pedidos ---
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

    case 'order_success':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->orderSuccess();
        break;

    case 'save_location':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->saveLocationApi();
        break;
    case 'update_location':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->updateLocationApi();
        break;

    // --- Administración (Dashboard y POS) ---
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

    case 'pos_store':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->posStore();
        break;

    // --- Gestión de Productos ---
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

    // --- Gestión de Categorías ---
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

    case 'settings_reset':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->reset();
        break;

    // --- Gestión de Menú del Día ---
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

    // --- Gestión de Hero Promos ---
    case 'hero_promos':
        require_once '../controllers/HeroPromoController.php';
        $controller = new HeroPromoController();
        $controller->index();
        break;
    case 'hero_promos_edit':
        require_once '../controllers/HeroPromoController.php';
        $controller = new HeroPromoController();
        $controller->edit();
        break;
    case 'hero_promos_update':
        require_once '../controllers/HeroPromoController.php';
        $controller = new HeroPromoController();
        $controller->update();
        break;

    // --- Ajustes de Marca ---
    case 'settings':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->index();
        break;

    case 'settings_update':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->update();
        break;

    // --- Logística / Delivery ---
    case 'delivery':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->index();
        break;

    case 'orders':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->index();
        break;

    case 'orders_show':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->show();
        break;

    case 'orders_api':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->apiIndex();
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

    default:
        echo "Ruta no encontrada.";
        break;
}