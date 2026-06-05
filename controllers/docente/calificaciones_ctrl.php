<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_DOCENTE, ROL_ADMIN, ROL_BEDEL]);

require_once __DIR__ . '/../../models/NotaModel.php';
require_once __DIR__ . '/../../models/ComisionModel.php';

$modelNota    = new NotaModel($pdo);
$modelComision = new ComisionModel($pdo);

$id_profesor = (int) ($_SESSION['id_profesor'] ?? 0);
$id_rol      = (int) ($_SESSION['id_rol'] ?? 0);

/* Docente solo ve sus comisiones; admin/bedel ve todas las activas */
$comisiones = ($id_rol === ROL_DOCENTE)
    ? $modelComision->getByProfesor($id_profesor)
    : $modelComision->getActivas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'guardar_nota') {
            $resultado = $modelNota->registrar(
                (int)   ($_POST['id_inscripcion'] ?? 0),
                trim(   $_POST['tipo']            ?? ''),
                (float) ($_POST['calificacion']   ?? 0),
                trim(   $_POST['fecha']           ?? date('Y-m-d')),
                trim(   $_POST['observacion']     ?? '')
            );
            $id_comision = (int) ($_POST['id_comision'] ?? 0);
            if (str_starts_with($resultado, 'OK:')) {
                $msg = urlencode($resultado);
                header('Location: ' . BASE_URL . '/controllers/docente/calificaciones_ctrl.php'
                    . '?id_comision=' . $id_comision . '&msg=' . $msg);
            } else {
                $err = urlencode($resultado ?: 'Error al registrar la nota.');
                header('Location: ' . BASE_URL . '/controllers/docente/calificaciones_ctrl.php'
                    . '?id_comision=' . $id_comision . '&err=' . $err);
            }
        } else {
            header('Location: ' . BASE_URL . '/controllers/docente/calificaciones_ctrl.php');
        }
    } catch (PDOException $e) {
        $err = urlencode($e->errorInfo[2] ?? $e->getMessage());
        header('Location: ' . BASE_URL . '/controllers/docente/calificaciones_ctrl.php?err=' . $err);
    }
    exit;
}

$id_comision = isset($_GET['id_comision']) ? (int) $_GET['id_comision'] : 0;
$inscriptos  = [];
$notas       = [];

if ($id_comision > 0) {
    $inscriptos = $modelComision->getInscriptos($id_comision);
    foreach ($inscriptos as $ins) {
        $id_insc = (int) $ins['id_inscripcion'];
        $notas[$id_insc] = $modelNota->getByInscripcion($id_insc);
    }
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/docente/calificaciones_view.php';
