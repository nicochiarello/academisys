<?php

require_once __DIR__ . '/../includes/auth.php';

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

    public function crear(array $datos): string
    {
        $passwordTemporal = bin2hex(random_bytes(5));

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO Profesor (dni, apellido, nombre, email, telefono, fecha_ingreso, activo)
                 VALUES (:dni, :apellido, :nombre, :email, :telefono, :fecha_ingreso, 1)'
            );
            $stmt->execute([
                ':dni'           => $datos['dni'],
                ':apellido'      => $datos['apellido'],
                ':nombre'        => $datos['nombre'],
                ':email'         => $datos['email'],
                ':telefono'      => $datos['telefono']      ?? null,
                ':fecha_ingreso' => $datos['fecha_ingreso'] ?? null,
            ]);

            $idProfesor = (int) $this->pdo->lastInsertId();

            $stmtUsr = $this->pdo->prepare(
                'INSERT INTO Usuario (email, password_hash, id_rol, id_profesor, activo, debe_cambiar_password)
                 VALUES (:email, :password_hash, :id_rol, :id_profesor, 1, 1)'
            );
            $stmtUsr->execute([
                ':email'         => $datos['email'],
                ':password_hash' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
                ':id_rol'        => ROL_DOCENTE,
                ':id_profesor'   => $idProfesor,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $passwordTemporal;
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
