<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Inscripciones</h1>
</div>

<?php if ($mensaje): ?>
    <?php
    /* El SP devuelve strings como "OK: Alumno inscripto" o "ERROR: ..." */
    $badge_ok = !$es_error && !str_starts_with($mensaje, 'ERROR:');
    ?>
    <div class="alert <?= $badge_ok ? 'alert-ok' : 'alert-err' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- Formulario de inscripción -->
<div class="card">
    <h2>Inscribir Alumno a Comisión</h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php">
        <input type="hidden" name="action" value="inscribir">
        <div class="form-row">
            <div class="form-group">
                <label>Legajo del alumno *</label>
                <input type="text" name="legajo" required maxlength="20" placeholder="Ej: 2024001"
                    value="<?= htmlspecialchars($_GET['legajo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group" style="flex:3">
                <label>Comisión *</label>
                <select name="id_comision" required>
                    <option value="">— Seleccionar comisión —</option>
                    <?php foreach ($comisiones as $c): ?>
                        <option value="<?= (int) $c['id_comision'] ?>">
                            <?= htmlspecialchars(
                                $c['nombre_materia']
                                . ' — ' . $c['profesor']
                                . ' — ' . ucfirst($c['turno'])
                                . ' [' . (int)$c['inscriptos_activos'] . '/' . (int)$c['cupo_maximo'] . ']',
                                ENT_QUOTES, 'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Inscribir</button>
    </form>
</div>

<!-- Inscripciones recientes del ciclo activo -->
<h2 style="font-size:1.1rem;color:#1a237e;margin-bottom:14px;font-weight:700">
    Inscripciones recientes — ciclo activo
</h2>

<div class="tabla-wrap">
    <table class="tabla">
        <thead>
            <tr>
                <th>#</th><th>Legajo</th><th>Alumno</th><th>Materia</th>
                <th>Estado</th><th>Fecha</th><th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inscripciones_recientes as $i): ?>
            <tr>
                <td><?= (int) $i['id_inscripcion'] ?></td>
                <td><?= htmlspecialchars($i['id_alumno'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($i['apellido'] . ', ' . $i['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($i['materia'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php
                    $badge_class = match($i['estado']) {
                        'activa'      => 'badge-act',
                        'aprobada'    => 'badge-ok',
                        'baja'        => 'badge-off',
                        'desaprobada' => 'badge-warn',
                        default       => ''
                    };
                    ?>
                    <span class="badge <?= $badge_class ?>">
                        <?= htmlspecialchars(ucfirst($i['estado']), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($i['fecha_inscripcion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($i['estado'] === 'activa'): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Dar de baja la inscripción de <?= htmlspecialchars($i['apellido'] . ', ' . $i['nombre'], ENT_QUOTES, 'UTF-8') ?>?')">
                        <input type="hidden" name="action" value="baja">
                        <input type="hidden" name="id_inscripcion" value="<?= (int) $i['id_inscripcion'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Baja</button>
                    </form>
                    <?php else: ?>
                        <span style="color:#ccc;font-size:.75rem">—</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($inscripciones_recientes)): ?>
            <tr><td colspan="7" style="text-align:center;color:#90a4ae">Sin inscripciones en el ciclo activo.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
