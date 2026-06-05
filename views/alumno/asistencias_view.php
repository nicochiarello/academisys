<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mis Asistencias<?= $inscripcion ? ' — ' . htmlspecialchars($inscripcion['nombre_materia'], ENT_QUOTES, 'UTF-8') : '' ?></h1>
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
            <a href="<?= BASE_URL ?>/controllers/alumno/asistencias_ctrl.php?id_inscripcion=<?= (int)$c['id_inscripcion'] ?>"
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
<!-- KPI grande de porcentaje -->
<?php $pct_ok = $porcentaje >= 75; ?>
<div class="card" style="text-align:center;padding:28px 24px;margin-bottom:24px">
    <div style="font-size:.85rem;color:#78909c;font-weight:600;margin-bottom:8px">
        PORCENTAJE DE ASISTENCIA
    </div>
    <div style="font-size:3.5rem;font-weight:900;color:<?= $pct_ok ? '#2e7d32' : '#c62828' ?>;line-height:1">
        <?= number_format($porcentaje, 1) ?>%
    </div>
    <div style="margin-top:8px;font-size:.85rem;color:<?= $pct_ok ? '#2e7d32' : '#c62828' ?>">
        <?= $pct_ok ? '✓ Asistencia regular' : '✗ Asistencia insuficiente (mínimo 75%)' ?>
    </div>
</div>

<?php if (empty($asistencias)): ?>
    <div class="alert alert-err">No hay registros de asistencia para esta materia.</div>
<?php else: ?>
<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>Fecha</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($asistencias as $a): ?>
            <?php
            if ($a['presente'])    { $estado_a = 'Presente';   $color = '#2e7d32'; $icono = '&#10003;'; }
            elseif ($a['justificada']) { $estado_a = 'Justificada'; $color = '#f57c00'; $icono = '&#9888;'; }
            else                   { $estado_a = 'Ausente';    $color = '#c62828'; $icono = '&#10007;'; }
            ?>
            <tr>
                <td><?= htmlspecialchars($a['fecha_clase'], ENT_QUOTES, 'UTF-8') ?></td>
                <td style="color:<?= $color ?>;font-weight:600">
                    <?= $icono ?> <?= $estado_a ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; /* $inscripcion */ ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
