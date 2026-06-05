<?php

class CicloModel
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM Ciclo_Academico ORDER BY anio DESC, cuatrimestre DESC'
        );
        return $stmt->fetchAll();
    }

    public function getActivo(): array|false
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM Ciclo_Academico WHERE activo = 1 LIMIT 1'
        );
        return $stmt->fetch();
    }

    /* El trigger trg_after_update_ciclo desactiva los demás automáticamente */
    public function activar(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Ciclo_Academico SET activo = 1 WHERE id_ciclo = :id'
        );
        return $stmt->execute([':id' => $id]);
    }

    public function crear(array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Ciclo_Academico (anio, cuatrimestre, fecha_inicio, fecha_fin, activo)
             VALUES (:anio, :cuatrimestre, :fecha_inicio, :fecha_fin, 0)'
        );
        return $stmt->execute([
            ':anio'         => $datos['anio'],
            ':cuatrimestre' => $datos['cuatrimestre'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin'    => $datos['fecha_fin'],
        ]);
    }
}
