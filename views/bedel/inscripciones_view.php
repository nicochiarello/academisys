<?php require_once __DIR__ . '/_style.php'; ?>
<?php
$comisiones                ??= [];
$alumnos_encontrados       ??= [];
$inscripciones_por_carrera ??= [];
$q_alumno                  ??= '';
$mensaje                   ??= null;
$es_error                  ??= false;
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
                            data-legajo="<?= htmlspecialchars($al['legajo'], ENT_QUOTES, 'UTF-8') ?>"
                            data-nombre="<?= htmlspecialchars($al['apellido'] . ', ' . $al['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                            onclick="seleccionarAlumno(this.dataset.legajo, this.dataset.nombre)">
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

<!-- Inscripciones del ciclo activo agrupadas por carrera → materia -->
<h2 style="font-size:1.1rem;color:#1a237e;margin-bottom:16px;font-weight:700">
    Inscripciones — ciclo activo
</h2>

<?php if (empty($inscripciones_por_carrera)): ?>
    <p style="color:#90a4ae;text-align:center;padding:24px 0">Sin inscripciones en el ciclo activo.</p>
<?php else: ?>
<?php foreach ($inscripciones_por_carrera as $carrera => $materias): ?>

<div class="tabla-wrap" style="margin-bottom:28px">
    <!-- Cabecera de carrera -->
    <div style="background:#1a237e;color:#fff;padding:11px 16px;font-weight:700;font-size:.92rem;letter-spacing:.3px;display:flex;justify-content:space-between;align-items:center">
        <span><?= htmlspecialchars($carrera, ENT_QUOTES, 'UTF-8') ?></span>
        <span style="font-size:.78rem;font-weight:400;color:#9fa8da">
            <?= array_sum(array_map('count', $materias)) ?> inscriptos · <?= count($materias) ?> materia<?= count($materias) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- Una sola tabla por carrera, materias como filas separadoras -->
    <table class="tabla">
        <thead>
            <tr>
                <th>Legajo</th><th>Alumno</th><th>Estado</th><th>Fecha inscripción</th><th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($materias as $materia => $inscripciones): ?>
            <tr style="background:#e8eaf6">
                <td colspan="5" style="padding:7px 14px;font-size:.82rem;font-weight:700;color:#283593;border-bottom:1px solid #c5cae9;border-top:2px solid #c5cae9">
                    <?= htmlspecialchars($materia, ENT_QUOTES, 'UTF-8') ?>
                    <span style="font-weight:400;color:#7986cb;margin-left:8px">
                        — <?= count($inscripciones) ?> inscripto<?= count($inscripciones) !== 1 ? 's' : '' ?>
                    </span>
                </td>
            </tr>
            <?php foreach ($inscripciones as $i): ?>
            <tr>
                <td style="font-family:monospace;font-size:.82rem"><?= htmlspecialchars($i['id_alumno'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($i['apellido'] . ', ' . $i['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
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
                <td style="color:#78909c;font-size:.82rem"><?= htmlspecialchars($i['fecha_inscripcion'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($i['estado'] === 'activa'): ?>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/bedel/inscripciones_ctrl.php"
                          style="display:inline"
                          data-nombre="<?= htmlspecialchars($i['apellido'] . ', ' . $i['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                          onsubmit="return confirm('¿Dar de baja la inscripción de ' + this.dataset.nombre + '?')">
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
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endforeach; ?>
<?php endif; ?>

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
