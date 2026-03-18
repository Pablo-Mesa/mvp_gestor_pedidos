<?php
session_start();

// Cargar DB
require_once '../config/db.php';

// Enrutador Básico
$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'home':
        // Ejemplo de lógica
        // require_once '../controllers/HomeController.php';
        // $controller = new HomeController();
        // $controller->index();
        require_once '../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        //echo '<h1>Bienvenido al Comedor</h1><p>Selecciona una ruta: ?route=login</p>';
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
        
    case 'admin':
        require_once '../controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
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
