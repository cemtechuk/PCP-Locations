<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $isEdit = $cabinet !== null; ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container">
        <div class="s-label mb-1">
            <a href="/" style="color:#999; text-decoration:none;">Index</a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <a href="/exchange/<?= urlencode($db) ?>/<?= urlencode($exchange) ?>"
               style="color:#999; text-decoration:none;"><?= esc($exchange) ?></a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <?= $isEdit ? esc($cabinet['cab']) : 'New Cabinet' ?>
        </div>
        <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">
            <?= $isEdit ? 'Edit Cabinet' : 'Add Cabinet' ?>
        </h2>
    </div>
</div>

<div class="container py-4" style="max-width:560px; margin-left:0;">

    <?php if ($errors): ?>
        <div class="s-alert-error mb-3">
            <ul style="margin:0; padding-left:1.1rem;">
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="s-alert-success mb-3"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif ?>

    <div class="s-card" style="padding:2rem;">
        <form method="post" action="<?= $isEdit ? '/cabinet/edit/' . $cabinet['id'] : '/cabinet/create' ?>">
            <?= csrf_field() ?>

            <?php if (! $isEdit): ?>
            <!-- Hidden exchange context fields for new cabinets -->
            <input type="hidden" name="db"       value="<?= esc($db) ?>">
            <input type="hidden" name="db_name"  value="<?= esc($db_name ?? $cabinet['db_name'] ?? '') ?>">
            <input type="hidden" name="exchange"  value="<?= esc($exchange) ?>">
            <?php endif ?>

            <div style="margin-bottom:1.2rem;">
                <label class="s-field-label">Cabinet Number</label>
                <input type="text" name="cab" class="form-control"
                       style="font-family:'Share Tech Mono',monospace; font-size:.875rem; text-transform:uppercase;"
                       value="<?= esc(old('cab', $cabinet['cab'] ?? '')) ?>"
                       placeholder="e.g. P1 or EXCH"
                       required maxlength="20">
            </div>

            <div style="margin-bottom:1.2rem;">
                <label class="s-field-label">Address</label>
                <input type="text" name="address" class="form-control"
                       value="<?= esc(old('address', $cabinet['address'] ?? '')) ?>"
                       maxlength="500">
            </div>

            <div style="margin-bottom:1.2rem; display:flex; gap:1rem;">
                <div style="flex:1;">
                    <label class="s-field-label">Latitude</label>
                    <input type="text" name="lat" class="form-control"
                           style="font-family:'Share Tech Mono',monospace; font-size:.85rem;"
                           value="<?= esc(old('lat', $cabinet['lat'] ?? '')) ?>"
                           placeholder="51.5074">
                </div>
                <div style="flex:1;">
                    <label class="s-field-label">Longitude</label>
                    <input type="text" name="lng" class="form-control"
                           style="font-family:'Share Tech Mono',monospace; font-size:.85rem;"
                           value="<?= esc(old('lng', $cabinet['lng'] ?? '')) ?>"
                           placeholder="-0.1278">
                </div>
            </div>

            <div id="coordHelper" style="margin-bottom:1.2rem;">
                <button type="button" onclick="useMyLocation()"
                        class="btn-s-ghost" style="padding:.3rem .8rem; font-size:.72rem;">
                    Use my location
                </button>
                <span id="coordStatus" style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:#999; margin-left:.6rem;"></span>
            </div>

            <div style="margin-bottom:2rem;">
                <label class="s-field-label">Notes</label>
                <input type="text" name="notes" class="form-control"
                       value="<?= esc(old('notes', $cabinet['notes'] ?? '')) ?>"
                       maxlength="500">
            </div>

            <div style="display:flex; gap:.6rem;">
                <button type="submit" class="btn-s-primary">
                    <?= $isEdit ? 'Save Changes' : 'Add Cabinet' ?>
                </button>
                <a href="/exchange/<?= urlencode($db) ?>/<?= urlencode($exchange) ?>" class="btn-s-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .s-field-label {
        font-family: 'Share Tech Mono', monospace;
        font-size: .7rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #999;
        display: block;
        margin-bottom: .3rem;
    }
</style>

<script>
function useMyLocation() {
    const status = document.getElementById('coordStatus');
    status.textContent = 'LOCATING...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.querySelector('[name="lat"]').value = pos.coords.latitude.toFixed(8);
        document.querySelector('[name="lng"]').value = pos.coords.longitude.toFixed(8);
        status.textContent = 'DONE';
        setTimeout(() => status.textContent = '', 2000);
    }, function() {
        status.textContent = 'UNAVAILABLE';
    });
}
</script>

<?= $this->endSection() ?>
