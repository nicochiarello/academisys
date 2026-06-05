<?php

class AulaModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM Aula ORDER BY edificio, numero'
        );
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
            'INSERT INTO Aula (numero, edificio, capacidad, equipamiento)
             VALUES (:numero, :edificio, :capacidad, :equipamiento)'
        );
        return $stmt->execute([
            ':numero'       => $datos['numero'],
            ':edificio'     => $datos['edificio']     ?? null,
            ':capacidad'    => $datos['capacidad']    ?? null,
            ':equipamiento' => $datos['equipamiento'] ?? null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Aula
             SET numero = :numero, edificio = :edificio,
                 capacidad = :capacidad, equipamiento = :equipamiento
             WHERE id_aula = :id'
        );
        return $stmt->execute([
            ':id'           => $id,
            ':numero'       => $datos['numero'],
            ':edificio'     => $datos['edificio']     ?? null,
            ':capacidad'    => $datos['capacidad']    ?? null,
            ':equipamiento' => $datos['equipamiento'] ?? null,
        ]);
    }
}
