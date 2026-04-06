<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('settings/_tabs', ['activeTab' => 'general']) ?>

<div class="container py-4" style="max-width:640px;">

    <?php if ($success): ?>
        <div class="s-alert-success mb-4"><?= esc($success) ?></div>
    <?php endif ?>
    <?php if ($errors): ?>
        <div class="s-alert-error mb-4">
            <?php foreach ((array) $errors as $e): ?><div><?= esc($e) ?></div><?php endforeach ?>
        </div>
    <?php endif ?>

    <form method="post" action="/settings" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Site Title -->
        <div style="background:#fff; border:1px solid #e0e0e0; padding:1.25rem 1.5rem; margin-bottom:1rem;">
            <div class="s-label mb-2">SITE TITLE</div>
            <input type="text" name="site_title" class="form-control"
                   value="<?= esc(setting('site_title', 'PCP Locations')) ?>"
                   placeholder="PCP Locations">
            <div style="font-size:.72rem; color:#999; margin-top:.35rem;">
                Appears in the navigation bar and browser tab.
            </div>
        </div>

        <!-- Logo -->
        <div style="background:#fff; border:1px solid #e0e0e0; padding:1.25rem 1.5rem; margin-bottom:1rem;">
            <div class="s-label mb-2">LOGO</div>

            <?php $logo = setting('logo_path'); ?>
            <?php if ($logo): ?>
                <div style="margin-bottom:.8rem; display:flex; align-items:center; gap:1rem;">
                    <img src="/uploads/settings/<?= esc($logo) ?>" alt="Logo"
                         style="max-height:48px; max-width:200px; border:1px solid #e0e0e0; padding:.3rem; background:#fafafa;">
                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                        <input type="checkbox" name="remove_logo" value="1">
                        <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#c8001e; letter-spacing:.04em;">REMOVE</span>
                    </label>
                </div>
            <?php endif ?>

            <input type="file" name="logo" accept=".png,.jpg,.jpeg,.gif,.svg,.webp"
                   style="font-family:'Share Tech Mono',monospace; font-size:.78rem; color:#555; padding:.4rem; border:1px solid #e0e0e0; background:#fafafa; width:100%; cursor:pointer;">
            <div style="font-size:.72rem; color:#999; margin-top:.35rem;">
                PNG, JPG, SVG or WEBP. Displayed in the nav bar in place of the text brand. Leave empty to keep the current logo.
            </div>
        </div>

        <!-- Favicon -->
        <div style="background:#fff; border:1px solid #e0e0e0; padding:1.25rem 1.5rem; margin-bottom:1.5rem;">
            <div class="s-label mb-2">FAVICON</div>

            <?php $fav = setting('favicon_path'); ?>
            <?php if ($fav): ?>
                <div style="margin-bottom:.8rem; display:flex; align-items:center; gap:1rem;">
                    <img src="/uploads/settings/<?= esc($fav) ?>" alt="Favicon"
                         style="width:32px; height:32px; border:1px solid #e0e0e0; padding:.2rem; background:#fafafa; object-fit:contain;">
                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                        <input type="checkbox" name="remove_favicon" value="1">
                        <span style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#c8001e; letter-spacing:.04em;">REMOVE</span>
                    </label>
                </div>
            <?php endif ?>

            <input type="file" name="favicon" accept=".ico,.png,.svg"
                   style="font-family:'Share Tech Mono',monospace; font-size:.78rem; color:#555; padding:.4rem; border:1px solid #e0e0e0; background:#fafafa; width:100%; cursor:pointer;">
            <div style="font-size:.72rem; color:#999; margin-top:.35rem;">
                ICO, PNG or SVG. Leave empty to keep the current favicon.
            </div>
        </div>

        <button type="submit" class="btn-s-primary">Save Settings</button>
    </form>
</div>

<?= $this->endSection() ?>
