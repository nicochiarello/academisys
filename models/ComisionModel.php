<?php

class ComisionModel
{
    public function __construct(private PDO $pdo) {}

    public function getActivas(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM v_comisiones_activas ORDER BY nombre_materia'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT co.*,
                    m.nombre AS nombre_materia,
                    p.nombre AS nombre_profesor, p.apellido AS apellido_profesor,
                    a.numero AS numero_aula, a.edificio,
                    ca.anio, ca.cuatrimestre
             FROM Comision co
             JOIN Materia m ON m.id_materia = co.id_materia
             JOIN Profesor p ON p.id_profesor = co.id_profesor
             JOIN Aula a ON a.id_aula = co.id_aula
             JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
             WHERE co.id_comision = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /* v_comisiones_activas no tiene id_profesor — query directa a Comision */
    public function getByProfesor(int $id_profesor): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT co.id_comision, m.nombre AS nombre_materia,
                    a.numero AS aula, a.edificio, co.turno, co.cupo_maximo,
                    ca.anio, ca.cuatrimestre,
                    COUNT(i.id_inscripcion) AS inscriptos_activos
             FROM Comision co
             JOIN Materia m ON m.id_materia = co.id_materia
             JOIN Aula a ON a.id_aula = co.id_aula
             JOIN Ciclo_Academico ca ON ca.id_ciclo = co.id_ciclo
             LEFT JOIN Inscripcion i
                    ON i.id_comision = co.id_comision
                   AND i.estado IN (\'activa\', \'aprobada\')
             WHERE co.id_profesor = :id AND ca.activo = 1
             GROUP BY co.id_comision, m.nombre, a.numero, a.edificio,
                      co.turno, co.cupo_maximo, ca.anio, ca.cuatrimestre'
        );
        $stmt->execute([':id' => $id_profesor]);
        return $stmt->fetchAll();
    }

    /* El FK en Inscripcion es id_alumno (referencia Alumno.legajo) */
    public function getInscriptos(int $id_comision): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT i.*, al.nombre, al.apellido, al.legajo
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.id_alumno
             WHERE i.id_comision = :id AND i.estado IN ('activa', 'aprobada')
             ORDER BY al.apellido, al.nombre"
        );
        $stmt->execute([':id' => $id_comision]);
        return $stmt->fetchAll();
    }

    public function actualizar(int $id, array $datos): bool
    {
        // Verificar conflicto aula+turno+ciclo excluyendo la comisión actual
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM Comision
             WHERE id_aula = :aula AND turno = :turno AND id_ciclo = :ciclo
               AND id_comision != :id'
        );
        $stmt->execute([
            ':aula'  => $datos['id_aula'],
            ':turno' => $datos['turno'],
            ':ciclo' => $datos['id_ciclo'],
            ':id'    => $id,
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new RuntimeException('El aula ya tiene una comisión asignada en ese turno y ciclo.');
        }

        $stmt2 = $this->pdo->prepare(
            'UPDATE Comision
             SET id_profesor = :id_profesor, id_aula = :id_aula,
                 turno = :turno, cupo_maximo = :cupo_maximo
             WHERE id_comision = :id'
        );
        return $stmt2->execute([
            ':id_profesor' => $datos['id_profesor'],
            ':id_aula'     => $datos['id_aula'],
            ':turno'       => $datos['turno'],
            ':cupo_maximo' => $datos['cupo_maximo'],
            ':id'          => $id,
        ]);
    }

    public function tieneInscripciones(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM Inscripcion WHERE id_comision = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function eliminar(int $id): bool
    {
        if ($this->tieneInscripciones($id)) {
            throw new RuntimeException('No se puede eliminar: la comisión tiene inscripciones registradas.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM Comision WHERE id_comision = :id');
        return $stmt->execute([':id' => $id]);
    }

    /* Puede lanzar PDOException si el trigger rechaza la inserción */
    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Comision (id_materia, id_profesor, id_aula, id_ciclo, turno, cupo_maximo)
             VALUES (:id_materia, :id_profesor, :id_aula, :id_ciclo, :turno, :cupo_maximo)'
        );
        return $stmt->execute([
            ':id_materia'  => $datos['id_materia'],
            ':id_profesor' => $datos['id_profesor'],
            ':id_aula'     => $datos['id_aula'],
            ':id_ciclo'    => $datos['id_ciclo'],
            ':turno'       => $datos['turno'],
            ':cupo_maximo' => $datos['cupo_maximo'],
        ]);
    }
}
