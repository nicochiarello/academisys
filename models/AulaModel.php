<?php

class AulaModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM Aula ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Aula WHERE id_aula = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Aula (nombre, capacidad, tipo)
             VALUES (:nombre, :capacidad, :tipo)'
        );
        return $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':capacidad' => $datos['capacidad'],
            ':tipo'      => $datos['tipo'] ?? null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Aula SET nombre = :nombre, capacidad = :capacidad, tipo = :tipo
             WHERE id_aula = :id'
        );
        return $stmt->execute([
            ':id'        => $id,
            ':nombre'    => $datos['nombre'],
            ':capacidad' => $datos['capacidad'],
            ':tipo'      => $datos['tipo'] ?? null,
        ]);
    }
}
