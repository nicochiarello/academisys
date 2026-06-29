<?php

class UsuarioModel
{
    public function __construct(private PDO $pdo) {}

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nombre AS nombre_rol,
                    CONCAT(al.apellido, \', \', al.nombre) AS nombre_alumno,
                    CONCAT(p.apellido,  \', \', p.nombre)  AS nombre_profesor
             FROM Usuario u
             JOIN Rol r ON r.id_rol = u.id_rol
             LEFT JOIN Alumno  al ON al.legajo      = u.legajo_alumno
             LEFT JOIN Profesor p  ON p.id_profesor = u.id_profesor
             WHERE u.id_usuario = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nombre AS nombre_rol
             FROM Usuario u
             JOIN Rol r ON r.id_rol = u.id_rol
             WHERE u.email = :email AND u.activo = 1
             LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT u.*, r.nombre AS nombre_rol,
                    CONCAT(al.apellido, \', \', al.nombre) AS nombre_alumno,
                    CONCAT(p.apellido, \', \', p.nombre)   AS nombre_profesor
             FROM Usuario u
             JOIN Rol r ON r.id_rol = u.id_rol
             LEFT JOIN Alumno al ON al.legajo = u.legajo_alumno
             LEFT JOIN Profesor p ON p.id_profesor = u.id_profesor
             ORDER BY u.email'
        );
        return $stmt->fetchAll();
    }

    private function whereParams(string $q): array
    {
        if ($q === '') return ['', []];
        $like = "%$q%";
        $where = 'WHERE (u.email        LIKE ?
                      OR u.legajo_alumno LIKE ?
                      OR al.dni          LIKE ?
                      OR al.nombre       LIKE ?
                      OR al.apellido     LIKE ?
                      OR p.nombre        LIKE ?
                      OR p.apellido      LIKE ?)';
        return [$where, array_fill(0, 7, $like)];
    }

    public function buscar(string $q, int $limit, int $offset): array
    {
        [$where, $params] = $this->whereParams($q);
        $stmt = $this->pdo->prepare(
            "SELECT u.*, r.nombre AS nombre_rol,
                    CONCAT(al.apellido, ', ', al.nombre) AS nombre_alumno,
                    CONCAT(p.apellido,  ', ', p.nombre)  AS nombre_profesor
             FROM Usuario u
             JOIN Rol r ON r.id_rol = u.id_rol
             LEFT JOIN Alumno  al ON al.legajo     = u.legajo_alumno
             LEFT JOIN Profesor p ON p.id_profesor = u.id_profesor
             $where
             ORDER BY u.email
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function contar(string $q): int
    {
        [$where, $params] = $this->whereParams($q);
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM Usuario u
             LEFT JOIN Alumno  al ON al.legajo     = u.legajo_alumno
             LEFT JOIN Profesor p ON p.id_profesor = u.id_profesor
             $where"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Usuario (email, password_hash, id_rol, legajo_alumno, id_profesor, activo)
             VALUES (:email, :password_hash, :id_rol, :legajo_alumno, :id_profesor, 1)'
        );
        return $stmt->execute([
            ':email'         => $datos['email'],
            ':password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
            ':id_rol'        => $datos['id_rol'],
            ':legajo_alumno' => !empty($datos['legajo_alumno']) ? $datos['legajo_alumno'] : null,
            ':id_profesor'   => !empty($datos['id_profesor'])   ? (int) $datos['id_profesor'] : null,
        ]);
    }

    public function activar(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Usuario SET activo = 1 WHERE id_usuario = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function desactivar(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Usuario SET activo = 0 WHERE id_usuario = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function cambiarPassword(int $id_usuario, string $nuevaPassword): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Usuario
             SET password_hash = :password_hash, debe_cambiar_password = 0
             WHERE id_usuario = :id'
        );
        return $stmt->execute([
            ':password_hash' => password_hash($nuevaPassword, PASSWORD_DEFAULT),
            ':id'            => $id_usuario,
        ]);
    }
}
