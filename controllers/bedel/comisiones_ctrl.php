<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN, ROL_BEDEL]);

require_once __DIR__ . '/../../models/ComisionModel.php';
require_once __DIR__ . '/../../models/MateriaModel.php';
require_once __DIR__ . '/../../models/ProfesorModel.php';
require_once __DIR__ . '/../../models/AulaModel.php';
require_once __DIR__ . '/../../models/CicloModel.php';

$modelo     = new ComisionModel($pdo);
$modelMat   = new MateriaModel($pdo);
$modelProf  = new ProfesorModel($pdo);
$modelAula  = new AulaModel($pdo);
$modelCiclo = new CicloModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Comisión creada correctamente.');
                break;
            case 'editar':
                $modelo->actualizar((int) $_POST['id_comision'], $_POST);
                $msg = urlencode('Comisión actualizada correctamente.');
                break;
            case 'eliminar':
                $modelo->eliminar((int) $_POST['id_comision']);
                $msg = urlencode('Comisión eliminada.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/bedel/comisiones_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        $msg = urlencode($e->errorInfo[2] ?? $e->getMessage());
        header('Location: ' . BASE_URL . '/controllers/bedel/comisiones_ctrl.php?err=' . $msg);
    } catch (RuntimeException $e) {
        $msg = urlencode($e->getMessage());
        header('Location: ' . BASE_URL . '/controllers/bedel/comisiones_ctrl.php?err=' . $msg);
    }
    exit;
}

$comisiones   = $modelo->getActivas();
$materias     = $modelMat->getAll();
$profesores   = $modelProf->getAll();
$aulas        = $modelAula->getAll();
$ciclo_activo = $modelCiclo->getActivo();

$editando = null;
if (isset($_GET['editar'])) {
    $editando = $modelo->getById((int) $_GET['editar']);
}

$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/bedel/comisiones_view.php';
