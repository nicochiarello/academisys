<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Carreras</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php?nuevo=1" class="btn btn-primary">+ Nueva Carrera</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($editando || isset($_GET['nuevo'])): ?>
<div class="card">
    <h2><?= $editando ? 'Editar Carrera' : 'Nueva Carrera' ?></h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php">
        <input type="hidden" name="action" value="<?= $editando ? 'editar' : 'crear' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $editando['id_carrera'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required maxlength="100"
                    value="<?= htmlspecialchars($editando['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Duración (años) *</label>
                <input type="number" name="duracion_años" required min="1" max="10"
                    value="<?= htmlspecialchars((string)($editando['duracion_años'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Título que otorga</label>
                <input type="text" name="titulo_otorga" maxlength="100"
                    value="<?= htmlspecialchars($editando['titulo_otorga'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Modalidad *</label>
                <select name="modalidad" required>
                    <?php foreach (['presencial', 'semipresencial', 'virtual'] as $m): ?>
                        <option value="<?= $m ?>" <?= ($editando['modalidad'] ?? '') === $m ? 'selected' : '' ?>>
                            <?= ucfirst($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editando ? 'Guardar cambios' : 'Crear Carrera' ?></button>
        <a href="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Duración</th><th>Título que otorga</th>
                <th>Modalidad</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($carreras as $c): ?>
            <tr>
                <td><?= (int) $c['id_carrera'] ?></td>
                <td><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $c['duracion_años'] ?> años</td>
                <td><?= htmlspecialchars($c['titulo_otorga'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars(ucfirst($c['modalidad']), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge <?= $c['activa'] ? 'badge-ok' : 'badge-off' ?>">
                        <?= $c['activa'] ? 'Activa' : 'Inactiva' ?>
                    </span>
                </td>
                <td style="white-space:nowrap">
                    <a href="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php?editar=<?= (int) $c['id_carrera'] ?>"
                       class="btn btn-info btn-sm">Editar</a>
                    <?php if ($c['activa']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Dar de baja la carrera «<?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>»?')">
                        <input type="hidden" name="action" value="baja">
                        <input type="hidden" name="id" value="<?= (int) $c['id_carrera'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Baja</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/carreras_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Reactivar la carrera «<?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>»?')">
                        <input type="hidden" name="action" value="alta">
                        <input type="hidden" name="id" value="<?= (int) $c['id_carrera'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Alta</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($carreras)): ?>
            <tr><td colspan="7" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
