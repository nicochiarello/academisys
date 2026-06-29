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

/* Búsqueda de alumnos para autocompletar el formulario de inscripción */
$q_alumno           = trim($_GET['q'] ?? '');
$alumnos_encontrados = [];
if ($q_alumno !== '') {
    $like = "%$q_alumno%";
    $stmt = $pdo->prepare(
        'SELECT a.legajo, a.dni, a.apellido, a.nombre, a.email, c.nombre AS nombre_carrera
         FROM Alumno a
         JOIN Carrera c ON c.id_carrera = a.id_carrera
         WHERE a.activo = 1
           AND (a.legajo   LIKE ?
             OR a.dni      LIKE ?
             OR a.apellido LIKE ?
             OR a.nombre   LIKE ?
             OR a.email    LIKE ?)
         ORDER BY a.apellido, a.nombre
         LIMIT 10'
    );
    $stmt->execute(array_fill(0, 5, $like));
    $alumnos_encontrados = $stmt->fetchAll();
}

/* Inscripciones del ciclo activo agrupadas por carrera → materia */
$stmt = $pdo->query(
    'SELECT i.id_inscripcion, i.id_alumno, a.apellido, a.nombre,
            m.nombre AS materia, i.estado, i.fecha_inscripcion,
            car.nombre AS carrera
     FROM Inscripcion i
     JOIN Alumno a     ON a.legajo        = i.id_alumno
     JOIN Carrera car  ON car.id_carrera  = a.id_carrera
     JOIN Comision co  ON co.id_comision  = i.id_comision
     JOIN Materia m    ON m.id_materia    = co.id_materia
     JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
     WHERE ca.activo = 1
     ORDER BY car.nombre, m.nombre, a.apellido, a.nombre'
);
$inscripciones_por_carrera = [];
foreach ($stmt->fetchAll() as $row) {
    $inscripciones_por_carrera[$row['carrera']][$row['materia']][] = $row;
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/bedel/inscripciones_view.php';
