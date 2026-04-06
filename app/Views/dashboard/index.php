<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="s-label mb-1">ADMIN</div>
            <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">Dashboard</h2>
        </div>
        <a href="/dashboard/export" class="btn-s-ghost" style="font-size:.72rem;">Export All CSV</a>
    </div>
</div>

<div class="container py-4">

    <!-- Stats row -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:1px; background:#e0e0e0; border:1px solid #e0e0e0; margin-bottom:2rem;">
        <?php
        $stats = [
            ['CABINETS',    number_format($statCabinets)],
            ['EXCHANGES',   number_format($statExchanges)],
            ['REGIONS',     number_format($statRegions)],
            ['USERS',       number_format($statUsers)],
            ['ACTIVE KEYS', number_format($statActiveKeys)],
            ['API TODAY',   number_format($statApiToday)],
            ['API 7 DAYS',  number_format($statApiWeek)],
        ];
        foreach ($stats as $s):
        ?>
        <div style="background:#fff; padding:1rem 1.25rem;">
            <div style="font-family:'Share Tech Mono',monospace; font-size:.62rem; color:#999; letter-spacing:.1em; margin-bottom:.3rem;"><?= $s[0] ?></div>
            <div style="font-family:'Share Tech Mono',monospace; font-size:1.4rem; color:#0d0d0d; letter-spacing:.02em;"><?= $s[1] ?></div>
        </div>
        <?php endforeach ?>
    </div>

    <div class="row g-4">

        <!-- Recent Activity -->
        <div class="col-lg-6">
            <div class="s-label mb-2">RECENT ACTIVITY</div>
            <?php if (empty($recentActivity)): ?>
                <p style="font-size:.83rem; color:#999;">No activity recorded yet.</p>
            <?php else: ?>
            <table class="s-table" style="font-size:.8rem;">
                <thead><tr><th>Action</th><th>Description</th><th>User</th><th>When</th></tr></thead>
                <tbody>
                <?php foreach ($recentActivity as $entry):
                    $colour = ['created' => '#2a7a2a', 'updated' => '#555', 'deleted' => '#c8001e'][$entry['action']] ?? '#999';
                ?>
                <tr>
                    <td>
                        <span style="font-family:'Share Tech Mono',monospace; font-size:.68rem; letter-spacing:.06em; color:<?= $colour ?>;">
                            <?= strtoupper(esc($entry['action'])) ?>
                        </span>
                    </td>
                    <td style="color:#555;"><?= esc($entry['description']) ?></td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.75rem; color:#999;"><?= esc($entry['username'] ?? '—') ?></td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#bbb; white-space:nowrap;">
                        <?= date('d M H:i', strtotime($entry['created_at'] . ' UTC')) ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <?php endif ?>
        </div>

        <!-- Top Exchanges -->
        <div class="col-lg-3">
            <div class="s-label mb-2">TOP EXCHANGES BY CABINETS</div>
            <?php if (empty($topExchanges)): ?>
                <p style="font-size:.83rem; color:#999;">No data.</p>
            <?php else: ?>
            <table class="s-table" style="font-size:.8rem;">
                <thead><tr><th>Exchange</th><th style="text-align:right;">Cabs</th></tr></thead>
                <tbody>
                <?php foreach ($topExchanges as $ex): ?>
                <tr onclick="window.location.href='/exchange/<?= urlencode($ex['db']) ?>/<?= urlencode($ex['exchange']) ?>'"
                    style="cursor:pointer;">
                    <td>
                        <span class="cab-id"><?= esc($ex['exchange']) ?></span>
                        <div style="font-size:.7rem; color:#bbb;"><?= esc($ex['db_name']) ?></div>
                    </td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.78rem; color:#999; text-align:right;">
                        <?= number_format($ex['cabinet_count']) ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <?php endif ?>
        </div>

        <!-- API Key Usage -->
        <div class="col-lg-3">
            <div class="s-label mb-2">API KEY USAGE</div>
            <?php if (empty($apiKeyUsage)): ?>
                <p style="font-size:.83rem; color:#999;">No API requests logged yet.</p>
            <?php else: ?>
            <table class="s-table" style="font-size:.8rem;">
                <thead><tr><th>Key</th><th style="text-align:right;">Requests</th></tr></thead>
                <tbody>
                <?php foreach ($apiKeyUsage as $k): ?>
                <tr>
                    <td>
                        <span style="font-family:'Share Tech Mono',monospace; font-size:.78rem;"><?= esc($k['key_name']) ?></span>
                        <div style="font-size:.68rem; color:#bbb;"><?= date('d M H:i', strtotime($k['last_request'] . ' UTC')) ?></div>
                    </td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.85rem; color:#c8001e; text-align:right;">
                        <?= number_format($k['total']) ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <?php endif ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
