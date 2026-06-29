<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();

require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/AlumnoModel.php';

$modelUsr  = new UsuarioModel($pdo);
$modelAlum = new AlumnoModel($pdo);

$rol = (int) $_SESSION['id_rol'];

$usuario         = null;
$alumno          = null;
$estado_academico = null;

if (in_array($rol, [ROL_ADMIN, ROL_BEDEL])) {
    if (!empty($_GET['legajo'])) {
        $alumno = $modelAlum->getByLegajo(trim($_GET['legajo']));
        if ($alumno) {
            $stmt = $pdo->prepare(
                'SELECT u.*, r.nombre AS nombre_rol FROM Usuario u
                 JOIN Rol r ON r.id_rol = u.id_rol
                 WHERE u.legajo_alumno = :legajo LIMIT 1'
            );
            $stmt->execute([':legajo' => $alumno['legajo']]);
            $usuario = $stmt->fetch() ?: null;
        }
    } elseif (!empty($_GET['id_usuario'])) {
        $usuario = $modelUsr->getById((int) $_GET['id_usuario']);
        if ($usuario && $usuario['legajo_alumno']) {
            $alumno = $modelAlum->getByLegajo($usuario['legajo_alumno']);
        }
    }

    if (!$alumno && !$usuario) {
        header('Location: ' . BASE_URL . '/controllers/admin/usuarios_ctrl.php');
        exit;
    }

    if ($alumno) {
        $legajo = $alumno['legajo'];

        $stmt = $pdo->prepare(
            'SELECT * FROM v_estado_plan_carrera
             WHERE legajo = :legajo
             ORDER BY año_cursada, cuatrimestre'
        );
        $stmt->execute([':legajo' => $legajo]);
        $materias = $stmt->fetchAll();

        $total        = count($materias);
        $aprobadas    = count(array_filter($materias, fn($m) => strtolower($m['estado']) === 'aprobada'));
        $en_curso     = count(array_filter($materias, fn($m) => strtolower($m['estado']) === 'en_curso'));
        $desaprobadas = count(array_filter($materias, fn($m) => strtolower($m['estado']) === 'desaprobada'));

        $stmt2 = $pdo->prepare(
            'SELECT ROUND(promedio_final, 2) FROM v_promedio_por_alumno WHERE legajo = :legajo'
        );
        $stmt2->execute([':legajo' => $legajo]);
        $promedio = $stmt2->fetchColumn();

        $stmt3 = $pdo->prepare(
            'SELECT ROUND(AVG(v.porcentaje_asistencia), 1)
             FROM v_asistencia_por_inscripcion v
             JOIN Inscripcion i ON i.id_inscripcion = v.id_inscripcion
             WHERE i.id_alumno = :legajo AND i.estado = :estado'
        );
        $stmt3->execute([':legajo' => $legajo, ':estado' => 'activa']);
        $pct_asistencia = $stmt3->fetchColumn();

        $estado_academico = [
            'total'          => $total,
            'aprobadas'      => $aprobadas,
            'en_curso'       => $en_curso,
            'desaprobadas'   => $desaprobadas,
            'pendientes'     => $total - $aprobadas - $en_curso - $desaprobadas,
            'pct_carrera'    => $total > 0 ? round($aprobadas * 100 / $total) : 0,
            'promedio'       => $promedio !== false ? $promedio : null,
            'pct_asistencia' => $pct_asistencia !== false ? $pct_asistencia : null,
        ];
    }
} elseif ($rol === ROL_ALUMNO) {
    $legajo = $_SESSION['legajo_alumno'] ?? '';
    $alumno = $legajo ? $modelAlum->getByLegajo($legajo) : null;
    $stmt   = $pdo->prepare(
        'SELECT u.*, r.nombre AS nombre_rol FROM Usuario u
         JOIN Rol r ON r.id_rol = u.id_rol
         WHERE u.id_usuario = :id LIMIT 1'
    );
    $stmt->execute([':id' => $_SESSION['id_usuario']]);
    $usuario = $stmt->fetch() ?: null;
} else {
    header('Location: ' . BASE_URL . '/controllers/dashboard_ctrl.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/perfil_view.php';
