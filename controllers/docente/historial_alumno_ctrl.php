<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_DOCENTE, ROL_ADMIN]);

require_once __DIR__ . '/../../models/ComisionModel.php';
require_once __DIR__ . '/../../models/NotaModel.php';
require_once __DIR__ . '/../../models/AsistenciaModel.php';

$id_comision    = isset($_GET['id_comision']) ? (int) $_GET['id_comision'] : 0;
$modelComision  = new ComisionModel($pdo);
$modelNota      = new NotaModel($pdo);
$modelAsist     = new AsistenciaModel($pdo);

$comision   = $id_comision > 0 ? $modelComision->getById($id_comision) : null;
$inscriptos = $id_comision > 0 ? $modelComision->getInscriptos($id_comision) : [];

$notas       = [];
$porcentajes = [];

foreach ($inscriptos as $ins) {
    $id_insc = (int) $ins['id_inscripcion'];
    $notas[$id_insc]       = $modelNota->getByInscripcion($id_insc);
    $porcentajes[$id_insc] = $modelAsist->getPorcentaje($id_insc);
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/docente/historial_alumno_view.php';
