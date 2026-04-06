<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-dark text-white py-3">
    <div class="container">
        <form action="/search" method="get">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Exchange, cabinet number or address..."
                        value="<?= esc($query) ?>"
                    >
                </div>
                <div class="col-6 col-md-3">
                    <select name="region" class="form-select">
                        <option value="">All regions</option>
                        <?php foreach ($regions as $r): ?>
                            <option value="<?= esc($r['db']) ?>" <?= $region === $r['db'] ? 'selected' : '' ?>>
                                <?= esc($r['db_name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                </div>
                <div class="col-auto">
                    <a href="/" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container py-4">

    <?php if ($query !== '' || $region !== '' || $exchange !== ''): ?>
        <p class="text-muted mb-3">
            <?= number_format($totalFound) ?> result<?= $totalFound !== 1 ? 's' : '' ?>
            <?php if ($query !== ''): ?> for <strong>"<?= esc($query) ?>"</strong><?php endif ?>
            <?php if ($region !== ''): ?>
                in <strong><?= esc(array_column($regions, 'db_name', 'db')[$region] ?? $region) ?></strong>
            <?php endif ?>
        </p>
    <?php endif ?>

    <?php if (empty($results)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No cabinets found. Try a broader search term or remove filters.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white rounded shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Region</th>
                        <th>Exchange</th>
                        <th>Cabinet</th>
                        <th>Address</th>
                        <th class="text-center">Map</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $cab): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary badge-region"><?= esc($cab['db']) ?></span>
                            <br><small class="text-muted"><?= esc($cab['db_name']) ?></small>
                        </td>
                        <td class="fw-semibold"><?= esc($cab['exchange']) ?></td>
                        <td><span class="badge bg-primary fs-6"><?= esc($cab['cab']) ?></span></td>
                        <td class="text-muted small"><?= esc($cab['address']) ?></td>
                        <td class="text-center">
                            <?php if ($cab['lat'] && $cab['lng']): ?>
                                <a href="https://www.google.com/maps?q=<?= $cab['lat'] ?>,<?= $cab['lng'] ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-success maps-btn"
                                   title="Open in Google Maps">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <a href="/cabinet/<?= $cab['id'] ?>" class="btn btn-sm btn-outline-primary">
                                Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links('default', 'bootstrap_pagination') ?>
            </div>
        <?php endif ?>
    <?php endif ?>
</div>

<?= $this->endSection() ?>
