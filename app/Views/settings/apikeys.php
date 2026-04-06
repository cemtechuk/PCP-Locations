<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('settings/_tabs', ['activeTab' => 'apikeys']) ?>

<div class="container py-4">

    <?php if ($new_key): ?>
    <div style="background:#f8f8f8; border:1px solid #c8001e; padding:1rem 1.2rem; margin-bottom:1.5rem;">
        <div class="s-label" style="color:#c8001e; margin-bottom:.4rem;">NEW KEY GENERATED — COPY NOW, IT WILL NOT BE SHOWN AGAIN</div>
        <code style="font-family:'Share Tech Mono',monospace; font-size:.9rem; word-break:break-all; display:block; margin-bottom:.6rem;">
            <?= esc($new_key) ?>
        </code>
        <button onclick="navigator.clipboard.writeText('<?= esc($new_key) ?>').then(()=>this.textContent='Copied!')"
                class="btn-s-ghost" style="font-size:.72rem; padding:.2rem .7rem;">Copy to clipboard</button>
    </div>
    <?php endif ?>

    <?php if ($success): ?>
    <div class="s-alert-success mb-3"><?= esc($success) ?></div>
    <?php endif ?>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; letter-spacing:.06em;">
            <?= count($keys) ?> KEY<?= count($keys) !== 1 ? 'S' : '' ?>
        </span>
        <a href="/settings/apikeys/new" class="btn-s-primary" style="font-size:.75rem;">+ New Key</a>
    </div>

    <?php if (empty($keys)): ?>
        <p style="color:#999; font-size:.85rem;">No API keys yet.</p>
    <?php else: ?>
    <table class="s-table" style="font-size:.83rem;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Key (partial)</th>
                <th>Status</th>
                <th>Last Used</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($keys as $k): ?>
            <tr style="<?= $k['active'] ? '' : 'opacity:.45;' ?>">
                <td style="font-family:'Share Tech Mono',monospace;"><?= esc($k['name']) ?></td>
                <td style="font-family:'Share Tech Mono',monospace; color:#999; font-size:.78rem;">
                    <?= esc(substr($k['api_key'], 0, 8)) ?>…<?= esc(substr($k['api_key'], -4)) ?>
                </td>
                <td>
                    <?php if ($k['active']): ?>
                        <span style="color:#2a7a2a; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em;">ACTIVE</span>
                    <?php else: ?>
                        <span style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em;">REVOKED</span>
                    <?php endif ?>
                </td>
                <td style="color:#999; font-size:.78rem;"><?= $k['last_used_at'] ? esc($k['last_used_at']) : '—' ?></td>
                <td style="color:#999; font-size:.78rem;"><?= esc(substr($k['created_at'], 0, 10)) ?></td>
                <td style="white-space:nowrap; text-align:right;">
                    <?php if ($k['active']): ?>
                    <form method="post" action="/settings/apikeys/revoke/<?= $k['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-s-ghost" style="font-size:.72rem; padding:.2rem .6rem;"
                                onclick="return confirm('Revoke this key?')">Revoke</button>
                    </form>
                    <?php endif ?>
                    <form method="post" action="/settings/apikeys/delete/<?= $k['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit"
                                style="background:none; border:none; color:#c8001e; font-family:'Share Tech Mono',monospace; font-size:.72rem; cursor:pointer; padding:.2rem .4rem; letter-spacing:.04em;"
                                onclick="return confirm('Permanently delete this key?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php endif ?>

    <!-- API Reference (collapsed) -->
    <div style="margin-top:2.5rem; border-top:1px solid #eee; padding-top:1.5rem;">
        <button onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none'"
                class="btn-s-ghost" style="font-size:.72rem; margin-bottom:1rem;">Toggle API Reference</button>
        <div style="display:none;">
            <div class="s-label mb-3">API REFERENCE</div>

            <div style="margin-bottom:1.5rem;">
                <div class="s-label mb-1" style="color:#555;">AUTHENTICATION</div>
                <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">Pass your API key as a request header on every call.</p>
                <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">X-API-Key: your64characterkeyhere</pre>
            </div>

            <?php
            $endpoints = [
                ['GET', '/api/v1/exchanges',              'Search exchanges. Params: q (name filter), limit (default 20, max 100).'],
                ['GET', '/api/v1/exchanges/{db}/{exch}',  'Full exchange detail with all its cabinets.'],
                ['GET', '/api/v1/cabinets/{id}',          'Single cabinet by numeric ID.'],
                ['GET', '/api/v1/nearby',                 'Nearest exchanges. Params: lat, lng, limit (default 3, max 20).'],
                ['GET', '/api/v1/search',                 'Find a cabinet. Params: exchange (fuzzy), cab (exact, case-insensitive).'],
            ];
            foreach ($endpoints as $ep):
            ?>
            <div style="margin-bottom:1rem; display:flex; align-items:baseline; gap:.8rem; font-size:.83rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.68rem; padding:.1rem .4rem; white-space:nowrap;">GET</span>
                <code style="white-space:nowrap;"><?= $ep[1] ?></code>
                <span style="color:#777;"><?= $ep[2] ?></span>
            </div>
            <?php endforeach ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
