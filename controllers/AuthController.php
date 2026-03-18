<?php
require_once '../models/User.php';

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
                $_SESSION['user_name'] = $user->name;
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

    public function logout() {
        session_destroy();
        header('Location: ?route=login');
    }
}
?>