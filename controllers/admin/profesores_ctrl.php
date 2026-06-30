<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/ProfesorModel.php';
$modelo = new ProfesorModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $passwordTemporal = $modelo->crear($_POST);
                $_SESSION['flash_password_temporal'] = $passwordTemporal;
                $_SESSION['flash_email_docente']     = $_POST['email'];
                $msg = urlencode('Docente creado correctamente. Se generó un usuario de acceso.');
                break;
            case 'editar':
                $modelo->actualizar((int) $_POST['id'], $_POST);
                $msg = urlencode('Docente actualizado correctamente.');
                break;
            case 'baja':
                $modelo->darBaja((int) $_POST['id']);
                $msg = urlencode('Docente dado de baja.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/profesores_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/profesores_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$password_temporal   = $_SESSION['flash_password_temporal'] ?? null;
$email_docente_nuevo = $_SESSION['flash_email_docente']     ?? null;
unset($_SESSION['flash_password_temporal'], $_SESSION['flash_email_docente']);

$profesores = $modelo->getAll();
$editando   = null;
if (isset($_GET['editar'])) {
    $editando = $modelo->getById((int) $_GET['editar']);
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/profesores_view.php';
