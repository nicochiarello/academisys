<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/MateriaModel.php';
require_once __DIR__ . '/../../models/PlanModel.php';

$modelo     = new MateriaModel($pdo);
$modelPlan  = new PlanModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Materia creada correctamente.');
                break;
            case 'editar':
                $modelo->actualizar((int) $_POST['id'], $_POST);
                $msg = urlencode('Materia actualizada correctamente.');
                break;
            case 'set_correlativas':
                $modelo->setCorrelativas(
                    (int) $_POST['id_materia'],
                    $_POST['correlativas'] ?? []
                );
                $msg = urlencode('Correlativas actualizadas.');
                break;
            case 'eliminar':
                $modelo->eliminar((int) $_POST['id']);
                $msg = urlencode('Materia eliminada correctamente.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/materias_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/materias_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$materias = $modelo->getAll();
$planes   = $modelPlan->getAll();
$editando = null;

/* Modo edición de campos */
if (isset($_GET['editar'])) {
    $editando = $modelo->getById((int) $_GET['editar']);
}

/* Modo gestión de correlativas */
$correlativas_materia = null;
$materias_plan        = [];
if (isset($_GET['correlativas'])) {
    $id_mat = (int) $_GET['correlativas'];
    $correlativas_materia = $modelo->getById($id_mat);
    if ($correlativas_materia) {
        $materias_plan = $modelo->getByPlan((int) $correlativas_materia['id_plan']);
        // Marcar las ya seleccionadas
        $ya_corr = array_column($modelo->getCorrelativas($id_mat), 'id_materia');
        foreach ($materias_plan as &$m) {
            $m['es_correlativa'] = in_array($m['id_materia'], $ya_corr);
        }
        unset($m);
    }
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/materias_view.php';
