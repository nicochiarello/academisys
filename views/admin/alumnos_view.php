<?php
require_once __DIR__ . '/_style.php';
$mensaje  ??= null;
$es_error ??= false;
$alumnos  ??= [];
$carreras ??= [];
$planes   ??= [];
$editando ??= null;
$password_temporal ??= null;
$email_alumno_nuevo ??= null;
?>

<div class="sec-header">
    <h1>Alumnos</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Alumno</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($password_temporal): ?>
    <div class="alert alert-ok">
        Usuario generado para <strong><?= htmlspecialchars($email_alumno_nuevo ?? '', ENT_QUOTES, 'UTF-8') ?></strong> —
        contraseña temporal: <code style="font-size:1rem"><?= htmlspecialchars($password_temporal, ENT_QUOTES, 'UTF-8') ?></code>
        <br><small>Comunicásela al alumno. Deberá cambiarla en su primer ingreso. No se mostrará de nuevo.</small>
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
        <?php foreach ($alumnos as $idx => $a): ?>
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
                    <button type="button" class="btn btn-success btn-sm"
                            onclick="toggleTitulo(<?= $idx ?>)"
                            style="margin-left:2px">🎓 Título</button>
                </td>
            </tr>
            <!-- Fila colapsable para otorgar título -->
            <tr id="titulo-row-<?= $idx ?>" style="display:none;background:#f9fbe7">
                <td colspan="8" style="padding:14px 20px">
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/alumnos_ctrl.php"
                          style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                        <input type="hidden" name="action" value="otorgar_titulo">
                        <input type="hidden" name="legajo" value="<?= htmlspecialchars($a['legajo'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-group" style="margin-bottom:0;min-width:280px">
                            <label style="color:#33691e">Plan de estudio *</label>
                            <select name="id_plan" required>
                                <option value="">— Seleccionar plan —</option>
                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= (int) $plan['id_plan'] ?>">
                                        <?= htmlspecialchars(
                                            $plan['nombre_carrera'] . ' — ' . (int)$plan['año_vigencia'],
                                            ENT_QUOTES, 'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center">
                            <button type="submit" class="btn btn-success btn-sm"
                                    onclick="return confirm('¿Otorgar título a <?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre'], ENT_QUOTES, 'UTF-8') ?>?')">
                                Otorgar Título
                            </button>
                            <button type="button" class="btn btn-warn btn-sm"
                                    onclick="toggleTitulo(<?= $idx ?>)">Cancelar</button>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($alumnos)): ?>
            <tr><td colspan="8" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleTitulo(idx) {
    var row = document.getElementById('titulo-row-' + idx);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
