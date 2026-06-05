<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ALUMNO]);

require_once __DIR__ . '/../../models/NotaModel.php';
require_once __DIR__ . '/../../models/InscripcionModel.php';

$modelNota  = new NotaModel($pdo);
$modelInsc  = new InscripcionModel($pdo);
$legajo     = $_SESSION['legajo_alumno'] ?? '';

$id_inscripcion = isset($_GET['id_inscripcion']) ? (int) $_GET['id_inscripcion'] : 0;
$inscripcion    = null;
$notas          = [];

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
        $notas = $modelNota->getByInscripcion($id_inscripcion);
    }
}

/* Lista de cursadas para el selector cuando no se provee id_inscripcion */
$cursadas = $legajo ? $modelInsc->getByAlumno($legajo) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/alumno/notas_view.php';
