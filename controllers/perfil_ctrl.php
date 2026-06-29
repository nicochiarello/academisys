<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();

require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/AlumnoModel.php';

$modelUsr  = new UsuarioModel($pdo);
$modelAlum = new AlumnoModel($pdo);

$rol = (int) $_SESSION['id_rol'];

$usuario = null;
$alumno  = null;

if ($rol === ROL_ADMIN) {
    if (!empty($_GET['legajo'])) {
        $alumno  = $modelAlum->getByLegajo(trim($_GET['legajo']));
        if ($alumno) {
            $stmt = $pdo->prepare(
                'SELECT u.*, r.nombre AS nombre_rol FROM Usuario u
                 JOIN Rol r ON r.id_rol = u.id_rol
                 WHERE u.legajo_alumno = :legajo LIMIT 1'
            );
            $stmt->execute([':legajo' => $alumno['legajo']]);
            $usuario = $stmt->fetch() ?: null;
        }
    } elseif (!empty($_GET['id_usuario'])) {
        $usuario = $modelUsr->getById((int) $_GET['id_usuario']);
        if ($usuario && $usuario['legajo_alumno']) {
            $alumno = $modelAlum->getByLegajo($usuario['legajo_alumno']);
        }
    }

    if (!$alumno && !$usuario) {
        header('Location: ' . BASE_URL . '/controllers/admin/usuarios_ctrl.php');
        exit;
    }
} elseif ($rol === ROL_ALUMNO) {
    $legajo = $_SESSION['legajo_alumno'] ?? '';
    $alumno = $legajo ? $modelAlum->getByLegajo($legajo) : null;
    $stmt   = $pdo->prepare(
        'SELECT u.*, r.nombre AS nombre_rol FROM Usuario u
         JOIN Rol r ON r.id_rol = u.id_rol
         WHERE u.id_usuario = :id LIMIT 1'
    );
    $stmt->execute([':id' => $_SESSION['id_usuario']]);
    $usuario = $stmt->fetch() ?: null;
} else {
    header('Location: ' . BASE_URL . '/controllers/dashboard_ctrl.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/perfil_view.php';
