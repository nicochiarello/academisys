<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
requireLogin();

$rol        = $_SESSION['id_rol'] ?? 0;
$nombre     = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$nombre_rol = htmlspecialchars($_SESSION['nombre_rol'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiSys</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #333; }
        nav {
            background: #1a237e;
            color: #fff;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 56px;
            box-shadow: 0 2px 6px rgba(0,0,0,.35);
        }
        nav .brand { font-size: 1.2rem; font-weight: 700; letter-spacing: .5px; }
        nav ul { list-style: none; display: flex; gap: 4px; }
        nav ul li a {
            color: #c5cae9;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: .88rem;
            transition: background .2s, color .2s;
        }
        nav ul li a:hover { background: #283593; color: #fff; }
        nav .user-area { display: flex; align-items: center; gap: 16px; font-size: .88rem; }
        nav .user-area span { color: #c5cae9; }
        nav .user-area a {
            color: #ef9a9a;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ef9a9a;
            border-radius: 4px;
            transition: background .2s;
        }
        nav .user-area a:hover { background: #ef9a9a; color: #1a237e; }
        main { padding: 32px 24px; max-width: 1200px; margin: 0 auto; }
    </style>
</head>
<body>
<nav>
    <div class="brand">AcademiSys</div>
    <ul>
        <?php if ($rol === ROL_ADMIN): ?>
            <li><a href="/controllers/admin/carreras_ctrl.php">Carreras</a></li>
            <li><a href="/controllers/admin/materias_ctrl.php">Materias</a></li>
            <li><a href="/controllers/admin/planes_ctrl.php">Plan de Estudio</a></li>
            <li><a href="/controllers/admin/profesores_ctrl.php">Docentes</a></li>
            <li><a href="/controllers/admin/aulas_ctrl.php">Aulas</a></li>
            <li><a href="/controllers/admin/alumnos_ctrl.php">Alumnos</a></li>
            <li><a href="/controllers/admin/ciclos_ctrl.php">Ciclos</a></li>
            <li><a href="/controllers/admin/usuarios_ctrl.php">Usuarios</a></li>
        <?php elseif ($rol === ROL_BEDEL): ?>
            <li><a href="/controllers/bedel/comisiones_ctrl.php">Comisiones</a></li>
            <li><a href="/controllers/bedel/inscripciones_ctrl.php">Inscripciones</a></li>
            <li><a href="/controllers/bedel/asistencias_ctrl.php">Asistencias</a></li>
        <?php elseif ($rol === ROL_DOCENTE): ?>
            <li><a href="/controllers/docente/comisiones_ctrl.php">Mis Comisiones</a></li>
            <li><a href="/controllers/docente/calificaciones_ctrl.php">Calificaciones</a></li>
            <li><a href="/controllers/docente/asistencias_ctrl.php">Asistencias</a></li>
        <?php elseif ($rol === ROL_ALUMNO): ?>
            <li><a href="/controllers/alumno/cursadas_ctrl.php">Mis Cursadas</a></li>
            <li><a href="/controllers/alumno/notas_ctrl.php">Mis Notas</a></li>
            <li><a href="/controllers/alumno/asistencias_ctrl.php">Mis Asistencias</a></li>
            <li><a href="/controllers/alumno/carrera_ctrl.php">Mi Carrera</a></li>
            <li><a href="/controllers/alumno/titulo_ctrl.php">Mi Título</a></li>
        <?php endif; ?>
    </ul>
    <div class="user-area">
        <span><?= $nombre ?> &mdash; <?= $nombre_rol ?></span>
        <a href="/controllers/logout_ctrl.php">Salir</a>
    </div>
</nav>
<main>
