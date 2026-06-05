<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Calificaciones</h1>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- Selector de comisión -->
<div class="card">
    <h2>Seleccionar Comisión</h2>
    <form method="GET" action="<?= BASE_URL ?>/controllers/docente/calificaciones_ctrl.php">
        <div class="form-row">
            <div class="form-group" style="flex:3">
                <label>Comisión</label>
                <select name="id_comision" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($comisiones as $c): ?>
                        <?php
                        /* getByProfesor() devuelve aula/anio/cuatrimestre;
                           getActivas() devuelve nombre_materia/turno/anio/cuatrimestre/profesor */
                        $label = $c['nombre_materia']
                               . ' — ' . ucfirst($c['turno'])
                               . ' (' . (int)$c['anio'] . '/' . (int)$c['cuatrimestre'] . '° cuat.)';
                        if (!empty($c['profesor'])) {
                            $label .= ' — ' . $c['profesor'];
                        }
                        ?>
                        <option value="<?= (int)$c['id_comision'] ?>"
                            <?= $id_comision === (int)$c['id_comision'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Cargar Alumnos</button>
    </form>
</div>

<?php if ($id_comision > 0): ?>

<?php if (empty($inscriptos)): ?>
    <div class="alert alert-err" style="margin-top:4px">
        No hay alumnos con inscripción activa o aprobada en esta comisión.
    </div>
<?php else: ?>

<?php foreach ($inscriptos as $ins): ?>
    <?php $id_insc = (int) $ins['id_inscripcion']; ?>
    <div class="card">
        <h2>
            <?= htmlspecialchars($ins['apellido'] . ', ' . $ins['nombre'], ENT_QUOTES, 'UTF-8') ?>
            <span style="font-weight:400;color:#78909c;font-size:.85rem;margin-left:8px">
                Legajo: <?= htmlspecialchars($ins['legajo'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </h2>

        <!-- Notas existentes -->
        <?php if (!empty($notas[$id_insc])): ?>
        <div class="tabla-wrap" style="margin-bottom:16px;box-shadow:none;border:1px solid #e8eaf6">
            <table class="tabla" style="min-width:400px">
                <thead>
                    <tr>
                        <th>Tipo</th><th>Calificación</th><th>Fecha</th><th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($notas[$id_insc] as $nota): ?>
                    <tr>
                        <td><?= htmlspecialchars(str_replace('_', ' ', ucfirst($nota['tipo'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge <?= (float)$nota['calificacion'] >= 4 ? 'badge-ok' : 'badge-off' ?>">
                                <?= number_format((float)$nota['calificacion'], 2) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($nota['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($nota['observacion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#90a4ae;font-size:.85rem;margin-bottom:14px">Sin notas registradas.</p>
        <?php endif; ?>

        <!-- Formulario para agregar/actualizar nota -->
        <form method="POST" action="<?= BASE_URL ?>/controllers/docente/calificaciones_ctrl.php">
            <input type="hidden" name="action" value="guardar_nota">
            <input type="hidden" name="id_inscripcion" value="<?= $id_insc ?>">
            <input type="hidden" name="id_comision" value="<?= $id_comision ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de evaluación *</label>
                    <select name="tipo" required>
                        <option value="parcial_1">Parcial 1</option>
                        <option value="parcial_2">Parcial 2</option>
                        <option value="recuperatorio">Recuperatorio</option>
                        <option value="final">Final</option>
                        <option value="coloquio">Coloquio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Calificación * (0–10)</label>
                    <input type="number" name="calificacion" required
                           min="0" max="10" step="0.01" placeholder="Ej: 7.50">
                </div>
                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group" style="flex:2">
                    <label>Observación</label>
                    <input type="text" name="observacion" maxlength="255" placeholder="Opcional">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Guardar Nota</button>
        </form>
    </div>
<?php endforeach; ?>

<?php endif; /* $inscriptos */ ?>
<?php endif; /* $id_comision > 0 */ ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
