<?php
require_once '../models/CashRegister.php';

class CashController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function index() {
        $model = new CashRegister();
        $activeSession = $model->getActiveSession();
        $movements = [];
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
            $amount = $_POST['opening_balance'] ?? 0;
            
            if ($model->open($_SESSION['user_id'], $amount)) {
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
            $session = $model->getActiveSession();
            
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
            $session = $model->getActiveSession();
            
            if ($session) {
                $physical = $_POST['physical_balance'] ?? 0;
                $expected = $session['opening_balance'] + $_POST['ingress_total'] - $_POST['egress_total'];
                
                if ($model->close($session['id'], $physical, $expected)) {
                    header('Location: ?route=cash&success=closed');
                } else {
                    header('Location: ?route=cash&error=close_failed');
                }
            }
            exit;
        }
    }
}