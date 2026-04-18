<?php
// Archivo temporal para crear el primer admin
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

$password = 'admin123'; // Contraseña deseada
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :pass, :role)";
$stmt = $conn->prepare($sql);

try {
    $stmt->execute([
        ':name' => 'Administrador',
        ':email' => 'admin@comedor.com',
        ':pass' => $hashed_password,
        ':role' => 'admin'
    ]);
    echo "Usuario Admin creado con éxito.<br>Email: admin@comedor.com<br>Pass: admin123";
} catch (PDOException $e) {
    echo "Error (quizás ya existe): " . $e->getMessage();
}
?>