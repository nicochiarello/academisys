<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Planes de Estudio</h1>
    <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php?nuevo=1" class="btn btn-primary">+ Nuevo Plan</a>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['nuevo'])): ?>
<div class="card">
    <h2>Nuevo Plan de Estudio</h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php">
        <input type="hidden" name="action" value="crear">
        <div class="form-row">
            <div class="form-group">
                <label>Carrera *</label>
                <select name="id_carrera" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($carreras as $car): ?>
                        <option value="<?= (int) $car['id_carrera'] ?>">
                            <?= htmlspecialchars($car['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Año de vigencia *</label>
                <input type="number" name="año_vigencia" required min="2000" max="2100"
                    value="<?= date('Y') ?>">
            </div>
            <div class="form-group" style="flex:2">
                <label>Descripción</label>
                <input type="text" name="descripcion" maxlength="100">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Crear Plan</button>
        <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php" class="btn btn-warn" style="margin-left:8px">Cancelar</a>
    </form>
</div>
<?php endif; ?>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr><th>ID</th><th>Carrera</th><th>Año vigencia</th><th>Descripción</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($planes as $pl): ?>
            <tr <?= ($plan_detalle && $plan_detalle['id_plan'] == $pl['id_plan']) ? 'style="background:#e8eaf6"' : '' ?>>
                <td><?= (int) $pl['id_plan'] ?></td>
                <td><?= htmlspecialchars($pl['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $pl['año_vigencia'] ?></td>
                <td><?= htmlspecialchars($pl['descripcion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php?ver=<?= (int) $pl['id_plan'] ?>"
                       class="btn btn-info btn-sm">Ver materias</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($planes)): ?>
            <tr><td colspan="5" style="text-align:center;color:#90a4ae">Sin registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($plan_detalle): ?>
<div class="card">
    <h2>
        Materias del Plan: <?= htmlspecialchars($plan_detalle['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?>
        &mdash; <?= (int) $plan_detalle['año_vigencia'] ?>
        <?php if ($plan_detalle['descripcion']): ?>
            <small style="font-weight:400;color:#78909c">(<?= htmlspecialchars($plan_detalle['descripcion'], ENT_QUOTES, 'UTF-8') ?>)</small>
        <?php endif; ?>
    </h2>

    <?php if (empty($materias_plan)): ?>
        <p style="color:#90a4ae">Este plan aún no tiene materias asignadas.</p>
    <?php else: ?>
        <?php ksort($materias_plan); ?>
        <?php foreach ($materias_plan as $anio => $cuatrimestres): ?>
            <h3 style="color:#283593;font-size:.95rem;margin:18px 0 8px">
                <?= (int) $anio ?>° Año
            </h3>
            <?php ksort($cuatrimestres); ?>
            <?php foreach ($cuatrimestres as $cuat => $mats): ?>
                <p style="font-size:.8rem;color:#78909c;margin-bottom:6px">
                    <?= (int) $cuat ?>° Cuatrimestre
                </p>
                <table class="tabla" style="margin-bottom:14px">
                    <thead>
                        <tr><th>Código</th><th>Nombre</th><th>Carga horaria</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mats as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $m['carga_horaria'] ?> hs</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php" class="btn btn-warn btn-sm">Cerrar detalle</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
