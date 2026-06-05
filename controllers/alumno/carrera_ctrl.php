<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ALUMNO]);

$legajo = $_SESSION['legajo_alumno'] ?? '';

$stmt = $pdo->prepare(
    'SELECT * FROM v_estado_plan_carrera
     WHERE legajo = :legajo
     ORDER BY `año_cursada`, cuatrimestre'
);
$stmt->execute([':legajo' => $legajo]);
$raw = $stmt->fetchAll();

/* Agrupar por año_cursada */
$materias_por_anio = [];
foreach ($raw as $m) {
    $materias_por_anio[$m['año_cursada']][] = $m;
}
ksort($materias_por_anio);

/* Totales para barra de progreso */
$total_materias    = count($raw);
$total_aprobadas   = count(array_filter($raw, fn($m) => $m['estado'] === 'aprobada'));

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/alumno/carrera_view.php';
