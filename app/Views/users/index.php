<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0;">
    <div class="container d-flex align-items-center justify-content-between">
        <div>
            <div class="s-label mb-1">System</div>
            <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0;">
                User Accounts
            </h2>
        </div>
        <a href="/users/create" class="btn-s-primary">+ Add User</a>
    </div>
</div>

<div class="container py-4">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="s-alert-success mb-3"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="s-alert-error mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif ?>

    <?php $roleColour = ['guest' => '#d4a017', 'viewer' => '#bbb', 'user' => '#999', 'editor' => '#555', 'admin' => '#c8001e']; ?>
    <table class="s-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th style="width:80px;">Role</th>
                <th style="width:110px;">Created</th>
                <th style="width:120px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <span style="font-family:'Share Tech Mono',monospace; font-size:.85rem;"><?= esc($user['username']) ?></span>
                    <?php if ($user['id'] === session()->get('user_id')): ?>
                        <span style="font-family:'Share Tech Mono',monospace; font-size:.65rem; color:#c8001e; letter-spacing:.06em; margin-left:.5rem;">YOU</span>
                    <?php endif ?>
                </td>
                <td style="color:#555;"><?= esc($user['email']) ?></td>
                <td style="font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em; text-transform:uppercase; color:<?= $roleColour[$user['role'] ?? 'user'] ?>;">
                    <?= esc($user['role'] ?? 'user') ?>
                </td>
                <td style="font-family:'Share Tech Mono',monospace; font-size:.75rem; color:#999;">
                    <?= date('d M Y', strtotime($user['created_at'] . ' UTC')) ?>
                </td>
                <td style="text-align:right;">
                    <a href="/users/edit/<?= $user['id'] ?>"
                       style="font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#555; text-decoration:none; letter-spacing:.04em; text-transform:uppercase; margin-right:.8rem;">
                        Edit
                    </a>
                    <?php if ($user['id'] !== session()->get('user_id')): ?>
                    <form method="post" action="/users/delete/<?= $user['id'] ?>" class="d-inline"
                          onsubmit="return confirm('Delete <?= esc($user['username']) ?>?')">
                        <?= csrf_field() ?>
                        <button type="submit"
                                style="background:none; border:none; padding:0; font-family:'Share Tech Mono',monospace; font-size:.72rem; color:#c8001e; letter-spacing:.04em; text-transform:uppercase; cursor:pointer;">
                            Delete
                        </button>
                    </form>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
