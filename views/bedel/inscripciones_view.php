<?php require_once __DIR__ . '/_style.php'; ?>
<?php
$comisiones          ??= [];
$alumnos_encontrados ??= [];
$inscripciones_recientes ??= [];
$q_alumno            ??= '';
$mensaje             ??= null;
$es_error            ??= false;
?>

<div class="sec-header">
    <h1>Inscripciones</h1>
</div>

<?php if ($mensaje): ?>
    <?php $badge_ok = !$es_error && !str_starts_with($mensaje, 'ERROR:'); ?>
    <div class="alert <?= $badge_ok ? 'alert-ok' : 'alert-err' ?>">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- Buscador de alumno -->
<div class="card">
    <h2>Buscar alumno</h2>
    <form method="GET" action="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php"
          style="display:flex;gap:8px;margin-bottom:<?= empty($alumnos_encontrados) && $q_alumno === '' ? '0' : '16px' ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($q_alumno, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Buscar por nombre, apellido, DNI o legajo…"
               style="flex:1;padding:9px 12px;border:1.5px solid #cfd8dc;border-radius:6px;font-size:.9rem"
               autofocus>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if ($q_alumno !== ''): ?>
            <a href="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php" class="btn btn-warn">Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if ($q_alumno !== '' && empty($alumnos_encontrados)): ?>
        <p style="color:#90a4ae;font-size:.88rem">No se encontraron alumnos activos para "<?= htmlspecialchars($q_alumno, ENT_QUOTES, 'UTF-8') ?>".</p>
    <?php elseif (!empty($alumnos_encontrados)): ?>
    <table class="tabla" style="margin-top:0">
        <thead>
            <tr>
                <th>Legajo</th><th>Apellido y nombre</th><th>DNI</th><th>Carrera</th><th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos_encontrados as $al): ?>
            <tr>
                <td><?= htmlspecialchars($al['legajo'],   ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($al['apellido'] . ', ' . $al['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($al['dni'],      ENT_QUOTES, 'UTF-8') ?></td>
                <td style="font-size:.8rem"><?= htmlspecialchars($al['nombre_carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm"
                            onclick="seleccionarAlumno(<?= json_encode($al['legajo']) ?>, <?= json_encode($al['apellido'] . ', ' . $al['nombre']) ?>)">
                        Seleccionar
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Formulario de inscripción -->
<div class="card">
    <h2>Inscribir alumno a comisión</h2>
    <form method="POST" action="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php">
        <input type="hidden" name="action" value="inscribir">
        <div id="alumno-seleccionado" style="display:none;background:#e3f2fd;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:.9rem;color:#0d47a1;font-weight:600"></div>
        <div class="form-row">
            <div class="form-group">
                <label>Legajo del alumno *</label>
                <input type="text" id="campo-legajo" name="legajo" required maxlength="20"
                       placeholder="Ej: A-0001 — o buscá arriba"
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
                    <?php $badge_class = match($i['estado']) {
                        'activa'      => 'badge-act',
                        'aprobada'    => 'badge-ok',
                        'baja'        => 'badge-off',
                        'desaprobada' => 'badge-warn',
                        default       => ''
                    }; ?>
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

<script>
function seleccionarAlumno(legajo, nombre) {
    document.getElementById('campo-legajo').value = legajo;
    var aviso = document.getElementById('alumno-seleccionado');
    aviso.textContent = 'Alumno seleccionado: ' + nombre + ' [' + legajo + ']';
    aviso.style.display = 'block';
    document.getElementById('campo-legajo').scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
