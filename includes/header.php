<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
requireLogin();

if (!empty($_SESSION['debe_cambiar_password'])
    && basename($_SERVER['SCRIPT_NAME']) !== 'cambiar_password_ctrl.php') {
    header('Location: ' . BASE_URL . '/controllers/cambiar_password_ctrl.php');
    exit;
}

$rol          = $_SESSION['id_rol'] ?? 0;
$nombre       = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$nombre_rol   = htmlspecialchars($_SESSION['nombre_rol'] ?? '', ENT_QUOTES, 'UTF-8');
$script_actual = basename($_SERVER['SCRIPT_NAME']);

/* Garantizar que las variables de alerta estén siempre definidas en el scope de la vista */
$mensaje  ??= null;
$es_error ??= false;
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
        nav ul li a.nav-activo {
            background: #283593;
            color: #fff;
            border-bottom: 2px solid #7986cb;
        }
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
        <?php
        $a = fn(string $script, string $href, string $label) =>
            '<a href="' . BASE_URL . $href . '" class="' . ($script_actual === $script ? 'nav-activo' : '') . '">' . $label . '</a>';
        ?>
        <?php if ($rol === ROL_ADMIN): ?>
            <li><?= $a('carreras_ctrl.php',  '/controllers/admin/carreras_ctrl.php',  'Carreras') ?></li>
            <li><?= $a('materias_ctrl.php',  '/controllers/admin/materias_ctrl.php',  'Materias') ?></li>
            <li><?= $a('planes_ctrl.php',    '/controllers/admin/planes_ctrl.php',    'Plan de Estudio') ?></li>
            <li><?= $a('profesores_ctrl.php','/controllers/admin/profesores_ctrl.php','Docentes') ?></li>
            <li><?= $a('aulas_ctrl.php',     '/controllers/admin/aulas_ctrl.php',     'Aulas') ?></li>
            <li><?= $a('alumnos_ctrl.php',   '/controllers/admin/alumnos_ctrl.php',   'Alumnos') ?></li>
            <li><?= $a('ciclos_ctrl.php',    '/controllers/admin/ciclos_ctrl.php',    'Ciclos') ?></li>
            <li><?= $a('usuarios_ctrl.php',  '/controllers/admin/usuarios_ctrl.php',  'Usuarios') ?></li>
        <?php elseif ($rol === ROL_BEDEL): ?>
            <li><?= $a('comisiones_ctrl.php',   '/controllers/bedel/comisiones_ctrl.php',   'Comisiones') ?></li>
            <li><?= $a('inscripciones_ctrl.php','/controllers/bedel/inscripciones_ctrl.php','Inscripciones') ?></li>
            <li><?= $a('asistencias_ctrl.php',  '/controllers/bedel/asistencias_ctrl.php',  'Asistencias') ?></li>
        <?php elseif ($rol === ROL_DOCENTE): ?>
            <li><?= $a('comisiones_ctrl.php',   '/controllers/docente/comisiones_ctrl.php',   'Mis Comisiones') ?></li>
            <li><?= $a('calificaciones_ctrl.php','/controllers/docente/calificaciones_ctrl.php','Calificaciones') ?></li>
            <li><?= $a('asistencias_ctrl.php',  '/controllers/docente/asistencias_ctrl.php',  'Asistencias') ?></li>
        <?php elseif ($rol === ROL_ALUMNO): ?>
            <li><?= $a('cursadas_ctrl.php',  '/controllers/alumno/cursadas_ctrl.php',  'Mis Cursadas') ?></li>
            <li><?= $a('notas_ctrl.php',     '/controllers/alumno/notas_ctrl.php',     'Mis Notas') ?></li>
            <li><?= $a('asistencias_ctrl.php','/controllers/alumno/asistencias_ctrl.php','Mis Asistencias') ?></li>
            <li><?= $a('carrera_ctrl.php',   '/controllers/alumno/carrera_ctrl.php',   'Mi Carrera') ?></li>
            <li><?= $a('titulo_ctrl.php',    '/controllers/alumno/titulo_ctrl.php',    'Mi Título') ?></li>
        <?php endif; ?>
    </ul>
    <div class="user-area">
        <span><?= $nombre ?> &mdash; <?= $nombre_rol ?></span>
        <a href="<?= BASE_URL ?>/controllers/logout_ctrl.php">Salir</a>
    </div>
</nav>
<main>