<?php

class MateriaModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT m.*, p.anio AS plan_anio, c.nombre AS nombre_carrera
             FROM Materia m
             JOIN Plan p ON p.id_plan = m.id_plan
             JOIN Carrera c ON c.id_carrera = p.id_carrera
             ORDER BY c.nombre, m.nombre'
        );
        return $stmt->fetchAll();
    }

    public function getByPlan(int $id_plan): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Materia WHERE id_plan = :id_plan ORDER BY cuatrimestre, nombre'
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

    public function setCorrelativas(int $id_materia, array $ids_correlativas): bool
    {
        $this->pdo->prepare('DELETE FROM Correlativa WHERE id_materia = :id')
                  ->execute([':id' => $id_materia]);

        if (empty($ids_correlativas)) {
            return true;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO Correlativa (id_materia, id_correlativa) VALUES (:id_materia, :id_correlativa)'
        );
        foreach ($ids_correlativas as $id_corr) {
            $stmt->execute([':id_materia' => $id_materia, ':id_correlativa' => (int) $id_corr]);
        }
        return true;
    }
}
