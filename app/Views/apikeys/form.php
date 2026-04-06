<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container">
        <div class="s-label mb-1">ADMIN / <a href="/apikeys" style="color:#999; text-decoration:none;">API KEYS</a> / NEW</div>
        <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">New API Key</h2>
    </div>
</div>

<div class="container py-4">
    <div style="max-width:480px;">

        <?php if (session()->getFlashdata('error')): ?>
        <div style="border-left:3px solid #c8001e; padding:.7rem 1rem; margin-bottom:1.2rem; font-size:.85rem; color:#c8001e;">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
        <?php endif ?>

        <form method="post" action="/apikeys/create">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="s-label d-block mb-1">KEY NAME / PURPOSE</label>
                <input type="text" name="name" class="form-control"
                       placeholder="e.g. MyOtherApp Production"
                       value="<?= esc(old('name')) ?>" required>
                <div style="font-size:.72rem; color:#999; margin-top:.3rem;">
                    Descriptive label so you know which app is using this key.
                </div>
            </div>

            <div style="display:flex; gap:.6rem; margin-top:1.2rem;">
                <button type="submit" class="btn-s-primary">Generate Key</button>
                <a href="/apikeys" class="btn-s-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
