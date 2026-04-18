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
    case 'my_locations':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->myLocations();
        break;
    case 'my_billing':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->myBilling();
        break;
    case 'update_billing_api':
        require_once '../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->updateBillingApi();
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
    case 'delete_location':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->deleteLocationApi();
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

    // --- Gestión de Caja ---
    case 'cash':
        require_once '../controllers/CashController.php';
        $controller = new CashController();
        $controller->index();
        break;
    case 'cash_open':
        require_once '../controllers/CashController.php';
        $controller = new CashController();
        $controller->open();
        break;
    case 'cash_close':
        require_once '../controllers/CashController.php';
        $controller = new CashController();
        $controller->close();
        break;
    case 'cash_movement_store':
        require_once '../controllers/CashController.php';
        $controller = new CashController();
        $controller->storeMovement();
        break;

    // --- Gestión de Productos ---
    case 'products':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->index();
        break;

    case 'products_api':
        require_once '../controllers/ProductController.php';
        $controller = new ProductController();
        $controller->apiIndex();
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

    case 'shortcuts':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->shortcuts();
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

    case 'settings_location':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->location();
        break;

    case 'settings_location_update':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->updateLocation();
        break;

    case 'settings_delivery':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->deliveryRates();
        break;

    case 'settings_delivery_activate':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->setDeliveryRateActive();
        break;

    case 'settings_delivery_update':
        require_once '../controllers/SettingController.php';
        $controller = new SettingController();
        $controller->updateDeliveryRates();
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

    case 'admin_delivery_assists':
        require_once '../controllers/AdminController.php';
        $controller = new AdminController();
        $controller->deliveryAssists();
        break;

    case 'delivery_history':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->history();
        break;

    case 'delivery_production':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->production();
        break;

    case 'delivery_assists':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->assists();
        break;

    case 'delivery_checkin':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->checkin();
        break;

    case 'delivery_checkin_save':
        require_once '../controllers/DeliveryController.php';
        $controller = new DeliveryController();
        $controller->saveCheckinApi();
        break;

    case 'orders':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->index();
        break;

    case 'orders_pending':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->pendingOrders();
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

    case 'orders_pending_count':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->pendingCountApi();
        break;

    // --- Gestión de Usuarios (Staff) ---
    case 'users':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->index();
        break;
    case 'users_create':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->create();
        break;
    case 'users_store':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->store();
        break;
    case 'users_edit':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->edit();
        break;
    case 'users_update':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->update();
        break;
    case 'users_toggle_status':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->toggleStatus();
        break;
    case 'users_delete':
        require_once '../controllers/UserController.php';
        $controller = new UserController();
        $controller->delete();
        break;

    // --- Gestión de Clientes (Admin/POS) ---
    case 'admin_clients_search':
        require_once '../controllers/ClientController.php';
        $controller = new ClientController();
        $controller->search();
        break;

    case 'admin_clients_check_phone':
        require_once '../controllers/ClientController.php';
        $controller = new ClientController();
        $controller->checkPhoneApi();
        break;

    case 'admin_clients_store_api':
        require_once '../controllers/ClientController.php';
        $controller = new ClientController();
        $controller->storeApi();
        break;

    case 'admin_resolve_map_url':
        require_once '../controllers/ClientController.php';
        $controller = new ClientController();
        $controller->resolveMapUrl();
        break;

    // --- Gestión de Empresa ---
    case 'empresa':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->index();
        break;
    case 'empresa_create':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->create();
        break;
    case 'empresa_store':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->store();
        break;
    case 'empresa_edit':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->edit();
        break;
    case 'empresa_update':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->update();
        break;
    case 'empresa_delete':
        require_once '../controllers/EmpresaController.php';
        $controller = new EmpresaController();
        $controller->delete();
        break;

    default:
        echo "Ruta no encontrada.";
        break;
}