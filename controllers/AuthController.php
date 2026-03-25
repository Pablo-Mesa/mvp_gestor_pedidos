<?php
require_once '../models/User.php';
require_once '../models/Client.php';

class AuthController {
    
    public function login() {
        // Si la petición es POST, intentamos loguear
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($user->login($email, $password)) {
                // Guardar datos en sesión
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name; // Admin name
                $_SESSION['user_role'] = $user->role;
                
                // Redireccionar según el rol
                if ($user->role === 'admin') {
                    header('Location: ?route=admin');
                } else {
                    header('Location: ?route=home');
                }
                exit;
            } else {
                $error = "Credenciales incorrectas";
                // Aquí cargaremos la vista de nuevo con el error
                require_once '../views/auth/login.php';
            }
        } else {
            // Si es GET, mostramos el formulario
            require_once '../views/auth/login.php';
        }
    }

    // Método exclusivo para Login de Clientes
    public function clientLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = new Client();
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($client->login($email, $password)) {
                // Usamos claves de sesión DISTINTAS para no mezclar con admin
                $_SESSION['client_id'] = $client->id;
                $_SESSION['client_name'] = $client->name;
                
                header('Location: ?route=home');
                exit;
            } else {
                // Podrías redirigir con un error flag
                header('Location: ?route=home&error=login_failed');
                exit;
            }
        }
    }

    // Método exclusivo para Registro de Clientes
    public function clientRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = new Client();
            $client->name = $_POST['name'];
            $client->email = $_POST['email'];
            $client->password = $_POST['password'];
            $client->phone = $_POST['phone'];
            $client->has_whatsapp = isset($_POST['has_whatsapp']) ? 1 : 0;

            if ($client->register()) {
                // Auto-login tras registro exitoso
                $_SESSION['client_id'] = $client->id;
                $_SESSION['client_name'] = $client->name;
                header('Location: ?route=home');
            } else {
                header('Location: ?route=home&error=register_failed');
            }
            exit;
        }
    }

    public function logout() {
        $type = $_GET['type'] ?? null;

        if ($type === 'admin') {
            // Solo eliminamos datos del administrador
            unset($_SESSION['user_id']);
            unset($_SESSION['user_name']);
            unset($_SESSION['user_role']);
            header('Location: ?route=login');
            exit;
        } elseif ($type === 'client') {
            // Solo eliminamos datos del cliente
            unset($_SESSION['client_id']);
            unset($_SESSION['client_name']);
            header('Location: ?route=home');
            exit;
        }
        
        // Fallback por seguridad: si no hay tipo, redirigir al home
        header('Location: ?route=home');
        exit;
    }
}
?>