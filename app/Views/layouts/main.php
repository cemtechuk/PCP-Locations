<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PCP Locations' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:     #c8001e;
            --red-dim: #8a0015;
            --ink:     #0d0d0d;
            --mid:     #555;
            --dim:     #999;
            --line:    #e0e0e0;
            --bg:      #f9f9f9;
            --mono:    'Share Tech Mono', monospace;
        }

        * { border-radius: 0 !important; }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: .875rem;
        }

        /* NAV */
        .s-nav {
            background: #fff;
            border-bottom: 2px solid var(--red);
            padding: .6rem 0;
        }
        .s-nav .brand {
            font-family: var(--mono);
            font-size: .95rem;
            color: var(--ink);
            text-decoration: none;
            letter-spacing: .04em;
        }
        .s-nav .brand span { color: var(--red); }
        .s-nav .nav-meta {
            font-family: var(--mono);
            font-size: .72rem;
            color: var(--dim);
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .s-nav a.nav-link-item {
            font-family: var(--mono);
            font-size: .72rem;
            color: var(--dim);
            text-decoration: none;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .s-nav a.nav-link-item:hover { color: var(--red); }

        /* SEARCH HERO */
        .s-hero {
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 2.5rem 0 2rem;
        }
        .s-hero .label {
            font-family: var(--mono);
            font-size: .7rem;
            color: var(--red);
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: .4rem;
        }
        .s-hero h1 {
            font-size: 1.5rem;
            font-weight: 300;
            letter-spacing: -.02em;
            color: var(--ink);
            margin-bottom: .3rem;
        }
        .s-hero .sub {
            font-family: var(--mono);
            font-size: .75rem;
            color: var(--dim);
        }

        /* INPUTS */
        @keyframes borderGlitch {
            0%   { border-color: var(--line); transform: none;            box-shadow: none; }
            91%  { border-color: var(--line); transform: none;            box-shadow: none; }
            92%  { border-color: var(--red);  transform: translateX(1px); box-shadow: -2px 0 0 rgba(200,0,30,.12); }
            93%  { border-color: var(--line); transform: translateX(-1px);box-shadow:  2px 0 0 rgba(200,0,30,.07); }
            94%  { border-color: rgba(200,0,30,.5); transform: none;      box-shadow: none; }
            95%  { border-color: var(--line); transform: none;            box-shadow: none; }
            100% { border-color: var(--line); transform: none;            box-shadow: none; }
        }

        .form-control, .form-select {
            border: 1px solid var(--line);
            background: #fff;
            font-size: .875rem;
            color: var(--ink);
            animation: borderGlitch 6s infinite;
        }
        .form-control:nth-of-type(2), .form-select:nth-of-type(2) { animation-delay: 1.4s; }
        .form-control:nth-of-type(3), .form-select:nth-of-type(3) { animation-delay: 2.9s; }
        .form-control:nth-of-type(4), .form-select:nth-of-type(4) { animation-delay: 0.7s; }
        .form-control:nth-of-type(5), .form-select:nth-of-type(5) { animation-delay: 3.8s; }

        .form-control:focus, .form-select:focus {
            border-color: var(--red);
            box-shadow: none;
            outline: none;
            animation: none;
        }

        /* BUTTONS */
        .btn-s-primary {
            background: var(--red);
            color: #fff;
            border: 1px solid var(--red);
            font-family: var(--mono);
            font-size: .75rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .45rem 1.2rem;
        }
        .btn-s-primary:hover {
            background: var(--red-dim);
            border-color: var(--red-dim);
            color: #fff;
        }
        .btn-s-ghost {
            background: transparent;
            color: var(--mid);
            border: 1px solid var(--line);
            font-family: var(--mono);
            font-size: .75rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .45rem 1rem;
        }
        .btn-s-ghost:hover { border-color: var(--red); color: var(--red); }

        /* TABLES */
        .s-table {
            background: #fff;
            border: 1px solid var(--line);
            width: 100%;
            border-collapse: collapse;
        }
        .s-table thead th {
            font-family: var(--mono);
            font-size: .68rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--dim);
            font-weight: 400;
            border-bottom: 1px solid var(--line);
            padding: .7rem 1rem;
            background: #fff;
        }
        .s-table tbody td {
            border-bottom: 1px solid #f0f0f0;
            padding: .7rem 1rem;
            font-size: .855rem;
        }
        .s-table tbody tr:hover td { background: #fafafa; }
        .s-table tbody tr:last-child td { border-bottom: none; }

        /* MONO DATA */
        .mono { font-family: var(--mono); }
        .cab-id {
            font-family: var(--mono);
            font-size: .85rem;
            color: var(--red);
            letter-spacing: .04em;
        }

        /* CARDS */
        .s-card {
            background: #fff;
            border: 1px solid var(--line);
        }

        /* MISC */
        .s-label {
            font-family: var(--mono);
            font-size: .68rem;
            color: var(--red);
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .map-link {
            font-family: var(--mono);
            font-size: .72rem;
            color: var(--dim);
            text-decoration: none;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .map-link:hover { color: var(--red); }
        footer {
            font-family: var(--mono);
            font-size: .7rem;
            color: var(--dim);
            letter-spacing: .06em;
            border-top: 1px solid var(--line);
            padding: 1.5rem 0;
            text-align: center;
        }
        a { color: var(--ink); }
        a:hover { color: var(--red); }

        /* ALERTS */
        .s-alert-error {
            background: #fff5f5;
            border: 1px solid #fca5a5;
            border-left: 3px solid var(--red);
            color: #7f1d1d;
            padding: .6rem 1rem;
            font-size: .85rem;
        }
        .s-alert-success {
            background: #f9f9f9;
            border: 1px solid var(--line);
            border-left: 3px solid #555;
            color: var(--ink);
            padding: .6rem 1rem;
            font-size: .85rem;
        }
    </style>
</head>
<body>

<nav class="s-nav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="/" class="brand" style="display:flex; align-items:center; gap:.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 30 28" style="flex-shrink:0; margin-bottom:1px;">
                <polygon points="2,2 28,2 15,26" fill="none" stroke="#c8001e" stroke-width="2.5" stroke-linejoin="miter"/>
                <circle cx="15" cy="10" r="1.8" fill="#c8001e"/>
            </svg>
            PCP<span>_</span>LOCATIONS
        </a>
        <div class="d-flex align-items-center gap-4">
<?php if (session()->get('role') === 'admin'): ?><a href="/users" class="nav-link-item">Users</a><a href="/apikeys" class="nav-link-item">API Keys</a><a href="/import" class="nav-link-item">Import</a><?php endif ?>
            <span class="nav-meta"><?= esc(session()->get('username') ?? '') ?></span>
            <a href="/logout" class="nav-link-item">Logout</a>
        </div>
    </div>
</nav>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer>
    <div class="container">
        PCP LOCATIONS &nbsp;&mdash;&nbsp; <?= number_format($totalCount ?? 0) ?> RECORDS
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
