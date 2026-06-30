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
                <td style="white-space:nowrap">
                    <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php?ver=<?= (int) $pl['id_plan'] ?>"
                       class="btn btn-info btn-sm">Ver materias</a>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php"
                          style="display:inline"
                          onsubmit="return confirm('¿Eliminar el plan «<?= htmlspecialchars($pl['nombre_carrera'] . ' ' . $pl['año_vigencia'], ENT_QUOTES, 'UTF-8') ?>»? Esta acción no se puede deshacer.')">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id_plan" value="<?= (int) $pl['id_plan'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
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
                        <tr><th>Código</th><th>Nombre</th><th>Carga horaria</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mats as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $m['carga_horaria'] ?> hs</td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar la materia «<?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?>»?')">
                                    <input type="hidden" name="action" value="eliminar_materia">
                                    <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">
                                    <input type="hidden" name="id_plan" value="<?= (int) $plan_detalle['id_plan'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Formulario para agregar materia directamente al plan -->
    <div style="margin-top:24px;border-top:1px solid #e0e0e0;padding-top:20px">
        <h3 style="font-size:.95rem;color:#283593;margin-bottom:12px">+ Agregar materia a este plan</h3>

        <!-- Buscador con autocomplete -->
        <div style="position:relative;max-width:420px;margin-bottom:16px">
            <label style="font-size:.82rem;color:#546e7a;display:block;margin-bottom:4px">
                Buscar materia existente (por nombre o código)
            </label>
            <input type="text" id="mat-buscar" autocomplete="off" placeholder="Ej: Matemática, MAT01..."
                   style="width:100%;box-sizing:border-box">
            <div id="mat-sugerencias" style="display:none;position:absolute;z-index:100;width:100%;
                 background:#fff;border:1px solid #cfd8dc;border-top:none;border-radius:0 0 4px 4px;
                 max-height:220px;overflow-y:auto;box-shadow:0 4px 8px rgba(0,0,0,.1)"></div>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php" id="form-agregar-mat">
            <input type="hidden" name="action" value="crear_materia">
            <input type="hidden" name="id_plan" value="<?= (int) $plan_detalle['id_plan'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Código *</label>
                    <input type="text" id="mat-codigo" name="codigo" required maxlength="20">
                </div>
                <div class="form-group" style="flex:2">
                    <label>Nombre *</label>
                    <input type="text" id="mat-nombre" name="nombre" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Año de cursada *</label>
                    <input type="number" id="mat-anio" name="año_cursada" required min="1" max="10">
                </div>
                <div class="form-group">
                    <label>Cuatrimestre *</label>
                    <select id="mat-cuatrimestre" name="cuatrimestre" required>
                        <option value="1">1°</option>
                        <option value="2">2°</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Carga horaria (hs) *</label>
                    <input type="number" id="mat-carga" name="carga_horaria" required min="1">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Agregar materia</button>
            <button type="button" class="btn btn-warn btn-sm" style="margin-left:8px"
                    onclick="document.getElementById('form-agregar-mat').reset();document.getElementById('mat-buscar').value=''">
                Limpiar
            </button>
        </form>
    </div>

    <script>
    (function () {
        const catalogo = <?= $catalogo_json ?>;
        const buscar   = document.getElementById('mat-buscar');
        const lista    = document.getElementById('mat-sugerencias');

        function llenarFormulario(m) {
            document.getElementById('mat-codigo').value       = m.codigo;
            document.getElementById('mat-nombre').value       = m.nombre;
            document.getElementById('mat-anio').value         = m.año_cursada;
            document.getElementById('mat-cuatrimestre').value = m.cuatrimestre;
            document.getElementById('mat-carga').value        = m.carga_horaria;
            buscar.value = m.codigo + ' — ' + m.nombre;
            lista.style.display = 'none';
        }

        buscar.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            lista.innerHTML = '';
            if (q.length < 2) { lista.style.display = 'none'; return; }

            const coincidencias = catalogo.filter(m =>
                m.nombre.toLowerCase().includes(q) || m.codigo.toLowerCase().includes(q)
            ).slice(0, 12);

            if (!coincidencias.length) { lista.style.display = 'none'; return; }

            coincidencias.forEach(m => {
                const item = document.createElement('div');
                item.style.cssText = 'padding:8px 12px;cursor:pointer;font-size:.85rem;border-bottom:1px solid #f0f0f0';
                item.innerHTML = '<strong>' + m.codigo + '</strong> — ' + m.nombre +
                    ' <span style="color:#90a4ae;font-size:.75rem">(' + m.año_cursada + '° año, ' + m.cuatrimestre + '° cuat., ' + m.carga_horaria + ' hs)</span>';
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    llenarFormulario(m);
                });
                item.addEventListener('mouseover', function () { this.style.background = '#e8eaf6'; });
                item.addEventListener('mouseout',  function () { this.style.background = ''; });
                lista.appendChild(item);
            });
            lista.style.display = 'block';
        });

        buscar.addEventListener('blur', function () {
            setTimeout(() => { lista.style.display = 'none'; }, 150);
        });
        buscar.addEventListener('focus', function () {
            if (lista.children.length) lista.style.display = 'block';
        });
    })();
    </script>

    <div style="margin-top:16px">
        <a href="<?= BASE_URL ?>/controllers/admin/planes_ctrl.php" class="btn btn-warn btn-sm">Cerrar detalle</a>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
