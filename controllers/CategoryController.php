<?php
require_once '../models/Category.php';

class CategoryController {

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

        // 3. Bloqueo estricto: Solo el rol 'admin' puede gestionar categorías.
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
     * Muestra la lista de categorías.
     */
    public function index() {
        $category = new Category();
        $stmt = $category->readAll();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content_view = '../views/admin/categories/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create() {
        $content_view = '../views/admin/categories/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $category->name = $_POST['name'];

            if ($category->create()) {
                header('Location: ?route=categories');
            } else {
                echo "Error al crear categoría";
            }
        }
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $categoryModel = new Category();
            $categoryModel->id = $id;
            $category = $categoryModel->readOne();
            
            $content_view = '../views/admin/categories/form.php';
            require_once '../views/layouts/admin_layout.php';
        }
    }

    /**
     * Actualiza una categoría en la base de datos.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $category->id = $_POST['id'];
            $category->name = $_POST['name'];

            if ($category->update()) {
                header('Location: ?route=categories');
            } else {
                echo "Error al actualizar categoría";
            }
        }
    }

    /**
     * Elimina una categoría de la base de datos.
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $category = new Category();
            $category->id = $id;
            if ($category->delete()) {
                header('Location: ?route=categories');
            } else {
                // Manejar el error de restricción de clave foránea
                echo "Error al eliminar categoría. Asegúrate de que no tenga productos asociados.";
            }
        }
    }
}
?>