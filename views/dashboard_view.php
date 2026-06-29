<?php
// header.php ya fue incluido por el controlador — no incluirlo de nuevo
$nombre     = htmlspecialchars($_SESSION['nombre']     ?? '', ENT_QUOTES, 'UTF-8');
$nombre_rol = htmlspecialchars($_SESSION['nombre_rol'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<style>
    .bienvenida {
        margin-bottom: 32px;
    }
    .bienvenida h2 {
        font-size: 1.5rem;
        color: #1a237e;
        font-weight: 700;
    }
    .bienvenida p {
        color: #546e7a;
        margin-top: 4px;
        font-size: .95rem;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .kpi-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
        padding: 28px 24px;
        text-align: center;
        border-top: 4px solid #3949ab;
        transition: transform .15s, box-shadow .15s;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,.12);
    }

    .kpi-valor {
        font-size: 2.4rem;
        font-weight: 700;
        color: #1a237e;
        line-height: 1;
        margin-bottom: 10px;
    }

    .kpi-label {
        font-size: .82rem;
        color: #78909c;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .kpi-grid { grid-template-columns: 1fr 1fr; }
        .kpi-valor { font-size: 1.8rem; }
    }
</style>

<div class="bienvenida" style="display:flex;justify-content:space-between;align-items:flex-start">
    <div>
        <h2>Bienvenido, <?= $nombre ?></h2>
        <p>Accediste como <strong><?= $nombre_rol ?></strong>. Aquí está tu resumen del día.</p>
    </div>
    <?php if (((int) ($_SESSION['id_rol'] ?? 0)) === ROL_ALUMNO): ?>
        <a href="<?= BASE_URL ?>/controllers/perfil_ctrl.php" class="btn btn-primary" style="white-space:nowrap">Mi Perfil</a>
    <?php endif; ?>
</div>

<div class="kpi-grid">
    <?php foreach ($kpis as $kpi): ?>
        <div class="kpi-card">
            <div class="kpi-valor"><?= htmlspecialchars((string) $kpi['valor'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-label"><?= htmlspecialchars($kpi['label'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
