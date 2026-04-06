<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
if ($info['exch_lat'] && $info['exch_lng']) {
    $mapsUrl = 'https://www.google.com/maps?q=' . $info['exch_lat'] . ',' . $info['exch_lng'];
} else {
    $mapsUrl = 'https://www.google.com/maps/search/' . urlencode($info['exchange'] . ' telephone exchange London');
}
?>

<!-- Header -->
<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container">
        <div class="s-label mb-2">
            <a href="/" style="color:#999; text-decoration:none; letter-spacing:.08em;">INDEX</a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <?= esc($info['db_name']) ?>
        </div>
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.3rem; font-weight:400; letter-spacing:.04em; margin-bottom:.25rem;">
                    <?= esc($info['exchange']) ?>
                </h2>
                <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; letter-spacing:.06em;">
                    <?= number_format($info['cabinet_count']) ?> CABINETS &nbsp;/&nbsp; <?= esc($info['db']) ?>
                </span>
            </div>
            <div style="display:flex; gap:.6rem; flex-wrap:wrap; align-items:center;">
                <?php if (in_array(session()->get('role'), ['editor', 'admin'])): ?>
                    <a href="/cabinet/create/<?= urlencode($info['db']) ?>/<?= urlencode($info['exchange']) ?>"
                       class="btn-s-primary" style="font-size:.75rem;">+ Add Cabinet</a>
                <?php endif ?>
                <a href="<?= $mapsUrl ?>" target="_blank" class="btn-s-ghost">Map</a>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div style="background:#f9f9f9; border-bottom:1px solid #e0e0e0; padding:.75rem 0;">
    <div class="container">
        <form method="get" action="" class="d-flex gap-2" style="max-width:420px;">
            <input
                type="text"
                name="q"
                class="form-control form-control-sm"
                style="font-family:'Share Tech Mono',monospace; font-size:.8rem; letter-spacing:.04em;"
                placeholder="FILTER CABINET / ADDRESS..."
                value="<?= esc($filter) ?>"
                autofocus
            >
            <?php if ($filter !== ''): ?>
                <a href="?" class="btn-s-ghost" style="padding:.3rem .8rem; white-space:nowrap;">Clear</a>
            <?php else: ?>
                <button type="submit" class="btn-s-primary" style="padding:.3rem .9rem;">Filter</button>
            <?php endif ?>
        </form>
    </div>
</div>

<!-- Cabinet list -->
<div class="container py-4">

    <?php if ($filter !== ''): ?>
        <p style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; letter-spacing:.06em; margin-bottom:1rem;">
            <?= count($cabinets) ?> RESULT<?= count($cabinets) !== 1 ? 'S' : '' ?> FOR "<?= esc(strtoupper($filter)) ?>"
        </p>
    <?php endif ?>

    <?php if (empty($cabinets)): ?>
        <p style="font-family:'Share Tech Mono',monospace; font-size:.8rem; color:#999;">NO RECORDS FOUND</p>
    <?php else: ?>
        <table class="s-table">
            <thead>
                <tr>
                    <th>Cabinet</th>
                    <th style="width:100px; text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cabinets as $cab): ?>
                <tr onclick="window.location.href='/cabinet/<?= $cab['id'] ?>'"
                    style="cursor:pointer;">
                    <td>
                        <span class="cab-id"><?= esc($cab['cab']) ?></span>
                        <?php if ($cab['address']): ?>
                            <div style="font-size:.78rem; color:#777; margin-top:.2rem;"><?= esc($cab['address']) ?></div>
                        <?php endif ?>
                        <?php if (! empty($cab['notes'])): ?>
                            <div style="font-size:.74rem; color:#aaa; margin-top:.15rem;"><?= esc($cab['notes']) ?></div>
                        <?php endif ?>
                    </td>
                    <td style="text-align:right; white-space:nowrap; vertical-align:middle;">
                        <?php if ($cab['lat'] && $cab['lng']): ?>
                            <a href="https://www.google.com/maps?q=<?= $cab['lat'] ?>,<?= $cab['lng'] ?>"
                               target="_blank" class="map-link"
                               onclick="event.stopPropagation()">Map</a>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <p style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#bbb; letter-spacing:.06em; margin-top:.75rem;">
            <?= count($cabinets) ?> RECORD<?= count($cabinets) !== 1 ? 'S' : '' ?>
        </p>
    <?php endif ?>
</div>

<script>
(function () {
    var els = Array.from(document.querySelectorAll('.cab-id'));
    if (!els.length) return;
    // Adaptive stagger: cap total reveal time at ~900ms
    var stagger = Math.min(35 * SCRAMBLE_SPEED, Math.floor(900 * SCRAMBLE_SPEED / els.length));
    els.forEach(function (el, i) {
        var text = el.textContent;
        el.innerHTML = '&nbsp;';
        setTimeout(function () {
            new TextScramble(el).setText(text);
        }, i * stagger);
    });
})();
</script>

<?= $this->endSection() ?>
