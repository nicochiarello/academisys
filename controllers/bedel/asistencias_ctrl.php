<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
/* Accesible para admins, bedeles y docentes */
requireRol([ROL_ADMIN, ROL_BEDEL, ROL_DOCENTE]);

require_once __DIR__ . '/../../models/AsistenciaModel.php';
require_once __DIR__ . '/../../models/ComisionModel.php';

$modelo        = new AsistenciaModel($pdo);
$modelComision = new ComisionModel($pdo);

/* Docente solo ve sus propias comisiones */
$es_docente = ((int) $_SESSION['id_rol']) === ROL_DOCENTE;
$comisiones = $es_docente
    ? $modelComision->getByProfesor((int) ($_SESSION['id_profesor'] ?? 0))
    : $modelComision->getActivas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action'] ?? '';
    $id_comision = (int) ($_POST['id_comision'] ?? 0);
    $fecha       = $_POST['fecha'] ?? date('Y-m-d');

    try {
        if ($action === 'guardar_asistencias') {
            $asistencias = $_POST['asistencia'] ?? [];
            foreach ($asistencias as $id_inscripcion => $val) {
                $presente    = ($val === 'presente')    ? 1 : 0;
                $justificada = ($val === 'justificada') ? 1 : 0;
                $modelo->registrar((int) $id_inscripcion, $fecha, $presente, $justificada);
            }
            $msg = urlencode('Asistencias guardadas correctamente.');
            $redirect = BASE_URL . '/controllers/bedel/asistencias_ctrl.php'
                      . '?id_comision=' . $id_comision
                      . '&fecha=' . urlencode($fecha)
                      . '&msg=' . $msg;
            header('Location: ' . $redirect);
        } else {
            header('Location: ' . BASE_URL . '/controllers/bedel/asistencias_ctrl.php');
        }
    } catch (PDOException $e) {
        $err = urlencode($e->errorInfo[2] ?? $e->getMessage());
        $redirect = BASE_URL . '/controllers/bedel/asistencias_ctrl.php'
                  . '?id_comision=' . $id_comision
                  . '&fecha=' . urlencode($fecha)
                  . '&err=' . $err;
        header('Location: ' . $redirect);
    }
    exit;
}

$id_comision = isset($_GET['id_comision']) ? (int) $_GET['id_comision'] : 0;
$fecha       = $_GET['fecha'] ?? date('Y-m-d');
$alumnos     = [];

if ($id_comision > 0) {
    $alumnos = $modelo->getByComisionFecha($id_comision, $fecha);
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/bedel/asistencias_view.php';
