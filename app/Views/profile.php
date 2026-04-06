<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width:560px; padding-top:2.5rem; padding-bottom:3rem;">

    <p class="s-label mb-1">Account</p>
    <h1 style="font-size:1.4rem; font-weight:300; letter-spacing:-.02em; margin-bottom:2rem;">My Profile</h1>

    <?php if ($errors): ?>
        <div class="s-alert-error mb-3">
            <?php foreach ($errors as $e): ?>
                <div><?= esc($e) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?php if ($success): ?>
        <div class="s-alert-success mb-3"><?= esc($success) ?></div>
    <?php endif ?>

    <form method="post" action="/profile" class="s-card p-4 mb-4">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label s-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="<?= esc(old('username', $user['username'])) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label s-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= esc(old('email', $user['email'])) ?>" required>
        </div>

        <hr style="border-color:var(--line); margin:1.5rem 0;">
        <p class="s-label mb-3" style="color:var(--dim);">Change Password — leave blank to keep current</p>

        <div class="mb-3">
            <label class="form-label s-label">New Password</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label s-label">Confirm New Password</label>
            <input type="password" name="password_confirm" class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-s-primary">Save Changes</button>
    </form>

    <div class="s-card p-4">
        <p class="s-label mb-1">Session</p>
        <p style="font-size:.85rem; color:var(--mid); margin-bottom:1.25rem;">
            Signed in as <strong class="mono"><?= esc(session()->get('username')) ?></strong>
            &nbsp;·&nbsp; Role: <span class="mono"><?= esc(session()->get('role')) ?></span>
        </p>
        <form method="get" action="/logout">
            <button type="submit" class="btn"
                    style="background:var(--red); color:#fff; border:1px solid var(--red);
                           font-family:var(--mono); font-size:.75rem; letter-spacing:.08em;
                           text-transform:uppercase; padding:.45rem 1.2rem;">
                Logout
            </button>
        </form>
    </div>

</div>

<?= $this->endSection() ?>
