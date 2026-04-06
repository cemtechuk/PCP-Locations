<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="s-hero">
    <div class="container" style="max-width: 700px;">
        <div class="mt-4 position-relative">
            <input
                type="text"
                id="exchangeSearch"
                class="form-control"
                style="font-family: 'Share Tech Mono', monospace; font-size:.9rem; padding:.65rem 1rem; letter-spacing:.03em;"
                placeholder="TYPE EXCHANGE NAME..."
                autocomplete="off"
            >

            <!-- Live search results dropdown -->
            <div id="searchResults" class="d-none position-absolute w-100"
                 style="z-index:1000; background:#fff; border:1px solid #e0e0e0; border-top:2px solid #c8001e; top:calc(100% + 1px); max-height:460px; overflow-y:auto;">
                <div id="resultsList"></div>
            </div>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:.6rem;">
            <span style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:#bbb; letter-spacing:.06em;">MIN. 2 CHARACTERS &mdash; RESULTS APPEAR IN REAL TIME</span>
            <?php if (in_array(session()->get('role'), ['editor', 'admin'])): ?>
                <a href="/exchange/create" class="btn-s-primary" style="font-size:.72rem; padding:.3rem .9rem;">+ Add Exchange</a>
            <?php endif ?>
        </div>

        <!-- Nearby exchanges -->
        <div id="nearbyWrap" style="margin-top:1.5rem;">
            <div id="nearbyStatus" style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:#bbb; letter-spacing:.06em;">
                LOCATING...
            </div>
            <div id="nearbyList" style="margin-top:.6rem;"></div>
        </div>
    </div>
</div>

<script>
(function () {
    /* ── TextScramble helpers ── */
    let scrambleTimers  = [];
    let nearbyTimers    = [];

    function scrambleList(rowEls, dataArr) {
        scrambleTimers.forEach(clearTimeout);
        scrambleTimers = [];
        rowEls.forEach(function (row, i) {
            scrambleTimers.push(setTimeout(function () {
                const nameEl   = row.querySelector('.sr-name');
                const regionEl = row.querySelector('.sr-region');
                if (nameEl)   new TextScramble(nameEl).setText(nameEl.dataset.text);
                if (regionEl) new TextScramble(regionEl).setText(regionEl.dataset.text);
            }, i * 45 * SCRAMBLE_SPEED));
        });
    }

    /* ── Search ── */
    const input = document.getElementById('exchangeSearch');
    const box   = document.getElementById('searchResults');
    const list  = document.getElementById('resultsList');
    let timer   = null;
    let current = -1;
    let rows    = [];

    function renderSearch(data) {
        rows    = data;
        current = -1;

        if (!data.length) {
            list.innerHTML = '<div style="padding:.8rem 1rem; font-family:\'Share Tech Mono\',monospace; font-size:.75rem; color:#999; letter-spacing:.06em;">NO RECORDS FOUND</div>';
            box.classList.remove('d-none');
            return;
        }

        list.innerHTML = data.map((r, i) => `
            <div class="s-result-row"
                 data-url="${r.detail_url}"
                 data-index="${i}"
                 style="display:flex; align-items:center; justify-content:space-between;
                        padding:.65rem 1rem; border-bottom:1px solid #f0f0f0; cursor:pointer;">
                <div>
                    <div class="sr-name" data-text="${r.exchange}"
                         style="font-family:'Share Tech Mono',monospace; font-size:.88rem; letter-spacing:.04em; min-width:4ch;">&nbsp;</div>
                    <div class="sr-region" data-text="${r.db_name.toUpperCase()}"
                         style="font-size:.72rem; color:#999; margin-top:2px; letter-spacing:.04em; min-width:3ch;">&nbsp;</div>
                </div>
                <div style="display:flex; align-items:center; gap:1.2rem;">
                    <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#bbb;">${r.cabinet_count} CAB${r.cabinet_count !== 1 ? 'S' : ''}</span>
                    ${r.maps_url ? `<a href="${r.maps_url}" target="_blank" class="map-link" onclick="event.stopPropagation()">MAP</a>` : ''}
                </div>
            </div>
        `).join('');

        box.classList.remove('d-none');

        list.querySelectorAll('.s-result-row').forEach(function (row) {
            row.addEventListener('click', function () { window.location.href = row.dataset.url; });
        });

        scrambleList(Array.from(list.querySelectorAll('.s-result-row')), data);
    }

    function doSearch(q) {
        if (q.length < 2) { box.classList.add('d-none'); return; }
        fetch('/api/exchanges?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(renderSearch);
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => doSearch(this.value.trim()), 200);
    });

    input.addEventListener('keydown', function (e) {
        const items = list.querySelectorAll('.s-result-row');
        if      (e.key === 'ArrowDown') current = Math.min(current + 1, items.length - 1);
        else if (e.key === 'ArrowUp')   current = Math.max(current - 1, 0);
        else if (e.key === 'Enter' && current >= 0 && rows[current]) {
            window.location.href = rows[current].detail_url; return;
        } else if (e.key === 'Escape') { box.classList.add('d-none'); return; }
        items.forEach((el, i) => el.style.background = i === current ? '#fafafa' : '');
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !box.contains(e.target)) box.classList.add('d-none');
    });

    /* ── Nearby ── */
    const nearbyStatus = document.getElementById('nearbyStatus');
    const nearbyList   = document.getElementById('nearbyList');

    function renderNearby(exchanges) {
        nearbyStatus.textContent = 'NEAREST EXCHANGES';

        nearbyList.innerHTML = exchanges.map(r => `
            <div style="display:flex; align-items:center; justify-content:space-between;
                        padding:.55rem 0; border-bottom:1px solid #f0f0f0; cursor:pointer;"
                 onclick="window.location.href='${r.detail_url}'">
                <div>
                    <span class="nr-name" data-text="${r.exchange}"
                          style="font-family:'Share Tech Mono',monospace; font-size:.88rem; letter-spacing:.04em;">&nbsp;</span>
                    <span class="nr-region" data-text="${r.db_name.toUpperCase()}"
                          style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:#999; margin-left:.8rem;">&nbsp;</span>
                </div>
                <div style="display:flex; align-items:center; gap:1rem;">
                    <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#bbb;">${r.cabinet_count} CABS</span>
                    <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#c8001e; letter-spacing:.04em;">${r.distance_km} KM</span>
                </div>
            </div>
        `).join('');

        nearbyTimers.forEach(clearTimeout);
        nearbyTimers = [];
        nearbyList.querySelectorAll('[data-text]').forEach(function (el, i) {
            nearbyTimers.push(setTimeout(function () {
                new TextScramble(el).setText(el.dataset.text);
            }, i * 40 * SCRAMBLE_SPEED));
        });
    }

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                fetch('/api/nearby?lat=' + lat + '&lng=' + lng)
                    .then(r => r.json())
                    .then(function (data) {
                        if (data.length) renderNearby(data);
                        else nearbyStatus.textContent = 'NO NEARBY EXCHANGES FOUND';
                    });
            },
            function () { nearbyStatus.textContent = 'LOCATION UNAVAILABLE'; },
            { timeout: 8000 }
        );
    } else {
        nearbyStatus.textContent = 'GEOLOCATION NOT SUPPORTED';
    }
})();
</script>

<?= $this->endSection() ?>
