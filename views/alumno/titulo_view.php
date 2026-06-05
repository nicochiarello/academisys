<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mi Título</h1>
</div>

<?php if ($titulo): ?>
<!-- Tiene título otorgado -->
<div class="card" style="text-align:center;padding:40px 32px;border:2px solid #a5d6a7">
    <div style="font-size:3rem;margin-bottom:12px">🎓</div>
    <div style="font-size:1.6rem;font-weight:900;color:#1a237e;margin-bottom:6px">
        <?= htmlspecialchars($titulo['titulo_otorga'] ?? '', ENT_QUOTES, 'UTF-8') ?>
    </div>
    <div style="font-size:1rem;color:#455a64;margin-bottom:20px">
        <?= htmlspecialchars($titulo['nombre_carrera'] ?? '', ENT_QUOTES, 'UTF-8') ?>
    </div>

    <div style="display:flex;justify-content:center;gap:40px;margin-bottom:24px">
        <?php if (!empty($titulo['fecha_egreso'])): ?>
        <div>
            <span style="font-size:.78rem;color:#78909c;font-weight:600;display:block">FECHA DE EGRESO</span>
            <span style="font-size:1.1rem;font-weight:700;color:#333">
                <?= htmlspecialchars($titulo['fecha_egreso'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <?php endif; ?>
        <?php if (!empty($titulo['promedio_final'])): ?>
        <div>
            <span style="font-size:.78rem;color:#78909c;font-weight:600;display:block">PROMEDIO FINAL</span>
            <span style="font-size:1.6rem;font-weight:900;color:#2e7d32">
                <?= number_format((float)$titulo['promedio_final'], 2) ?>
            </span>
        </div>
        <?php endif; ?>
        <?php if (!empty($titulo['año_vigencia'])): ?>
        <div>
            <span style="font-size:.78rem;color:#78909c;font-weight:600;display:block">PLAN</span>
            <span style="font-size:1.1rem;font-weight:700;color:#333">
                <?= (int)$titulo['año_vigencia'] ?>
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div style="background:#e8f5e9;border-radius:8px;padding:14px 20px;display:inline-block">
        <span style="color:#2e7d32;font-weight:700;font-size:1rem">
            ¡Felicitaciones por tu egreso!
        </span>
    </div>
</div>

<?php else: ?>
<!-- Sin título aún -->
<div class="alert alert-err" style="font-size:.95rem">
    Aún no obtuviste tu título universitario.
</div>

<?php if ($total_materias > 0): ?>
<?php
$pct = $total_materias > 0
    ? round($total_aprobadas * 100 / $total_materias)
    : 0;
$pendientes_count = count($materias_pendientes);
?>
<div class="card" style="padding:24px">
    <h2>Progreso hacia el título</h2>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <span style="font-weight:700;color:#1a237e">Materias aprobadas</span>
        <span style="color:#78909c"><?= $total_aprobadas ?> de <?= $total_materias ?> (<?= $pct ?>%)</span>
    </div>
    <div style="background:#e8eaf6;border-radius:8px;height:14px;overflow:hidden;margin-bottom:20px">
        <div style="background:#1a237e;width:<?= $pct ?>%;height:100%;border-radius:8px"></div>
    </div>

    <?php if (!empty($materias_pendientes)): ?>
    <h2 style="margin-top:0">Materias pendientes / desaprobadas (<?= $pendientes_count ?>)</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px">
        <?php foreach ($materias_pendientes as $m): ?>
            <?php
            $estado_p = $m['estado'] ?? 'pendiente';
            [$bg_p, $borde_p, $texto_p] = $estado_p === 'desaprobada'
                ? ['#ffebee', '#ef9a9a', '#c62828']
                : ['#fafafa',  '#e0e0e0', '#757575'];
            ?>
            <div style="background:<?= $bg_p ?>;border:1.5px solid <?= $borde_p ?>;
                        border-radius:8px;padding:10px 14px">
                <div style="font-weight:700;color:#333;font-size:.85rem">
                    <?= htmlspecialchars($m['nombre_materia'] ?? $m['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div style="font-size:.73rem;color:<?= $texto_p ?>;margin-top:3px;font-weight:600">
                    <?= htmlspecialchars(ucfirst($estado_p), ENT_QUOTES, 'UTF-8') ?>
                    — <?= (int)($m['año_cursada'] ?? 0) ?>° año,
                    <?= (int)($m['cuatrimestre'] ?? 0) ?>° cuat.
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; /* $titulo */ ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
