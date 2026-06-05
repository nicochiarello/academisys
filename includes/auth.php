<?php

define('ROL_ADMIN',   1);
define('ROL_BEDEL',   2);
define('ROL_DOCENTE', 3);
define('ROL_ALUMNO',  4);

function requireLogin(): void
{
    if (empty($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . '/controllers/login_ctrl.php');
        exit;
    }
}

function requireRol(array $roles): void
{
    if (!in_array($_SESSION['id_rol'] ?? null, $roles, true)) {
        header('Location: ' . BASE_URL . '/controllers/login_ctrl.php');
        exit;
    }
}
