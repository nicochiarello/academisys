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
                header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?msg=' . $msg);
                exit;

            case 'eliminar':
                $id_plan = (int) $_POST['id_plan'];
                if ($modelo->tieneMaterias($id_plan)) {
                    $err = urlencode('No se puede eliminar el plan porque tiene materias asignadas.');
                    header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?err=' . $err);
                } else {
                    $modelo->eliminar($id_plan);
                    $msg = urlencode('Plan eliminado correctamente.');
                    header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?msg=' . $msg);
                }
                exit;

            case 'crear_materia':
                $modelMat->crear($_POST);
                $msg = urlencode('Materia agregada al plan.');
                $redir = (int) $_POST['id_plan'];
                header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?ver=' . $redir . '&msg=' . $msg);
                exit;

            case 'eliminar_materia':
                $id_mat  = (int) $_POST['id_materia'];
                $id_plan = (int) $_POST['id_plan'];
                $modelMat->eliminar($id_mat);
                $msg = urlencode('Materia eliminada del plan.');
                header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php?ver=' . $id_plan . '&msg=' . $msg);
                exit;
        }
        header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php');
    } catch (PDOException $e) {
        $redir = isset($_POST['id_plan']) ? '?ver=' . (int) $_POST['id_plan'] . '&err=' : '?err=';
        header('Location: ' . BASE_URL . '/controllers/admin/planes_ctrl.php' . $redir . urlencode($e->getMessage()));
    }
    exit;
}

$planes   = $modelo->getAll();
$carreras = $modelCar->getAll();

/* Detalle de plan: materias agrupadas por año y cuatrimestre */
$plan_detalle     = null;
$materias_plan    = [];
$catalogo_json    = '[]';
if (isset($_GET['ver'])) {
    $plan_detalle = $modelo->getById((int) $_GET['ver']);
    if ($plan_detalle) {
        $raw = $modelMat->getByPlan((int) $_GET['ver']);
        foreach ($raw as $m) {
            $materias_plan[$m['año_cursada']][$m['cuatrimestre']][] = $m;
        }
        /* Catálogo para el autocomplete: todas las materias del sistema */
        $catalogo = array_map(fn($m) => [
            'codigo'        => $m['codigo'],
            'nombre'        => $m['nombre'],
            'año_cursada'   => $m['año_cursada'],
            'cuatrimestre'  => $m['cuatrimestre'],
            'carga_horaria' => $m['carga_horaria'],
        ], $modelMat->getAll());
        $catalogo_json = json_encode($catalogo, JSON_UNESCAPED_UNICODE);
    }
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/planes_view.php';
