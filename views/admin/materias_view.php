<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Materias</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php?nuevo=1" class="btn btn-primary">+ Nueva Materia</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($editando || isset($_GET['nuevo'])): ?>
<div class="card">
    <h2><?= $editando ? 'Editar Materia' : 'Nueva Materia' ?></h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php">
        <input type="hidden" name="action" value="<?= $editando ? 'editar' : 'crear' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $editando['id_materia'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Plan de Estudio *</label>
                <select name="id_plan" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($planes as $pl): ?>
                        <option value="<?= (int) $pl['id_plan'] ?>"
                            <?= ($editando['id_plan'] ?? '') == $pl['id_plan'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pl['nombre_carrera'] . ' — ' . $pl['año_vigencia'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Código *</label>
                <input type="text" name="codigo" required maxlength="20"
                    value="<?= htmlspecialchars($editando['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required maxlength="100"
                    value="<?= htmlspecialchars($editando['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Año de cursada *</label>
                <input type="number" name="año_cursada" required min="1" max="10"
                    value="<?= htmlspecialchars((string)($editando['año_cursada'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Cuatrimestre *</label>
                <select name="cuatrimestre" required>
                    <option value="1" <?= ($editando['cuatrimestre'] ?? '') == 1 ? 'selected' : '' ?>>1° Cuatrimestre</option>
                    <option value="2" <?= ($editando['cuatrimestre'] ?? '') == 2 ? 'selected' : '' ?>>2° Cuatrimestre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Carga horaria (hs) *</label>
                <input type="number" name="carga_horaria" required min="1"
                    value="<?= htmlspecialchars((string)($editando['carga_horaria'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editando ? 'Guardar cambios' : 'Crear Materia' ?></button>
        <a href="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<?php if ($correlativas_materia): ?>
<div class="panel">
    <h3>Correlativas de: <?= htmlspecialchars($correlativas_materia['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php">
        <input type="hidden" name="action" value="set_correlativas">
        <input type="hidden" name="id_materia" value="<?= (int) $correlativas_materia['id_materia'] ?>">
        <p style="font-size:.82rem;color:#546e7a;margin-bottom:12px">
            Seleccioná las materias que el alumno debe tener aprobadas antes de cursar ésta:
        </p>
        <div class="checkgrid">
        <?php foreach ($materias_plan as $m): ?>
            <?php if ($m['id_materia'] === $correlativas_materia['id_materia']) continue; ?>
            <label>
                <input type="checkbox" name="correlativas[]"
                    value="<?= (int) $m['id_materia'] ?>"
                    <?= $m['es_correlativa'] ? 'checked' : '' ?>>
                <?= htmlspecialchars('[' . $m['codigo'] . '] ' . $m['nombre'], ENT_QUOTES, 'UTF-8') ?>
                <span style="color:#90a4ae;font-size:.75rem">(<?= (int)$m['año_cursada'] ?>° — <?= (int)$m['cuatrimestre'] ?>° cuat.)</span>
            </label>
        <?php endforeach; ?>
        </div>
        <br>
        <button type="submit" class="btn btn-primary btn-sm">Guardar correlativas</button>
        <a href="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php" class="btn btn-warn btn-sm" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Código</th><th>Nombre</th><th>Carrera / Plan</th>
                <th>Año</th><th>Cuat.</th><th>Hs.</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($materias as $m): ?>
            <tr>
                <td><?= (int) $m['id_materia'] ?></td>
                <td><?= htmlspecialchars($m['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td style="font-size:.8rem">
                    <?= htmlspecialchars($m['nombre_carrera'] . ' ' . $m['plan_anio'], ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td><?= (int) $m['año_cursada'] ?>°</td>
                <td><?= (int) $m['cuatrimestre'] ?>°</td>
                <td><?= (int) $m['carga_horaria'] ?> hs</td>
                <td style="white-space:nowrap">
                    <a href="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php?editar=<?= (int) $m['id_materia'] ?>"
                       class="btn btn-info btn-sm">Editar</a>
                    <a href="<?= BASE_URL ?>/controllers/admin/materias_ctrl.php?correlativas=<?= (int) $m['id_materia'] ?>"
                       class="btn btn-warn btn-sm">Correlativas</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($materias)): ?>
            <tr><td colspan="8" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
