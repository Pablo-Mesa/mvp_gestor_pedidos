<?php
/**
 * SCRIPT DE RESET TOTAL DE TRANSACCIONES
 * Vacía completamente las tablas operativas para iniciar pruebas desde cero.
 * Mantiene los datos maestros (Productos, Usuarios, Clientes, Configuración).
 */

// 1. Establecer zona horaria
date_default_timezone_set('America/Asuncion');
require_once '../config/db.php';
header('Content-Type: text/plain; charset=utf-8');
echo "--- INICIANDO LIMPIEZA TOTAL DEL SISTEMA ---" . PHP_EOL;
echo "Fecha de ejecución: " . date('d/m/Y H:i:s') . PHP_EOL . PHP_EOL;

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Desactivar revisión de llaves foráneas para permitir TRUNCATE masivo
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = [
        'pagos_detalles',
        'pagos',
        'pos_ventas_detalle',
        'pos_ventas_cabecera',
        'orders_items',
        'order_shipments',
        'orders',
        'cash_movements',
        'cash_registers',
        'delivery_checkins'
    ];

    foreach ($tables as $table) {
        $db->exec("TRUNCATE TABLE $table");
        echo "✅ Tabla '$table' vaciada e IDs reiniciados." . PHP_EOL;
    }

    // Volver a activar revisión de llaves foráneas
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo PHP_EOL . "🚀 EL SISTEMA HA SIDO VACIADO CON ÉXITO." . PHP_EOL;
    echo "Se han conservado Productos, Clientes, Menús, Usuarios y Configuraciones." . PHP_EOL;
    echo "Los pedidos ahora comenzarán nuevamente desde el ID #1." . PHP_EOL;

} catch (Exception $e) {
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo PHP_EOL . "❌ ERROR CRÍTICO: " . $e->getMessage() . PHP_EOL;
}
?>