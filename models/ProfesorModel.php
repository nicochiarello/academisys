<?php

class ProfesorModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM Profesor WHERE activo = 1 ORDER BY apellido, nombre'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Profesor WHERE id_profesor = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Profesor (nombre, apellido, dni, email, telefono, titulo, activo)
             VALUES (:nombre, :apellido, :dni, :email, :telefono, :titulo, 1)'
        );
        return $stmt->execute([
            ':nombre'    => $datos['nombre'],
            ':apellido'  => $datos['apellido'],
            ':dni'       => $datos['dni'],
            ':email'     => $datos['email'],
            ':telefono'  => $datos['telefono'] ?? null,
            ':titulo'    => $datos['titulo']   ?? null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Profesor
             SET nombre = :nombre, apellido = :apellido, dni = :dni,
                 email = :email, telefono = :telefono, titulo = :titulo
             WHERE id_profesor = :id'
        );
        return $stmt->execute([
            ':id'       => $id,
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':dni'      => $datos['dni'],
            ':email'    => $datos['email'],
            ':telefono' => $datos['telefono'] ?? null,
            ':titulo'   => $datos['titulo']   ?? null,
        ]);
    }

    public function darBaja(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Profesor SET activo = 0 WHERE id_profesor = :id');
        return $stmt->execute([':id' => $id]);
    }
}
