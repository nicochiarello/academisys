<?php require_once __DIR__ . '/admin/_style.php'; ?>
<?php
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
