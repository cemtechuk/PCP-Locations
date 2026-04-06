<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $isEdit = $user !== null; ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container">
        <div class="s-label mb-1">
            <a href="/users" style="color:#999; text-decoration:none;">Users</a>
            <span style="color:#ddd; margin:0 .5rem;">/</span>
            <?= $isEdit ? 'Edit' : 'New' ?>
        </div>
        <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">
            <?= $isEdit ? esc($user['username']) : 'Add User' ?>
        </h2>
    </div>
</div>

<div class="container py-4" style="max-width:500px; margin-left:0;">

    <?php if ($errors): ?>
        <div class="s-alert-error mb-3">
            <ul style="margin:0; padding-left:1.1rem;">
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <div class="s-card" style="padding:2rem;">
        <form method="post" action="<?= $isEdit ? "/users/edit/{$user['id']}" : '/users/create' ?>">
            <?= csrf_field() ?>

            <div style="margin-bottom:1.2rem;">
                <label style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; color:#999; display:block; margin-bottom:.3rem;">Username</label>
                <input type="text" name="username" class="form-control"
                       style="font-family:'Share Tech Mono',monospace; font-size:.875rem;"
                       value="<?= esc(old('username', $user['username'] ?? '')) ?>"
                       required minlength="3" maxlength="50">
            </div>

            <div style="margin-bottom:1.2rem;">
                <label style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; color:#999; display:block; margin-bottom:.3rem;">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= esc(old('email', $user['email'] ?? '')) ?>"
                       required maxlength="150">
            </div>

            <div style="margin-bottom:1.2rem;">
                <label style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; color:#999; display:block; margin-bottom:.3rem;">Role</label>
                <select name="role" class="form-control" style="font-family:'Share Tech Mono',monospace; font-size:.85rem;">
                    <?php
                    $currentRole = old('role', $user['role'] ?? 'user');
                    foreach (['guest' => 'Guest', 'viewer' => 'Viewer', 'user' => 'User', 'editor' => 'Editor', 'admin' => 'Admin'] as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= $currentRole === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div style="margin-bottom:2rem;">
                <label style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.1em; text-transform:uppercase; color:#999; display:block; margin-bottom:.3rem;">
                    Password<?= $isEdit ? ' <span style="color:#bbb;">(blank = keep current)</span>' : '' ?>
                </label>
                <input type="password" name="password" class="form-control"
                       style="font-family:'Share Tech Mono',monospace; font-size:.875rem;"
                       <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
            </div>

            <div style="display:flex; gap:.6rem;">
                <button type="submit" class="btn-s-primary">
                    <?= $isEdit ? 'Save' : 'Create' ?>
                </button>
                <a href="/users" class="btn-s-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
