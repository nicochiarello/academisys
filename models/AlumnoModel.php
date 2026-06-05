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
            'INSERT INTO Alumno (legajo, dni, apellido, nombre, email, telefono,
                                 fecha_nacimiento, fecha_ingreso, id_carrera, activo)
             VALUES (:legajo, :dni, :apellido, :nombre, :email, :telefono,
                     :fecha_nacimiento, :fecha_ingreso, :id_carrera, 1)'
        );
        return $stmt->execute([
            ':legajo'          => $datos['legajo'],
            ':dni'             => $datos['dni'],
            ':apellido'        => $datos['apellido'],
            ':nombre'          => $datos['nombre'],
            ':email'           => $datos['email'],
            ':telefono'        => $datos['telefono']         ?? null,
            ':fecha_nacimiento'=> $datos['fecha_nacimiento'] ?? null,
            ':fecha_ingreso'   => $datos['fecha_ingreso']    ?? null,
            ':id_carrera'      => $datos['id_carrera'],
        ]);
    }

    public function actualizar(string $legajo, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Alumno
             SET dni = :dni, apellido = :apellido, nombre = :nombre,
                 email = :email, telefono = :telefono,
                 fecha_nacimiento = :fecha_nacimiento,
                 fecha_ingreso = :fecha_ingreso,
                 id_carrera = :id_carrera
             WHERE legajo = :legajo'
        );
        return $stmt->execute([
            ':legajo'          => $legajo,
            ':dni'             => $datos['dni'],
            ':apellido'        => $datos['apellido'],
            ':nombre'          => $datos['nombre'],
            ':email'           => $datos['email'],
            ':telefono'        => $datos['telefono']         ?? null,
            ':fecha_nacimiento'=> $datos['fecha_nacimiento'] ?? null,
            ':fecha_ingreso'   => $datos['fecha_ingreso']    ?? null,
            ':id_carrera'      => $datos['id_carrera'],
        ]);
    }

    public function darBaja(string $legajo): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Alumno SET activo = 0 WHERE legajo = :legajo');
        return $stmt->execute([':legajo' => $legajo]);
    }
}
