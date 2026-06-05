<?php

class PlanModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.*, c.nombre AS nombre_carrera
             FROM Plan p
             JOIN Carrera c ON c.id_carrera = p.id_carrera
             ORDER BY c.nombre, p.anio DESC'
        );
        return $stmt->fetchAll();
    }

    public function getByCarrera(int $id_carrera): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Plan WHERE id_carrera = :id ORDER BY anio DESC'
        );
        $stmt->execute([':id' => $id_carrera]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.nombre AS nombre_carrera
             FROM Plan p
             JOIN Carrera c ON c.id_carrera = p.id_carrera
             WHERE p.id_plan = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Plan (id_carrera, anio, descripcion)
             VALUES (:id_carrera, :anio, :descripcion)'
        );
        return $stmt->execute([
            ':id_carrera'  => $datos['id_carrera'],
            ':anio'        => $datos['anio'],
            ':descripcion' => $datos['descripcion'] ?? null,
        ]);
    }
}
