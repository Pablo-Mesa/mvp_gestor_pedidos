<?php
class AdminController {

    public function __construct() {
        // 1. Verificación de Seguridad: Solo Admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ?route=login');
            exit;
        }
    }

    public function dashboard() {
        // Datos simulados para la vista (luego vendrán de la DB)
        $data = [
            'title' => 'Resumen del Día',
            'pedidos_pendientes' => 5,
            'ingresos_hoy' => 150.00,
            'platos_vendidos' => 32
        ];

        // Definimos qué vista interna queremos cargar
        $content_view = '../views/admin/dashboard.php';
        
        // Cargamos el Layout Principal (que incluirá a $content_view)
        require_once '../views/layouts/admin_layout.php';
    }
}
?>