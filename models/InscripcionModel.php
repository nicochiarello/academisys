<?php

class InscripcionModel
{
    public function __construct(private PDO $pdo) {}

    /* InscribirAlumno devuelve un result set con columna 'mensaje', no OUT param */
    public function inscribir(string $legajo, int $id_comision): string
    {
        $stmt = $this->pdo->prepare('CALL InscribirAlumno(?, ?)');
        $stmt->execute([$legajo, $id_comision]);
        $row = $stmt->fetch();
        while ($stmt->nextRowset()) {}
        return $row['mensaje'] ?? '';
    }

    public function darBaja(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE Inscripcion SET estado = 'baja' WHERE id_inscripcion = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    /* La vista v_historial_alumno usa I.id_alumno AS legajo */
    public function getByAlumno(string $legajo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM v_historial_alumno
             WHERE legajo = :legajo
             ORDER BY anio DESC, cuatrimestre DESC'
        );
        $stmt->execute([':legajo' => $legajo]);
        return $stmt->fetchAll();
    }

    /* El FK en Inscripcion es id_alumno (referencia Alumno.legajo) */
    public function getByComision(int $id_comision): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, al.nombre, al.apellido
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.id_alumno
             WHERE i.id_comision = :id
             ORDER BY al.apellido, al.nombre'
        );
        $stmt->execute([':id' => $id_comision]);
        return $stmt->fetchAll();
    }
}
