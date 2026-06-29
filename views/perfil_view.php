<?php require_once __DIR__ . '/admin/_style.php'; ?>
<?php
$usuario          ??= null;
$alumno           ??= null;
$estado_academico ??= null;
$es_admin = ((int) ($_SESSION['id_rol'] ?? 0)) === ROL_ADMIN;
$volver   = $es_admin
    ? BASE_URL . '/controllers/admin/usuarios_ctrl.php'
    : BASE_URL . '/controllers/dashboard_ctrl.php';
?>

<div class="sec-header">
    <h1><?= $es_admin ? 'Perfil de usuario' : 'Mi Perfil' ?></h1>
    <a href="<?= $volver ?>" class="btn btn-warn">← Volver</a>
</div>

<!-- Datos de cuenta -->
<?php if ($usuario): ?>
<div class="card">
    <h2>Cuenta de acceso</h2>
    <div class="form-row">
        <div class="form-group">
            <label>Email</label>
            <input type="text" readonly value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Rol</label>
            <input type="text" readonly value="<?= htmlspecialchars($usuario['nombre_rol'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Estado</label>
            <input type="text" readonly value="<?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>">
        </div>
        <div class="form-group">
            <label>Alta</label>
            <input type="text" readonly value="<?= htmlspecialchars(
                date('d/m/Y', strtotime($usuario['created_at'])),
                ENT_QUOTES, 'UTF-8'
            ) ?>">
        </div>
    </div>
    <?php if (!$es_admin): ?>
    <div style="margin-top:8px">
        <a href="<?= BASE_URL ?>/controllers/cambiar_password_ctrl.php" class="btn btn-primary btn-sm">
            Cambiar contraseña
        </a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Datos del alumno -->
<?php if ($alumno): ?>
<div class="card">
    <h2>Datos personales y académicos</h2>
    <div class="form-row">
        <div class="form-group">
            <label>Legajo</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['legajo'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>DNI</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['dni'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Apellido</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['apellido'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Email</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['telefono'] ?? '—', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Fecha de nacimiento</label>
            <input type="text" readonly value="<?= $alumno['fecha_nacimiento']
                ? htmlspecialchars(date('d/m/Y', strtotime($alumno['fecha_nacimiento'])), ENT_QUOTES, 'UTF-8')
                : '—' ?>">
        </div>
        <div class="form-group">
            <label>Fecha de ingreso</label>
            <input type="text" readonly value="<?= $alumno['fecha_ingreso']
                ? htmlspecialchars(date('d/m/Y', strtotime($alumno['fecha_ingreso'])), ENT_QUOTES, 'UTF-8')
                : '—' ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Carrera</label>
            <input type="text" readonly value="<?= htmlspecialchars($alumno['nombre_carrera'] ?? '—', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
            <label>Estado</label>
            <input type="text" readonly value="<?= $alumno['activo'] ? 'Activo' : 'Baja' ?>">
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($estado_academico ?? null): ?>
<?php $ea = $estado_academico; ?>
<div class="card">
    <h2>Estado académico</h2>

    <!-- Barra de progreso de carrera -->
    <div style="margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <span style="font-size:.85rem;font-weight:600;color:#455a64">Avance en la carrera</span>
            <span style="font-size:.85rem;color:#78909c"><?= $ea['aprobadas'] ?> / <?= $ea['total'] ?> materias (<?= $ea['pct_carrera'] ?>%)</span>
        </div>
        <?php
        $color_barra = $ea['pct_carrera'] >= 75 ? '#2e7d32' : ($ea['pct_carrera'] >= 40 ? '#f57c00' : '#1a237e');
        ?>
        <div style="background:#e8eaf6;border-radius:8px;height:12px;overflow:hidden">
            <div style="background:<?= $color_barra ?>;width:<?= $ea['pct_carrera'] ?>%;height:100%;border-radius:8px;transition:width .4s"></div>
        </div>
    </div>

    <!-- KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:<?= ($ea['promedio'] !== null || $ea['pct_asistencia'] !== null) ? '20px' : '0' ?>">
        <div style="background:#e8f5e9;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#2e7d32"><?= $ea['aprobadas'] ?></div>
            <div style="font-size:.75rem;color:#388e3c;font-weight:600;margin-top:2px">Aprobadas</div>
        </div>
        <div style="background:#e3f2fd;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#0d47a1"><?= $ea['en_curso'] ?></div>
            <div style="font-size:.75rem;color:#1565c0;font-weight:600;margin-top:2px">En curso</div>
        </div>
        <div style="background:#ffebee;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#c62828"><?= $ea['desaprobadas'] ?></div>
            <div style="font-size:.75rem;color:#c62828;font-weight:600;margin-top:2px">Desaprobadas</div>
        </div>
        <div style="background:#fafafa;border:1.5px solid #e0e0e0;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#546e7a"><?= $ea['pendientes'] ?></div>
            <div style="font-size:.75rem;color:#78909c;font-weight:600;margin-top:2px">Pendientes</div>
        </div>
        <?php if ($ea['promedio'] !== null): ?>
        <div style="background:#f3e5f5;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#6a1b9a"><?= $ea['promedio'] ?></div>
            <div style="font-size:.75rem;color:#7b1fa2;font-weight:600;margin-top:2px">Promedio</div>
        </div>
        <?php endif; ?>
        <?php if ($ea['pct_asistencia'] !== null): ?>
        <div style="background:#fff8e1;border-radius:8px;padding:14px;text-align:center">
            <div style="font-size:1.6rem;font-weight:700;color:#e65100"><?= $ea['pct_asistencia'] ?>%</div>
            <div style="font-size:.75rem;color:#ef6c00;font-weight:600;margin-top:2px">Asistencia ciclo</div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
