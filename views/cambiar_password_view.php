<?php require_once __DIR__ . '/admin/_style.php'; ?>

<div class="sec-header">
    <h1>Cambiar contraseña</h1>
</div>

<?php if (!empty($_SESSION['debe_cambiar_password'])): ?>
    <div class="alert alert-err">Por seguridad, debés cambiar tu contraseña temporal antes de continuar.</div>
<?php endif; ?>

<?php if ($error ?? null): ?>
    <div class="alert alert-err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card" style="max-width:420px">
    <form method="POST" action="<?= BASE_URL ?>/controllers/cambiar_password_ctrl.php">
        <div class="form-group">
            <label>Nueva contraseña *</label>
            <input type="password" name="password_nueva" required minlength="8">
        </div>
        <div class="form-group">
            <label>Confirmar contraseña *</label>
            <input type="password" name="password_confirmar" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary">Guardar contraseña</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
