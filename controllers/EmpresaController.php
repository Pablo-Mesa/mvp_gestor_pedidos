<?php
require_once '../models/Empresa.php';

class EmpresaController {
    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $model = new Empresa();
        $empresas = $model->readAll()->fetchAll(PDO::FETCH_ASSOC);
        $content_view = '../views/admin/empresa/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function create() {
        $model = new Empresa();
        $existing = $model->readAll()->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($existing) > 0) {
            header('Location: ?route=empresa');
            exit;
        }

        $content_view = '../views/admin/empresa/form.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación: Solo Razón Social es obligatorio
            if (empty(trim($_POST['razon_social'] ?? ''))) {
                header('Location: ?route=empresa_create&error=required_fields');
                exit;
            }

            $model = new Empresa();
            $existing = $model->readAll()->fetchAll(PDO::FETCH_ASSOC);
            if (count($existing) > 0) {
                header('Location: ?route=empresa');
                exit;
            }

            if ($this->validateData($_POST) && $model->create($_POST)) {
                header('Location: ?route=empresa&success=created');
            } else {
                header('Location: ?route=empresa_create&error=invalid_data');
            }
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $model = new Empresa();
            $empresa = $model->readOne($id);
            $content_view = '../views/admin/empresa/form.php';
            require_once '../views/layouts/admin_layout.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($_POST['razon_social'] ?? ''))) {
                header('Location: ?route=empresa_edit&id=' . $_POST['id'] . '&error=required_fields');
                exit;
            }

            $model = new Empresa();
            if ($this->validateData($_POST) && $model->update($_POST['id'], $_POST)) {
                header('Location: ?route=empresa&success=updated');
            } else {
                header('Location: ?route=empresa_edit&id=' . $_POST['id'] . '&error=invalid_data');
            }
            exit;
        }
    }

    public function delete() {
        // Funcionalidad deshabilitada por requerimiento: Solo puede existir un registro.
        header('Location: ?route=empresa');
    }

    /**
     * Valida la integridad de los datos recibidos
     */
    private function validateData($data) {
        // Validar Email si se proporciona
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validar que la fecha hasta no sea menor a la fecha desde
        if (!empty($data['fecha_desde_timbrado']) && !empty($data['fecha_hasta_timbrado'])) {
            if (strtotime($data['fecha_hasta_timbrado']) < strtotime($data['fecha_desde_timbrado'])) {
                return false;
            }
        }

        // Validar longitudes máximas según DB
        if (strlen($data['dv'] ?? '') > 1) return false;
        if (strlen($data['timbrado_vigente'] ?? '') > 8) return false;
        if (strlen($data['ruc'] ?? '') > 15) return false;

        return true;
    }
}