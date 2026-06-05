<?php

class AlumnoModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT a.*, c.nombre AS nombre_carrera
             FROM Alumno a
             JOIN Carrera c ON c.id_carrera = a.id_carrera
             ORDER BY a.apellido, a.nombre'
        );
        return $stmt->fetchAll();
    }

    public function getByLegajo(string $legajo): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, c.nombre AS nombre_carrera
             FROM Alumno a
             JOIN Carrera c ON c.id_carrera = a.id_carrera
             WHERE a.legajo = :legajo'
        );
        $stmt->execute([':legajo' => $legajo]);
        return $stmt->fetch();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Alumno (legajo, nombre, apellido, dni, email, telefono, id_carrera, fecha_ingreso, activo)
             VALUES (:legajo, :nombre, :apellido, :dni, :email, :telefono, :id_carrera, :fecha_ingreso, 1)'
        );
        return $stmt->execute([
            ':legajo'        => $datos['legajo'],
            ':nombre'        => $datos['nombre'],
            ':apellido'      => $datos['apellido'],
            ':dni'           => $datos['dni'],
            ':email'         => $datos['email'],
            ':telefono'      => $datos['telefono'] ?? null,
            ':id_carrera'    => $datos['id_carrera'],
            ':fecha_ingreso' => $datos['fecha_ingreso'],
        ]);
    }

    public function actualizar(string $legajo, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Alumno
             SET nombre = :nombre, apellido = :apellido, dni = :dni,
                 email = :email, telefono = :telefono, id_carrera = :id_carrera
             WHERE legajo = :legajo'
        );
        return $stmt->execute([
            ':legajo'     => $legajo,
            ':nombre'     => $datos['nombre'],
            ':apellido'   => $datos['apellido'],
            ':dni'        => $datos['dni'],
            ':email'      => $datos['email'],
            ':telefono'   => $datos['telefono'] ?? null,
            ':id_carrera' => $datos['id_carrera'],
        ]);
    }

    public function darBaja(string $legajo): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Alumno SET activo = 0 WHERE legajo = :legajo');
        return $stmt->execute([':legajo' => $legajo]);
    }
}
