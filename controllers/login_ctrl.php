<?php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

// Si ya hay sesión, redirigir al dashboard
if (!empty($_SESSION['id_usuario'])) {
    header('Location: ' . BASE_URL . '/controllers/dashboard_ctrl.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $model   = new UsuarioModel($pdo);
        $usuario = $model->getByEmail($email);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['id_usuario']    = $usuario['id_usuario'];
            $_SESSION['email']         = $usuario['email'];
            $_SESSION['id_rol']        = (int) $usuario['id_rol'];
            $_SESSION['nombre_rol']    = $usuario['nombre_rol'];
            $_SESSION['nombre']        = $usuario['nombre'] ?? $usuario['email'];
            $_SESSION['legajo_alumno'] = $usuario['legajo_alumno'] ?? null;
            $_SESSION['id_profesor']   = $usuario['id_profesor']   ?? null;
            $_SESSION['debe_cambiar_password'] = (bool) ($usuario['debe_cambiar_password'] ?? false);

            header('Location: ' . BASE_URL . '/controllers/dashboard_ctrl.php');
            exit;
        }
    }

    $error = 'Credenciales incorrectas. Verificá tu email y contraseña.';
}

require_once __DIR__ . '/../views/login_view.php';
