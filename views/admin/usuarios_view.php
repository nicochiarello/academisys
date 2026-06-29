<?php require_once __DIR__ . '/_style.php'; ?>
<?php
$q       ??= '';
$pagina  ??= 1;
$paginas ??= 1;
$total   ??= 0;

function urlPaginaUsuarios(int $p, string $q): string {
    $params = ['page' => $p];
    if ($q !== '') $params['q'] = $q;
    return BASE_URL . '/controllers/admin/usuarios_ctrl.php?' . http_build_query($params);
}
?>

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

<!-- Barra de búsqueda -->
<form method="GET" action="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php"
      style="display:flex;gap:8px;margin-bottom:16px">
    <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
           placeholder="Buscar por nombre, DNI o legajo…"
           style="flex:1;padding:9px 12px;border:1.5px solid #cfd8dc;border-radius:6px;font-size:.9rem">
    <button type="submit" class="btn btn-primary">Buscar</button>
    <?php if ($q !== ''): ?>
        <a href="<?= BASE_URL ?>/controllers/admin/usuarios_ctrl.php" class="btn btn-warn">Limpiar</a>
    <?php endif; ?>
</form>

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

<!-- Paginación -->
<?php if ($paginas > 1): ?>
<div style="display:flex;align-items:center;gap:6px;margin-top:16px;flex-wrap:wrap">
    <?php if ($pagina > 1): ?>
        <a href="<?= urlPaginaUsuarios($pagina - 1, $q) ?>" class="btn btn-primary btn-sm">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $paginas; $p++): ?>
        <?php if ($p === $pagina): ?>
            <span style="padding:4px 10px;background:#1a237e;color:#fff;border-radius:5px;font-size:.82rem;font-weight:700"><?= $p ?></span>
        <?php elseif (abs($p - $pagina) <= 2 || $p === 1 || $p === $paginas): ?>
            <a href="<?= urlPaginaUsuarios($p, $q) ?>" class="btn btn-primary btn-sm" style="background:#e8eaf6;color:#1a237e"><?= $p ?></a>
        <?php elseif (abs($p - $pagina) === 3): ?>
            <span style="color:#90a4ae;font-size:.82rem">…</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($pagina < $paginas): ?>
        <a href="<?= urlPaginaUsuarios($pagina + 1, $q) ?>" class="btn btn-primary btn-sm">Siguiente &raquo;</a>
    <?php endif; ?>

    <span style="margin-left:8px;font-size:.8rem;color:#78909c">
        <?= $total ?> usuario<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?>
    </span>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
