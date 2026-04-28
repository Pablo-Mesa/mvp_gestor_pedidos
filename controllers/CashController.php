<?php
require_once '../models/CashRegister.php';
require_once '../models/User.php';

class CashController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'cajero'])) {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $model = new CashRegister();
        $userModel = new User();

        $activeSession = $model->getActiveSession($_SESSION['user_id']);
        $movements = [];
        $recentSessions = $model->getRecentSessions();
        $cashiers = array_filter($userModel->readAll()->fetchAll(PDO::FETCH_ASSOC), function($u) {
            return in_array($u['role'], ['admin', 'cajero']);
        });
        $totals = ['ingress' => 0, 'egress' => 0];

        if ($activeSession) {
            $movements = $model->getMovements($activeSession['id']);
            foreach ($movements as $m) {
                $totals[$m['type']] += $m['amount'];
            }
        }

        $content_view = '../views/admin/cash/index.php';
        require_once '../views/layouts/admin_layout.php';
    }

    public function open() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new CashRegister();
            $amount = $_POST['opening_amount'] ?? 0;
            $station = $_POST['cash_station'] ?? 'Principal';
            // Priorizamos el user_id del formulario (enviado por admin) o usamos el propio de la sesión
            $userId = $_POST['user_id'] ?? $_SESSION['user_id'];
            
            // Validar que el usuario seleccionado no tenga ya una sesión abierta
            if ($model->getActiveSession($userId)) {
                header('Location: ?route=cash&error=user_has_active_session');
                exit;
            }

            // Validar que la caja física no esté abierta por otro
            if ($model->isStationOpen($station)) {
                header('Location: ?route=cash&error=station_occupied');
                exit;
            }

            if ($model->open($userId, $amount, $station)) {
                header('Location: ?route=cash&success=opened');
            } else {
                header('Location: ?route=cash&error=open_failed');
            }
            exit;
        }
    }

    public function storeMovement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new CashRegister();
            $session = $model->getActiveSession($_SESSION['user_id']);
            
            if ($session) {
                $model->addMovement(
                    $session['id'],
                    $_POST['amount'],
                    $_POST['type'],
                    $_POST['description'],
                    'manual'
                );
                header('Location: ?route=cash&success=movement_added');
            } else {
                header('Location: ?route=cash&error=no_active_session');
            }
            exit;
        }
    }

    public function close() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new CashRegister();
            $sessionId = $_POST['session_id'] ?? null;
            $session = $model->getSessionById($sessionId);
            
            if ($session && $session['status'] === 'open') {
                $physical = $_POST['physical_balance'] ?? 0;
                
                // Calculamos el saldo esperado real desde el servidor (más seguro)
                $totals = $model->getSessionTotals($sessionId);
                $expected = $session['opening_amount'] + ($totals['ingress'] ?? 0) - ($totals['egress'] ?? 0);
                
                if ($model->close($sessionId, $physical, $expected)) {
                    header('Location: ?route=cash&success=closed');
                } else {
                    header('Location: ?route=cash&error=close_failed');
                }
            }
            exit;
        }
    }
}