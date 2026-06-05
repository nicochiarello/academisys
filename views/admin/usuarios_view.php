<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Usuarios del Sistema</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Usuario</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['nuevo'])): ?>
<div class="card">
    <h2>Nuevo Usuario</h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php">
        <input type="hidden" name="action" value="crear">
        <div class="form-row">
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required maxlength="150">
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Rol *</label>
                <select name="id_rol" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['id_rol'] ?>">
                            <?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Vincular alumno (opcional)</label>
                <select name="legajo_alumno">
                    <option value="">— Ninguno —</option>
                    <?php foreach ($alumnos as $al): ?>
                        <option value="<?= htmlspecialchars($al['legajo'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($al['apellido'] . ', ' . $al['nombre'] . ' [' . $al['legajo'] . ']', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Vincular docente (opcional)</label>
                <select name="id_profesor">
                    <option value="">— Ninguno —</option>
                    <?php foreach ($profesores as $pr): ?>
                        <option value="<?= (int) $pr['id_profesor'] ?>">
                            <?= htmlspecialchars($pr['apellido'] . ', ' . $pr['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
        <a href="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Email</th><th>Rol</th>
                <th>Vinculado a</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= (int) $u['id_usuario'] ?></td>
                <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['nombre_rol'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($u['legajo_alumno'] && $u['nombre_alumno']): ?>
                        <span style="font-size:.8rem">Alumno: <?= htmlspecialchars($u['nombre_alumno'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php elseif ($u['id_profesor'] && $u['nombre_profesor']): ?>
                        <span style="font-size:.8rem">Docente: <?= htmlspecialchars($u['nombre_profesor'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php else: ?>
                        <span style="color:#90a4ae">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?= $u['activo'] ? 'badge-ok' : 'badge-off' ?>">
                        <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td style="white-space:nowrap">
                    <?php if ($u['activo']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Desactivar usuario <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>?')">
                        <input type="hidden" name="action" value="desactivar">
                        <input type="hidden" name="id" value="<?= (int) $u['id_usuario'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Desactivar</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php"
                          style="display:inline">
                        <input type="hidden" name="action" value="activar">
                        <input type="hidden" name="id" value="<?= (int) $u['id_usuario'] ?>">
                        <button type="submit" class="btn btn-success btn-sm">Activar</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($usuarios)): ?>
            <tr><td colspan="6" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
