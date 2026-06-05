<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mis Comisiones</h1>
</div>

<?php if (empty($comisiones)): ?>
    <div class="alert alert-err">
        No tenés comisiones asignadas en el ciclo académico activo.
    </div>
<?php else: ?>
<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>Materia</th><th>Aula</th><th>Turno</th>
                <th>Ciclo</th><th>Inscriptos / Cupo</th><th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comisiones as $c): ?>
            <?php $llena = (int)$c['inscriptos_activos'] >= (int)$c['cupo_maximo']; ?>
            <tr>
                <td><?= htmlspecialchars($c['nombre_materia'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?= htmlspecialchars($c['aula'], ENT_QUOTES, 'UTF-8') ?>
                    <?php if (!empty($c['edificio'])): ?>
                        <span style="color:#78909c;font-size:.8rem">— <?= htmlspecialchars($c['edificio'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars(ucfirst($c['turno']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int)$c['anio'] ?> — <?= (int)$c['cuatrimestre'] ?>° cuat.</td>
                <td>
                    <span class="badge <?= $llena ? 'badge-off' : 'badge-ok' ?>">
                        <?= (int)$c['inscriptos_activos'] ?> / <?= (int)$c['cupo_maximo'] ?>
                    </span>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/controllers/docente/historial_alumno_ctrl.php?id_comision=<?= (int)$c['id_comision'] ?>"
                       class="btn btn-info btn-sm">Ver alumnos</a>
                    <a href="<?= BASE_URL ?>/controllers/docente/calificaciones_ctrl.php?id_comision=<?= (int)$c['id_comision'] ?>"
                       class="btn btn-primary btn-sm" style="margin-left:4px">Calificaciones</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
