<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('settings/_tabs', ['activeTab' => 'apikeys']) ?>

<div class="container py-4">
    <div style="max-width:480px;">

        <div style="margin-bottom:1.2rem;">
            <a href="/settings/apikeys" style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#999; text-decoration:none; letter-spacing:.06em;">&larr; BACK TO API KEYS</a>
        </div>

        <?php if ($error): ?>
        <div class="s-alert-error mb-3"><?= esc($error) ?></div>
        <?php endif ?>

        <div style="background:#fff; border:1px solid #e0e0e0; padding:1.5rem;">
            <div class="s-label mb-3">NEW API KEY</div>
            <form method="post" action="/settings/apikeys/new">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="s-label d-block mb-1">KEY NAME / PURPOSE</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="e.g. MyApp Production"
                           value="<?= esc(old('name')) ?>" required>
                    <div style="font-size:.72rem; color:#999; margin-top:.3rem;">
                        Descriptive label so you know which app is using this key.
                    </div>
                </div>
                <div style="display:flex; gap:.6rem; margin-top:1.2rem;">
                    <button type="submit" class="btn-s-primary">Generate Key</button>
                    <a href="/settings/apikeys" class="btn-s-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
