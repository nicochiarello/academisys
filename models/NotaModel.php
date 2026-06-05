<?php

class NotaModel
{
    public function __construct(private PDO $pdo) {}

    /* RegistrarNota devuelve result set con 'mensaje', no OUT param */
    public function registrar(
        int    $id_inscripcion,
        string $tipo,
        float  $calificacion,
        string $fecha,
        string $observacion
    ): string {
        $stmt = $this->pdo->prepare('CALL RegistrarNota(?, ?, ?, ?, ?)');
        $stmt->execute([$id_inscripcion, $tipo, $calificacion, $fecha, $observacion]);
        $row = $stmt->fetch();
        while ($stmt->nextRowset()) {}
        return $row['mensaje'] ?? '';
    }

    public function getByInscripcion(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Nota
             WHERE id_inscripcion = :id
             ORDER BY fecha DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }
}
