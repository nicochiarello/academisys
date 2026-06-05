<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Registro de Asistencias</h1>
</div>

<?php if ($mensaje): ?>
    <div class="alert <?= $es_error ? 'alert-err' : 'alert-ok' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- Formulario selector (GET) -->
<div class="card">
    <h2>Seleccionar Comisión y Fecha</h2>
    <form method="GET" action="<?= BASE_URL ?>/controllers/bedel/asistencias_ctrl.php">
        <div class="form-row">
            <div class="form-group" style="flex:3">
                <label>Comisión</label>
                <select name="id_comision" required>
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($comisiones as $c): ?>
                        <?php
                        $label = $c['nombre_materia']
                               . ' — ' . ucfirst($c['turno'])
                               . ' (' . (int)$c['anio'] . '/' . (int)$c['cuatrimestre'] . '° cuat.)';
                        /* getActivas() incluye 'profesor'; getByProfesor() no */
                        if (!empty($c['profesor'])) {
                            $label .= ' — ' . $c['profesor'];
                        }
                        ?>
                        <option value="<?= (int) $c['id_comision'] ?>"
                            <?= $id_comision === (int)$c['id_comision'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha de clase</label>
                <input type="date" name="fecha" value="<?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Cargar Asistencias</button>
    </form>
</div>

<?php if ($id_comision > 0): ?>

<?php if (empty($alumnos)): ?>
    <div class="alert alert-err" style="margin-top:4px">
        No hay alumnos con inscripción activa en esta comisión para la fecha seleccionada.
    </div>
<?php else: ?>

<!-- Formulario de asistencias (POST) -->
<form method="POST" action="<?= BASE_URL ?>/controllers/bedel/asistencias_ctrl.php">
    <input type="hidden" name="action" value="guardar_asistencias">
    <input type="hidden" name="id_comision" value="<?= $id_comision ?>">
    <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>">

    <div class="tabla-wrap">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Legajo</th><th>Apellido</th><th>Nombre</th>
                    <th>Asistencia</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($alumnos as $al): ?>
                <?php
                /* Determinar valor actual según DB (COALESCE devuelve 0 por defecto) */
                if ($al['presente'])    $default = 'presente';
                elseif ($al['justificada']) $default = 'justificada';
                else                    $default = 'ausente';
                $id_insc = (int) $al['id_inscripcion'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($al['legajo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($al['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($al['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <label style="margin-right:16px;font-weight:400;font-size:.85rem;cursor:pointer">
                            <input type="radio" name="asistencia[<?= $id_insc ?>]"
                                   value="presente" <?= $default === 'presente' ? 'checked' : '' ?>>
                            <span style="color:#2e7d32">&#10003; Presente</span>
                        </label>
                        <label style="margin-right:16px;font-weight:400;font-size:.85rem;cursor:pointer">
                            <input type="radio" name="asistencia[<?= $id_insc ?>]"
                                   value="ausente" <?= $default === 'ausente' ? 'checked' : '' ?>>
                            <span style="color:#c62828">&#10007; Ausente</span>
                        </label>
                        <label style="font-weight:400;font-size:.85rem;cursor:pointer">
                            <input type="radio" name="asistencia[<?= $id_insc ?>]"
                                   value="justificada" <?= $default === 'justificada' ? 'checked' : '' ?>>
                            <span style="color:#f57c00">&#9888; Justificada</span>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:4px;margin-bottom:24px">
        <button type="submit" class="btn btn-primary">Guardar Asistencias</button>
        <span style="font-size:.8rem;color:#78909c;margin-left:12px">
            <?= count($alumnos) ?> alumno<?= count($alumnos) !== 1 ? 's' : '' ?> —
            Fecha: <?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</form>

<?php endif; /* $alumnos */ ?>
<?php endif; /* $id_comision > 0 */ ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
