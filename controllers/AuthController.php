<?php
require_once '../models/User.php';
require_once '../models/Client.php';

class AuthController {
    
    public function login() {
        // Redirección automática si ya existe una sesión activa
        if (isset($_SESSION['user_role'])) {
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: ?route=admin');
                exit;
            } elseif ($_SESSION['user_role'] === 'delivery') {
                header('Location: ?route=delivery');
                exit;
            }
        } elseif (isset($_SESSION['client_id'])) {
            header('Location: ?route=home');
            exit;
        }

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
                
                // Redireccionar según el rol específico
                if ($user->role === 'admin') {
                    header('Location: ?route=admin');
                } elseif ($user->role === 'delivery') {
                    header('Location: ?route=delivery');
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

    public function forgotPassword() {
        require_once '../views/auth/forgot_password.php';
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $user = new User();
            
            // Supongamos que User tiene un método findByEmail
            $userData = $user->findByEmail($email);
            
            if ($userData) {
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
                
                // Guardar token en DB (requiere implementar saveResetToken en User.php)
                $user->id = $userData['id'];
                $user->saveResetToken($token, $expires);
                
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/mvp_gestor_pedidos/comedor-app/public/?route=reset_password&token=" . $token;
                
                /**
                 * NOTA DE DESARROLLO:
                 * En producción aquí usarías PHPMailer para enviar el $resetLink.
                 * En local (WAMP), la función mail() no funcionará sin configurar SMTP.
                 * Para probar el flujo, mostraremos el link en la vista.
                 */
                $debug_link = $resetLink; 
                $success = "Se ha enviado un enlace de recuperación a tu correo.";
                require_once '../views/auth/forgot_password.php';
            } else {
                $error = "El correo no está registrado.";
                require_once '../views/auth/forgot_password.php';
            }
        }
    }

    public function resetPassword() {
        // Buscamos el token tanto en el enlace (GET) como en el formulario enviado (POST)
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if (empty($token)) { header('Location: ?route=login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'];
            $user = new User();
            // Validar token y actualizar (requiere implementar resetPasswordWithToken en User.php)
            if ($user->resetPasswordWithToken($token, $newPassword)) {
                header('Location: ?route=login&message=password_updated');
            } else {
                $error = "El enlace ha expirado o es inválido.";
                require_once '../views/auth/reset_password.php';
            }
        } else {
            require_once '../views/auth/reset_password.php';
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
                
                // Redirección inteligente post-login
                $redirectTo = $_POST['redirect_to'] ?? 'home';
                header('Location: ?route=' . $redirectTo);
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

                // Redirección inteligente post-registro
                $redirectTo = $_POST['redirect_to'] ?? 'home';
                header('Location: ?route=' . $redirectTo);
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