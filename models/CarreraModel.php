<?php

class CarreraModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM Carrera ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Carrera WHERE id_carrera = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Carrera (nombre, duracion_años, titulo_otorga, modalidad, activa)
             VALUES (:nombre, :duracion, :titulo, :modalidad, 1)'
        );
        return $stmt->execute([
            ':nombre'   => $datos['nombre'],
            ':duracion' => $datos['duracion_años'],
            ':titulo'   => $datos['titulo_otorga'] ?? null,
            ':modalidad'=> $datos['modalidad'] ?? 'presencial',
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Carrera
             SET nombre = :nombre, duracion_años = :duracion,
                 titulo_otorga = :titulo, modalidad = :modalidad
             WHERE id_carrera = :id'
        );
        return $stmt->execute([
            ':id'       => $id,
            ':nombre'   => $datos['nombre'],
            ':duracion' => $datos['duracion_años'],
            ':titulo'   => $datos['titulo_otorga'] ?? null,
            ':modalidad'=> $datos['modalidad'] ?? 'presencial',
        ]);
    }

    public function darBaja(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Carrera SET activa = 0 WHERE id_carrera = :id');
        return $stmt->execute([':id' => $id]);
    }
}
