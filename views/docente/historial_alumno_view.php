<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Historial de alumnos<?= $comision ? ' — ' . htmlspecialchars($comision['nombre_materia'], ENT_QUOTES, 'UTF-8') : '' ?></h1>
    <a href="<?= BASE_URL ?>/controllers/docente/comisiones_ctrl.php" class="btn btn-warn">← Volver</a>
</div>

<?php if (!$comision): ?>
    <div class="alert alert-err">No se encontró la comisión solicitada.</div>
<?php elseif (empty($inscriptos)): ?>
    <div class="alert alert-err">No hay alumnos inscriptos en esta comisión.</div>
<?php else: ?>

<?php foreach ($inscriptos as $ins): ?>
    <?php
    $id_insc   = (int) $ins['id_inscripcion'];
    $pct       = $porcentajes[$id_insc] ?? 0.0;
    $pct_color = $pct >= 75 ? '#2e7d32' : '#c62828';
    $estado    = $ins['estado'] ?? '';
    $badge_est = match($estado) {
        'activa'      => 'badge-act',
        'aprobada'    => 'badge-ok',
        'desaprobada' => 'badge-warn',
        'baja'        => 'badge-off',
        default       => ''
    };
    ?>
    <div class="card">
        <h2>
            <?= htmlspecialchars($ins['apellido'] . ', ' . $ins['nombre'], ENT_QUOTES, 'UTF-8') ?>
            <span style="font-weight:400;color:#78909c;font-size:.82rem;margin-left:8px">
                Legajo: <?= htmlspecialchars($ins['legajo'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <span class="badge <?= $badge_est ?>" style="margin-left:10px">
                <?= htmlspecialchars(ucfirst($estado), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </h2>

        <div style="display:flex;gap:32px;margin-bottom:14px;align-items:center">
            <div>
                <span style="font-size:.8rem;color:#78909c;font-weight:600">ASISTENCIA</span><br>
                <span style="font-size:1.5rem;font-weight:700;color:<?= $pct_color ?>">
                    <?= number_format($pct, 1) ?>%
                </span>
            </div>
            <?php if (!empty($notas[$id_insc])): ?>
            <?php
            $promedio_notas = array_sum(array_column($notas[$id_insc], 'calificacion'))
                            / count($notas[$id_insc]);
            ?>
            <div>
                <span style="font-size:.8rem;color:#78909c;font-weight:600">PROMEDIO NOTAS</span><br>
                <span style="font-size:1.5rem;font-weight:700;color:<?= $promedio_notas >= 4 ? '#2e7d32' : '#c62828' ?>">
                    <?= number_format($promedio_notas, 2) ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Notas -->
        <?php if (!empty($notas[$id_insc])): ?>
        <div class="tabla-wrap" style="box-shadow:none;border:1px solid #e8eaf6;margin-bottom:0">
            <table class="tabla" style="min-width:400px">
                <thead>
                    <tr>
                        <th>Tipo</th><th>Calificación</th><th>Fecha</th><th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($notas[$id_insc] as $nota): ?>
                    <tr>
                        <td><?= htmlspecialchars(str_replace('_', ' ', ucfirst($nota['tipo'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge <?= (float)$nota['calificacion'] >= 4 ? 'badge-ok' : 'badge-off' ?>">
                                <?= number_format((float)$nota['calificacion'], 2) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($nota['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($nota['observacion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#90a4ae;font-size:.85rem">Sin notas registradas.</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
