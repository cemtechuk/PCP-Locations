<div style="background:#fff; border-bottom:1px solid #e0e0e0; padding:1.5rem 0 0;">
    <div class="container">
        <div class="s-label mb-2">ADMIN</div>
        <h2 style="font-family:'Share Tech Mono',monospace; font-size:1.1rem; font-weight:400; letter-spacing:.04em; margin:0 0 1rem;">
            Settings
        </h2>
        <div style="display:flex; gap:0; border-bottom:none;">
            <?php
            $tabs = [
                'general' => ['label' => 'General',  'url' => '/settings'],
                'import'  => ['label' => 'Import',   'url' => '/settings/import'],
                'apikeys' => ['label' => 'API Keys', 'url' => '/settings/apikeys'],
            ];
            foreach ($tabs as $key => $tab):
                $active = ($activeTab === $key);
            ?>
            <a href="<?= $tab['url'] ?>"
               style="font-family:'Share Tech Mono',monospace; font-size:.72rem; letter-spacing:.06em;
                      text-decoration:none; padding:.5rem 1.2rem;
                      border:1px solid <?= $active ? '#e0e0e0' : 'transparent' ?>;
                      border-bottom:<?= $active ? '2px solid #fff' : '1px solid transparent' ?>;
                      margin-bottom:<?= $active ? '-1px' : '0' ?>;
                      color:<?= $active ? '#0d0d0d' : '#999' ?>;
                      background:<?= $active ? '#fff' : 'transparent' ?>;">
                <?= $tab['label'] ?>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</div>
