<?php

class AsistenciaModel
{
    public function __construct(private PDO $pdo) {}

    /* RegistrarAsistencia devuelve result set con 'mensaje', no OUT param */
    public function registrar(
        int    $id_inscripcion,
        string $fecha_clase,
        int    $presente,
        int    $justificada
    ): string {
        $stmt = $this->pdo->prepare('CALL RegistrarAsistencia(?, ?, ?, ?)');
        $stmt->execute([$id_inscripcion, $fecha_clase, $presente, $justificada]);
        $row = $stmt->fetch();
        while ($stmt->nextRowset()) {}
        return $row['mensaje'] ?? '';
    }

    /* El FK en Inscripcion es id_alumno; columna de fecha en Asistencia es fecha_clase */
    public function getByComisionFecha(int $id_comision, string $fecha): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT i.id_inscripcion, al.legajo, al.nombre, al.apellido,
                    COALESCE(a.presente, 0)    AS presente,
                    COALESCE(a.justificada, 0) AS justificada
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.id_alumno
             LEFT JOIN Asistencia a
                    ON a.id_inscripcion = i.id_inscripcion
                   AND a.fecha_clase = :fecha
             WHERE i.id_comision = :id_comision AND i.estado = 'activa'
             ORDER BY al.apellido, al.nombre"
        );
        $stmt->execute([':id_comision' => $id_comision, ':fecha' => $fecha]);
        return $stmt->fetchAll();
    }

    public function getByInscripcion(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Asistencia
             WHERE id_inscripcion = :id
             ORDER BY fecha_clase DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function getPorcentaje(int $id_inscripcion): float
    {
        $stmt = $this->pdo->prepare(
            'SELECT porcentaje_asistencia
             FROM v_asistencia_por_inscripcion
             WHERE id_inscripcion = :id'
        );
        $stmt->execute([':id' => $id_inscripcion]);
        $row = $stmt->fetch();
        return (float) ($row['porcentaje_asistencia'] ?? 0);
    }
}
