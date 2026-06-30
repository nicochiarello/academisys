<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Docentes</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/profesores_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Docente</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($password_temporal ?? null): ?>
    <div class="alert alert-ok">
        Usuario generado para <strong><?= htmlspecialchars($email_docente_nuevo ?? '', ENT_QUOTES, 'UTF-8') ?></strong> —
        contraseña temporal: <code style="font-size:1rem"><?= htmlspecialchars($password_temporal, ENT_QUOTES, 'UTF-8') ?></code>
        <br><small>Comunicásela al docente. Deberá cambiarla en su primer ingreso. No se mostrará de nuevo.</small>
    </div>
<?php endif; ?>

<?php if ($editando || isset($_GET['nuevo'])): ?>
<div class="card">
    <h2><?= $editando ? 'Editar Docente' : 'Nuevo Docente' ?></h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/profesores_ctrl.php">
        <input type="hidden" name="action" value="<?= $editando ? 'editar' : 'crear' ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $editando['id_profesor'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>DNI *</label>
                <input type="text" name="dni" required maxlength="15"
                    value="<?= htmlspecialchars($editando['dni'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Apellido *</label>
                <input type="text" name="apellido" required maxlength="60"
                    value="<?= htmlspecialchars($editando['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required maxlength="60"
                    value="<?= htmlspecialchars($editando['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required maxlength="150"
                    value="<?= htmlspecialchars($editando['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" maxlength="30"
                    value="<?= htmlspecialchars($editando['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso"
                    value="<?= htmlspecialchars($editando['fecha_ingreso'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editando ? 'Guardar cambios' : 'Crear Docente' ?></button>
        <a href="<?= BASE_URL ?>/controllers/admin/profesores_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Apellido</th><th>Nombre</th><th>DNI</th>
                <th>Email</th><th>Teléfono</th><th>Ingreso</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($profesores as $p): ?>
            <tr>
                <td><?= (int) $p['id_profesor'] ?></td>
                <td><?= htmlspecialchars($p['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['dni'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['telefono'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['fecha_ingreso'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge <?= $p['activo'] ? 'badge-ok' : 'badge-off' ?>">
                        <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td style="white-space:nowrap">
                    <a href="<?= BASE_URL ?>/controllers/admin/profesores_ctrl.php?editar=<?= (int) $p['id_profesor'] ?>"
                       class="btn btn-info btn-sm">Editar</a>
                    <?php if ($p['activo']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/profesores_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Dar de baja a <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre'], ENT_QUOTES, 'UTF-8') ?>?')">
                        <input type="hidden" name="action" value="baja">
                        <input type="hidden" name="id" value="<?= (int) $p['id_profesor'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Baja</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($profesores)): ?>
            <tr><td colspan="9" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
