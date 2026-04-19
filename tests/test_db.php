<?php
/**
 * SCRIPT DE DIAGNÓSTICO DE BASE DE DATOS
 * Ejecuta este archivo desde el navegador: http://localhost/.../tests/test_db.php
 */

require_once '../config/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- Diagnóstico de Conexión ---" . PHP_EOL;

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($db instanceof PDO) {
        echo "✅ ÉXITO: Conexión establecida correctamente." . PHP_EOL;
        
        // Verificación de integridad: Consultar nombre de DB y versión
        $stmt = $db->query("SELECT DATABASE() as db, VERSION() as ver");
        $info = $stmt->fetch();
        
        echo "Base de datos activa: " . ($info['db'] ?? 'No seleccionada') . PHP_EOL;
        echo "Versión de MySQL/MariaDB: " . $info['ver'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
}