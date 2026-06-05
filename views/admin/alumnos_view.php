<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Alumnos</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Alumno</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($editando || isset($_GET['nuevo'])): ?>
<div class="card">
    <h2><?= $editando ? 'Editar Alumno' : 'Nuevo Alumno' ?></h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php">
        <input type="hidden" name="action" value="<?= $editando ? 'editar' : 'crear' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Legajo *</label>
                <input type="text" name="legajo" required maxlength="20"
                    <?= $editando ? 'readonly' : '' ?>
                    value="<?= htmlspecialchars($editando['legajo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
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
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento"
                    value="<?= htmlspecialchars($editando['fecha_nacimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso"
                    value="<?= htmlspecialchars($editando['fecha_ingreso'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Carrera *</label>
                <select name="id_carrera" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($carreras as $car): ?>
                        <option value="<?= (int) $car['id_carrera'] ?>"
                            <?= ($editando['id_carrera'] ?? '') == $car['id_carrera'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($car['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editando ? 'Guardar cambios' : 'Crear Alumno' ?></button>
        <a href="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>Legajo</th><th>Apellido</th><th>Nombre</th><th>DNI</th>
                <th>Email</th><th>Carrera</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['legajo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['dni'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($a['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge <?= $a['activo'] ? 'badge-ok' : 'badge-off' ?>">
                        <?= $a['activo'] ? 'Activo' : 'Baja' ?>
                    </span>
                </td>
                <td style="white-space:nowrap">
                    <a href="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php?editar=<?= urlencode($a['legajo']) ?>"
                       class="btn btn-info btn-sm">Editar</a>
                    <?php if ($a['activo']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Dar de baja al alumno <?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre'], ENT_QUOTES, 'UTF-8') ?>?')">
                        <input type="hidden" name="action" value="baja">
                        <input type="hidden" name="legajo" value="<?= htmlspecialchars($a['legajo'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Baja</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($alumnos)): ?>
            <tr><td colspan="8" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
