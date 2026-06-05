<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/UsuarioModel.php';
require_once __DIR__ . '/../../models/AlumnoModel.php';
require_once __DIR__ . '/../../models/ProfesorModel.php';

$modelo    = new UsuarioModel($pdo);
$modelAlum = new AlumnoModel($pdo);
$modelProf = new ProfesorModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                if ($modelo->getByEmail($_POST['email'])) {
                    throw new RuntimeException('Ya existe un usuario con ese email.');
                }
                $modelo->crear($_POST);
                $msg = urlencode('Usuario creado correctamente.');
                break;
            case 'activar':
                $modelo->activar((int) $_POST['id']);
                $msg = urlencode('Usuario activado.');
                break;
            case 'desactivar':
                $modelo->desactivar((int) $_POST['id']);
                $msg = urlencode('Usuario desactivado.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/usuarios_ctrl.php?msg=' . $msg);
    } catch (PDOException | RuntimeException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/usuarios_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$usuarios   = $modelo->getAll();
$alumnos    = $modelAlum->getAll();
$profesores = $modelProf->getAll();
$roles      = $pdo->query('SELECT * FROM Rol ORDER BY id_rol')->fetchAll();

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/usuarios_view.php';
