<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name')) ?> - Admin</title>
    <style>
        body{font-family:Arial,sans-serif;margin:0;background:#f5f7fb;color:#1f2937}.container{max-width:1000px;margin:auto;padding:24px}.nav{background:#1f2937;color:#fff}.nav .container{display:flex;justify-content:space-between;align-items:center}.nav a{color:#fff;text-decoration:none;margin-left:14px}.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:16px;box-shadow:0 4px 14px rgba(0,0,0,.05)}input,textarea,select{width:100%;padding:10px;margin:6px 0 14px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box}.btn{display:inline-block;padding:9px 14px;border:0;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;cursor:pointer}.danger{background:#dc2626}.muted{color:#6b7280}.alert{padding:12px;border-radius:6px;margin-bottom:15px;background:#dcfce7}.error{background:#fee2e2}table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left}
    </style>
    <link rel="stylesheet" href="<?= assets('css/admin/app.css') ?>">
</head>
<body>
    <nav class="nav" style="background: #111827; border-bottom: 2px solid #3b82f6;">
        <div class="container">
            <a href="/"><strong><?= e(config('app.name')) ?> Admin Panel</strong></a>
            <div>
                <?php if ($user = app()->session()->get('user')): ?>
                    <a href="/admin/dashboard" style="margin-left: 14px; font-weight: bold; color: #60a5fa;">Dashboard</a>
                    <a class="d-none" href="/admin/posts">Manage Posts</a>
                    <a href="/admin/quizzes" style="margin-left: 14px;">Manage Quizzes</a>
                    <a href="/admin/settings" style="margin-left: 14px;">Manage Settings</a>
                    <a href="/admin/users" style="margin-left: 14px;">Manage Users</a>
                    <span class="muted" style="margin-left: 14px; margin-right: 14px; color: #d1d5db;">Hello, <?= e($user['name']) ?> (Admin)</span>
                    <form action="/logout" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="btn" type="submit">Logout</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </nav>

    <main class="container">
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
