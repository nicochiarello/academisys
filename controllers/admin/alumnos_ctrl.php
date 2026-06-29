<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/AlumnoModel.php';
require_once __DIR__ . '/../../models/CarreraModel.php';
require_once __DIR__ . '/../../models/PlanModel.php';

$modelo    = new AlumnoModel($pdo);
$modelCar  = new CarreraModel($pdo);
$modelPlan = new PlanModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $passwordTemporal = $modelo->crear($_POST);
                $_SESSION['flash_password_temporal'] = $passwordTemporal;
                $_SESSION['flash_email_alumno']      = $_POST['email'];
                $msg = urlencode('Alumno creado correctamente. Se generó un usuario de acceso.');
                break;
            case 'editar':
                $modelo->actualizar($_POST['legajo'], $_POST);
                $msg = urlencode('Alumno actualizado correctamente.');
                break;
            case 'baja':
                $modelo->darBaja($_POST['legajo']);
                $msg = urlencode('Alumno dado de baja.');
                break;
            case 'otorgar_titulo':
                /* OtorgarTitulo devuelve result set con 'mensaje', no OUT param */
                $stmt = $pdo->prepare('CALL OtorgarTitulo(?, ?)');
                $stmt->execute([trim($_POST['legajo'] ?? ''), (int) ($_POST['id_plan'] ?? 0)]);
                $row = $stmt->fetch();
                while ($stmt->nextRowset()) {}
                $resultado = $row['mensaje'] ?? '';
                if (str_starts_with($resultado, 'OK:')) {
                    header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?msg=' . urlencode($resultado));
                } else {
                    header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?err=' . urlencode($resultado ?: 'Error al otorgar el título.'));
                }
                exit;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/alumnos_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

$password_temporal = $_SESSION['flash_password_temporal'] ?? null;
$email_alumno_nuevo = $_SESSION['flash_email_alumno'] ?? null;
unset($_SESSION['flash_password_temporal'], $_SESSION['flash_email_alumno']);

try {
    $alumnos  = $modelo->getAll();
    $carreras = $modelCar->getAll();
    $planes   = $modelPlan->getAll();
    $editando = null;
    if (isset($_GET['editar'])) {
        $editando = $modelo->getByLegajo($_GET['editar']);
    }
} catch (PDOException $e) {
    $alumnos  = [];
    $carreras = [];
    $planes   = [];
    $editando = null;
    $mensaje  = $e->getMessage();
    $es_error = true;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/alumnos_view.php';
