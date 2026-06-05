<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Ciclos Académicos</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/ciclos_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Ciclo</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['nuevo'])): ?>
<div class="card">
    <h2>Nuevo Ciclo Académico</h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/ciclos_ctrl.php">
        <input type="hidden" name="action" value="crear">
        <div class="form-row">
            <div class="form-group">
                <label>Año *</label>
                <input type="number" name="anio" required min="2000" max="2100"
                    value="<?= date('Y') ?>">
            </div>
            <div class="form-group">
                <label>Cuatrimestre *</label>
                <select name="cuatrimestre" required>
                    <option value="1">1° Cuatrimestre</option>
                    <option value="2">2° Cuatrimestre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio">
            </div>
            <div class="form-group">
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Crear Ciclo</button>
        <a href="<?= BASE_URL ?>/controllers/admin/ciclos_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th><th>Año</th><th>Cuatrimestre</th>
                <th>Fecha inicio</th><th>Fecha fin</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ciclos as $c): ?>
            <tr>
                <td><?= (int) $c['id_ciclo'] ?></td>
                <td><?= (int) $c['anio'] ?></td>
                <td><?= (int) $c['cuatrimestre'] ?>° Cuatrimestre</td>
                <td><?= htmlspecialchars($c['fecha_inicio'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['fecha_fin']   ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($c['activo']): ?>
                        <span class="badge badge-act">ACTIVO</span>
                    <?php else: ?>
                        <span class="badge badge-off">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$c['activo']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/ciclos_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Activar el ciclo <?= (int)$c['anio'] ?> - <?= (int)$c['cuatrimestre'] ?>° cuatrimestre? Se desactivarán los demás.')">
                        <input type="hidden" name="action" value="activar">
                        <input type="hidden" name="id" value="<?= (int) $c['id_ciclo'] ?>">
                        <button type="submit" class="btn btn-success btn-sm">Activar</button>
                    </form>
                    <?php else: ?>
                        <span style="color:#78909c;font-size:.78rem">Ciclo en curso</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($ciclos)): ?>
            <tr><td colspan="7" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
