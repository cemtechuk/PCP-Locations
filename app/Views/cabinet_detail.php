<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container">
        <div class="s-label mb-2">
            <a href="/" style="color:#999; text-decoration:none;">INDEX</a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <a href="/exchange/<?= urlencode($cabinet['db']) ?>/<?= urlencode($cabinet['exchange']) ?>"
               style="color:#999; text-decoration:none; letter-spacing:.06em;"><?= esc($cabinet['exchange']) ?></a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <?= esc($cabinet['cab']) ?>
        </div>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
            <div>
                <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.2rem; font-weight:400; letter-spacing:.04em; margin-bottom:.2rem;">
                    <?= esc($cabinet['exchange']) ?> <span style="color:#c8001e;"><?= esc($cabinet['cab']) ?></span>
                </h2>
                <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; letter-spacing:.06em;">
                    <?= esc($cabinet['db_name']) ?> &nbsp;/&nbsp; <?= esc($cabinet['db']) ?>
                </span>
            </div>
            <?php if (in_array(session()->get('role'), ['editor', 'admin'])): ?>
                <a href="/cabinet/edit/<?= $cabinet['id'] ?>" class="btn-s-ghost" style="padding:.3rem .9rem; font-size:.75rem;">Edit</a>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        <div class="col-md-5">
            <table class="s-table" style="font-size:.85rem;">
                <tbody>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase; width:110px;">Exchange</td>
                        <td>
                            <a href="/exchange/<?= urlencode($cabinet['db']) ?>/<?= urlencode($cabinet['exchange']) ?>">
                                <?= esc($cabinet['exchange']) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Cabinet</td>
                        <td class="cab-id"><?= esc($cabinet['cab']) ?></td>
                    </tr>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Region</td>
                        <td><?= esc($cabinet['db_name']) ?></td>
                    </tr>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Address</td>
                        <td style="color:#555;"><?= esc($cabinet['address']) ?></td>
                    </tr>
                    <?php if ($cabinet['lat'] && $cabinet['lng']): ?>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Coords</td>
                        <td style="font-family:'Share Tech Mono',monospace; font-size:.8rem; color:#555;">
                            <?= $cabinet['lat'] ?>, <?= $cabinet['lng'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Distance</td>
                        <td style="font-family:'Share Tech Mono',monospace; font-size:.8rem; color:#555;">
                            <span id="distance-val">—</span>
                        </td>
                    </tr>
                    <?php endif ?>
                    <?php if ($cabinet['notes']): ?>
                    <tr>
                        <td style="color:#999; font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">Notes</td>
                        <td style="color:#555;"><?= esc($cabinet['notes']) ?></td>
                    </tr>
                    <?php endif ?>
                </tbody>
            </table>

            <?php if ($cabinet['lat'] && $cabinet['lng']): ?>
            <div style="margin-top:1rem; display:flex; gap:.6rem;">
                <a href="https://www.google.com/maps?q=<?= $cabinet['lat'] ?>,<?= $cabinet['lng'] ?>"
                   target="_blank" class="btn-s-primary">Maps</a>
                <a href="https://maps.google.com/?q=<?= $cabinet['lat'] ?>,<?= $cabinet['lng'] ?>&layer=c"
                   target="_blank" class="btn-s-ghost">Street View</a>
            </div>
            <?php endif ?>

            <div style="margin-top:1.5rem;">
                <a href="/exchange/<?= urlencode($cabinet['db']) ?>/<?= urlencode($cabinet['exchange']) ?>"
                   style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; text-decoration:none; letter-spacing:.06em; text-transform:uppercase;">
                    &larr; Back to <?= esc($cabinet['exchange']) ?>
                </a>
            </div>
        </div>

        <?php if ($cabinet['lat'] && $cabinet['lng']): ?>
        <div class="col-md-7">
            <div style="position:relative; border:1px solid #e0e0e0; overflow:hidden; height:380px; background:#f9f9f9;">
                <div id="samaritan-map" style="width:100%; height:100%;"></div>
                <!-- Corner brackets -->
                <div style="position:absolute; inset:0; pointer-events:none; z-index:900;">
                    <div style="position:absolute; top:10px; left:10px; width:18px; height:18px; border-top:2px solid #c8001e; border-left:2px solid #c8001e;"></div>
                    <div style="position:absolute; top:10px; right:10px; width:18px; height:18px; border-top:2px solid #c8001e; border-right:2px solid #c8001e;"></div>
                    <div style="position:absolute; bottom:10px; left:10px; width:18px; height:18px; border-bottom:2px solid #c8001e; border-left:2px solid #c8001e;"></div>
                    <div style="position:absolute; bottom:10px; right:10px; width:18px; height:18px; border-bottom:2px solid #c8001e; border-right:2px solid #c8001e;"></div>
                    <!-- Coords readout -->
                    <div style="position:absolute; top:14px; left:50%; transform:translateX(-50%); font-family:'Share Tech Mono',monospace; font-size:.62rem; color:#c8001e; letter-spacing:.08em; white-space:nowrap; opacity:.85;">
                        <?= number_format((float)$cabinet['lat'], 6) ?>&nbsp;&nbsp;/&nbsp;&nbsp;<?= number_format((float)$cabinet['lng'], 6) ?>
                    </div>
                    <!-- Target label -->
                    <div style="position:absolute; bottom:14px; left:50%; transform:translateX(-50%); font-family:'Share Tech Mono',monospace; font-size:.62rem; color:#c8001e; letter-spacing:.1em; white-space:nowrap; opacity:.85;">
                        TARGET&nbsp;ACQUIRED&nbsp;—&nbsp;<?= esc($cabinet['exchange']) ?>&nbsp;<?= esc($cabinet['cab']) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>

    </div>
</div>

<?php if ($cabinet['lat'] && $cabinet['lng']): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    var cabLat = <?= (float)$cabinet['lat'] ?>;
    var cabLng = <?= (float)$cabinet['lng'] ?>;

    // --- Samaritan map ---
    var map = L.map('samaritan-map', {
        zoomControl: false,
        attributionControl: false,
    }).setView([cabLat, cabLng], 17);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Hollow red triangle marker — base at top, tip pointing down to location
    var triangleSvg = [
        '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="28" viewBox="0 0 30 28">',
        '  <polygon points="2,2 28,2 15,26" fill="none" stroke="#c8001e" stroke-width="2" stroke-linejoin="miter"/>',
        '  <circle cx="15" cy="10" r="1.5" fill="#c8001e"/>',
        '</svg>',
    ].join('');

    var icon = L.divIcon({
        html: triangleSvg,
        className: '',
        iconSize: [30, 28],
        iconAnchor: [15, 27],
    });

    L.marker([cabLat, cabLng], { icon: icon }).addTo(map);

    // Subtle red scan-line circle pulse
    L.circle([cabLat, cabLng], {
        radius: 40,
        color: '#c8001e',
        weight: 1,
        fillColor: '#c8001e',
        fillOpacity: 0.04,
        opacity: 0.35,
    }).addTo(map);

    // --- Distance from user ---
    function haversine(lat1, lng1, lat2, lng2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2)
              + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
              * Math.sin(dLng/2) * Math.sin(dLng/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    var el = document.getElementById('distance-val');
    if (!navigator.geolocation) { el.textContent = 'N/A'; return; }

    el.textContent = 'LOCATING...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        var d = haversine(pos.coords.latitude, pos.coords.longitude, cabLat, cabLng);
        el.textContent = d < 1
            ? Math.round(d * 1000) + ' m'
            : d.toFixed(2) + ' km';
    }, function() {
        el.textContent = 'UNAVAILABLE';
    }, { timeout: 8000 });
})();
</script>
<?php endif ?>

<?= $this->endSection() ?>
