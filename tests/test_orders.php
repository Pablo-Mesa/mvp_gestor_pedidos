<?php
/**
 * SCRIPT DE PRUEBAS AUTOMATIZADAS DE INTEGRIDAD DE PEDIDOS
 * Este script verifica que la reestructuración de la base de datos (orders + order_shipments) 
 * funcione correctamente para todos los casos de uso.
 *
 * PREREQUISITOS:
 * - Asegúrate de tener al menos 3 clientes en la tabla `clients` con IDs 1, 2, 3.
 * - Asegúrate de tener al menos 4 productos en la tabla `products` con IDs 1, 2, 3, 4.
 * - Asegúrate de tener al menos 1 ubicación de cliente guardada para `client_id = 1` en `client_locations` con ID 1.
 * - Asegúrate de tener al menos 2 tarifas de delivery activas en `delivery_rate_details` con IDs 1, 2.
 * - Asegúrate de tener al menos 1 usuario con rol 'admin' en la tabla `users` con ID 1.
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
echo "Caso 1: Pedido Web con Delivery y Ubicación Guardada (Estado Pendiente)\n";
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
assertTest($success1, "Creación de pedido exitosa para Caso 1");

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

    assertTest($shipment !== false, "Se creó el registro en 'order_shipments' para Caso 1");
    assertTest($shipment['client_location_id'] == 1, "Vínculo correcto con ubicación del cliente para Caso 1");
    assertTest($shipment['address_snapshot'] === "Calle Falsa 123 (Snapshot)", "Snapshot de dirección guardado correctamente para Caso 1");
    assertTest($shipment['lat_snapshot'] === "-25.3000", "Snapshot de latitud guardado correctamente para Caso 1");
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
assertTest($success2, "Creación de venta POS exitosa para Caso 2");

if ($success2) {
    $testOrderIds[] = $order2->id;
    // Verificar que NO existe registro logístico (Porque es pickup)
    $stmtShip = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE order_id = ?");
    $stmtShip->execute([$order2->id]);
    $count = $stmtShip->fetchColumn();
    assertTest($count == 0, "No se creó registro en 'order_shipments' (Correcto para pickup) para Caso 2");
}

echo "\n------------------------------------------------\n";

// --- CASO 3: Pedido Web para Retiro (Pickup) (Estado Pendiente) ---
echo "Caso 3: Pedido Web para Retiro (Pickup) (Estado Pendiente)\n";
$order3 = new Order();
$order3->client_id = 2; // Otro cliente
$order3->channel_id = 1; // Web
$order3->delivery_type = 'pickup';
$order3->total = 15000;
$order3->status = 'pending';
$order3->payment_method = 'pos'; // Pago con tarjeta/POS
$order3->details = [
    ['product_id' => 3, 'quantity' => 1, 'price' => 15000]
];

$success3 = $order3->create();
assertTest($success3, "Creación de pedido exitosa para Caso 3");

if ($success3) {
    $testOrderIds[] = $order3->id;
    $stmtShip = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE order_id = ?");
    $stmtShip->execute([$order3->id]);
    $count = $stmtShip->fetchColumn();
    assertTest($count == 0, "No se creó registro en 'order_shipments' (Correcto para pickup) para Caso 3");
}

echo "\n------------------------------------------------\n";

// --- CASO 4: Venta POS para Delivery (Nueva Ubicación, con Datos de Facturación) (Estado Confirmado) ---
echo "Caso 4: Venta POS para Delivery (Nueva Ubicación, con Datos de Facturación) (Estado Confirmado)\n";
$order4 = new Order();
$order4->user_id = 1; // Admin/Cajero
$order4->client_id = 1; 
$order4->channel_id = 2; // POS
$order4->delivery_type = 'delivery';
$order4->total = 60000; // 25000 + 30000 + 5000 (delivery rate 1)
$order4->status = 'confirmed';
$order4->payment_method = 'efectivo';
$order4->billing_name = 'Empresa de Prueba S.A.';
$order4->billing_ruc = '80000000-1';
$order4->delivery_address = "Av. Principal 456 (POS)";
$order4->delivery_lat = "-25.3100";
$order4->delivery_lng = "-57.6100";
$order4->delivery_rate_id = 1; // Supongamos que existe
$order4->details = [
    ['product_id' => 1, 'quantity' => 1, 'price' => 25000],
    ['product_id' => 2, 'quantity' => 1, 'price' => 30000]
];

$success4 = $order4->create();
assertTest($success4, "Creación de pedido exitosa para Caso 4");

if ($success4) {
    $testOrderIds[] = $order4->id;
    $stmtShip = $db->prepare("SELECT * FROM order_shipments WHERE order_id = ?");
    $stmtShip->execute([$order4->id]);
    $shipment = $stmtShip->fetch(PDO::FETCH_ASSOC);

    assertTest($shipment !== false, "Se creó el registro en 'order_shipments' para Caso 4");
    assertTest($shipment['client_location_id'] === null, "No se vinculó con ubicación guardada para Caso 4 (nueva ubicación)");
    assertTest($shipment['address_snapshot'] === "Av. Principal 456 (POS)", "Snapshot de dirección guardado correctamente para Caso 4");
    assertTest($order4->billing_name === "Empresa de Prueba S.A.", "Datos de facturación guardados para Caso 4");
}

echo "\n------------------------------------------------\n";

// --- CASO 5: Pedido Web para Consumo en Local (Estado En Cocina) ---
echo "Caso 5: Pedido Web para Consumo en Local (Estado En Cocina)\n";
$order5 = new Order();
$order5->client_id = 3; // Otro cliente
$order5->channel_id = 1; // Web
$order5->delivery_type = 'local';
$order5->total = 20000;
$order5->status = 'preparing';
$order5->payment_method = 'efectivo';
$order5->billing_name = 'Juan Perez';
$order5->billing_ruc = '1234567-8';
$order5->details = [
    ['product_id' => 4, 'quantity' => 2, 'price' => 10000]
];

$success5 = $order5->create();
assertTest($success5, "Creación de pedido exitosa para Caso 5");

if ($success5) {
    $testOrderIds[] = $order5->id;
    $stmtShip = $db->prepare("SELECT COUNT(*) FROM order_shipments WHERE order_id = ?");
    $stmtShip->execute([$order5->id]);
    $count = $stmtShip->fetchColumn();
    assertTest($count == 0, "No se creó registro en 'order_shipments' (Correcto para consumo en local) para Caso 5");
    assertTest($order5->billing_name === "Juan Perez", "Datos de facturación guardados para Caso 5");
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
