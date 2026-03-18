<?php
require_once '../models/Product.php';

class ProductController {
    // No es necesario instanciar CategoryModel aquí, se hace en los métodos que lo necesitan.

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $product = new Product();
        $stmt = $product->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content_view = '../views/admin/products/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function create() {
        require_once '../models/Category.php'; // Cargar el modelo Category
        $categoryModel = new Category();
        $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        $content_view = '../views/admin/products/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id']; // Cambiado a category_id
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Manejo de Imagen
            $product->image = ''; // Por defecto
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $target_dir = "../public/uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                
                $filename = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $product->image = $filename;
                }
            }

            if ($product->create()) {
                header('Location: ?route=products');
            } else {
                echo "Error al crear producto";
            }
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $productModel = new Product();
            $productModel->id = $id;
            $product = $productModel->readOne(); // Esto ahora incluye category_name

            require_once '../models/Category.php'; // Cargar el modelo Category
            $categoryModel = new Category();
            $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
            
            $content_view = '../views/admin/products/form.php';
            require_once '../views/layouts/admin_layout.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->id = $_POST['id'];
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id']; // Cambiado a category_id
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->is_active = isset($_POST['is_active']) ? 1 : 0;
            $product->image = $_POST['current_image']; // Mantener imagen anterior si no se sube nueva

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $target_dir = "../public/uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                
                $filename = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $filename;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $product->image = $filename;
                }
            }

            if ($product->update()) {
                header('Location: ?route=products');
            } else {
                echo "Error al actualizar";
            }
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $product = new Product();
            $product->id = $id;
            if ($product->delete()) {
                header('Location: ?route=products');
            }
        }
    }
}
?>