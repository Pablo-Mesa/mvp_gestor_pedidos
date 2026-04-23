<?php
require_once '../models/HeroPromo.php';

class HeroPromoController {

    public function __construct() {
        // Seguridad: Solo administradores
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $model = new HeroPromo();
        $promos = $model->readAll();
        // Apuntamos a la ubicación correcta de la vista
        $content_view = '../views/admin/hero_promos/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=hero_promos'); exit; }

        $model = new HeroPromo();
        $promo = $model->readOne($id);
        
        $content_view = '../views/admin/hero_promos/edit.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $model = new HeroPromo();

            // Validación: No permitir guardar JSON inválido en tipos 'hours'
            if (trim($_POST['type'] ?? '') === 'hours') {
                $test = json_decode($_POST['content'], true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($test)) {
                    header('Location: ?route=hero_promos_edit&id=' . $id . '&error=invalid_json');
                    exit;
                }
            }

            $data = [
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'type' => $_POST['type'],
                'css_class' => $_POST['css_class'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'image' => $_POST['current_image']
            ];

            // Gestión de imagen de fondo
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $target_dir = "../public/uploads/";
                $filename = 'hero_' . time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $filename)) {
                    $data['image'] = $filename;
                }
            }

            if ($model->update($id, $data)) {
                header('Location: ?route=hero_promos&success=1');
            } else {
                echo "Error al actualizar la tarjeta.";
            }
        }
    }
}