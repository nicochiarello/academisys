<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';  // define constantes ROL_*
require_once __DIR__ . '/../includes/header.php'; // session_start + requireLogin + navbar

$rol  = (int) $_SESSION['id_rol'];
$kpis = [];

try {
    switch ($rol) {

        case ROL_ADMIN:
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM Alumno WHERE activo = 1'
                )->fetchColumn(),
                'label' => 'Alumnos activos',
            ];
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM v_comisiones_activas'
                )->fetchColumn(),
                'label' => 'Comisiones ciclo activo',
            ];
            $kpis[] = [
                'valor' => $pdo->query(
                    "SELECT ROUND(AVG(calificacion), 1) FROM Nota WHERE tipo = 'final' AND calificacion >= 4"
                )->fetchColumn() ?? '—',
                'label' => 'Promedio aprobación final',
            ];
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM Titulo_Obtenido WHERE YEAR(fecha_egreso) = YEAR(CURDATE())'
                )->fetchColumn(),
                'label' => 'Títulos este año',
            ];
            break;

        case ROL_BEDEL:
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM v_comisiones_activas'
                )->fetchColumn(),
                'label' => 'Comisiones activas',
            ];
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM Inscripcion i
                     JOIN Comision co ON co.id_comision = i.id_comision
                     JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
                     WHERE ca.activo = 1'
                )->fetchColumn(),
                'label' => 'Inscripciones este ciclo',
            ];
            $kpis[] = [
                'valor' => $pdo->query(
                    'SELECT COUNT(*) FROM v_comisiones_activas WHERE inscriptos_activos >= cupo_maximo'
                )->fetchColumn(),
                'label' => 'Aulas al límite de cupo',
            ];
            break;

        case ROL_DOCENTE:
            $id_prof = (int) ($_SESSION['id_profesor'] ?? 0);
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM v_comisiones_activas WHERE id_profesor = :id'
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Mis comisiones activas'];

            $stmt = $pdo->prepare(
                'SELECT COALESCE(SUM(vc.inscriptos_activos), 0)
                 FROM v_comisiones_activas vc WHERE vc.id_profesor = :id'
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Mis alumnos este ciclo'];

            $stmt = $pdo->prepare(
                "SELECT ROUND(AVG(n.calificacion), 1)
                 FROM Nota n
                 JOIN Inscripcion i ON i.id_inscripcion = n.id_inscripcion
                 JOIN Comision co ON co.id_comision = i.id_comision
                 JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
                 WHERE co.id_profesor = :id AND n.tipo = 'final' AND n.calificacion >= 4
                   AND ca.activo = 1"
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn() ?? '—', 'label' => 'Promedio aprobación'];
            break;

        case ROL_ALUMNO:
            $legajo = $_SESSION['legajo_alumno'] ?? '';
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM Inscripcion WHERE legajo = :legajo AND estado = 'activa'"
            );
            $stmt->execute([':legajo' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Materias en curso'];

            $stmt = $pdo->prepare(
                'SELECT ROUND(promedio_general, 2) FROM v_promedio_por_alumno WHERE legajo = :legajo'
            );
            $stmt->execute([':legajo' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn() ?? '—', 'label' => 'Promedio general'];

            $stmt = $pdo->prepare(
                'SELECT ROUND(AVG(v.porcentaje_asistencia), 1)
                 FROM v_asistencia_por_inscripcion v
                 JOIN Inscripcion i ON i.id_inscripcion = v.id_inscripcion
                 WHERE i.legajo = :legajo AND i.estado = \'activa\''
            );
            $stmt->execute([':legajo' => $legajo]);
            $kpis[] = ['valor' => ($stmt->fetchColumn() ?? '—') . '%', 'label' => '% Asistencia ciclo actual'];

            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM Inscripcion WHERE legajo = :legajo AND estado = 'aprobada'"
            );
            $stmt->execute([':legajo' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Materias aprobadas'];
            break;
    }
} catch (PDOException $e) {
    $kpis = [['valor' => 'Error', 'label' => 'No se pudieron cargar los datos: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]];
}

require_once __DIR__ . '/../views/dashboard_view.php';
