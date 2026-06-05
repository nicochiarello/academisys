<?php

class InscripcionModel
{
    public function __construct(private PDO $pdo) {}

    public function inscribir(string $legajo, int $id_comision): string
    {
        $stmt = $this->pdo->prepare('CALL InscribirAlumno(:legajo, :id_comision, @msg)');
        $stmt->execute([':legajo' => $legajo, ':id_comision' => $id_comision]);
        $stmt->closeCursor();

        $row = $this->pdo->query('SELECT @msg AS mensaje')->fetch();
        return $row['mensaje'] ?? '';
    }

    public function darBaja(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE Inscripcion SET estado = 'baja' WHERE id_inscripcion = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function getByAlumno(string $legajo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM v_historial_alumno WHERE legajo = :legajo ORDER BY anio DESC, cuatrimestre DESC'
        );
        $stmt->execute([':legajo' => $legajo]);
        return $stmt->fetchAll();
    }

    public function getByComision(int $id_comision): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, al.nombre, al.apellido
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.legajo
             WHERE i.id_comision = :id
             ORDER BY al.apellido, al.nombre'
        );
        $stmt->execute([':id' => $id_comision]);
        return $stmt->fetchAll();
    }
}
