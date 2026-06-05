<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();
requireRol([ROL_ADMIN]);

require_once __DIR__ . '/../../models/CicloModel.php';
$modelo = new CicloModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        switch ($action) {
            case 'crear':
                $modelo->crear($_POST);
                $msg = urlencode('Ciclo académico creado correctamente.');
                break;
            case 'activar':
                /* El trigger trg_after_update_ciclo desactiva los demás automáticamente */
                if ($_POST['action'] === 'activar') {
                    try {
                        $id = (int) $_POST['id'];
                        // Primero desactivar todos
                        $pdo->exec("UPDATE Ciclo_Academico SET activo = 0");
                        // Luego activar el seleccionado
                        $modelo->activar($id);
                        header('Location: ' . BASE_URL . '/controllers/admin/ciclos_ctrl.php?msg=Ciclo activado correctamente');
                        exit;
                    } catch (PDOException $e) {
                        $error = $e->getMessage();
                    }
                };
                $msg = urlencode('Ciclo activado. Los demás ciclos fueron desactivados.');
                break;
            default:
                $msg = '';
        }
        header('Location: ' . BASE_URL . '/controllers/admin/ciclos_ctrl.php?msg=' . $msg);
    } catch (PDOException $e) {
        header('Location: ' . BASE_URL . '/controllers/admin/ciclos_ctrl.php?err=' . urlencode($e->getMessage()));
    }
    exit;
}

$ciclos   = $modelo->getAll();
$mensaje  = $_GET['msg'] ?? ($_GET['err'] ?? null);
$es_error = isset($_GET['err']);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../views/admin/ciclos_view.php';
