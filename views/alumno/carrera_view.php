<?php require_once __DIR__ . '/_style.php'; ?>

<div class="sec-header">
    <h1>Mi Plan de Carrera</h1>
</div>

<?php if (empty($materias_por_anio)): ?>
    <div class="alert alert-err">No se encontró información de tu plan de carrera.</div>
<?php else: ?>

<!-- Barra de progreso -->
<?php
$pct_prog = $total_materias > 0
    ? round($total_aprobadas * 100 / $total_materias)
    : 0;
$color_prog = $pct_prog >= 75 ? '#2e7d32' : ($pct_prog >= 40 ? '#f57c00' : '#1a237e');
?>
<div class="card" style="padding:20px 24px;margin-bottom:24px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <span style="font-weight:700;color:#1a237e">Progreso de carrera</span>
        <span style="font-size:.9rem;color:#78909c">
            <?= $total_aprobadas ?> de <?= $total_materias ?> materias aprobadas (<?= $pct_prog ?>%)
        </span>
    </div>
    <div style="background:#e8eaf6;border-radius:8px;height:14px;overflow:hidden">
        <div style="background:<?= $color_prog ?>;width:<?= $pct_prog ?>%;height:100%;border-radius:8px;transition:width .4s"></div>
    </div>
</div>

<!-- Materias por año -->
<?php foreach ($materias_por_anio as $anio => $materias): ?>
<div style="margin-bottom:28px">
    <h2 style="font-size:1.1rem;color:#1a237e;font-weight:700;margin-bottom:14px;
               border-left:4px solid #1a237e;padding-left:10px">
        Año <?= (int)$anio ?>
    </h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px">
    <?php foreach ($materias as $m): ?>
        <?php
        $estado_m = strtolower(str_replace(' ', '_', $m['estado'] ?? 'pendiente'));
        [$icono_m, $bg_m, $borde_m, $texto_m] = match($estado_m) {
            'aprobada'    => ['✅', '#e8f5e9', '#a5d6a7', '#2e7d32'],
            'en_curso'    => ['🔄', '#e3f2fd', '#90caf9', '#0d47a1'],
            'desaprobada' => ['❌', '#ffebee', '#ef9a9a', '#c62828'],
            default       => ['⬜', '#fafafa',  '#e0e0e0', '#757575'],
        };
        ?>
        <div style="background:<?= $bg_m ?>;border:1.5px solid <?= $borde_m ?>;
                    border-radius:8px;padding:14px 16px">
            <div style="font-size:1rem;margin-bottom:4px"><?= $icono_m ?></div>
            <div style="font-weight:700;color:#333;font-size:.88rem">
                <?= htmlspecialchars($m['nombre_materia'] ?? $m['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div style="font-size:.75rem;color:<?= $texto_m ?>;margin-top:4px;font-weight:600">
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $estado_m)), ENT_QUOTES, 'UTF-8') ?>
                — <?= (int)($m['cuatrimestre'] ?? 0) ?>° cuat.
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
