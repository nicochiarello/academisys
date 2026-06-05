<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/PlanModel.php';
require_once __DIR__ . '/../../models/CarreraModel.php';
require_once __DIR__ . '/../../models/MateriaModel.php';

$modelo     = new PlanModel($pdo);
$modelCar   = new CarreraModel($pdo);
$modelMat   = new MateriaModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Plan de estudio creado correctamente.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$planes   = $modelo->getAll();
$carreras = $modelCar->getAll();

/* Detalle de plan: materias agrupadas por año y cuatrimestre */
$plan_detalle  = null;
$materias_plan = [];
if (isset($_GET['ver'])) {
    $plan_detalle = $modelo->getById((int) $_GET['ver']);
    if ($plan_detalle) {
        $raw = $modelMat->getByPlan((int) $_GET['ver']);
        foreach ($raw as $m) {
            $materias_plan[$m['año_cursada']][$m['cuatrimestre']][] = $m;
        }
    }
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/planes_view.php';
