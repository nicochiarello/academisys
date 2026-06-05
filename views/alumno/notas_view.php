<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mis Notas<?= $inscripcion ? ' — ' . htmlspecialchars($inscripcion['nombre_materia'], ENT_QUOTES, 'UTF-8') : '' ?></h1>
    <a href="<?= BASE_URL ?>/controllers/alumno/cursadas_ctrl.php" class="btn btn-warn">← Mis Cursadas</a>
</div>

<?php if (!$id_inscripcion): ?>
<!-- Sin inscripción seleccionada: mostrar lista de cursadas -->
<div class="card">
    <h2>Seleccioná una materia</h2>
    <?php if (empty($cursadas)): ?>
        <p style="color:#90a4ae">No tenés cursadas registradas.</p>
    <?php else: ?>
    <ul style="list-style:none;padding:0">
        <?php foreach ($cursadas as $c): ?>
        <li style="margin-bottom:8px">
            <a href="<?= BASE_URL ?>/controllers/alumno/notas_ctrl.php?id_inscripcion=<?= (int)$c['id_inscripcion'] ?>"
               class="btn btn-info btn-sm">
                <?= htmlspecialchars($c['nombre_materia'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                — <?= (int)($c['anio'] ?? 0) ?> / <?= (int)($c['cuatrimestre'] ?? 0) ?>° cuat.
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>

<?php elseif (!$inscripcion): ?>
    <div class="alert alert-err">No se encontró esa inscripción o no te pertenece.</div>

<?php else: ?>
<!-- Resumen al tope -->
<?php
$estado_insc   = $inscripcion['estado'] ?? '';
$badge_estado  = match($estado_insc) {
    'aprobada'    => 'badge-ok',
    'activa'      => 'badge-act',
    'desaprobada' => 'badge-off',
    'baja'        => 'badge-off',
    default       => ''
};
$notas_finales = array_filter($notas, fn($n) => $n['tipo'] === 'final');
$promedio_final = count($notas_finales) > 0
    ? array_sum(array_column($notas_finales, 'calificacion')) / count($notas_finales)
    : null;
?>
<div class="card" style="display:flex;gap:32px;align-items:center;padding:20px 24px">
    <div>
        <span style="font-size:.78rem;color:#78909c;font-weight:600">ESTADO</span><br>
        <span class="badge <?= $badge_estado ?>" style="font-size:.9rem;padding:5px 12px">
            <?= htmlspecialchars(ucfirst($estado_insc), ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
    <?php if ($promedio_final !== null): ?>
    <div>
        <span style="font-size:.78rem;color:#78909c;font-weight:600">PROMEDIO NOTAS FINALES</span><br>
        <span style="font-size:1.6rem;font-weight:700;color:<?= $promedio_final >= 4 ? '#2e7d32' : '#c62828' ?>">
            <?= number_format($promedio_final, 2) ?>
        </span>
    </div>
    <?php endif; ?>
    <div>
        <span style="font-size:.78rem;color:#78909c;font-weight:600">EVALUACIONES</span><br>
        <span style="font-size:1.6rem;font-weight:700;color:#1a237e"><?= count($notas) ?></span>
    </div>
</div>

<?php if (empty($notas)): ?>
    <div class="alert alert-err">No hay notas registradas para esta materia.</div>
<?php else: ?>
<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>Tipo de evaluación</th><th>Calificación</th><th>Resultado</th>
                <th>Fecha</th><th>Observación</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($notas as $nota): ?>
            <?php $aprobada = (float)$nota['calificacion'] >= 4; ?>
            <tr>
                <td><?= htmlspecialchars(str_replace('_', ' ', ucfirst($nota['tipo'])), ENT_QUOTES, 'UTF-8') ?></td>
                <td style="font-weight:700;font-size:1rem"><?= number_format((float)$nota['calificacion'], 2) ?></td>
                <td>
                    <span class="badge <?= $aprobada ? 'badge-ok' : 'badge-off' ?>">
                        <?= $aprobada ? 'Aprobado' : 'Desaprobado' ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($nota['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($nota['observacion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; /* $inscripcion */ ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
