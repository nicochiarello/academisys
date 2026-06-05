<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ALUMNO]);

require_once __DIR__ . '/../../models/InscripcionModel.php';

$modelo  = new InscripcionModel($pdo);
$legajo  = $_SESSION['legajo_alumno'] ?? '';
$cursadas = $legajo ? $modelo->getByAlumno($legajo) : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/alumno/cursadas_view.php';
