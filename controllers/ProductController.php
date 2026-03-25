<?php
require_once '../models/Category.php'; // Añadir esta línea para incluir el modelo Category
require_once '../models/Product.php';

class ProductController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        // Obtener categorías para el filtro
        $categoryModel = new Category();
        $stmtCat = $categoryModel->readAll();
        $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

        $product = new Product();
        $stmt = $product->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrar productos si se seleccionó una categoría
        if (isset($_GET['category']) && $_GET['category'] !== 'all') {
            $filterId = $_GET['category'];
            $products = array_filter($products, function($p) use ($filterId) {
                return isset($p['category_id']) && $p['category_id'] == $filterId;
            });
        }

        $content_view = '../views/admin/products/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function create() {
        // Obtener todas las categorías para el formulario
        $categoryModel = new Category();
        $stmt = $categoryModel->readAll();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $content_view = '../views/admin/products/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            // 1. CAPTURAR EL PRECIO MEDIO PLATO
            // Si viene vacío (porque no se activó el toggle), se asigna null
            $product->price_half = !empty($_POST['price_half']) ? $_POST['price_half'] : null;

            // VALIDACIÓN: El medio plato no puede ser más caro que el entero
            if (!is_null($product->price_half) && (float)$product->price_half >= (float)$product->price) {
                echo "<script>alert('Error: El precio de medio plato no puede ser mayor o igual al plato entero.'); window.history.back();</script>";
                return;
            }

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
            $product = $productModel->readOne();
            
            // Cargar categorías para el select en modo edición
            $categoryModel = new Category();
            $stmt = $categoryModel->readAll();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $content_view = '../views/admin/products/form.php';
            require_once '../views/layouts/admin_layout.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->id = $_POST['id'];
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            // 2. CAPTURAR EL PRECIO MEDIO PLATO TAMBIÉN AQUÍ
            $product->price_half = !empty($_POST['price_half']) ? $_POST['price_half'] : null;

            // VALIDACIÓN: El medio plato no puede ser más caro que el entero
            if (!is_null($product->price_half) && (float)$product->price_half >= (float)$product->price) {
                echo "<script>alert('Error: El precio de medio plato no puede ser mayor o igual al plato entero.'); window.history.back();</script>";
                return;
            }

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