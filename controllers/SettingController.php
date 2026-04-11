<?php
require_once '../models/Setting.php';

class SettingController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $model = new Setting();
        $settings = $model->getAll();
        
        $content_view = '../views/admin/settings/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function location() {
        $model = new Setting();
        $settings = $model->getAll();
        
        $content_view = '../views/admin/settings/location.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function deliveryRates() {
        $model = new Setting();
        $settings = $model->getAll();
        
        $content_view = '../views/admin/settings/delivery_rates.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function updateDeliveryRates() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Setting();
            
            $rates = [];
            if (isset($_POST['km_start'])) {
                foreach ($_POST['km_start'] as $i => $start) {
                    $rates[] = [
                        'start' => (float)$start,
                        'end'   => (float)$_POST['km_end'][$i],
                        'price' => (float)$_POST['price'][$i]
                    ];
                }
            }

            if ($model->update('delivery_rates_json', json_encode($rates))) {
                header('Location: ?route=settings_delivery&success=1');
            } else {
                header('Location: ?route=settings_delivery&error=update_failed');
            }
            exit;
        }
    }

    public function updateLocation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Setting();
            
            // Recopilamos los datos del mapa y la dirección
            $data = [
                'store_lat'     => $_POST['store_lat'] ?? '',
                'store_lng'     => $_POST['store_lng'] ?? '',
                'store_address' => $_POST['store_address'] ?? ''
            ];

            $success = true;
            foreach ($data as $key => $value) {
                if (!$model->update($key, $value)) {
                    $success = false;
                }
            }

            if ($success) {
                header('Location: ?route=settings_location&success=1');
            } else {
                header('Location: ?route=settings_location&error=update_failed');
            }
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Setting();
            
            // Actualizar Nombre
            if (isset($_POST['site_name'])) {
                $model->update('site_name', $_POST['site_name']);
            }

            // Actualizar Tarifas de Delivery
            if (isset($_POST['delivery_base_cost'])) {
                $model->update('delivery_base_cost', $_POST['delivery_base_cost']);
                $model->update('delivery_km_cost', $_POST['delivery_km_cost']);
                $model->update('delivery_base_distance', $_POST['delivery_base_distance']);
            }

            // Manejo del Logo
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
                // 1. Validar tamaño (máximo 1MB)
                if ($_FILES['site_logo']['size'] > 1024 * 1024) {
                    header('Location: ?route=settings&error=file_too_large');
                    exit;
                }

                // 2. Validar dimensiones y tipo
                $imageInfo = getimagesize($_FILES['site_logo']['tmp_name']);
                if (!$imageInfo) {
                    header('Location: ?route=settings&error=invalid_image');
                    exit;
                }

                if ($imageInfo[0] > 1200 || $imageInfo[1] > 1200) {
                    header('Location: ?route=settings&error=dimensions_too_large');
                    exit;
                }

                $uploadDir = 'uploads/';
                
                // Crear directorio si no existe
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileExtension = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
                $fileName = 'logo_brand_' . time() . '.' . $fileExtension;
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetFile)) {
                    // Opcional: Borrar logo anterior si existe
                    $oldLogo = $model->getValue('site_logo');
                    if ($oldLogo && file_exists($uploadDir . $oldLogo)) {
                        unlink($uploadDir . $oldLogo);
                    }
                    $model->update('site_logo', $fileName);
                }
            }

            header('Location: ?route=settings&success=1');
            exit;
        }
    }

    public function reset() {
        $model = new Setting();
        
        // Borrar el archivo físico del logo si existe
        $oldLogo = $model->getValue('site_logo');
        if ($oldLogo && file_exists('uploads/' . $oldLogo)) {
            unlink('uploads/' . $oldLogo);
        }

        // Restaurar valores por defecto
        $model->update('site_name', 'Solver');
        $model->update('site_logo', '');

        header('Location: ?route=settings&success=reset');
    }
}