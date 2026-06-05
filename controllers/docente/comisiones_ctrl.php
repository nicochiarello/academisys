<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_DOCENTE, ROL_ADMIN]);

require_once __DIR__ . '/../../models/ComisionModel.php';

$modelo      = new ComisionModel($pdo);
$id_profesor = (int) ($_SESSION['id_profesor'] ?? 0);
$comisiones  = $modelo->getByProfesor($id_profesor);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/docente/comisiones_view.php';
