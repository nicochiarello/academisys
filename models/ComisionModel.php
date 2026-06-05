<?php

class ComisionModel
{
    public function __construct(private PDO $pdo) {}

    public function getActivas(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM v_comisiones_activas ORDER BY nombre_materia');
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT co.*,
                    m.nombre AS nombre_materia,
                    p.nombre AS nombre_profesor, p.apellido AS apellido_profesor,
                    a.nombre AS nombre_aula,
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

    public function getByProfesor(int $id_profesor): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT vc.*
             FROM v_comisiones_activas vc
             WHERE vc.id_profesor = :id_profesor'
        );
        $stmt->execute([':id_profesor' => $id_profesor]);
        return $stmt->fetchAll();
    }

    public function getInscriptos(int $id_comision): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, al.nombre, al.apellido, al.legajo
             FROM Inscripcion i
             JOIN Alumno al ON al.legajo = i.legajo
             WHERE i.id_comision = :id AND i.estado IN (\'activa\', \'aprobada\')
             ORDER BY al.apellido, al.nombre'
        );
        $stmt->execute([':id' => $id_comision]);
        return $stmt->fetchAll();
    }

    /* Puede lanzar PDOException si el trigger T2 rechaza la inserción */
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
