<?php
require_once '../config/db.php';

class Client {
    private $conn;
    private $table = 'clients';

    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $has_whatsapp;
    public $billing_name;
    public $billing_ruc;
    
    // Campos adicionales para FacturaSend
    public $nombre_fantasia;
    public $tipo_operacion;
    public $numero_casa;
    public $departamento;
    public $departamento_descripcion;
    public $distrito;
    public $distrito_descripcion;
    public $ciudad;
    public $ciudad_descripcion;
    public $pais;
    public $pais_descripcion;
    public $tipo_contribuyente;
    public $documento_numero;
    public $celular;
    public $codigo;
    public $contribuyente;
    public $billing_address;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register() {
        // 1. Limpieza de Email: Si no es un correo válido o está vacío, forzamos NULL
        // Esto evita el error de "Duplicate entry ''" y previene datos basura combinados.
        $isValidEmail = filter_var($this->email, FILTER_VALIDATE_EMAIL);
        $this->email = $isValidEmail ? trim($this->email) : null;

        // 2. Lógica de Contraseña: Solo generamos un hash si el cliente tiene un email válido.
        // Sin email, el cliente no puede loguearse a la PWA, por lo que no necesita contraseña.
        $password_hash = null;
        if ($this->email) {
            $plainPassword = !empty($this->password) ? $this->password : $this->phone;
            $password_hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        }

        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, phone, has_whatsapp, billing_name, billing_ruc) 
                  VALUES (:name, :email, :password, :phone, :has_whatsapp, :billing_name, :billing_ruc)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindValue(':has_whatsapp', (int)$this->has_whatsapp, PDO::PARAM_INT);
        $stmt->bindParam(':billing_name', $this->billing_name);
        $stmt->bindParam(':billing_ruc', $this->billing_ruc);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBilling($id, $name, $ruc) {
        $query = "UPDATE " . $this->table . " SET billing_name = :name, billing_ruc = :ruc WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':ruc' => $ruc,
            ':id' => $id
        ]);
    }
    
    /**
     * Actualiza datos completos de facturación para FacturaSend
     */
    public function updateFacturacionCompleta($id, $datos) {
        $query = "UPDATE " . $this->table . " SET 
            nombre_fantasia = :nombre_fantasia,
            tipo_operacion = :tipo_operacion,
            numero_casa = :numero_casa,
            departamento = :departamento,
            departamento_descripcion = :departamento_descripcion,
            distrito = :distrito,
            distrito_descripcion = :distrito_descripcion,
            ciudad = :ciudad,
            ciudad_descripcion = :ciudad_descripcion,
            pais = :pais,
            pais_descripcion = :pais_descripcion,
            tipo_contribuyente = :tipo_contribuyente,
            documento_numero = :documento_numero,
            celular = :celular,
            codigo = :codigo,
            contribuyente = :contribuyente,
            billing_address = :billing_address
            WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre_fantasia' => $datos['nombre_fantasia'] ?? null,
            ':tipo_operacion' => $datos['tipo_operacion'] ?? 1,
            ':numero_casa' => $datos['numero_casa'] ?? null,
            ':departamento' => $datos['departamento'] ?? null,
            ':departamento_descripcion' => $datos['departamento_descripcion'] ?? null,
            ':distrito' => $datos['distrito'] ?? null,
            ':distrito_descripcion' => $datos['distrito_descripcion'] ?? null,
            ':ciudad' => $datos['ciudad'] ?? null,
            ':ciudad_descripcion' => $datos['ciudad_descripcion'] ?? null,
            ':pais' => $datos['pais'] ?? 'PRY',
            ':pais_descripcion' => $datos['pais_descripcion'] ?? 'Paraguay',
            ':tipo_contribuyente' => $datos['tipo_contribuyente'] ?? 1,
            ':documento_numero' => $datos['documento_numero'] ?? null,
            ':celular' => $datos['celular'] ?? null,
            ':codigo' => $datos['codigo'] ?? null,
            ':contribuyente' => $datos['contribuyente'] ?? 0,
            ':billing_address' => $datos['billing_address'] ?? null,
            ':id' => $id
        ]);
    }

    public function login($email, $password) {
        $query = "SELECT id, name, password, billing_name, billing_ruc FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->billing_name = $row['billing_name'];
                $this->billing_ruc = $row['billing_ruc'];
                return true;
            }
        }
        return false;
    }
}
?>