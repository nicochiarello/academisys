<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN, ROL_BEDEL]);

require_once __DIR__ . '/../../models/InscripcionModel.php';
require_once __DIR__ . '/../../models/ComisionModel.php';

$modelo         = new InscripcionModel($pdo);
$modelComision  = new ComisionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'inscribir':
                /* SP devuelve string que empieza con 'OK:' o 'ERROR:' */
                $resultado = $modelo->inscribir(
                    trim($_POST['legajo'] ?? ''),
                    (int) ($_POST['id_comision'] ?? 0)
                );
                if (str_starts_with($resultado, 'OK:')) {
                    $msg = urlencode($resultado);
                    header('Location: ' . BASE_URL . '/controllers/bedel/inscripciones_ctrl.php?msg=' . $msg);
                } else {
                    $err = urlencode($resultado ?: 'Error desconocido en la inscripción.');
                    header('Location: ' . BASE_URL . '/controllers/bedel/inscripciones_ctrl.php?err=' . $err);
                }
                break;

            case 'baja':
                $modelo->darBaja((int) $_POST['id_inscripcion']);
                $msg = urlencode('Inscripción dada de baja correctamente.');
                header('Location: ' . BASE_URL . '/controllers/bedel/inscripciones_ctrl.php?msg=' . $msg);
                break;

            default:
                header('Location: ' . BASE_URL . '/controllers/bedel/inscripciones_ctrl.php');
        }
    } catch (PDOException $e) {
        $err = urlencode($e->errorInfo[2] ?? $e->getMessage());
        header('Location: ' . BASE_URL . '/controllers/bedel/inscripciones_ctrl.php?err=' . $err);
    }
    exit;
}

$comisiones = $modelComision->getActivas();

/* Últimas 20 inscripciones del ciclo activo — query directa */
$stmt = $pdo->query(
    'SELECT i.id_inscripcion, i.id_alumno, a.apellido, a.nombre,
            m.nombre AS materia, i.estado, i.fecha_inscripcion
     FROM Inscripcion i
     JOIN Alumno a ON a.legajo = i.id_alumno
     JOIN Comision c ON c.id_comision = i.id_comision
     JOIN Materia m ON m.id_materia = c.id_materia
     JOIN Ciclo_Academico ca ON ca.id_ciclo = c.id_ciclo
     WHERE ca.activo = 1
     ORDER BY i.fecha_inscripcion DESC
     LIMIT 20'
);
$inscripciones_recientes = $stmt->fetchAll();

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/bedel/inscripciones_view.php';
