<?php
require_once '../models/Setting.php';
require_once '../models/DeliveryRate.php';

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

    public function sifende() {
        $model = new Setting();
        $settings = $model->getAll();
        
        $content_view = '../views/admin/settings/sifende.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function checkout() {
        $model = new Setting();
        $settings = $model->getAll();
        
        $content_view = '../views/admin/settings/checkout.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function shortcuts() {
        $content_view = '../views/admin/settings/shortcuts.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function deliveryRates() {
        $rateModel = new DeliveryRate();
        
        // Si se pasa un ID, cargamos esa versión, si no, la activa por defecto
        $id = $_GET['id'] ?? null;
        $isNew = isset($_GET['new']);

        if ($isNew) {
            $activeRate = null;
        } else {
            $activeRate = $id ? $rateModel->getById($id) : $rateModel->getActive();
        }
        
        $allRates = $rateModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        
        $content_view = '../views/admin/settings/delivery_rates.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function setDeliveryRateActive() {
        while (ob_get_level()) ob_end_clean();
        ob_start();
        header('Content-Type: application/json');

        // Asegurarse de que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }

        // Obtener el ID de la versión a activar desde el cuerpo de la solicitud POST
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID de versión no proporcionado o inválido.']);
            exit;
        }

        try {
            $rateModel = new DeliveryRate();
            if ($rateModel->setActive($id)) {
                // Éxito: devolver JSON
                echo json_encode(['success' => true, 'id' => $id, 'message' => 'Versión activada correctamente.']);
                exit;
            } else {
                // Fallo en el modelo (ej. ID no encontrado, error de DB)
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'message' => 'No se pudo activar la versión de tarifa.']);
                exit;
            }
        } catch (InvalidArgumentException $e) {
            // Captura la excepción específica si el ID no existe o no se pudo activar
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        } catch (PDOException $e) {
            // Error de base de datos
            error_log("Error PDO al activar tarifa de delivery: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error de base de datos al activar la tarifa.']);
            exit;
        } catch (Exception $e) {
            // Otros errores inesperados
            error_log("Error inesperado al activar tarifa de delivery: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Ocurrió un error inesperado.']);
            exit;
        }
    }

    public function updateDeliveryRates() {
        while (ob_get_level()) ob_end_clean();
        ob_start();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rateModel = new DeliveryRate();
            
            $rates = [];
            if (isset($_POST['km_start'])) {
                foreach ($_POST['km_start'] as $i => $start) {
                    $end = $_POST['km_end'][$i];
                    $price = $_POST['price'][$i];

                    // Ignorar filas que estén completamente vacías
                    if ($start === '' && $end === '' && $price === '') continue;

                    $rates[] = [
                        'start' => (float)$start,
                        'end'   => (float)$end,
                        'price' => (float)$price
                    ];
                }
            }

            $newId = $rateModel->createVersion($_SESSION['user_id'], $rates);
            if ($newId) {
                echo json_encode(['success' => true, 'id' => $newId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No se pudo crear la nueva versión.']);
            }
        }
        exit;
    }

    public function addDeliveryRanges() {
        while (ob_get_level()) ob_end_clean();
        ob_start();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rateId = filter_input(INPUT_POST, 'rate_id', FILTER_VALIDATE_INT);
            if (!$rateId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de versión no válido.']);
                exit;
            }

            $rateModel = new DeliveryRate();
            $newRanges = [];
            
            if (isset($_POST['km_start'])) {
                foreach ($_POST['km_start'] as $i => $start) {
                    // Al estar los viejos disabled, solo recibiremos los nuevos enabled
                    $end = $_POST['km_end'][$i];
                    $price = $_POST['price'][$i];

                    if ($start === '' || $end === '' || $price === '') continue;

                    $newRanges[] = [
                        'start' => (float)$start,
                        'end'   => (float)$end,
                        'price' => (float)$price
                    ];
                }
            }

            if ($rateModel->addRanges($rateId, $newRanges)) {
                echo json_encode(['success' => true, 'id' => $rateId, 'message' => 'Nuevos rangos añadidos correctamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No se pudieron añadir los rangos.']);
            }
        }
        exit;
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
            $redirectRoute = 'settings';
            
            // Actualizar Nombre
            if (isset($_POST['site_name'])) {
                $model->update('site_name', $_POST['site_name']);
            }

            // Actualizar Credenciales de Sifende (API Facturación)
            if (isset($_POST['sifende_settings'])) {
                $model->update('sifende_app_id', $_POST['sifende_app_id'] ?? '');
                $model->update('sifende_app_key', $_POST['sifende_app_key'] ?? '');
                $model->update('sifende_api_url', $_POST['sifende_api_url'] ?? '');
                $redirectRoute = 'settings_sifende';
            }

            // Actualizar Módulos de Checkout
            if (isset($_POST['checkout_settings'])) {
                $model->update('enable_legal_invoice', isset($_POST['enable_legal_invoice']) ? '1' : '0');
                $redirectRoute = 'settings_checkout';
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

            header('Location: ?route=' . $redirectRoute . '&success=1');
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

    public function contactSettings() {
        $settingModel = new Setting();
        $rawContacts = $settingModel->get('contact_channels');
        
        $contacts = $rawContacts ? json_decode($rawContacts, true) : [];

        $content_view = '../views/admin/settings_contact.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function saveContactSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel = new Setting();
            
            $rawContacts = $_POST['contacts'] ?? [];
            $processedContacts = [];

            foreach ($rawContacts as $c) {
                if (!empty(trim($c['phone']))) {
                    $processedContacts[] = [
                        'label'    => htmlspecialchars(strip_tags($c['label'])),
                        'phone'    => htmlspecialchars(strip_tags($c['phone'])),
                        'calls'    => isset($c['calls']) ? 1 : 0,
                        'sms'      => isset($c['sms']) ? 1 : 0,
                        'whatsapp' => isset($c['whatsapp']) ? 1 : 0
                    ];
                }
            }

            $jsonContacts = json_encode($processedContacts, JSON_UNESCAPED_UNICODE);
            
            if ($settingModel->update('contact_channels', $jsonContacts)) {
                header('Location: ?route=settings_contact&success=1');
            } else {
                header('Location: ?route=settings_contact&error=save_failed');
            }
            exit;
        }
    }
}