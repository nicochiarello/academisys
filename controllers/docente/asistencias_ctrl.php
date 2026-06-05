<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_DOCENTE, ROL_ADMIN, ROL_BEDEL]);

/* El módulo bedel ya maneja los 3 roles correctamente */
header('Location: ' . BASE_URL . '/controllers/bedel/asistencias_ctrl.php');
exit;
