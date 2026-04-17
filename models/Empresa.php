<?php
require_once '../config/db.php';

class Empresa {
    private $conn;
    private $table = 'empresa';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY razon_social ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (razon_social, ruc, dv, direccion, telefono, email, timbrado_vigente, fecha_desde_timbrado, fecha_hasta_timbrado, punto_emision, sucursal, actividad_economica, estado) 
                  VALUES (:razon_social, :ruc, :dv, :direccion, :telefono, :email, :timbrado_vigente, :fecha_desde_timbrado, :fecha_hasta_timbrado, :punto_emision, :sucursal, :actividad_economica, :estado)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($this->mapData($data));
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  razon_social = :razon_social, ruc = :ruc, dv = :dv, direccion = :direccion, 
                  telefono = :telefono, email = :email, timbrado_vigente = :timbrado_vigente, 
                  fecha_desde_timbrado = :fecha_desde_timbrado, fecha_hasta_timbrado = :fecha_hasta_timbrado, 
                  punto_emision = :punto_emision, sucursal = :sucursal, 
                  actividad_economica = :actividad_economica, estado = :estado 
                  WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($this->mapData($data));
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        return $this->conn->prepare($query)->execute([':id' => $id]);
    }

    private function mapData($data) {
        $fields = ['razon_social', 'ruc', 'dv', 'direccion', 'telefono', 'email', 'timbrado_vigente', 'fecha_desde_timbrado', 'fecha_hasta_timbrado', 'punto_emision', 'sucursal', 'actividad_economica', 'estado'];
        if (isset($data['id'])) $fields[] = 'id';
        
        $mapped = [];
        foreach ($fields as $f) {
            $mapped[':' . $f] = $data[$f] ?? null;
        }
        return $mapped;
    }
}