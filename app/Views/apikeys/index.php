<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
        <div>
            <div class="s-label mb-1">ADMIN / API KEYS</div>
            <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">API Key Management</h2>
        </div>
        <a href="/apikeys/create" class="btn-s-primary">+ New Key</a>
    </div>
</div>

<div class="container py-4">

    <?php if (session()->getFlashdata('new_key')): ?>
    <div style="background:#f8f8f8; border:1px solid #c8001e; padding:1rem 1.2rem; margin-bottom:1.5rem;">
        <div class="s-label" style="color:#c8001e; margin-bottom:.4rem;">NEW KEY GENERATED — COPY NOW, IT WILL NOT BE SHOWN AGAIN</div>
        <code style="font-family:'Share Tech Mono',monospace; font-size:.9rem; word-break:break-all; display:block; margin-bottom:.6rem;">
            <?= esc(session()->getFlashdata('new_key')) ?>
        </code>
        <button onclick="navigator.clipboard.writeText('<?= esc(session()->getFlashdata('new_key')) ?>').then(()=>this.textContent='Copied!')"
                class="btn-s-ghost" style="font-size:.72rem; padding:.2rem .7rem;">Copy to clipboard</button>
    </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div style="background:#f8f8f8; border-left:3px solid #555; padding:.7rem 1rem; margin-bottom:1.2rem; font-size:.85rem;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif ?>

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
                    <form method="post" action="/apikeys/revoke/<?= $k['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-s-ghost" style="font-size:.72rem; padding:.2rem .6rem;"
                                onclick="return confirm('Revoke this key?')">Revoke</button>
                    </form>
                    <?php endif ?>
                    <form method="post" action="/apikeys/delete/<?= $k['id'] ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" style="background:none; border:none; color:#c8001e; font-family:'Share Tech Mono',monospace; font-size:.72rem; cursor:pointer; padding:.2rem .4rem; letter-spacing:.04em;"
                                onclick="return confirm('Permanently delete this key?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php endif ?>

    <div style="margin-top:2.5rem; border-top:1px solid #eee; padding-top:1.5rem;">
        <div class="s-label mb-3">API REFERENCE</div>

        <!-- Authentication -->
        <div style="margin-bottom:2rem;">
            <div class="s-label mb-1" style="color:#555;">AUTHENTICATION</div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Every request must include your API key as a request header. There are no cookies or sessions involved.
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">X-API-Key: your64characterkeyhere</pre>
        </div>

        <!-- Endpoint 1 -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex; align-items:baseline; gap:.7rem; margin-bottom:.5rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.7rem; padding:.15rem .45rem; letter-spacing:.06em;">GET</span>
                <code style="font-size:.85rem;">/api/v1/exchanges</code>
            </div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Search for exchanges by name. The <code>q</code> parameter is a partial match — passing <code>hack</code>, <code>hackn</code>, or <code>hackney</code> all return Hackney exchange. Omit <code>q</code> to return all exchanges. Results are capped at <code>limit</code> (default 20, max 100). Use <code>limit=0</code> for no cap.
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">GET /api/v1/exchanges?q=hackney&limit=5
GET /api/v1/exchanges?limit=0</pre>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-top:none; padding:.8rem 1rem;">
<pre style="font-size:.75rem; margin:0; color:#555;">{
  "data": [
    {
      "db": "CL",
      "db_name": "Central London",
      "exchange": "HACKNEY",
      "cabinet_count": 42,
      "lat": 51.545678,
      "lng": -0.056789,
      "url": "https://pcplocations.dcg-web.com:8088/exchange/CL/HACKNEY"
    }
  ],
  "count": 1
}</pre>
            </div>
        </div>

        <!-- Endpoint 2 -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex; align-items:baseline; gap:.7rem; margin-bottom:.5rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.7rem; padding:.15rem .45rem; letter-spacing:.06em;">GET</span>
                <code style="font-size:.85rem;">/api/v1/exchanges/{db}/{exchange}</code>
            </div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Returns full details for a single exchange plus every cabinet belonging to it. Use the <code>db</code> and <code>exchange</code> values from the search results above — for example <code>CL</code> and <code>HACKNEY</code>. Cabinets are sorted in natural order (P1, P2 … P10, not P1, P10, P11).
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">GET /api/v1/exchanges/CL/HACKNEY</pre>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-top:none; padding:.8rem 1rem;">
<pre style="font-size:.75rem; margin:0; color:#555;">{
  "exchange": {
    "db": "CL",
    "db_name": "Central London",
    "exchange": "HACKNEY",
    "cabinet_count": 42,
    "lat": 51.545678,
    "lng": -0.056789,
    "url": "https://pcplocations.dcg-web.com:8088/exchange/CL/HACKNEY"
  },
  "cabinets": [
    {
      "id": 1234,
      "db": "CL",
      "db_name": "Central London",
      "exchange": "HACKNEY",
      "cab": "P1",
      "address": "12 Mare Street, London",
      "lat": 51.545100,
      "lng": -0.055900,
      "notes": null,
      "url": "https://pcplocations.dcg-web.com:8088/cabinet/1234"
    },
    ...
  ]
}</pre>
            </div>
        </div>

        <!-- Endpoint 3 -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex; align-items:baseline; gap:.7rem; margin-bottom:.5rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.7rem; padding:.15rem .45rem; letter-spacing:.06em;">GET</span>
                <code style="font-size:.85rem;">/api/v1/cabinets/{id}</code>
            </div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Fetch a single cabinet by its numeric ID. The ID is the <code>id</code> field returned in cabinet lists. Useful when you've already stored a cabinet ID and need to refresh its data.
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">GET /api/v1/cabinets/1234</pre>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-top:none; padding:.8rem 1rem;">
<pre style="font-size:.75rem; margin:0; color:#555;">{
  "data": {
    "id": 1234,
    "db": "CL",
    "db_name": "Central London",
    "exchange": "HACKNEY",
    "cab": "P1",
    "address": "12 Mare Street, London",
    "lat": 51.545100,
    "lng": -0.055900,
    "notes": null,
    "url": "https://pcplocations.dcg-web.com:8088/cabinet/1234"
  }
}</pre>
            </div>
        </div>

        <!-- Endpoint 4 -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex; align-items:baseline; gap:.7rem; margin-bottom:.5rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.7rem; padding:.15rem .45rem; letter-spacing:.06em;">GET</span>
                <code style="font-size:.85rem;">/api/v1/search</code>
            </div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Look up a specific cabinet without needing to know the region code. <code>exchange</code> is a fuzzy match — <code>hackney</code> will match <code>HACKNEY</code>, <code>HACKNEY ATE</code>, <code>HACKNEY UNIT B</code>, etc. <code>cab</code> is an exact match (case-insensitive), so <code>P1</code> returns only P1 and not P10 or P11. If the exchange name is ambiguous and matches more than one exchange, all matching cabinets are returned.
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">GET /api/v1/search?exchange=hackney&cab=P1</pre>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-top:none; padding:.8rem 1rem;">
<pre style="font-size:.75rem; margin:0; color:#555;">{
  "data": [
    {
      "id": 1234,
      "db": "CL",
      "db_name": "Central London",
      "exchange": "HACKNEY",
      "cab": "P1",
      "address": "12 Mare Street, London",
      "lat": 51.545100,
      "lng": -0.055900,
      "notes": null,
      "url": "https://pcplocations.dcg-web.com:8088/cabinet/1234"
    }
  ],
  "count": 1
}</pre>
            </div>
        </div>

        <!-- Endpoint 5 -->
        <div style="margin-bottom:1rem;">
            <div style="display:flex; align-items:baseline; gap:.7rem; margin-bottom:.5rem;">
                <span style="background:#0d0d0d; color:#fff; font-family:'Share Tech Mono',monospace; font-size:.7rem; padding:.15rem .45rem; letter-spacing:.06em;">GET</span>
                <code style="font-size:.85rem;">/api/v1/nearby</code>
            </div>
            <p style="font-size:.83rem; color:#555; margin-bottom:.6rem;">
                Returns the nearest exchanges to a GPS coordinate, sorted by distance. Provide decimal <code>lat</code> and <code>lng</code>. <code>limit</code> defaults to 3, max 20. Only exchanges that have a known building location (an EXCH row) are included. Distance is in kilometres.
            </p>
            <pre style="background:#f4f4f4; border:1px solid #e0e0e0; padding:.8rem 1rem; font-size:.78rem; margin:0;">GET /api/v1/nearby?lat=51.5074&lng=-0.1278&limit=3</pre>
            <div style="background:#f9f9f9; border:1px solid #e0e0e0; border-top:none; padding:.8rem 1rem;">
<pre style="font-size:.75rem; margin:0; color:#555;">{
  "data": [
    {
      "db": "CL",
      "db_name": "Central London",
      "exchange": "PADDINGTON",
      "cabinet_count": 38,
      "lat": 51.516700,
      "lng": -0.177800,
      "distance_km": 0.94,
      "url": "https://pcplocations.dcg-web.com:8088/exchange/CL/PADDINGTON"
    },
    ...
  ]
}</pre>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>
