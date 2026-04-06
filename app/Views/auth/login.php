<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCP Locations — Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { border-radius: 0 !important; }
        body {
            background: #f0f0f0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 360px;
        }
        .login-header {
            border-left: 3px solid #c8001e;
            padding-left: 1rem;
            margin-bottom: 2rem;
        }
        .login-header .sys {
            font-family: 'Share Tech Mono', monospace;
            font-size: .68rem;
            color: #c8001e;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .login-header h1 {
            font-family: 'Share Tech Mono', monospace;
            font-size: 1.1rem;
            font-weight: 400;
            letter-spacing: .06em;
            color: #0d0d0d;
            margin: .2rem 0 0;
        }
        .login-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 2rem;
        }
        label {
            font-family: 'Share Tech Mono', monospace;
            font-size: .7rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: .3rem;
            display: block;
        }
        .form-control {
            font-family: 'Share Tech Mono', monospace;
            font-size: .85rem;
            border: 1px solid #e0e0e0;
            color: #0d0d0d;
            padding: .5rem .75rem;
        }
        .form-control:focus {
            border-color: #c8001e;
            box-shadow: none;
            outline: none;
        }
        .btn-submit {
            background: #c8001e;
            color: #fff;
            border: none;
            font-family: 'Share Tech Mono', monospace;
            font-size: .75rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .6rem 1.5rem;
            width: 100%;
            cursor: pointer;
        }
        .btn-submit:hover { background: #8a0015; }
        .error-box {
            background: #fff5f5;
            border: 1px solid #fca5a5;
            border-left: 3px solid #c8001e;
            color: #7f1d1d;
            padding: .6rem .9rem;
            font-size: .82rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

<div class="login-wrap">
    <div class="login-header">
        <div class="sys">System Access</div>
        <h1>PCP_LOCATIONS</h1>
    </div>

    <div class="login-card">
        <?php if ($error): ?>
            <div class="error-box"><?= esc($error) ?></div>
        <?php endif ?>

        <form method="post" action="/login">
            <?= csrf_field() ?>
            <div style="margin-bottom:1.2rem;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" autofocus autocomplete="username" required>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-submit">Authenticate</button>
        </form>
    </div>
</div>

</body>
</html>
