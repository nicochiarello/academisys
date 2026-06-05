<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ALUMNO]);

$legajo = $_SESSION['legajo_alumno'] ?? '';

/* Buscar título obtenido */
$stmt = $pdo->prepare(
    'SELECT t.*, c.nombre AS nombre_carrera, c.titulo_otorga,
            p.`año_vigencia`, p.descripcion AS desc_plan
     FROM Titulo_Obtenido t
     JOIN Carrera c ON c.id_carrera = t.id_carrera
     JOIN Plan    p ON p.id_plan    = t.id_plan
     WHERE t.id_alumno = :legajo
     LIMIT 1'
);
$stmt->execute([':legajo' => $legajo]);
$titulo = $stmt->fetch();

/* Si no tiene título, calcular progreso desde el plan */
$materias_pendientes = [];
$total_materias      = 0;
$total_aprobadas     = 0;

if (!$titulo) {
    $stmt2 = $pdo->prepare(
        'SELECT * FROM v_estado_plan_carrera
         WHERE legajo = :legajo
         ORDER BY `año_cursada`, cuatrimestre'
    );
    $stmt2->execute([':legajo' => $legajo]);
    $plan = $stmt2->fetchAll();

    $total_materias  = count($plan);
    $total_aprobadas = count(array_filter($plan, fn($m) => $m['estado'] === 'aprobada'));
    $materias_pendientes = array_filter(
        $plan,
        fn($m) => in_array($m['estado'], ['pendiente', 'desaprobada'])
    );
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/alumno/titulo_view.php';
