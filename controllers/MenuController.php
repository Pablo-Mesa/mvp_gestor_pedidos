<?php
date_default_timezone_set('America/Asuncion');

require_once '../models/DailyMenu.php';
require_once '../models/Product.php';

class MenuController {

    public function __construct() {
        // 1. Si es un cliente logueado, lo mandamos a la web pública
        if (isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

        // 2. Si no hay sesión de staff, al login
        if (!isset($_SESSION['user_role'])) {
            header('Location: ?route=login');
            exit;
        }

        // 3. Bloqueo estricto: Solo el rol 'admin' puede gestionar el menú.
        if ($_SESSION['user_role'] !== 'admin') {
            if ($_SESSION['user_role'] === 'delivery') {
                header('Location: ?route=delivery');
            } else {
                header('Location: ?route=login');
            }
            exit;
        }
    }

    /**
     * Muestra la página para gestionar el menú del día.
     */
    public function index() {
        // Determinar la fecha a mostrar (desde GET o hoy por defecto)
        $current_date = $_GET['date'] ?? date('Y-m-d');

        // Obtener los menús ya asignados para esa fecha
        $dailyMenuModel = new DailyMenu();
        $assigned_menus = $dailyMenuModel->readForDate($current_date)->fetchAll(PDO::FETCH_ASSOC);

        // Obtener todos los productos activos para el dropdown de asignación
        $productModel = new Product();
        $available_products = $productModel->readAllActive()->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/menus/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Asigna un producto al menú de un día.
     */
    public function assign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $menu = new DailyMenu();
            $menu->product_id = $_POST['product_id'];
            $menu->menu_date = $_POST['menu_date'];
            $menu->daily_stock = $_POST['daily_stock']; // El modelo se encarga de convertir '' a NULL

            if ($menu->assign()) {
                // Éxito
            } else {
                // Error (probablemente ya existía)
                // Podríamos agregar un mensaje de error en la sesión (flash message)
            }
            header('Location: ?route=menus&date=' . $menu->menu_date);
            exit;
        }
    }

    /**
     * Quita un producto del menú de un día.
     */
    public function unassign() {
        $id = $_GET['id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d'); // Para redirigir a la fecha correcta

        if ($id) {
            $menu = new DailyMenu();
            $menu->id = $id;
            $menu->unassign();
        }
        header('Location: ?route=menus&date=' . $date);
        exit;
    }

    /**
     * Cambia el estado de disponibilidad de un plato del menú (disponible/agotado).
     */
    public function toggleAvailability() {
        $id = $_GET['id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        $current_status = $_GET['status'] ?? '1';

        if ($id) {
            $menu = new DailyMenu();
            $menu->id = $id;
            $menu->is_available = ($current_status == '1') ? 0 : 1; // Invierte el estado
            $menu->updateAvailability();
        }

        header('Location: ?route=menus&date=' . $date);
        exit;
    }
}
?>