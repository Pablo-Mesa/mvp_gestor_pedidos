<?php
/**
 * SCRIPT DE PRUEBAS AUTOMATIZADAS DE INTEGRIDAD DE PEDIDOS
 * Este script verifica que la reestructuración de la base de datos (orders + order_shipments) 
 * funcione correctamente para todos los casos de uso.
 */

// 1. Definir que estamos en modo prueba para evitar redirecciones en archivos incluidos
define('IS_TEST_MODE', true);

// 2. Asegurar que la sesión exista para evitar que db.php redirija al login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/plain');
require_once '../config/db.php';
require_once '../models/Order.php';

function assertTest($condition, $message) {
    echo ($condition ? "✅ PASSED" : "❌ FAILED") . ": $message\n";
    return $condition;
}

echo "--- INICIANDO PRUEBAS DE INTEGRIDAD DE PEDIDOS ---\n\n";

$db = (new Database())->getConnection();
// Guardaremos los IDs creados para borrarlos al final y mantener la DB limpia
$testOrderIds = [];

// --- CASO 1: Pedido Web para Delivery ---
echo "Caso 1: Pedido Web con Delivery y Ubicación Guardada\n";
$order1 = new Order();
$order1->client_id = 1;
$order1->channel_id = 1; // Web
$order1->delivery_type = 'delivery';
$order1->total = 55000;
$order1->status = 'pending';
$order1->payment_method = 'transferencia';
$order1->client_location_id = 1; // Supongamos que existe
$order1->delivery_rate_id = 2;   // Supongamos que existe
$order1->delivery_address = "Calle Falsa 123 (Snapshot)";
$order1->delivery_lat = "-25.3000";
$order1->delivery_lng = "-57.6000";
$order1->details = [
    ['product_id' => 1, 'quantity' => 2, 'price' => 25000]
];

$success1 = $order1->create();
assertTest($success1, "Creación de pedido exitosa");

if ($success1) {
    // Verificar que existe en orders
    $stmt = $db->prepare("SELECT id FROM orders WHERE id = ?");
    $testOrderIds[] = $order1->id;
    $stmt->execute([$order1->id]);
    assertTest($stmt->fetch(), "El pedido #{$order1->id} existe en la tabla 'orders'");

    // Verificar que existe el registro logístico
    $stmtShip = $db->prepare("SELECT * FROM order_shipments WHERE order_id = ?");
    $stmtShip->execute([$order1->id]);
    $shipment = $stmtShip->fetch(PDO::FETCH_ASSOC);

    assertTest($shipment !== false, "Se creó el registro en 'order_shipments'");
    assertTest($shipment['client_location_id'] == 1, "Vínculo correcto con ubicación del cliente");
    assertTest($shipment['address_snapshot'] === "Calle Falsa 123 (Snapshot)", "Snapshot de dirección guardado correctamente");
    assertTest($shipment['lat_snapshot'] === "-25.3000", "Snapshot de latitud guardado correctamente");
}

echo "\n------------------------------------------------\n";

// --- CASO 2: Venta de Mostrador (Pickup) ---
echo "Caso 2: Venta POS Mostrador (Sin Delivery)\n";
$order2 = new Order();
$order2->user_id = 1; // Admin/Cajero
$order2->client_id = 1; 
$order2->channel_id = 2; // POS
$order2->delivery_type = 'pickup';
$order2->total = 30000;
$order2->status = 'completed';
$order2->payment_method = 'efectivo';
$order2->details = [
    ['product_id' => 2, 'quantity' => 1, 'price' => 30000]
];

$success2 = $order2->create();
assertTest($success2, "Creación de venta POS exitosa");

if ($success2) {
    // Verificar que NO existe registro logístico (Porque es pickup)
    $stmtShip = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE order_id = ?");
    $testOrderIds[] = $order2->id;
    $stmtShip->execute([$order2->id]);
    $count = $stmtShip->fetchColumn();
    assertTest($count == 0, "No se creó registro en 'order_shipments' (Correcto para pickup)");
}

echo "\n------------------------------------------------\n";

// --- LIMPIEZA DE DATOS DE PRUEBA ---
if (!empty($testOrderIds)) {
    echo "Limpiando datos de prueba (IDs: " . implode(', ', $testOrderIds) . ")... ";
    $idsPlaceholder = implode(',', array_fill(0, count($testOrderIds), '?'));
    // Gracias a ON DELETE CASCADE en la FK, borrar el pedido borra automáticamente el shipment y los items
    $stmtDelete = $db->prepare("DELETE FROM orders WHERE id IN ($idsPlaceholder)");
    $stmtDelete->execute($testOrderIds);
    echo "✅ DB Limpia.\n";
}

echo "\n--- PRUEBAS FINALIZADAS ---\n";
?>
