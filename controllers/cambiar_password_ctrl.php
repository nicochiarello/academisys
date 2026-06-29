<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();

require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new UsuarioModel($pdo);
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva     = trim($_POST['password_nueva']    ?? '');
    $confirmar = trim($_POST['password_confirmar'] ?? '');

    if (strlen($nueva) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $modelo->cambiarPassword((int) $_SESSION['id_usuario'], $nueva);
        $_SESSION['debe_cambiar_password'] = false;
        header('Location: ' . BASE_URL . '/controllers/dashboard_ctrl.php?msg=' . urlencode('Contraseña actualizada correctamente.'));
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/cambiar_password_view.php';
