<?php

class ProfesorModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM Profesor ORDER BY apellido, nombre'
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
            'INSERT INTO Profesor (dni, apellido, nombre, email, telefono, fecha_ingreso, activo)
             VALUES (:dni, :apellido, :nombre, :email, :telefono, :fecha_ingreso, 1)'
        );
        return $stmt->execute([
            ':dni'          => $datos['dni'],
            ':apellido'     => $datos['apellido'],
            ':nombre'       => $datos['nombre'],
            ':email'        => $datos['email'],
            ':telefono'     => $datos['telefono']     ?? null,
            ':fecha_ingreso'=> $datos['fecha_ingreso'] ?? null,
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Profesor
             SET dni = :dni, apellido = :apellido, nombre = :nombre,
                 email = :email, telefono = :telefono, fecha_ingreso = :fecha_ingreso
             WHERE id_profesor = :id'
        );
        return $stmt->execute([
            ':id'           => $id,
            ':dni'          => $datos['dni'],
            ':apellido'     => $datos['apellido'],
            ':nombre'       => $datos['nombre'],
            ':email'        => $datos['email'],
            ':telefono'     => $datos['telefono']     ?? null,
            ':fecha_ingreso'=> $datos['fecha_ingreso'] ?? null,
        ]);
    }

    public function darBaja(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Profesor SET activo = 0 WHERE id_profesor = :id');
        return $stmt->execute([':id' => $id]);
    }
}
