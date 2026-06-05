<?php

class AsistenciaModel
{
    public function __construct(private PDO $pdo) {}

    public function registrar(
        int    $id_inscripcion,
        string $fecha,
        int    $presente,
        int    $justificada
    ): string {
        $stmt = $this->pdo->prepare(
            'CALL RegistrarAsistencia(:id_inscripcion, :fecha, :presente, :justificada, @msg)'
        );
        $stmt->execute([
            ':id_inscripcion' => $id_inscripcion,
            ':fecha'          => $fecha,
            ':presente'       => $presente,
            ':justificada'    => $justificada,
        ]);
        $stmt->closeCursor();

        $row = $this->pdo->query('SELECT @msg AS mensaje')->fetch();
        return $row['mensaje'] ?? '';
    }

    public function getByComisionFecha(int $id_comision, string $fecha): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.id_inscripcion, al.legajo, al.nombre, al.apellido,
                    COALESCE(as2.presente, 0) AS presente,
                    COALESCE(as2.justificada, 0) AS justificada
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.legajo
             LEFT JOIN Asistencia as2
                    ON as2.id_inscripcion = i.id_inscripcion AND as2.fecha = :fecha
             WHERE i.id_comision = :id_comision AND i.estado = \'activa\'
             ORDER BY al.apellido, al.nombre'
        );
        $stmt->execute([':id_comision' => $id_comision, ':fecha' => $fecha]);
        return $stmt->fetchAll();
    }

    public function getByInscripcion(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Asistencia WHERE id_inscripcion = :id ORDER BY fecha DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function getPorcentaje(int $id_inscripcion): float
    {
        $stmt = $this->pdo->prepare(
            'SELECT porcentaje_asistencia FROM v_asistencia_por_inscripcion WHERE id_inscripcion = :id'
        );
        $stmt->execute([':id' => $id_inscripcion]);
        $row = $stmt->fetch();
        return (float) ($row['porcentaje_asistencia'] ?? 0);
    }
}
