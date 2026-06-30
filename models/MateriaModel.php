<?php

class MateriaModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT m.*, p.`año_vigencia` AS plan_anio, c.nombre AS nombre_carrera
             FROM Materia m
             JOIN Plan p ON p.id_plan = m.id_plan
             JOIN Carrera c ON c.id_carrera = p.id_carrera
             ORDER BY c.nombre, m.`año_cursada`, m.cuatrimestre, m.nombre'
        );
        return $stmt->fetchAll();
    }

    public function getByPlan(int $id_plan): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Materia
             WHERE id_plan = :id_plan
             ORDER BY `año_cursada`, cuatrimestre, nombre'
        );
        $stmt->execute([':id_plan' => $id_plan]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Materia WHERE id_materia = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Materia (id_plan, nombre, codigo, `año_cursada`, cuatrimestre, carga_horaria)
             VALUES (:id_plan, :nombre, :codigo, :anio, :cuatrimestre, :carga)'
        );
        return $stmt->execute([
            ':id_plan'      => $datos['id_plan'],
            ':nombre'       => $datos['nombre'],
            ':codigo'       => $datos['codigo'],
            ':anio'         => $datos['año_cursada'],
            ':cuatrimestre' => $datos['cuatrimestre'],
            ':carga'        => $datos['carga_horaria'],
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Materia
             SET id_plan = :id_plan, nombre = :nombre, codigo = :codigo,
                 `año_cursada` = :anio, cuatrimestre = :cuatrimestre,
                 carga_horaria = :carga
             WHERE id_materia = :id'
        );
        return $stmt->execute([
            ':id'           => $id,
            ':id_plan'      => $datos['id_plan'],
            ':nombre'       => $datos['nombre'],
            ':codigo'       => $datos['codigo'],
            ':anio'         => $datos['año_cursada'],
            ':cuatrimestre' => $datos['cuatrimestre'],
            ':carga'        => $datos['carga_horaria'],
        ]);
    }

    public function getCorrelativas(int $id_materia): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*
             FROM Correlativa c
             JOIN Materia m ON m.id_materia = c.id_correlativa
             WHERE c.id_materia = :id'
        );
        $stmt->execute([':id' => $id_materia]);
        return $stmt->fetchAll();
    }

    public function eliminar(int $id): bool
    {
        $this->pdo->prepare('DELETE FROM Correlativa WHERE id_materia = :id OR id_correlativa = :id')
                  ->execute([':id' => $id]);
        $stmt = $this->pdo->prepare('DELETE FROM Materia WHERE id_materia = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function setCorrelativas(int $id_materia, array $ids_correlativas): bool
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM Correlativa WHERE id_materia = :id')
                      ->execute([':id' => $id_materia]);

            if (!empty($ids_correlativas)) {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO Correlativa (id_materia, id_correlativa) VALUES (?, ?)'
                );
                foreach ($ids_correlativas as $id_corr) {
                    $stmt->execute([$id_materia, (int) $id_corr]);
                }
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return true;
    }
}
