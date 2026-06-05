<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Comisiones — Ciclo Activo</h1>
    <a href="<?= BASE_URL ?>/controllers/bedel/comisiones_ctrl.php?nuevo=1" class="btn btn-primary">+ Nueva Comisión</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['nuevo'])): ?>
<div class="card">
    <h2>Nueva Comisión</h2>

    <?php if (!$ciclo_activo): ?>
        <div class="alert alert-err">
            No hay un ciclo académico activo. Activá uno en <strong>Ciclos</strong> antes de crear comisiones.
        </div>
    <?php else: ?>
    <form method="POST" action="<?= BASE_URL ?>/controllers/bedel/comisiones_ctrl.php">
        <input type="hidden" name="action" value="crear">
        <input type="hidden" name="id_ciclo" value="<?= (int) $ciclo_activo['id_ciclo'] ?>">

        <div class="form-row">
            <div class="form-group" style="flex:2">
                <label>Materia *</label>
                <select name="id_materia" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= (int) $m['id_materia'] ?>">
                            [<?= htmlspecialchars($m['codigo'], ENT_QUOTES, 'UTF-8') ?>]
                            <?= htmlspecialchars($m['nombre'] . ' — ' . $m['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex:2">
                <label>Docente *</label>
                <select name="id_profesor" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($profesores as $p): ?>
                        <option value="<?= (int) $p['id_profesor'] ?>">
                            <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Aula *</label>
                <select name="id_aula" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($aulas as $a): ?>
                        <option value="<?= (int) $a['id_aula'] ?>">
                            <?= htmlspecialchars($a['numero'] . ($a['edificio'] ? ' — ' . $a['edificio'] : ''), ENT_QUOTES, 'UTF-8') ?>
                            <?= $a['capacidad'] ? '(' . (int)$a['capacidad'] . 'p)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Turno *</label>
                <select name="turno" required>
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="noche">Noche</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cupo máximo *</label>
                <input type="number" name="cupo_maximo" required min="1" max="500" value="30">
            </div>
            <div class="form-group">
                <label>Ciclo (activo)</label>
                <input type="text" readonly
                    value="<?= (int)$ciclo_activo['anio'] . ' — ' . (int)$ciclo_activo['cuatrimestre'] . '° cuatrimestre' ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Crear Comisión</button>
        <a href="<?= BASE_URL ?>/controllers/bedel/comisiones_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Materia</th><th>Docente</th><th>Aula</th>
                <th>Edificio</th><th>Turno</th><th>Inscriptos / Cupo</th><th>Ciclo</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comisiones as $c): ?>
            <?php $llena = (int)$c['inscriptos_activos'] >= (int)$c['cupo_maximo']; ?>
            <tr <?= $llena ? 'style="background:#ffebee"' : '' ?>>
                <td><?= (int) $c['id_comision'] ?></td>
                <td><?= htmlspecialchars($c['nombre_materia'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['profesor'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['aula'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['edificio'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars(ucfirst($c['turno']), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge <?= $llena ? 'badge-off' : 'badge-ok' ?>">
                        <?= (int)$c['inscriptos_activos'] ?> / <?= (int)$c['cupo_maximo'] ?>
                    </span>
                </td>
                <td><?= (int)$c['anio'] ?> — <?= (int)$c['cuatrimestre'] ?>° cuat.</td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($comisiones)): ?>
            <tr><td colspan="8" style="text-align:center;color:#90a4ae">No hay comisiones en el ciclo activo.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
