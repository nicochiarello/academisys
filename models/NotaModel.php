<?php

class NotaModel
{
    public function __construct(private PDO $pdo) {}

    public function registrar(
        int    $id_inscripcion,
        string $tipo,
        float  $calificacion,
        string $fecha,
        string $obs
    ): string {
        $stmt = $this->pdo->prepare(
            'CALL RegistrarNota(:id_inscripcion, :tipo, :calificacion, :fecha, :obs, @msg)'
        );
        $stmt->execute([
            ':id_inscripcion' => $id_inscripcion,
            ':tipo'           => $tipo,
            ':calificacion'   => $calificacion,
            ':fecha'          => $fecha,
            ':obs'            => $obs,
        ]);
        $stmt->closeCursor();

        $row = $this->pdo->query('SELECT @msg AS mensaje')->fetch();
        return $row['mensaje'] ?? '';
    }

    public function getByInscripcion(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Nota WHERE id_inscripcion = :id ORDER BY fecha DESC'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }
}
