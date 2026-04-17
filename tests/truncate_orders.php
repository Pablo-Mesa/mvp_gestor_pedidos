<?php
/**
 * SCRIPT DE UTILIDAD: Vaciado de tablas de pedidos
 * PRECAUCIÓN: Esta acción es irreversible y borrará todos los pedidos.
 */

require_once '../config/db.php';

header('Content-Type: text/plain');

try {
    $db = (new Database())->getConnection();
    
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE orders_items;");
    $db->exec("TRUNCATE TABLE order_shipments;");
    $db->exec("TRUNCATE TABLE orders;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "✅ ÉXITO: Las tablas orders, orders_items y order_shipments han sido vaciadas.\n";
    echo "Los contadores de ID han sido reiniciados a 1.";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}