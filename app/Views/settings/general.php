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

            <!-- Tab toggle -->
            <div style="display:flex; gap:0; margin-bottom:.75rem; border-bottom:1px solid #e0e0e0;">
                <button type="button" id="fav-tab-file" onclick="switchFavTab('file')"
                        style="font-family:'Share Tech Mono',monospace; font-size:.68rem; letter-spacing:.06em; padding:.35rem .9rem; border:1px solid #e0e0e0; border-bottom:none; background:#fff; cursor:pointer; margin-bottom:-1px; color:#0d0d0d;">
                    UPLOAD FILE
                </button>
                <button type="button" id="fav-tab-svg" onclick="switchFavTab('svg')"
                        style="font-family:'Share Tech Mono',monospace; font-size:.68rem; letter-spacing:.06em; padding:.35rem .9rem; border:1px solid transparent; border-bottom:none; background:transparent; cursor:pointer; margin-bottom:-1px; color:#999;">
                    PASTE SVG
                </button>
            </div>

            <div id="fav-panel-file">
                <input type="file" name="favicon" accept=".ico,.png,.svg"
                       style="font-family:'Share Tech Mono',monospace; font-size:.78rem; color:#555; padding:.4rem; border:1px solid #e0e0e0; background:#fafafa; width:100%; cursor:pointer;">
                <div style="font-size:.72rem; color:#999; margin-top:.35rem;">ICO, PNG or SVG file.</div>
            </div>

            <div id="fav-panel-svg" style="display:none;">
                <textarea name="favicon_svg" rows="6" class="form-control"
                          style="font-family:'Share Tech Mono',monospace; font-size:.75rem; resize:vertical;"
                          placeholder="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 32 32&quot;>...</svg>"><?= esc(old('favicon_svg')) ?></textarea>
                <div style="font-size:.72rem; color:#999; margin-top:.35rem;">Paste a valid SVG string. The <code>&lt;svg&gt;</code> tag must be present.</div>
                <div id="fav-svg-preview" style="margin-top:.6rem; display:none; align-items:center; gap:.6rem;">
                    <div id="fav-svg-render" style="width:32px; height:32px; border:1px solid #e0e0e0; background:#fafafa; display:flex; align-items:center; justify-content:center; overflow:hidden;"></div>
                    <span style="font-family:'Share Tech Mono',monospace; font-size:.68rem; color:#999; letter-spacing:.04em;">PREVIEW</span>
                </div>
            </div>

            <div style="font-size:.72rem; color:#999; margin-top:.5rem;">Leave both empty to keep the current favicon.</div>
        </div>

        <button type="submit" class="btn-s-primary">Save Settings</button>
    </form>
</div>

<script>
function switchFavTab(tab) {
    var isFile = tab === 'file';
    document.getElementById('fav-panel-file').style.display = isFile ? '' : 'none';
    document.getElementById('fav-panel-svg').style.display  = isFile ? 'none' : '';

    var tabFile = document.getElementById('fav-tab-file');
    var tabSvg  = document.getElementById('fav-tab-svg');

    tabFile.style.border        = isFile ? '1px solid #e0e0e0' : '1px solid transparent';
    tabFile.style.borderBottom  = 'none';
    tabFile.style.background    = isFile ? '#fff' : 'transparent';
    tabFile.style.color         = isFile ? '#0d0d0d' : '#999';

    tabSvg.style.border        = isFile ? '1px solid transparent' : '1px solid #e0e0e0';
    tabSvg.style.borderBottom  = 'none';
    tabSvg.style.background    = isFile ? 'transparent' : '#fff';
    tabSvg.style.color         = isFile ? '#999' : '#0d0d0d';
}

// Live SVG preview
document.querySelector('textarea[name="favicon_svg"]').addEventListener('input', function () {
    var val     = this.value.trim();
    var preview = document.getElementById('fav-svg-preview');
    var render  = document.getElementById('fav-svg-render');

    if (val.toLowerCase().includes('<svg')) {
        render.innerHTML = val;
        var svg = render.querySelector('svg');
        if (svg) {
            svg.style.width  = '100%';
            svg.style.height = '100%';
        }
        preview.style.display = 'flex';
    } else {
        preview.style.display = 'none';
        render.innerHTML = '';
    }
});
</script>

<?= $this->endSection() ?>
