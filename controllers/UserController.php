<?php
require_once '../models/User.php';

class UserController {

    public function __construct() {
        // Seguridad: Solo Admins entran aquí
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $userModel = new User();
        $stmt = $userModel->readAll();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $content_view = '../views/admin/users/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function create() {
        $content_view = '../views/admin/users/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            
            $_POST['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            
            // Validación básica: evitar emails duplicados
            if ($userModel->findByEmail($_POST['email'])) {
                header('Location: ?route=users_create&error=email_exists');
                exit;
            }

            if ($userModel->create($_POST)) {
                header('Location: ?route=users&success=created');
            } else {
                header('Location: ?route=users_create&error=sql_error');
            }
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ?route=users'); exit; }

        $userModel = new User();
        $user = $userModel->readOne($id);
        
        if (!$user) { header('Location: ?route=users'); exit; }

        $content_view = '../views/admin/users/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            
            $_POST['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            
            // No permitir que el admin se cambie el rol a sí mismo si es el único admin
            // (Lógica de seguridad opcional pero recomendada)
            
            if ($userModel->update($_POST)) {
                header('Location: ?route=users&success=updated');
            } else {
                header('Location: ?route=users_edit&id=' . $_POST['id'] . '&error=sql_error');
            }
        }
    }

    public function toggleStatus() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 1;

        if ($id && $id != $_SESSION['user_id']) {
            $userModel = new User();
            $data = $userModel->readOne($id);
            if ($data) {
                $data['is_active'] = ($status == 1) ? 0 : 1;
                $data['password'] = ""; // No actualizar password
                $userModel->update($data);
            }
        }
        header('Location: ?route=users&success=updated');
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        
        // Seguridad: El admin no puede borrarse a sí mismo
        if ($id == $_SESSION['user_id']) {
            header('Location: ?route=users&error=self_delete');
            exit;
        }

        if ($id) {
            $userModel = new User();
            if ($userModel->delete($id)) {
                header('Location: ?route=users&success=deleted');
            } else {
                header('Location: ?route=users&error=delete_failed');
            }
        }
    }
}
?>