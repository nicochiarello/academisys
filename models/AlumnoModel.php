<?php

require_once __DIR__ . '/../includes/auth.php';

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

    private function whereParams(string $q): array
    {
        if ($q === '') return ['', []];
        $like  = "%$q%";
        $where = 'WHERE (a.legajo   LIKE ?
                      OR a.dni      LIKE ?
                      OR a.apellido LIKE ?
                      OR a.nombre   LIKE ?
                      OR a.email    LIKE ?)';
        return [$where, array_fill(0, 5, $like)];
    }

    public function buscar(string $q, int $limit, int $offset): array
    {
        [$where, $params] = $this->whereParams($q);
        $stmt = $this->pdo->prepare(
            "SELECT a.*, c.nombre AS nombre_carrera
             FROM Alumno a
             JOIN Carrera c ON c.id_carrera = a.id_carrera
             $where
             ORDER BY a.apellido, a.nombre
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function contar(string $q): int
    {
        [$where, $params] = $this->whereParams($q);
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM Alumno a $where"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
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

    /**
     * Crea el alumno y, en la misma transacción, su Usuario de acceso
     * (contraseña temporal que deberá cambiar en el primer login).
     * Devuelve la contraseña temporal en texto plano para mostrársela al admin una sola vez.
     */
    public function crear(array $datos): string
    {
        $passwordTemporal = $this->generarPasswordTemporal();

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO Alumno (legajo, dni, apellido, nombre, email, telefono,
                                     fecha_nacimiento, fecha_ingreso, id_carrera, activo)
                 VALUES (:legajo, :dni, :apellido, :nombre, :email, :telefono,
                         :fecha_nacimiento, :fecha_ingreso, :id_carrera, 1)'
            );
            $stmt->execute([
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

            $stmtUsr = $this->pdo->prepare(
                'INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, activo, debe_cambiar_password)
                 VALUES (:email, :password_hash, :id_rol, :legajo_alumno, 1, 1)'
            );
            $stmtUsr->execute([
                ':email'         => $datos['email'],
                ':password_hash' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
                ':id_rol'        => ROL_ALUMNO,
                ':legajo_alumno' => $datos['legajo'],
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $passwordTemporal;
    }

    private function generarPasswordTemporal(): string
    {
        return bin2hex(random_bytes(5));
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
