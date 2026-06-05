<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/AulaModel.php';
$modelo = new AulaModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Aula creada correctamente.');
                break;
            case 'editar':
                $modelo->actualizar((int) $_POST['id'], $_POST);
                $msg = urlencode('Aula actualizada correctamente.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/aulas_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/aulas_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$aulas    = $modelo->getAll();
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $modelo->getById((int) $_GET['editar']);
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/aulas_view.php';
