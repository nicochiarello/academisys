<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
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
                    "SELECT ROUND(AVG(calificacion),1) FROM Nota WHERE tipo='final' AND calificacion >= 4"
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

            /* v_comisiones_activas no expone id_profesor — consulta directa */
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM Comision co
                 JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
                 WHERE co.id_profesor = :id AND ca.activo = 1'
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Mis comisiones activas'];

            $stmt = $pdo->prepare(
                "SELECT COUNT(i.id_inscripcion)
                 FROM Inscripcion i
                 JOIN Comision co ON co.id_comision = i.id_comision
                 JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
                 WHERE co.id_profesor = :id AND ca.activo = 1
                   AND i.estado IN ('activa', 'aprobada')"
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Mis alumnos este ciclo'];

            $stmt = $pdo->prepare(
                "SELECT ROUND(AVG(n.calificacion), 1)
                 FROM Nota n
                 JOIN Inscripcion i ON i.id_inscripcion = n.id_inscripcion
                 JOIN Comision co ON co.id_comision = i.id_comision
                 JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
                 WHERE co.id_profesor = :id AND n.tipo = 'final'
                   AND n.calificacion >= 4 AND ca.activo = 1"
            );
            $stmt->execute([':id' => $id_prof]);
            $kpis[] = ['valor' => $stmt->fetchColumn() ?? '—', 'label' => 'Promedio aprobación'];
            break;

        case ROL_ALUMNO:
            /* El FK en Inscripcion es id_alumno (referencia Alumno.legajo) */
            $legajo = $_SESSION['legajo_alumno'] ?? '';

            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM Inscripcion WHERE id_alumno = :leg AND estado = 'activa'"
            );
            $stmt->execute([':leg' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Materias en curso'];

            $stmt = $pdo->prepare(
                'SELECT ROUND(promedio_final, 2) FROM v_promedio_por_alumno WHERE legajo = :leg'
            );
            $stmt->execute([':leg' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn() ?? '—', 'label' => 'Promedio general'];

            $stmt = $pdo->prepare(
                "SELECT ROUND(AVG(v.porcentaje_asistencia), 1)
                 FROM v_asistencia_por_inscripcion v
                 JOIN Inscripcion i ON i.id_inscripcion = v.id_inscripcion
                 WHERE i.id_alumno = :leg AND i.estado = 'activa'"
            );
            $stmt->execute([':leg' => $legajo]);
            $val = $stmt->fetchColumn();
            $kpis[] = ['valor' => ($val !== false ? $val : '—') . '%', 'label' => '% Asistencia ciclo actual'];

            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM Inscripcion WHERE id_alumno = :leg AND estado = 'aprobada'"
            );
            $stmt->execute([':leg' => $legajo]);
            $kpis[] = ['valor' => $stmt->fetchColumn(), 'label' => 'Materias aprobadas'];
            break;
    }
} catch (PDOException $e) {
    $kpis = [['valor' => '!', 'label' => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]];
}

require_once __DIR__ . '/../views/dashboard_view.php';
