<?php
/**
 * SCRIPT DE RESET PARA PRUEBAS
 * Borra todas las transacciones (Pedidos, Ventas, Pagos, Caja) realizadas el día de hoy.
 */

// 1. Establecer zona horaria
date_default_timezone_set('America/Asuncion');

require_once '../config/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- INICIANDO RESET DE TRANSACCIONES ---" . PHP_EOL;
echo "Fecha de limpieza: " . date('d/m/Y') . PHP_EOL . PHP_EOL;

try {
    $database = new Database();
    $db = $database->getConnection();
    $today = date('Y-m-d');

    $db->beginTransaction();

    // --- 1. LIMPIEZA DE CAJA ---
    // Borramos movimientos de hoy
    $st = $db->prepare("DELETE FROM cash_movements WHERE DATE(created_at) = :today");
    $st->execute([':today' => $today]);
    echo "✅ Movimientos de caja eliminados: " . $st->rowCount() . PHP_EOL;

    // Borramos sesiones de caja abiertas hoy
    $st = $db->prepare("DELETE FROM cash_registers WHERE DATE(opened_at) = :today");
    $st->execute([':today' => $today]);
    echo "✅ Sesiones de caja eliminadas: " . $st->rowCount() . PHP_EOL;

    // --- 2. LIMPIEZA DE LOGÍSTICA ---
    $st = $db->prepare("DELETE FROM delivery_checkins WHERE DATE(checkin_time) = :today");
    $st->execute([':today' => $today]);
    echo "✅ Asistencias de delivery eliminadas: " . $st->rowCount() . PHP_EOL;

    // --- 3. LIMPIEZA DE VENTAS Y PAGOS ---
    // Subquery para identificar ventas de hoy
    $salesTodaySub = "SELECT id FROM pos_ventas_cabecera WHERE DATE(fecha_hora) = '$today'";

    // Borramos detalles de pagos y pagos
    $db->exec("DELETE FROM pagos_detalles WHERE pago_id IN (SELECT id FROM pagos WHERE venta_id IN ($salesTodaySub))");
    $db->exec("DELETE FROM pagos WHERE venta_id IN ($salesTodaySub)");
    
    // Borramos detalles de ventas
    $db->exec("DELETE FROM pos_ventas_detalle WHERE venta_id IN ($salesTodaySub)");

    // Borramos cabeceras de ventas
    $st = $db->prepare("DELETE FROM pos_ventas_cabecera WHERE DATE(fecha_hora) = :today");
    $st->execute([':today' => $today]);
    echo "✅ Ventas y Facturas eliminadas: " . $st->rowCount() . PHP_EOL;

    // --- 4. LIMPIEZA DE PEDIDOS ---
    // Subquery para identificar pedidos de hoy
    $ordersTodaySub = "SELECT id FROM orders WHERE DATE(created_at) = '$today'";

    // Borramos ítems y shipments (envíos)
    $db->exec("DELETE FROM orders_items WHERE order_id IN ($ordersTodaySub)");
    $db->exec("DELETE FROM order_shipments WHERE order_id IN ($ordersTodaySub)");

    // Borramos cabeceras de pedidos
    $st = $db->prepare("DELETE FROM orders WHERE DATE(created_at) = :today");
    $st->execute([':today' => $today]);
    echo "✅ Pedidos eliminados: " . $st->rowCount() . PHP_EOL;

    $db->commit();
    
    echo PHP_EOL . "🚀 EL SISTEMA HA SIDO RESETEADO CON ÉXITO." . PHP_EOL;
    echo "Ya puedes realizar nuevas pruebas de flujo completo (Pedido -> Venta -> Pago -> Caja)." . PHP_EOL;

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    echo PHP_EOL . "❌ ERROR CRÍTICO: " . $e->getMessage() . PHP_EOL;
}
?>