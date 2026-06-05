<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Aulas</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/aulas_ctrl.php?nuevo=1" class="btn btn-primary">+ Nueva Aula</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($editando || isset($_GET['nuevo'])): ?>
<div class="card">
    <h2><?= $editando ? 'Editar Aula' : 'Nueva Aula' ?></h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/aulas_ctrl.php">
        <input type="hidden" name="action" value="<?= $editando ? 'editar' : 'crear' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $editando['id_aula'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Número / Nombre *</label>
                <input type="text" name="numero" required maxlength="10"
                    value="<?= htmlspecialchars($editando['numero'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Edificio</label>
                <input type="text" name="edificio" maxlength="50"
                    value="<?= htmlspecialchars($editando['edificio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Capacidad</label>
                <input type="number" name="capacidad" min="1" max="500"
                    value="<?= htmlspecialchars((string)($editando['capacidad'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Equipamiento</label>
                <input type="text" name="equipamiento" maxlength="100"
                    value="<?= htmlspecialchars($editando['equipamiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editando ? 'Guardar cambios' : 'Crear Aula' ?></button>
        <a href="<?= BASE_URL ?>/controllers/admin/aulas_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr><th>ID</th><th>Número</th><th>Edificio</th><th>Capacidad</th><th>Equipamiento</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($aulas as $a): ?>
            <tr>
                <td><?= (int) $a['id_aula'] ?></td>
                <td><?= htmlspecialchars($a['numero'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['edificio'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $a['capacidad'] !== null ? (int) $a['capacidad'] . ' alumnos' : '—' ?></td>
                <td><?= htmlspecialchars($a['equipamiento'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/controllers/admin/aulas_ctrl.php?editar=<?= (int) $a['id_aula'] ?>"
                       class="btn btn-info btn-sm">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($aulas)): ?>
            <tr><td colspan="6" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
