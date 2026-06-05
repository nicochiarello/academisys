<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ALUMNO]);

require_once __DIR__ . '/../../models/AsistenciaModel.php';
require_once __DIR__ . '/../../models/InscripcionModel.php';

$modelAsist = new AsistenciaModel($pdo);
$modelInsc  = new InscripcionModel($pdo);
$legajo     = $_SESSION['legajo_alumno'] ?? '';

$id_inscripcion = isset($_GET['id_inscripcion']) ? (int) $_GET['id_inscripcion'] : 0;
$inscripcion    = null;
$asistencias    = [];
$porcentaje     = 0.0;

if ($id_inscripcion > 0) {
    /* Verificar que la inscripción pertenece al alumno logueado */
    $stmt = $pdo->prepare(
        'SELECT i.id_inscripcion, i.estado,
                m.nombre AS nombre_materia
         FROM Inscripcion i
         JOIN Comision c ON c.id_comision = i.id_comision
         JOIN Materia  m ON m.id_materia  = c.id_materia
         WHERE i.id_inscripcion = :id AND i.id_alumno = :legajo'
    );
    $stmt->execute([':id' => $id_inscripcion, ':legajo' => $legajo]);
    $inscripcion = $stmt->fetch();

    if ($inscripcion) {
        $asistencias = $modelAsist->getByInscripcion($id_inscripcion);
        $porcentaje  = $modelAsist->getPorcentaje($id_inscripcion);
    }
}

/* Lista de cursadas para el selector cuando no se provee id_inscripcion */
$cursadas = $legajo ? $modelInsc->getByAlumno($legajo) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/alumno/asistencias_view.php';
