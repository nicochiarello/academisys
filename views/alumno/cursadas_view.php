<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mis Cursadas</h1>
</div>

<?php if (empty($cursadas)): ?>
    <div class="alert alert-err">No tenés cursadas registradas.</div>
<?php else: ?>
<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>Materia</th><th>Año</th><th>Cuatrimestre</th>
                <th>Profesor</th><th>Estado</th><th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cursadas as $c): ?>
            <?php
            $estado = $c['estado_inscripcion'] ?? '';
            $badge  = match($estado) {
                'aprobada'    => 'badge-ok',
                'activa'      => 'badge-act',
                'desaprobada' => 'badge-off',
                'baja'        => 'badge-off',
                default       => ''
            };
            ?>
            <tr>
                <td><?= htmlspecialchars($c['nombre_materia'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) ($c['anio'] ?? 0) ?></td>
                <td><?= (int) ($c['cuatrimestre'] ?? 0) ?>°</td>
                <td><?= htmlspecialchars($c['profesor'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars(ucfirst($estado), ENT_QUOTES, 'UTF-8') ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/controllers/alumno/notas_ctrl.php?id_inscripcion=<?= (int)$c['id_inscripcion'] ?>"
                       class="btn btn-info btn-sm">Ver notas</a>
                    <a href="<?= BASE_URL ?>/controllers/alumno/asistencias_ctrl.php?id_inscripcion=<?= (int)$c['id_inscripcion'] ?>"
                       class="btn btn-primary btn-sm" style="margin-left:4px">Asistencias</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
