<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('settings/_tabs', ['activeTab' => 'import']) ?>

<div class="container py-4" style="max-width:720px;">

    <?php if ($errors): ?>
    <div class="s-alert-error mb-4">
        <?php foreach ((array) $errors as $e): ?><div><?= esc($e) ?></div><?php endforeach ?>
    </div>
    <?php endif ?>

    <?php if ($result): ?>
    <div style="background:#fff; border:1px solid #e0e0e0; border-left:3px solid <?= $result['inserted'] > 0 ? '#2a7a2a' : '#999' ?>; padding:1rem 1.2rem; margin-bottom:1.5rem;">
        <div class="s-label mb-2" style="color:#555;">IMPORT COMPLETE — <?= esc($result['filename']) ?></div>
        <div style="display:flex; gap:2.5rem; flex-wrap:wrap;">
            <div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:1.4rem; color:<?= $result['inserted'] > 0 ? '#2a7a2a' : '#999' ?>;">
                    <?= number_format($result['inserted']) ?>
                </div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#999; letter-spacing:.06em; margin-top:.1rem;">INSERTED</div>
            </div>
            <div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:1.4rem; color:#999;">
                    <?= number_format($result['skipped']) ?>
                </div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#999; letter-spacing:.06em; margin-top:.1rem;">SKIPPED (DUPLICATE)</div>
            </div>
            <?php if ($result['invalid'] > 0): ?>
            <div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:1.4rem; color:#c8001e;">
                    <?= number_format($result['invalid']) ?>
                </div>
                <div style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#999; letter-spacing:.06em; margin-top:.1rem;">INVALID (SKIPPED)</div>
            </div>
            <?php endif ?>
        </div>
    </div>
    <?php endif ?>

    <div style="background:#fff; border:1px solid #e0e0e0; padding:1.5rem; margin-bottom:2rem;">
        <div class="s-label mb-3">UPLOAD CSV</div>
        <form method="post" action="/settings/import" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-4">
                <input type="file" name="csv_file" accept=".csv"
                       style="font-family:'Share Tech Mono',monospace; font-size:.8rem; color:#555; width:100%; padding:.5rem; border:1px solid #e0e0e0; background:#fafafa; cursor:pointer;">
                <div style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#bbb; letter-spacing:.05em; margin-top:.4rem;">
                    ACCEPTS .CSV — MAX <?= ini_get('upload_max_filesize') ?>
                </div>
            </div>
            <button type="submit" class="btn-s-primary">Import</button>
        </form>
    </div>

    <div>
        <div class="s-label mb-2">EXPECTED FORMAT</div>
        <p style="font-size:.83rem; color:#555; margin-bottom:.8rem;">
            Header row required. Rows matching an existing <code>db</code> + <code>exchange</code> + <code>cab</code> are skipped — safe to re-import without duplicates.
            Both <code>long</code> and <code>lng</code> are accepted for longitude.
        </p>
        <table class="s-table" style="font-size:.8rem;">
            <thead><tr><th>Column</th><th>Required</th><th>Notes</th></tr></thead>
            <tbody>
                <tr><td class="cab-id">db</td><td style="color:#2a7a2a; font-family:'Share Tech Mono',monospace; font-size:.72rem;">YES</td><td style="color:#555;">Short region code, e.g. <code>CL</code></td></tr>
                <tr><td class="cab-id">db_name</td><td style="color:#2a7a2a; font-family:'Share Tech Mono',monospace; font-size:.72rem;">YES</td><td style="color:#555;">Region name, e.g. <code>Central London</code></td></tr>
                <tr><td class="cab-id">exchange</td><td style="color:#2a7a2a; font-family:'Share Tech Mono',monospace; font-size:.72rem;">YES</td><td style="color:#555;">Exchange name. Auto-uppercased.</td></tr>
                <tr><td class="cab-id">cab</td><td style="color:#2a7a2a; font-family:'Share Tech Mono',monospace; font-size:.72rem;">YES</td><td style="color:#555;">Cabinet ID, e.g. <code>P1</code>. Use <code>EXCH</code> for the exchange building. Auto-uppercased.</td></tr>
                <tr><td class="cab-id">address</td><td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem;">NO</td><td style="color:#555;">Street address.</td></tr>
                <tr><td class="cab-id">lat</td><td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem;">NO</td><td style="color:#555;">Latitude (WGS84 decimal).</td></tr>
                <tr><td class="cab-id">long / lng</td><td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem;">NO</td><td style="color:#555;">Longitude (WGS84 decimal). Both column names accepted.</td></tr>
                <tr><td class="cab-id">notes</td><td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem;">NO</td><td style="color:#555;">Additional notes.</td></tr>
            </tbody>
        </table>
        <div style="margin-top:1rem; background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-family:'Share Tech Mono',monospace; font-size:.75rem; color:#555; overflow-x:auto; white-space:nowrap;">
            db,db_name,exchange,cab,address,lat,long,notes<br>
            CL,Central London,HACKNEY,EXCH,,51.53020100,-0.08204400,<br>
            CL,Central London,HACKNEY,P1,"JCN QUEENSBRIDGE ROAD",51.54412300,-0.05531200,
        </div>
    </div>

</div>

<?= $this->endSection() ?>
