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
             ORDER BY c.nombre, p.`año_vigencia` DESC'
        );
        return $stmt->fetchAll();
    }

    public function getByCarrera(int $id_carrera): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Plan WHERE id_carrera = :id ORDER BY `año_vigencia` DESC'
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
            'INSERT INTO Plan (id_carrera, `año_vigencia`, descripcion)
             VALUES (:id_carrera, :anio, :descripcion)'
        );
        return $stmt->execute([
            ':id_carrera'  => $datos['id_carrera'],
            ':anio'        => $datos['año_vigencia'],
            ':descripcion' => $datos['descripcion'] ?? null,
        ]);
    }

    public function tieneMaterias(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM Materia WHERE id_plan = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Plan WHERE id_plan = :id');
        return $stmt->execute([':id' => $id]);
    }
}
