<?php
require_once '../models/Product.php';
require_once '../models/Category.php';

class ProductController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            if (isset($_GET['route']) && $_GET['route'] === 'products_api') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'No autorizado']);
            } else {
                header('Location: ?route=login');
            }
            exit;
        }
    }

    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();

        $filters = [
            'search'   => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? 'all'
        ];

        $products = $productModel->readAll($filters)->fetchAll(PDO::FETCH_ASSOC);
        $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/products/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    /**
     * Endpoint para búsqueda y filtrado dinámico vía AJAX
     */
    public function apiIndex() {
        $productModel = new Product();
        
        // Recogemos los filtros del GET
        $filters = [
            'search'   => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? 'all'
        ];

        // El modelo ya tiene la lógica para buscar por ID (código) o Nombre
        $stmt = $productModel->readAll($filters);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolvemos el JSON
        header('Content-Type: application/json');
        echo json_encode($products);
        exit;
    }

    public function create() {
        $categoryModel = new Category();
        $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        
        $content_view = '../views/admin/products/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->codigobarra = $_POST['codigobarra'] ?? null;
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id'];
            $product->description = $_POST['description'];
            $product->es_vendible = isset($_POST['es_vendible']) ? 1 : 0;
            $product->price = $_POST['price'];
            $product->price_half = $_POST['price_half'] ?? null;
            $product->is_active = isset($_POST['is_active']) ? 1 : 0;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $filename = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
                $product->image = $filename;
            }

            if ($product->create()) {
                header('Location: ?route=products&success=created');
            } else {
                echo "Error al crear el producto.";
            }
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=products'); exit; }

        $productModel = new Product();
        $productModel->id = $id;
        $product = $productModel->readOne();

        $categoryModel = new Category();
        $categories = $categoryModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/products/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = new Product();
            $product->id = $_POST['id'];
            $product->codigobarra = $_POST['codigobarra'] ?? null;
            $product->name = $_POST['name'];
            $product->category_id = $_POST['category_id'];
            $product->description = $_POST['description'];
            $product->es_vendible = isset($_POST['es_vendible']) ? 1 : 0;
            $product->price = $_POST['price'];
            $product->price_half = $_POST['price_half'] ?? null;
            $product->is_active = isset($_POST['is_active']) ? 1 : 0;
            $product->image = $_POST['current_image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $filename = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
                $product->image = $filename;
            }

            if ($product->update()) {
                header('Location: ?route=products&success=updated');
            } else {
                echo "Error al actualizar el producto.";
            }
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $product = new Product();
            $product->id = $id;
            if ($product->delete()) {
                header('Location: ?route=products&success=deleted');
            } else {
                echo "Error al eliminar.";
            }
        }
    }
}
?>