<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e(config('app.name')) ?> - Admin</title>
    <style>
        body{font-family:Arial,sans-serif;margin:0;background:#f5f7fb;color:#1f2937}.container{max-width:1000px;margin:auto;padding:24px}.nav{background:#1f2937;color:#fff}.nav .container{display:flex;justify-content:space-between;align-items:center}.nav a{color:#fff;text-decoration:none;margin-left:14px}.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:16px;box-shadow:0 4px 14px rgba(0,0,0,.05)}input,textarea,select{width:100%;padding:10px;margin:6px 0 14px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box}.btn{display:inline-block;padding:9px 14px;border:0;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;cursor:pointer}.danger{background:#dc2626}.muted{color:#6b7280}.alert{padding:12px;border-radius:6px;margin-bottom:15px;background:#dcfce7}.error{background:#fee2e2}table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left}
    </style>
    <link rel="stylesheet" href="<?= assets('css/admin/app.css') ?>">
    <script>
        window.APP_URL = <?= json_encode(rtrim(url(), '/')) ?>;
    </script>
</head>
<body>
    <nav class="nav" style="background: #111827; border-bottom: 2px solid #3b82f6;">
        <div class="container">
            <a href="<?= url('admin/dashboard') ?>" style="display: flex; align-items: center;">
                <strong><?= e(config('app.name')) ?> Admin Panel</strong>
                <?php if (setting('maintenance_mode') === '1'): ?>
                    <span style="display: inline-flex; align-items: center; gap: 5px; margin-left: 10px; padding: 3px 9px; border-radius: 9999px; background: #dc2626; color: #fff; font-size: 0.72rem; font-weight: bold; letter-spacing: 0.04em;">
                        <span style="width: 6px; height: 6px; background: #fff; border-radius: 50%; display: inline-block;"></span>
                        Maintenance Active
                    </span>
                <?php endif; ?>
            </a>
            <div>
                <?php if ($user = app()->session()->get('user')): ?>
                    <a href="<?= url('admin/dashboard') ?>" style="margin-left: 14px; font-weight: bold; color: #60a5fa;">Dashboard</a>
                    <a class="d-none" href="<?= url('admin/posts') ?>">Manage Posts</a>
                    <a href="<?= url('admin/quizzes') ?>" style="margin-left: 14px;">Manage Quizzes</a>
                    <a href="<?= url('admin/settings') ?>" style="margin-left: 14px;">Manage Settings</a>
                    <a href="<?= url('admin/users') ?>" style="margin-left: 14px;">Manage Users</a>
                    <a href="<?= url('/') ?>" target="_blank" style="margin-left: 14px;">User Site</a>
                    <span class="muted" style="margin-left: 14px; margin-right: 14px; color: #d1d5db;">Hello, <?= e($user['name']) ?> (Admin)</span>
                    <form action="<?= url('logout') ?>" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="btn" type="submit">Logout</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </nav>

    <main class="container">
        <?php if (setting('maintenance_mode') === '1'): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span><strong>Maintenance Mode is Active:</strong> Public visitors cannot access the site. <a href="<?= url('admin/settings') ?>" style="color: #b91c1c; text-decoration: underline; font-weight: 600;">Manage Settings</a></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success = app()->session()->pullFlash('success')): ?>
            <div class="alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = app()->session()->pullFlash('error')): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <script src="<?= assets('js/admin/app.js') ?>"></script>
</body>
</html>
