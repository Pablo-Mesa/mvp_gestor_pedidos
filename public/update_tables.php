<?php
// Archivo temporal para actualizar la estructura de la base de datos
require_once '../config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Lista de columnas individuales para probar una por una
    $columns = [
        "ADD COLUMN delivery_type VARCHAR(50) DEFAULT 'pickup'",
        "ADD COLUMN payment_method VARCHAR(50) DEFAULT 'efectivo'",
        "ADD COLUMN delivery_address TEXT NULL",
        "ADD COLUMN delivery_lat VARCHAR(50) NULL",
        "ADD COLUMN delivery_lng VARCHAR(50) NULL",
        "ADD COLUMN observation TEXT NULL",
        "MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'" // Aseguramos que status tenga longitud suficiente
    ];

    echo "<div style='font-family: sans-serif; padding: 20px; background: #f8f9fa; border-radius: 5px; margin: 20px;'>";
    echo "<h3>🛠️ Reparación de Base de Datos</h3>";

    foreach ($columns as $col) {
        try {
            $conn->exec("ALTER TABLE orders $col");
            echo "<p style='color: green;'>✅ Se agregó: " . explode(' ', $col)[2] . "</p>";
        } catch (PDOException $e) {
            // 42S21 = Column already exists
            if ($e->getCode() == '42S21') {
                echo "<p style='color: #666;'>ℹ️ Ya existe (omitido): " . explode(' ', $col)[2] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Error en " . explode(' ', $col)[2] . ": " . $e->getMessage() . "</p>";
            }
        }
    }

    // También verificamos que la tabla orders_items exista, por si acaso
    $sqlItems = "CREATE TABLE IF NOT EXISTS orders_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL
    )";
    $conn->exec($sqlItems);
    echo "<p style='color: green;'>✅ Tabla orders_items verificada.</p>";

    echo "<br><a href='index.php?route=orders' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir a Pedidos (Admin)</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "Error general: " . $e->getMessage();
}
?>