<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/AlumnoModel.php';
require_once __DIR__ . '/../../models/CarreraModel.php';

$modelo   = new AlumnoModel($pdo);
$modelCar = new CarreraModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Alumno creado correctamente.');
                break;
            case 'editar':
                $modelo->actualizar($_POST['legajo'], $_POST);
                $msg = urlencode('Alumno actualizado correctamente.');
                break;
            case 'baja':
                $modelo->darBaja($_POST['legajo']);
                $msg = urlencode('Alumno dado de baja.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$alumnos  = $modelo->getAll();
$carreras = $modelCar->getAll();
$editando = null;
if (isset($_GET['editar'])) {
    $editando = $modelo->getByLegajo($_GET['editar']);
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/alumnos_view.php';
