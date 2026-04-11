<?php
require_once '../config/db.php';

class Setting {
    private $conn;
    private $table = 'settings';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtiene todos los ajustes como un array asociativo llave => valor
     */
    public function getAll() {
        $query = "SELECT setting_key, setting_value FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    /**
     * Actualiza o crea un ajuste
     */
    public function update($key, $value) {
        $query = "INSERT INTO " . $this->table . " (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value2";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':value2', $value);
        
        return $stmt->execute();
    }

    /**
     * Actualiza un valor específico en la tabla de configuraciones.
     */
    public function updateValue($key, $value) {
        return $this->update($key, $value);
    }

    public static function getValue($key) {
        $db = (new Database())->getConnection();
        $query = "SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['setting_value'] : null;
    }
}